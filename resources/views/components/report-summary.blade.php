@props([
    'qrCode' => '',
    'summaryItems' => [],
])

{{-- Table-based bottom section holding the totals box --}}
<table class="bottom-section">
    <tr>
        <td class="summary-cell">
            <div class="summary-box">
                {{ $slot }}
            </div>
        </td>
    </tr>
</table>
