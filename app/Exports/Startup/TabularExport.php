<?php

namespace App\Exports\Startup;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TabularExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly Collection $rows, private readonly array $columns) {}

    public function collection(): Collection
    {
        return $this->rows->map(fn ($row) => array_map(fn (string $column) => $row[$column] ?? '', $this->columns));
    }

    public function headings(): array
    {
        return array_map(fn (string $column): string => __('owner.startup.columns.'.$column), $this->columns);
    }
}
