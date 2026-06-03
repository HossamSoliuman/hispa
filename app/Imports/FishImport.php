<?php

namespace App\Imports;

use App\Models\Fish;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;

class FishImport implements ShouldQueue, ToModel, WithChunkReading, WithHeadingRow
{
    use Importable;

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Fish([
            'code' => $row['code'] ?? null,
            'scientific_name' => $row['scientific_name'] ?? null,
            'english_name' => $row['english_name'] ?? null,
            'red_sea_name' => $row['red_sea_name'] ?? null,
            'arabian_gulf_name' => $row['arabian_gulf_name'] ?? null,
            'status' => 1,
        ]);
    }

    public static function afterImport(AfterImport $event)
    {
        Log::info('Fish import completed!');
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => [self::class, 'afterImport'],
        ];
    }

    public function chunkSize(): int
    {
        return 100; // or more depending on your server limits
    }

    public function batchSize(): int
    {
        return 100;
    }
}
