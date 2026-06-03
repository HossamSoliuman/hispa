<?php

namespace App\Traits;

use App\Models\DalalStock;

trait DalalStockStatusChecker
{
    use RespondsWithHttpStatus;

    public function checkDalalStockStatus($dalalStockId)
    {
        $dalalStock = DalalStock::find($dalalStockId);

        if (! $dalalStock) {
            return [
                'allowed' => false,
                'response' => $this->failure('مخزون الدلال غير موجود.', [], 404),
            ];
        }

        // منع أي عملية إذا كانت الحالة 1 أو 2
        if (in_array($dalalStock->status, [1, 2])) {
            $message = $dalalStock->status == 1
                ? 'لا يمكن تنفيذ العملية. مخزون الدلال قيد المعالجة.'
                : 'لا يمكن تنفيذ العملية. مخزون الدلال منتهي.';

            return [
                'allowed' => false,
                'response' => $this->failure($message, [], 403),
            ];
        }

        return [
            'allowed' => true,
            'dalalStock' => $dalalStock,
        ];
    }
}
