#!/usr/bin/env node

/*
 * Sikhun PDF worker — single-purpose Node process that turns ONE book PDF
 * into the canonical format the Laravel importer consumes.
 *
 *   Parser: OpenDataLoader PDF (official npm SDK `@opendataloader/pdf`,
 *   backed by a local Java engine — no cloud, no GPU).
 *
 *   WHY a separate worker? PDF parsing is CPU/memory heavy and must never
 *   run inside an HTTP request or be reimplemented in PHP. Laravel's queue
 *   job (ProcessBookPdf) spawns this worker via the Process facade with a
 *   safe argv array (never shell strings) and reads back its output.
 *
 *   OUTPUT  {outputDir}/
 *     manifest.json   small index: page count, parser/version info
 *     page-0001.json  one file per page (bounded memory — Laravel streams
 *                     pages one at a time instead of loading one giant JSON)
 *     images/…        extracted image binaries, referenced via `file`
 *     book.md         secondary Markdown (informational, not primary)
 *
 *   EXIT  0 success · 1 OpenDataLoader/conversion failure · 2 not runnable
 *         (missing SDK/Java) — stderr carries the human-readable reason.
 *
 *   INVOKE  node parse.js --input <pdf> --output <dir> [--ocr-lang <lang>]
 *
 * The canonical shape is deliberately vendor-neutral, so OpenDataLoader's
 * own JSON schema (a flat `kids` array — see normalizers below) is fully
 * absorbed here and never leaks into Laravel or the database.
 */

import { spawnSync } from 'node:child_process';
import { createHash } from 'node:crypto';
import { mkdirSync, writeFileSync, readFileSync, readdirSync, statSync, existsSync } from 'node:fs';
import { dirname, extname, join } from 'node:path';

const ARGS = parseArgs(process.argv.slice(2));

if (ARGS.help) {
  printHelp();
  process.exit(0);
}

if (!ARGS.input || !ARGS.output) {
  console.error('Usage: node parse.js --input <pdf> --output <dir> [--ocr-lang <lang>]');
  process.exit(2);
}

try {
  await run();
  process.exit(0);
} catch (err) {
  console.error(err?.message || String(err));
  process.exit(err?.kind === 'unrunnable' ? 2 : 1);
}

async function run() {
  verifyJava();
  const { convert } = await loadOdl();

  const outDir = ARGS.output;
  mkdirSync(outDir, { recursive: true });

  // OpenDataLoader converts to JSON (primary, structured) + Markdown
  // (secondary reference). Each invocation spawns a JVM, so batch one file.
  await convert([ARGS.input], {
    outputDir: outDir,
    format: 'json,markdown',
    ...(ARGS.ocrLang ? { ocrLang: ARGS.ocrLang } : {}),
  });

  const odlJsonFile = findOdlJson(outDir);
  if (!odlJsonFile) {
    throw new Error('OpenDataLoader produced no JSON output.');
  }

  const odl = JSON.parse(readFileSync(odlJsonFile, 'utf8'));
  const pages = extractPages(odl);

  if (pages.length === 0) {
    throw new Error('OpenDataLoader produced a JSON with no readable pages.');
  }

  const imagesDir = join(outDir, 'images');
  mkdirSync(imagesDir, { recursive: true });

  for (const page of pages) {
    for (const element of page.elements) {
      if (element.type === 'image') {
        materializeImage(element, imagesDir, outDir);
      }
    }
  }

  // One file per page keeps Laravel's memory bounded for large PDFs.
  for (const page of pages) {
    writeFileSync(
      join(outDir, `page-${String(page.page).padStart(4, '0')}.json`),
      JSON.stringify(page),
    );
  }

  writeFileSync(
    join(outDir, 'manifest.json'),
    JSON.stringify({
      parser: 'opendataloader',
      odl_version: odl.version ?? odl['api version'] ?? 'unknown',
      sdk_version: odlSdkVersion(),
      pages: pages.length,
      generated_at: new Date().toISOString(),
    }),
  );
}

function verifyJava() {
  const res = spawnSync('java', ['-version'], { encoding: 'utf8', timeout: 15_000 });
  if (res.status !== 0) {
    throw unrunnable(
      'OpenDataLoader requires Java 11+ on PATH (java -version). ' +
        'Install a JDK (e.g. Adoptium) and re-run.',
    );
  }
}

