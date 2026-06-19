<x-report-layout
    :title="__('owner.expenses.print.title')"
    :titleEn="'Expense Report'"
    :documentNumber="$expense->number"
    :settings="$settings ?? []"
>
    <x-slot name="extraStyles">
        .table thead th { text-align: center; font-weight: bold; }
        .table tbody td { text-align: center; }
        .unit svg { width: 14px; height: 14px; display: inline-block; vertical-align: middle; }
        .metadata { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; }
        .meta-item { display: inline-block; width: 48%; margin-bottom: 10px; }
        .meta-label { font-weight: bold; color: #495057; }
        .meta-value { color: #212529; }
        h5 { margin-top: 25px; margin-bottom: 15px; color: #2c3e50; font-weight: bold; border-bottom: 2px solid #3498db; padding-bottom: 8px; }
    </x-slot>

    <x-report-header
        :documentNumber="$expense->number"
        :title="__('owner.expenses.print.title')"
        :titleEn="'Expense Report'"
        :settings="$settings ?? []"
    />

    <x-report-info :settings="$settings ?? []">
        <x-slot name="additionalInfo">
            <table style="width: 100%; border: none; margin-bottom: 10px;">
                <tr>
                    <td style="border: none; vertical-align: top; text-align: start;">
                        <p style="margin: 5px 0;"><strong>{{ __('owner.expenses.table.expense_number') }}:</strong> {{ $expense->number }}</p>
                        <p style="margin: 5px 0;"><strong>{{ __('owner.expenses.table.date') }}:</strong> {{ $expense->date }} <small class="text-muted">(@hijri($expense->date))</small></p>
                    </td>
                    <td style="border: none; vertical-align: top; text-align: end;">
                        <p style="margin: 5px 0;"><strong>{{ __('owner.expenses.table.status') }}:</strong>
                            <span class="badge {{ $expense->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                {{ $expense->status === 'paid' ? __('owner.paid') : __('owner.pending') }}
                            </span>
                        </p>
                        <p style="margin: 5px 0;"><strong>{{ __('owner.expenses.show.payment_method') }}:</strong> {{ $expense->paymentMethod->name ?? '-' }}</p>
                    </td>
                </tr>
            </table>
        </x-slot>
    </x-report-info>

    <div class="metadata">
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.expenses.table.category') }}:</span>
            <span class="meta-value">{{ optional($expense->category->parent)->name ?? '-' }} / {{ $expense->category->name ?? '-' }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.expenses.table.boat') }}:</span>
            <span class="meta-value">{{ $expense->boat->name ?? __('owner.general') }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.expenses.table.vendor') }}:</span>
            <span class="meta-value">{{ $expense->vendor->name ?? '-' }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.expenses.show.totals.final_total') }}:</span>
            <span class="meta-value" style="color: #28a745; font-weight: bold;">{{ number_format($expense->final_price, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>
    </div>

    @if($expense->notes)
    <div class="metadata" style="margin-top: 15px;">
        <div style="width: 100%;">
            <span class="meta-label">{{ __('owner.expenses.show.notes') }}:</span>
            <p class="meta-value" style="margin-top: 5px;">{{ $expense->notes }}</p>
        </div>
    </div>
    @endif

    @if(optional($expense->category->parent)->type === 'operating' && $expense->category->type === 'operating-equipments')
        <h5>{{ __('owner.expenses.print.equipment_details') }}</h5>
        <x-report-table>
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>{{ __('owner.expenses.print.equipment_name') }}</th>
                    <th style="width: 100px;">{{ __('owner.expenses.print.quantity') }}</th>
                    <th style="width: 120px;">{{ __('owner.expenses.print.unit_price') }}</th>
                    <th style="width: 120px;">{{ __('owner.expenses.print.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expense->details as $detail)
                    @php $item = $detail->expenseable; @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="text-align: start;">{{ $item->fishingEquipment->name ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></td>
                        <td style="font-weight: bold;">{{ number_format($item->total_price, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></td>
                    </tr>
                @endforeach
            </tbody>
        </x-report-table>
    @elseif(optional($expense->category->parent)->type === 'maintenance')
        <h5>{{ __('owner.expenses.print.maintenance_details') }}</h5>
        <x-report-table>
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th style="width: 100px;">{{ __('owner.expenses.table.date') }}</th>
                    <th>{{ __('owner.expenses.print.boat') }}</th>
                    <th>{{ __('owner.expenses.print.description') }}</th>
                    <th style="width: 100px;">{{ __('owner.expenses.print.technician') }}</th>
                    <th style="width: 120px;">{{ __('owner.expenses.print.estimated_cost') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expense->details as $detail)
                    @php $maintenance = $detail->expenseable; @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $maintenance->date }}
                            <br><small class="text-muted">@hijri($maintenance->date)</small>
                        </td>
                        <td style="text-align: start;">{{ $maintenance->boat->name ?? '-' }}</td>
                        <td style="text-align: start;">{{ $maintenance->description }}</td>
                        <td>{{ $maintenance->technician }}</td>
                        <td style="font-weight: bold;">{{ number_format($maintenance->estimated_cost, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></td>
                    </tr>
                @endforeach
            </tbody>
        </x-report-table>
    @endif

    <x-report-summary :qrCode="$qrCode ?? ''">
        <div class="summary-row">
            <span>{{ __('owner.expenses.show.totals.before_discount') }}:</span>
            <span>{{ number_format($expense->total_price, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>

        @if($expense->discount_value > 0)
        <div class="summary-row" style="color: #dc3545;">
            <span>{{ __('owner.expenses.show.totals.discount_value') }}
                @if($expense->discount_type === 'percentage')
                    ({{ $expense->discount_value }}%)
                @endif:
            </span>
            <span>- {{ number_format($expense->calculated_discount, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>
        @endif

        <div class="summary-row total-row">
            <span><strong>{{ __('owner.expenses.show.totals.final_total') }}:</strong></span>
            <span><strong style="color: #28a745; font-size: 1.1em;">{{ number_format($expense->final_price, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></strong></span>
        </div>
    </x-report-summary>

</x-report-layout>
