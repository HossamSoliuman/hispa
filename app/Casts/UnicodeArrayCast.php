<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Cast مصفوفة إلى JSON مع الاحتفاظ بالأحرف غير اللاتينية (مثل العربية) كنص واضح.
 * يخزن في قاعدة البيانات بشكل مثل: ["ميزة ١","ميزة ٢"] بدلاً من \u0645\u064a\u0632\u0629
 */
class UnicodeArrayCast implements CastsAttributes
{
    private const ENCODE_FLAGS = \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES;

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<int, string>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            return array_values(array_filter($value, fn ($v) => $v !== null && $v !== ''));
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? array_values(array_filter($decoded, fn ($v) => $v !== null && $v !== '')) : [];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $arr = is_array($value) ? array_values(array_filter($value, fn ($v) => $v !== null && (string) $v !== '')) : [];

        if (empty($arr)) {
            return null;
        }

        $json = json_encode($arr, self::ENCODE_FLAGS);

        return $json !== false ? $json : null;
    }
}
