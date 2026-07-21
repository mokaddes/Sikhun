<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Course;
use App\Models\CustomPage;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Regenerate public/sitemap.xml from published books, active courses, and CMS pages';

    public function handle(): int
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/')->setPriority(1.0))
            ->add(Url::create('/library')->setPriority(0.9))
            ->add(Url::create('/courses')->setPriority(0.9))
            ->add(Url::create('/contact')->setPriority(0.5));

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
