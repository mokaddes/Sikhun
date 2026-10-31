<?php

namespace Tests\Feature;

use App\Mail\BlogDraftDigestMail;
use App\Models\Admin;
use App\Models\AiProvider;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogSource;
use App\Models\BlogTag;
use App\Models\SiteSetting;
use App\Services\Blog\BlogDeduplicator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BlogModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin(): Admin
    {
        return Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@sikhun.com',
            'password' => 'password123',
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_public_can_view_published_blog_posts_without_login_and_draft_returns_404(): void
    {
        $category = BlogCategory::create([
            'name_bn' => 'প্রযুক্তি',
            'name_en' => 'Technology',
            'slug' => 'technology',
            'is_active' => true,
        ]);

        $publishedPost = BlogPost::create([
            'blog_category_id' => $category->id,
            'title' => 'কৃত্রিম বুদ্ধিমত্তা ও ভবিষ্যৎ শিক্ষা',
            'slug' => 'ai-future-education',
            'excerpt' => 'এআই কীভাবে শিক্ষা ব্যবস্থাকে বদলে দিচ্ছে।',
            'content' => '<h2>ভূমিকা</h2><p>কৃত্রিম বুদ্ধিমত্তা আজকের বিশ্বে অত্যন্ত গুরুত্বপূর্ণ।</p>',
            'status' => 'published',
            'origin' => 'manual',
            'published_at' => now()->subHour(),
        ]);

        $draftPost = BlogPost::create([
            'blog_category_id' => $category->id,
            'title' => 'ড্রাফট পোস্ট যা প্রকাশিত হয়নি',
            'slug' => 'unpublished-draft-post',
            'content' => '<p>ড্রাফট কন্টেন্ট</p>',
            'status' => 'draft',
            'origin' => 'auto',
        ]);

        // Guest can access /blog
        $indexResponse = $this->get('/blog');
        $indexResponse->assertStatus(200);

        // Guest can access published post and view_count increments
        $showResponse = $this->get('/blog/' . $publishedPost->slug);
        $showResponse->assertStatus(200);
        $this->assertEquals(1, $publishedPost->fresh()->view_count);

        // Guest cannot access draft post (404)
        $draftResponse = $this->get('/blog/' . $draftPost->slug);
        $draftResponse->assertStatus(404);
    }

    public function test_deduplicator_detects_url_hash_content_hash_and_title_similarity(): void
    {
        $deduplicator = app(BlogDeduplicator::class);

        $url = 'https://example.com/articles/quantum-computing-guide?utm_source=rss&fbclid=123';
        $rawContent = 'Quantum computing uses qubits and superposition to solve complex problems rapidly across modern scientific domains.';
        $title = 'A Complete Guide to Quantum Computing for Beginners in 2026';

        BlogPost::create([
            'title' => $title,
            'source_title' => $title,
            'slug' => 'quantum-computing-guide',
            'content' => '<p>কোয়ান্টাম কম্পিউটিং গাইড</p>',
            'status' => 'draft',
            'origin' => 'auto',
            'source_url' => $deduplicator->normalizeUrl($url),
            'source_url_hash' => $deduplicator->hashUrl($url),
            'content_hash' => $deduplicator->hashContent($rawContent),
            'title_fingerprint' => $deduplicator->fingerprintTitle($title),
        ]);

        // 1. Same URL without UTM params should match URL hash
        $this->assertTrue(
            $deduplicator->isDuplicate('https://example.com/articles/quantum-computing-guide')
        );

        // 2. Different URL, same content should match content hash
        $this->assertTrue(
            $deduplicator->isDuplicate(
                'https://another-site.com/post-2',
                'Different Title Entirely',
                $rawContent
            )
        );

        // 3. Different URL, different content, >=85% similar title should match title similarity
        $this->assertTrue(
            $deduplicator->isDuplicate(
                'https://another-site.com/post-3',
                'Complete Guide to Quantum Computing for Beginners 2026',
                'Completely different body text here that is long enough to hash separately without matching.'
            )
        );
    }

    public function test_blocking_post_or_deleting_auto_post_adds_to_blocklist_and_skips_future_scrape(): void
    {
        $admin = $this->createAdmin();
        $deduplicator = app(BlogDeduplicator::class);

        $url = 'https://huggingface.co/blog/new-open-llm';
        $title = 'Introducing a New Open Source LLM for Everyone';

        $autoPost = BlogPost::create([
            'title' => 'নতুন ওপেন সোর্স এলএলএম পরিচিতি',
            'source_title' => $title,
            'slug' => 'new-open-llm',
            'content' => '<p> বিস্তারিত কন্টেন্ট </p>',
            'status' => 'draft',
            'origin' => 'auto',
            'source_url' => $deduplicator->normalizeUrl($url),
            'source_url_hash' => $deduplicator->hashUrl($url),
            'title_fingerprint' => $deduplicator->fingerprintTitle($title),
        ]);

        // Deleting an auto post should automatically block it (Delete = Block)
        $response = $this->actingAs($admin, 'admin')->delete(route('admin.blog.destroy', $autoPost->id));
        $response->assertRedirect();

        // Verify post is soft-deleted and blocklist entry exists
        $this->assertSoftDeleted('blog_posts', ['id' => $autoPost->id]);
        $this->assertDatabaseHas('blog_blocklist', [
            'source_url_hash' => $deduplicator->hashUrl($url),
        ]);

        // Even if post is force-deleted later, blocklist still skips the URL and similar title
        $autoPost->forceDelete();

        $this->assertTrue($deduplicator->isBlocked($url, $title));
        $this->assertTrue(
            $deduplicator->isBlocked(
                'https://mirror-site.com/llm-post',
                'Introducing a New Open Source LLM for Everyone!'
            )
        );
    }

    public function test_automated_scrape_pipeline_creates_draft_post_and_sends_digest_email(): void
    {
        Mail::fake();

        SiteSetting::updateOrCreate(['key' => 'blog_digest_email'], ['value' => 'admin@sikhun.com']);

        $category = BlogCategory::create([
            'name_bn' => 'এআই ও মেশিন লার্নিং',
            'name_en' => 'AI & Machine Learning',
            'slug' => 'ai-ml',
            'is_active' => true,
        ]);

        BlogSource::create([
            'name' => 'Test AI RSS Feed',
            'url' => 'https://example-ai-blog.com/feed.xml',
            'type' => 'rss',
            'blog_category_id' => $category->id,
            'is_active' => true,
            'max_items_per_run' => 2,
        ]);

        // Configure AI provider for translation & rewrite
        $provider = AiProvider::create([
            'name' => 'Test OpenAI',
            'type' => 'openai',
            'api_key' => 'test-key',
            'model_name' => 'gpt-4o-mini',
            'api_endpoint' => 'https://api.openai.com/v1',
            'is_active' => true,
            'max_tokens' => 4096,
            'temperature' => 0.7,
        ]);

        $provider->useCases()->createMany([
            ['use_case' => 'blog_translate', 'is_default' => true],
            ['use_case' => 'blog_rewrite', 'is_default' => true],
        ]);

        $rssXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title>Test AI Blog</title>
    <item>
      <title>How Transformers Revolutionized Natural Language Processing</title>
      <link>https://example-ai-blog.com/posts/transformers-nlp</link>
      <description>Transformers introduced self-attention mechanisms that scaled deep learning models.</description>
    </item>
  </channel>
</rss>
XML;

        $articleHtml = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <title>How Transformers Revolutionized Natural Language Processing</title>
    <meta property="og:image" content="https://example-ai-blog.com/images/transformer.jpg" />
</head>
<body>
    <article>
        <p>Transformers introduced self-attention mechanisms that revolutionized natural language processing and deep learning architectures across the globe in modern machine learning systems.</p>
        <p>Unlike recurrent neural networks, transformers process tokens in parallel, enabling massive pre-training on large text corpora with unprecedented accuracy, scalability, and speed.</p>
        <p>Students and researchers in Bangladesh and worldwide can apply transformer models to translation, summarization, and question answering tasks effectively in real-world projects.</p>
    </article>
</body>
</html>
HTML;

        $rewrittenJson = json_encode([
            'title' => 'ট্রান্সফরমার মডেল কীভাবে ন্যাচারাল ল্যাঙ্গুয়েজ প্রসেসিং বদলে দিয়েছে',
            'slug' => 'transformers-revolutionized-nlp',
            'excerpt' => 'সেলফ-অ্যাটেনশন মেকানিজম কীভাবে আধুনিক এআই ও ডিপ লার্নিংকে নতুন উচ্চতায় নিয়ে গেছে তা জানুন।',
            'meta_title' => 'ট্রান্সফরমার ও এনএলপি গাইড | Sikhun',
            'meta_description' => 'ট্রান্সফরমার আর্কিটেকচার এবং ন্যাচারাল ল্যাঙ্গুয়েজ প্রসেসিং নিয়ে বাংলায় সহজ গাইড।',
            'content' => '<h2>ট্রান্সফরমার কী?</h2><p>ট্রান্সফরমার মডেল সেলফ-অ্যাটেনশন প্রযুক্তির মাধ্যমে ভাষা প্রক্রিয়াকরণে বিপ্লব ঘটিয়েছে।</p><h2>মূল সুবিধাসমূহ</h2><ul><li>সমান্তরাল ডেটা প্রসেসিং</li><li>দ্রুত প্রশিক্ষণ</li></ul>',
        ], JSON_UNESCAPED_UNICODE);

        Http::fake([
            'https://example-ai-blog.com/robots.txt' => Http::response("User-agent: *\nAllow: /", 200),
            'https://example-ai-blog.com/feed.xml' => Http::response($rssXml, 200),
            'https://example-ai-blog.com/posts/transformers-nlp' => Http::response($articleHtml, 200),
            'https://api.openai.com/v1/*' => Http::sequence()
                ->push([
                    'choices' => [
                        ['message' => ['content' => 'ট্রান্সফরমার মডেল সেলফ-অ্যাটেনশন মেকানিজম প্রবর্তন করেছে যা ন্যাচারাল ল্যাঙ্গুয়েজ প্রসেসিংকে বদলে দিয়েছে।']],
                    ],
                ], 200)
                ->push([
                    'choices' => [
                        ['message' => ['content' => $rewrittenJson]],
                    ],
                ], 200),
        ]);

        $this->artisan('blog:scrape')
            ->assertExitCode(0);

        // Verify post was saved strictly as DRAFT (never auto-published)
        $post = BlogPost::first();
        $this->assertNotNull($post);
        $this->assertEquals('draft', $post->status);
        $this->assertEquals('auto', $post->origin);
        $this->assertNull($post->published_at);
        $this->assertEquals('ট্রান্সফরমার মডেল কীভাবে ন্যাচারাল ল্যাঙ্গুয়েজ প্রসেসিং বদলে দিয়েছে', $post->title);
        $this->assertEquals('How Transformers Revolutionized Natural Language Processing', $post->source_title);

        // Verify digest email was sent to admin@sikhun.com
        Mail::assertSent(BlogDraftDigestMail::class, function (BlogDraftDigestMail $mail) {
            return $mail->hasTo('admin@sikhun.com') && ($mail->summary['new_drafts'] ?? 0) === 1;
        });

        // Running scrape a second time should skip the duplicate URL and send no new digest
        Mail::fake();
        $this->artisan('blog:scrape')
            ->assertExitCode(0);

        $this->assertEquals(1, BlogPost::count());
        Mail::assertNothingSent();
    }

    public function test_public_blog_index_filters_by_tag_and_detail_prioritizes_shared_tag_posts(): void
    {
        $tag = BlogTag::create([
            'name_bn' => 'জীববিজ্ঞান',
            'name_en' => 'Biology',
            'slug' => 'biology',
        ]);

        $post = BlogPost::create([
            'title' => 'জীববিজ্ঞানের মূল ধারণা',
            'slug' => 'biology-basics',
            'content' => '<p>জীববিজ্ঞান বিষয়ক লেখা।</p>',
            'status' => 'published',
            'origin' => 'manual',
            'published_at' => now()->subDays(3),
        ]);
        $sharedTagPost = BlogPost::create([
            'title' => 'কোষ ও জীবনের গঠন',
            'slug' => 'cells-and-life',
            'content' => '<p>কোষ জীবনের গঠনমূলক একক।</p>',
            'status' => 'published',
            'origin' => 'manual',
            'published_at' => now()->subDays(2),
        ]);
        BlogPost::create([
            'title' => 'প্রযুক্তির অগ্রগতি',
            'slug' => 'technology-progress',
            'content' => '<p>প্রযুক্তি বিষয়ক লেখা।</p>',
            'status' => 'published',
            'origin' => 'manual',
            'published_at' => now()->subDay(),
        ]);
        $post->tags()->attach($tag);
        $sharedTagPost->tags()->attach($tag);

        $this->withHeaders([
            'X-Inertia' => 'true',
            'X-Inertia-Version' => hash_file('xxh128', public_path('build/manifest.json')),
            'X-Requested-With' => 'XMLHttpRequest',
        ])->get('/blog?tag=biology')
            ->assertOk()
            ->assertJsonPath('component', 'Public/Blog/Index')
            ->assertJsonPath('props.activeTag.slug', 'biology')
            ->assertJsonPath('props.posts.data.0.slug', 'cells-and-life')
            ->assertJsonCount(2, 'props.posts.data');

        $this->withHeaders([
            'X-Inertia' => 'true',
            'X-Inertia-Version' => hash_file('xxh128', public_path('build/manifest.json')),
            'X-Requested-With' => 'XMLHttpRequest',
        ])->get('/blog/biology-basics')
            ->assertOk()
            ->assertJsonPath('component', 'Public/Blog/Show')
            ->assertJsonPath('props.post.tags.0.slug', 'biology')
            ->assertJsonPath('props.related.0.slug', 'cells-and-life');

    }

    public function test_admin_can_assign_and_replace_blog_post_tags(): void
    {
        $admin = $this->createAdmin();
        $firstTag = BlogTag::create(['name_bn' => 'পদার্থবিজ্ঞান', 'name_en' => 'Physics', 'slug' => 'physics']);
        $secondTag = BlogTag::create(['name_bn' => 'রসায়ন', 'name_en' => 'Chemistry', 'slug' => 'chemistry']);

        $payload = [
            'title' => 'পদার্থবিজ্ঞানের পরিচিতি',
            'slug' => 'physics-introduction',
            'content' => '<p>বিষয়টির পরিচিতি।</p>',
            'status' => 'draft',
            'tags' => [$firstTag->id],
        ];

        $this->actingAs($admin, 'admin')
            ->post(route('admin.blog.store'), $payload)
            ->assertRedirect(route('admin.blog.index'));

        $post = BlogPost::where('slug', 'physics-introduction')->firstOrFail();
        $this->assertSame([$firstTag->id], $post->tags()->pluck('blog_tags.id')->all());

        $this->actingAs($admin, 'admin')
            ->put(route('admin.blog.update', $post), [
                ...$payload,
                'slug' => $post->slug,
                'tags' => [$secondTag->id],
            ])
            ->assertRedirect(route('admin.blog.index'));

        $this->assertSame([$secondTag->id], $post->fresh()->tags()->pluck('blog_tags.id')->all());
    }
}
