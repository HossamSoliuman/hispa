<?php

use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function paginationResult($data, $rate = null)
{
    return [
        'current_page' => $data->currentPage(),
        'first_page_url' => $data->url(1),
        'from' => $data->firstItem(),
        'last_page' => $data->lastPage(),
        'last_page_url' => $data->url($data->lastPage()),
        'next_page_url' => $data->nextPageUrl(),
        'path' => $data->getOptions()['path'],
        'per_page' => $data->perPage(),
        'prev_page_url' => $data->previousPageUrl(),
        'to' => $data->lastItem(),
        'total' => $data->total(),
        'items' => $data->items(),
        'extra' => $rate,
    ];
}

function generateInvoiceNumber()
{
    $year = now()->format('y');     // مثال: "25"
    $month = now()->format('n');    // مثال: "6" بدون صفر أول
    $prefix = "{$year}-{$month}-";

    $lastSale = Sale::where('number', 'like', "{$prefix}%")
        ->orderBy('number', 'desc')
        ->first();

    if ($lastSale && preg_match('/\d+$/', $lastSale->number, $matches)) {
        $lastSequence = (int) $matches[0];
    } else {
        $lastSequence = 0;
    }

    $newSequence = str_pad($lastSequence + 1, 6, '0', STR_PAD_LEFT);

    return $prefix.$newSequence;
}

function fillBoatAndCrewData(array $data, $boatId, $captainId)
{
    $boat = \App\Models\Boat::findOrFail($boatId);

    $data['boat_id'] = $boat->id;
    $data['boat_name'] = $boat->name_ar;
    $data['boat_number'] = $boat->number;
    $data['boat_color'] = $boat->color;
    $data['boat_length'] = $boat->length;
    $data['boat_width'] = $boat->width;
    $data['crew_count'] = \App\Models\User::where('captain_id', $captainId)->count();

    return $data;
}

function UploadFileFromStoragePath($oldPath, $newDirectory)
{
    $newName = basename($oldPath);
    $newPath = $newDirectory.'/'.$newName;

    Storage::copy($oldPath, $newPath);

    return $newPath;
}
function getModelOrderByDesc($model)
{

    return $model::orderByDesc('id')->get();
}

function generateTripNumber()
{
    $year = date('Y');
    $prefix = 'TR-'.$year.'-';

    // Get the latest trip number with this year
    $latestTrip = \App\Models\Trip::where('number', 'like', $prefix.'%')
        ->orderByDesc('id')
        ->first();

    if ($latestTrip && preg_match('/-(\d+)$/', $latestTrip->number, $matches)) {
        $lastNumber = intval($matches[1]);
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '001';
    }

    return $prefix.$newNumber;
}

// function UploadFile(UploadedFile $file, $folder = null, $disk = 'ocean', $filename = null) //digitl ocean
// {
//    $FileName = $filename ?? Str::random(10);
//    return $file->storeAs(
//        $folder,
//        $FileName . '.' . $file->getClientOriginalExtension(),
//        $disk
//    );
// }

function UploadFile(UploadedFile $file, $folder = null, $disk = 'public', $filename = null) // local
{
    $FileName = ! is_null($filename) ? $filename : Str::random(10);

    return $file->storeAs(
        $folder,
        $FileName.'.'.$file->getClientOriginalExtension(),
        $disk
    );
}

function deleteFile($path, $disk = 'public') // local
{
    Storage::disk($disk)->delete($path);
}
// function deleteFile($path, $disk = 'ocean') //digital ocean
// {
//    Storage::disk($disk)->delete($path);
// }

function createCode()
{
    do {
        $num = sprintf('%04d', mt_rand(1000, 9999)); // كود 4 أرقام
    } while (
        preg_match("~^(\d)\\1{3}$|0000~", $num) // مثل 1111, 0000, 2222
    );

    return $num;
}

function get_order_number()
{
    $today = date('Ymd');
    $rand = strtoupper(substr(uniqid(sha1(time())), 0, 4));
    $unique = $today.$rand;

    return $unique;
}

function formatWeight($value, $threshold = 1000)
{
    if (! is_numeric($value)) {
        return '0 '.__('admin.units.kg');
    }

    // When value is above threshold, show in tons.
    // Use truncation to 2 decimals for tons (no rounding), and round kilos to integer for display.
    if (abs($value) >= $threshold) {
        $tons = $value / 1000;
        // truncate towards zero to keep behavior consistent (no rounding)
        $truncated = intval($tons * 100) / 100;
        // if truncated is whole number, show without decimals
        if (fmod($truncated, 1) == 0.0) {
            return intval($truncated).' '.__('admin.units.ton');
        }

        return number_format($truncated, 2).' '.__('admin.units.ton');
    }

    // For kilos, show rounded integer (no decimals)
    return round($value).' '.__('admin.units.kg');
}

function formatHijriDate($date, $pattern = 'dd/MM/yyyy')
{
    if (empty($date)) {
        return '';
    }

    // Try to use IntlDateFormatter with Islamic calendar
    try {
        $dt = new DateTime($date);
        $tz = $dt->getTimezone() ? $dt->getTimezone()->getName() : date_default_timezone_get();
        $locale = app()->getLocale() ? app()->getLocale().'@calendar=islamic' : 'en@calendar=islamic';

        $formatter = new \IntlDateFormatter(
            $locale,
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE,
            $tz,
            \IntlDateFormatter::TRADITIONAL,
            $pattern
        );

        $formatted = $formatter->format($dt);
        if ($formatted === false) {
            // fallback to original
            return $dt->format('d/m/Y');
        }

        return $formatted;
    } catch (\Throwable $e) {
        return (new DateTime($date))->format('d/m/Y');
    }
}

function getVatRate(): float
{
    return (float) (Setting::where('key', 'vat_rate')->value('value') ?? 15);
}

/**
 * Public URL of the uploaded company logo, or null when none has been set.
 * Used for the panel brand/header where an <img src> URL is needed.
 */
function companyLogoUrl(): ?string
{
    $path = Setting::where('key', 'logo')->value('value');

    if (empty($path)) {
        return null;
    }

    if (Str::startsWith($path, ['http://', 'https://', '/'])) {
        return $path;
    }

    return Storage::disk('public')->url($path);
}

/**
 * Convert a value to a safe display string for use in Blade/HTML.
 * Handles null, arrays (imploded), and scalar values. Use for model attributes
 * that may be stored as JSON/array (e.g. translated fields) to avoid
 * "htmlspecialchars(): Argument #1 ($string) must be of type string, array given".
 *
 * @param  mixed  $value  Raw value (string|array|null|int|float)
 * @param  string  $empty  String to return when value is null/empty
 */
function display_string(mixed $value, string $empty = '—'): string
{
    if ($value === null || $value === '') {
        return $empty;
    }
    if (is_array($value)) {
        return implode(', ', array_filter($value, fn ($v) => $v !== null && $v !== ''));
    }

    return (string) $value;
}
