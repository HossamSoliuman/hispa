<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Traits\RespondsWithHttpStatus;

class SettingController extends Controller
{
    use RespondsWithHttpStatus;

    public function index()
    {
        // Fetch settings from the database
        $settings = Setting::whereIn('key', ['title', 'title_en', 'APP_ENV', 'domain', 'logo', 'website_maintenance', 'commission_limit', 'email', 'phone'])->get();

        // Use the resource collection to return settings data
        return $this->success('تم جلب البيانات بنجاح', SettingResource::collection($settings), 200);
    }
}
