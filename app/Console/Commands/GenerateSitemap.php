<?php

namespace App\Console\Commands;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Book;
use App\Models\Course;
use App\Models\CustomPage;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Regenerate public/sitemap.xml from published books, active courses, blog posts, and CMS pages';

    public function handle(): int
    {
        // Always emit the public production URL even when generated on a
        // dev machine whose APP_URL points at localhost. forceRootUrl() is
        // required in addition to the config change: the URL generator is
        // already resolved by the time an artisan command runs, so it keeps
        // the boot-time root unless we force it.
        $base = rtrim(env('SEO_SITEMAP_BASE_URL', 'https://sikhun.com'), '/');

        config(['app.url' => $base]);
        \Illuminate\Support\Facades\URL::forceRootUrl($base);
        \Illuminate\Support\Facades\URL::forceScheme('https');

        $sitemap = Sitemap::create()
            ->add(Url::create('/')->setPriority(1.0))
            ->add(Url::create('/blog')->setPriority(0.9))
            ->add(Url::create('/library')->setPriority(0.9))
            ->add(Url::create('/courses')->setPriority(0.9))
            ->add(Url::create('/contact')->setPriority(0.5));

        foreach (BlogCategory::active()->get() as $category) {
            $sitemap->add(
                Url::create("/blog/category/{$category->slug}")
                    ->setLastModificationDate($category->updated_at)
                    ->setPriority(0.7)
            );
        }

        foreach (BlogPost::published()->get() as $post) {
            $sitemap->add(
                Url::create("/blog/{$post->slug}")
                    ->setLastModificationDate($post->updated_at)
                    ->setPriority(0.8)
            );
        }

        foreach (CustomPage::published()->where('slug', '!=', 'contact')->get() as $page) {
            $sitemap->add(
                Url::create("/p/{$page->slug}")
                    ->setLastModificationDate($page->updated_at)
                    ->setPriority(0.6)
            );
        }

        foreach (Book::published()->get() as $book) {
            $sitemap->add(
                Url::create("/library/{$book->slug}")
                    ->setLastModificationDate($book->updated_at)
                    ->setPriority(0.8)
            );
        }

        foreach (Course::active()->get() as $course) {
            $sitemap->add(
                Url::create("/courses/{$course->slug}")
                    ->setLastModificationDate($course->updated_at)
                    ->setPriority(0.8)
            );
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap written to public/sitemap.xml.');

        return self::SUCCESS;
    }
}