async function loadOdl() {
  try {
    return await import('@opendataloader/pdf');
  } catch (err) {
    throw unrunnable(
      '@opendataloader/pdf SDK is not installed. Run `npm install` inside pdf-worker/ ' +
        `(${err?.code || err?.message || 'load failed'}).`,
    );
  }
}

function odlSdkVersion() {
  try {
    const pkg = JSON.parse(
      readFileSync(join(dirname(process.argv[1]), 'node_modules', '@opendataloader', 'pdf', 'package.json'), 'utf8'),
    );
    return pkg.version ?? 'unknown';
  } catch {
    return 'unknown';
  }
}

function findOdlJson(dir) {
  let best = null;
  let bestSize = -1;

  const walk = (d) => {
    for (const entry of readdirSync(d)) {
      const full = join(d, entry);
      const st = statSync(full);
      if (st.isDirectory()) {
        walk(full);
      } else if (entry.toLowerCase().endsWith('.json') && !entry.startsWith('page-') && entry !== 'manifest.json') {
        if (st.size > bestSize) {
          bestSize = st.size;
          best = full;
        }
      }
    }
  };

  walk(dir);
  return best;
}

/**
 * OpenDataLoader emits ONE document object:
 *
 *   { "file name", "number of pages", "title", …, "kids": [
 *       { "type": "heading", "id": 1, "page number": 1,
 *         "bounding box": [x0,y0,x1,y1], "heading level": 1,
 *         "content": "…", … },
 *       { "type": "paragraph", "id": 2, "page number": 1, "content": "…" },
 *       … ] }
 *
 * `kids` is a FLAT reading-order list across every page. We group by
 * `page number` and write one canonical page file each, keeping page text
 * (concatenated element content) for the cheap full-text/FULLTEXT fallback.
 *
 * @returns {Array<{page: number, text: ?string, size: ?{width: number, height: number}, elements: object[]}>}
 */
function extractPages(odl) {
  const raw = [];
  collectRaw(odl.kids ?? odl.elements ?? odl.pages ?? odl.documents ?? [], raw, 0);

  const total = Number(odl['number of pages'] ?? odl.number_of_pages ?? 0);
  const byPage = new Map();

  raw.forEach((node, i) => {
    const page = pageNumberOf(node) ?? 1;
    const canonical = canonicalElement(node, page, i);
    if (!byPage.has(page)) byPage.set(page, []);
    byPage.get(page).push(canonical);
  });

  const present = [...byPage.keys()];
  const maxPage = Math.max(total, ...(present.length ? present : [0]), 1);

  const pages = [];
  for (let p = 1; p <= maxPage; p++) {
    const elements = byPage.get(p) ?? [];
    const text = elements.map((e) => e.text).filter(Boolean).join('\n') || null;
    pages.push({ page: p, text, size: pageSize(odl, p), elements });
  }

  return pages;
}

/**
 * Flatten OpenDataLoader's element tree in reading order: any object with a
 * `type` is an element; known container keys are traversed for nested
 * elements (list items, etc). Table internals (`cells`/`rows`) are NOT
 * traversed — they belong to the table element itself.
 */
function collectRaw(node, out, depth) {
  if (!node || typeof node !== 'object' || depth > 12) return;

  if (Array.isArray(node)) {
    for (const child of node) {
      collectRaw(child, out, depth + 1);
    }
    return;
  }

  const type = node.type ?? node.element_type;
  if (typeof type === 'string' && type !== '') {
    out.push(node);
  }

  for (const key of ['kids', 'children', 'elements', 'blocks', 'items']) {
    if (node[key]) {
      collectRaw(node[key], out, depth + 1);
    }
  }
}

