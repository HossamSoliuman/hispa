<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_report_masthead_uses_english_name_on_left_side(): void
    {
        $this->withoutMiddleware();

        Setting::query()->insert([
            ['key' => 'title', 'value' => 'Hispa Arabic', 'type' => 'text'],
            ['key' => 'site_name', 'value' => 'Hispa Arabic', 'type' => 'text'],
            ['key' => 'title_en', 'value' => 'Hispa Platform', 'type' => 'text'],
            ['key' => 'phone', 'value' => '5547894957', 'type' => 'text'],
            ['key' => 'email', 'value' => 'info@hispa.com', 'type' => 'text'],
        ]);

        $response = $this->get(route('admin.trip-report.print'));

        $response->assertOk();

        $html = $response->getContent();
        $englishColumnOffset = strpos($html, '<div class="rmast-side rmast-en">');
        $arabicColumnOffset = strpos($html, '<div class="rmast-side rmast-ar">');

        $this->assertIsInt($englishColumnOffset);
        $this->assertIsInt($arabicColumnOffset);
        $this->assertLessThan($arabicColumnOffset, $englishColumnOffset);

        $englishColumn = substr($html, $englishColumnOffset, $arabicColumnOffset - $englishColumnOffset);
        $arabicColumn = substr($html, $arabicColumnOffset);

        $this->assertStringContainsString('Hispa Platform', $englishColumn);
        $this->assertStringNotContainsString('Hispa Arabic', $englishColumn);
        $this->assertStringContainsString('Hispa Arabic', $arabicColumn);
    }
}
