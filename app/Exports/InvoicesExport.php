<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly \Illuminate\Support\Collection $invoices
    ) {
    }

    public function collection(): \Illuminate\Support\Collection
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        return [
            __('admin.invoices.invoice_number'),
            __('admin.invoices.user'),
            __('admin.invoices.amount'),
            __('admin.invoices.vat_amount'),
            __('admin.invoices.total_amount'),
            __('admin.invoices.payment_method'),
            __('admin.invoices.payment_status'),
            __('admin.invoices.payment_date'),
            __('admin.invoices.created_at'),
        ];
    }

    /**
     * @param  \App\Models\Invoice  $row
     */
    public function map($row): array
    {
        return [
            $row->invoice_number,
            $row->user?->name ?? '--',
            number_format((float) $row->amount, 2),
            number_format((float) $row->vat_amount, 2),
            number_format((float) $row->total_amount, 2),
            __('admin.invoices.payment_methods.' . ($row->payment_method ?? '')),
            $row->payment_status,
            $row->paid_at?->format('Y-m-d H:i') ?? '--',
            $row->created_at?->format('Y-m-d H:i') ?? '--',
        ];
    }
}