function canonicalElement(el, pageNumber, index) {
  const type = normalizeType(el.type ?? el.element_type);
  const bbox = normalizeBbox(
    el['bounding box'] ?? el.bbox ?? el.box ?? el.bounding_box ?? (Array.isArray(el.coords) ? el.coords : null),
  );

  const canonical = {
    id: String(el.id ?? el.element_id ?? `${pageNumber}-${type}-${index}`),
    type,
    text: pickText(el),
    bbox,
    level: headingLevel(el),
    metadata: remainingMetadata(el),
  };

  if (type === 'table') {
    canonical.table = structuredTable(el);
    canonical.markdown = el.markdown ?? el.table_markdown ?? null;
    canonical.html = el.html ?? null;
    canonical.title = el.title ?? el.caption ?? null;
  }

  if (type === 'formula') {
    canonical.latex = el.latex ?? el.latex_math ?? (typeof el.content === 'string' ? el.content : null);
  }

  if (type === 'image') {
    canonical.file = flatString(el.file ?? el.image ?? el.src ?? el.path ?? el.data);
    canonical.alt = pick(el, ['alt', 'alt_text', 'caption', 'description']);
    canonical.ocr = pick(el, ['ocr', 'ocr_text']);
    canonical.mime_type = el.mime_type ?? el.mimeType ?? null;
    canonical.width = el.width ?? null;
    canonical.height = el.height ?? null;
  }

  return canonical;
}

/**
 * Image binaries land in images/ as <hash>.<ext>; the canonical element
 * records the RELATIVE path from the run dir so Laravel can resolve it.
 */
function materializeImage(element, imagesDir, outDir) {
  const ref = element.file;
  if (!ref) {
    element.file = null;
    return;
  }

  try {
    let bytes = null;
    let ext = extname(ref).replace('.', '') || 'png';

    if (ref.startsWith('data:')) {
      const match = ref.match(/^data:(image\/[a-z0-9.+-]+);base64,(.+)$/i);
      if (!match) return;
      bytes = Buffer.from(match[2], 'base64');
      ext = match[1].split('/')[1] || 'png';
    } else if (existsSync(ref)) {
      bytes = readFileSync(ref);
    } else {
      const joined = join(outDir, ref);
      if (existsSync(joined)) {
        bytes = readFileSync(joined);
      }
    }

    if (!bytes) {
      element.file = null;
      return;
    }

    ext = ext.replace(/^[^a-z0-9]+/gi, '').toLowerCase() || 'png';
    const name = `${createHash('sha1').update(element.id).digest('hex').slice(0, 16)}.${ext}`;
    writeFileSync(join(imagesDir, name), bytes);

    element.file = `images/${name}`;
    element.mime_type = element.mime_type ?? `image/${ext === 'jpg' ? 'jpeg' : ext}`;
  } catch {
    element.file = null;
  }
}

function structuredTable(el) {
  const t = el.table ?? el.rows ?? el.cells ?? el.content;
  if (Array.isArray(t) && t.length > 0 && t.every((row) => Array.isArray(row))) {
    const [headers, ...rows] = t;
    return { headers: headers ?? [], rows: rows ?? [] };
  }
  return t && typeof t === 'object' ? t : null;
}

function headingLevel(el) {
  const raw = el['heading level'] ?? el.heading_level ?? el.level ?? el.depth ?? 1;
  const num = Number(raw);
  if (Number.isInteger(num) && num >= 1 && num <= 6) {
    return num;
  }

  // OpenDataLoader also emits semantic labels like "Doctitle" / "Subtitle".
  const label = String(raw).toLowerCase();
  if (label.includes('doctitle') || label === 'title') return 1;
  if (label.includes('subtitle')) return 2;
  if (/^h[1-6]$/.test(label)) return Number(label[1]);

  return 1;
}

function pageNumberOf(node) {
  const raw = node['page number'] ?? node.page_number ?? node.page ?? null;
  const num = Number(raw);
  return Number.isInteger(num) && num >= 1 ? num : null;
}

function pageSize(odl, page) {
  const sizes = odl['page sizes'] ?? odl.page_sizes ?? null;
  if (Array.isArray(sizes) && sizes[page - 1]) {
    return normalizeSize(sizes[page - 1]);
  }
  return null;
}

function normalizeSize(raw) {
  if (!raw || typeof raw !== 'object') return null;
  const width = Number(raw.width ?? raw.w ?? raw[0]);
  const height = Number(raw.height ?? raw.h ?? raw[1]);
  if (Number.isFinite(width) && Number.isFinite(height) && width > 0 && height > 0) {
    return { width, height };
  }
  return null;
}

