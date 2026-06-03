<?php

namespace App\Observers;

use App\Models\Page;
use Illuminate\Support\Str;

class PageObserver
{
    /**
     * Handle the Page "created" event.
     */
    public function created(Page $page): void
    {
        $title = $page->title_en ?: $page->title_ar;
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Page::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        $page->slug = $slug;
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(Page $page): void
    {
        //
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(Page $page): void
    {
        //
    }

    /**
     * Handle the Page "restored" event.
     */
    public function restored(Page $page): void
    {
        //
    }

    /**
     * Handle the Page "force deleted" event.
     */
    public function forceDeleted(Page $page): void
    {
        //
    }
}
