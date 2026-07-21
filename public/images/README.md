Add a real `og-default.png` (1200×630px recommended) here before launch —
`SeoService` references `/images/og-default.png` as the fallback Open
Graph image for pages without their own cover (home page, CMS pages).
Until then, `asset('images/og-default.png')` resolves to a URL that
returns 404 — social previews will just show no image, not break.