/** Keep every unknown ODL key so no extraction detail is lost. */
function remainingMetadata(el) {
  const known = new Set([
    'type', 'element_type', 'id', 'element_id', 'content', 'text', 'plain_text', 'value',
    'page number', 'page_number', 'page', 'number',
    'bounding box', 'bbox', 'box', 'bounding_box', 'coords',
    'level', 'heading level', 'heading_level', 'depth',
    'table', 'rows', 'cells', 'markdown', 'table_markdown', 'html', 'title', 'caption',
    'latex', 'latex_math', 'file', 'image', 'src', 'path', 'data', 'alt', 'alt_text',
    'description', 'ocr', 'ocr_text', 'mime_type', 'mimeType', 'width', 'height',
    'kids', 'children', 'elements', 'blocks', 'items', 'paragraphs', 'listItems',
  ]);

  const meta = {};
  for (const [key, value] of Object.entries(el)) {
    if (!known.has(key) && !['object', 'function'].includes(typeof value)) {
      meta[key] = value;
    }
  }

  return Object.keys(meta).length ? meta : null;
}

function normalizeType(raw) {
  const t = String(raw ?? '').trim().toLowerCase().replace(/[-_\s]+/g, '_');

  const map = {
    text: 'text', body: 'text', paragraph: 'text', span: 'text',
    heading: 'heading', title: 'heading', section_header: 'heading', section_heading: 'heading',
    table: 'table', image: 'image', figure: 'image', formula: 'formula', equation: 'formula',
    list: 'list', list_item: 'list_item', caption: 'caption', quote: 'quote',
    page_header: 'page_header', page_footer: 'page_footer', code: 'code', code_block: 'code',
  };

  return map[t] ?? 'other';
}

function normalizeBbox(raw) {
  if (Array.isArray(raw) && raw.length === 4 && raw.every((n) => !Number.isNaN(Number(n)))) {
    const [x0, y0, x1, y1] = raw.map(Number);
    return {
      x: Math.round(x0),
      y: Math.round(y0),
      width: Math.round(x1 - x0),
      height: Math.round(y1 - y0),
    };
  }

  if (raw && typeof raw === 'object') {
    if (!Number.isNaN(Number(raw.x1)) && !Number.isNaN(Number(raw.y1))) {
      const x0 = Number(raw.x0 ?? raw.x_left ?? raw.x ?? 0);
      const y0 = Number(raw.y0 ?? raw.y_top ?? raw.y ?? 0);
      return {
        x: Math.round(x0),
        y: Math.round(y0),
        width: Math.round(Number(raw.x1) - x0),
        height: Math.round(Number(raw.y1) - y0),
      };
    }
    if (!Number.isNaN(Number(raw.x)) && !Number.isNaN(Number(raw.y))
        && !Number.isNaN(Number(raw.width)) && !Number.isNaN(Number(raw.height))) {
      return {
        x: Math.round(Number(raw.x)),
        y: Math.round(Number(raw.y)),
        width: Math.round(Number(raw.width)),
        height: Math.round(Number(raw.height)),
      };
    }
  }

  return null;
}

function pickText(el) {
  const c = el.content ?? el.text ?? el.plain_text ?? el.value;

  if (typeof c === 'string' && c.trim() !== '') return c;
  if (typeof c === 'number') return String(c);
  if (Array.isArray(c)) {
    const parts = c.map((item) => (typeof item === 'string' ? item : '')).filter((s) => s !== '');
    return parts.length ? parts.join(' ') : null;
  }

  return null;
}

function pick(obj, keys) {
  for (const key of keys) {
    const value = obj[key];
    if (typeof value === 'string' && value.trim() !== '') return value;
    if (typeof value === 'number') return String(value);
  }
  return null;
}

function flatString(value) {
  return typeof value === 'string' && value.trim() !== '' ? value : null;
}

function parseArgs(argv) {
  const args = {};
  for (let i = 0; i < argv.length; i++) {
    switch (argv[i]) {
      case '--help':
      case '-h':
        args.help = true;
        break;
      case '--input':
        args.input = argv[++i];
        break;
      case '--output':
        args.output = argv[++i];
        break;
      case '--ocr-lang':
        args.ocrLang = argv[++i];
        break;
      default:
        break;
    }
  }
  return args;
}

function unrunnable(message) {
  const err = new Error(message);
  err.kind = 'unrunnable';
  return err;
}

function printHelp() {
  console.log(`Sikhun PDF worker (OpenDataLoader)
Usage: node parse.js --input <pdf> --output <dir> [--ocr-lang <lang>]
  --input    absolute path to the PDF to parse
  --output   absolute output directory (created if missing)
  --ocr-lang optional OCR language hint, e.g. eng+ben`);
}
