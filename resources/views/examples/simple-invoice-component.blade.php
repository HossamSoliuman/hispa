{{--
    Example: Simple Invoice Report
    This demonstrates how to use report components to create a custom invoice
--}}

<x-report-layout
    title="Invoice #12345"
    title-en="Invoice"
    document-number="#INV-12345"
    :settings="$settings"
    :qr-code="$qrCode">

    {{-- Header with logo and title --}}
    <x-report-header
        document-number="#INV-12345"
        title="فاتورة ضريبية"
        title-en="Tax Invoice"
        :settings="$settings" />

    {{-- Company and invoice info --}}
    <x-report-info
        :settings="$settings"
        from-date="2024-01-01"
        to-date="2024-01-31">
        <x-slot:additionalInfo>
            <p><strong>Customer:</strong> Example Customer</p>
            <p><strong>Invoice Date:</strong> {{ now()->format('Y-m-d') }}</p>
        </x-slot:additionalInfo>
    </x-report-info>

    {{-- Items table --}}
    <x-report-table
        :headers="['#', 'Item Name', 'Quantity', 'Price', 'Total']"
        :data="$items">

        <x-slot:metadata>
            <div class="meta-item">
                <span class="meta-label">Total Items:</span>
                <span class="meta-value">{{ $statistics['total_items'] }}</span>
            </div>
        </x-slot:metadata>

        @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price, 2) }}</td>
                <td class="currency-symbol">
                    {{ number_format($item->total, 2) }}
                    <x-riyal-icon />
                </td>
            </tr>
        @endforeach
    </x-report-table>

    {{-- Summary with QR code --}}
    <x-report-summary :qr-code="$qrCode">
        <x-report-summary-row
            label="Subtotal"
            :value="number_format($statistics['subtotal'], 2)" />

        <x-report-summary-row
            label="Tax (15%)"
            :value="number_format($statistics['tax'], 2)" />

        <x-report-summary-row
            label="Total"
            :value="number_format($statistics['total'], 2)"
            :is-total="true" />
    </x-report-summary>

    {{-- Footer --}}
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>{{ config('app.name') }} - {{ now()->year }}</p>
    </div>

</x-report-layout>
