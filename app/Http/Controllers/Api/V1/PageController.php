<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Traits\RespondsWithHttpStatus;

class PageController extends Controller
{
    use RespondsWithHttpStatus;

    public function pages()
    {

        $pages = Page::active()->get();

        return $this->success(trans('site.getData'), PageResource::collection($pages), 200);
    }

    public function show($slug)
    {

        $page = Page::active()->where('slug', $slug)->first();

        return $this->success(trans('site.getData'), new PageResource($page), 200);
    }
}
