<?php

namespace App\DataTable\Owner;

use Yajra\DataTables\DataTables;

class ExpensesDataTable
{
    public function getData($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('date', function ($expense) {
                // Prefer explicit date field, fallback to created_at
                $raw = $expense->date ?? $expense->created_at ?? null;
                if (! $raw) {
                    return '-';
                }
                try {
                    $dt = new \DateTime($raw);
                } catch (\Exception $e) {
                    return is_string($raw) ? $raw : '-';
                }

                $greg = $dt->format('Y-m-d');
                $hijri = '';
                if (class_exists('\IntlDateFormatter')) {
                    $locale = app()->getLocale() === 'ar' ? 'ar_SA@calendar=islamic' : 'en_US@calendar=islamic';
                    $fmt = new \IntlDateFormatter($locale, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE, null, \IntlDateFormatter::TRADITIONAL, 'd MMM yyyy');
                    $out = $fmt->format($dt);
                    if ($out !== false) {
                        $hijri = $out;
                    }
                }

                if ($hijri) {
                    return $greg.'<br><small class="text-muted">'.$hijri.'</small>';
                }

                return $greg;
            })
            ->addColumn('boat_name', function ($expense) {
                return $expense->boat ? $expense->boat->name : 'عام';
            })
            ->addColumn('vendor_name', function ($expense) {
                return $expense->vendor ? $expense->vendor->name : '-';
            })
            ->addColumn('payment_method_name', function ($expense) {
                return $expense->paymentMethod ? $expense->paymentMethod->name : '-';
            })
            ->addColumn('status_badge', function ($expense) {
                $class = $expense->status === 'paid' ? 'bg-success' : 'bg-danger';
                $text = $expense->status === 'paid' ? __('owner.paid') : __('owner.pending');

                return "<span class='badge $class'>$text</span>";
            })
            ->addColumn('formatted_total', function ($expense) {
                $icon = view('components.riyal-icon', ['size' => 'sm'])->render();

                return number_format($expense->total_price, 2).' <span class="unit">'.$icon.'</span>';
            })
            ->addColumn('formatted_final', function ($expense) {
                $icon = view('components.riyal-icon', ['size' => 'sm'])->render();

                return number_format($expense->final_price, 2).' <span class="unit">'.$icon.'</span>';
            })
            ->addColumn('expense_type', function ($expense) {
                return $expense->category ? $expense->category->name : '-';
            })
            ->addColumn('category_parent', function ($expense) {
                $categoryParent = $expense->category ? $expense->category->parent : null;
                if (! $categoryParent) {
                    return '-';
                }

                return ['type' => $categoryParent->type, 'name' => $categoryParent->name];
            })
            ->addColumn('action', function ($expense) {
                return view('owner.expenses.partials.actions', compact('expense'))->render();
            })
            ->rawColumns(['status_badge', 'category_parent', 'action', 'formatted_total', 'formatted_final', 'date'])
            ->make(true);
    }
}
