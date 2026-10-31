<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\JournalEntry;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function viewModal(JournalEntry $journalEntry)
    {
        $user = auth()->user();

        // 🔐 Branch guard (global rule)
        if (! $user->isSuperAdmin()) {
            abort_if(
                $journalEntry->branch_id !== $user->branch_id,
                403,
                'Unauthorized'
            );
        }

        // 🔐 Voucher-type specific rules (only if needed)
        if ($journalEntry->voucherType?->code === 'TRANSFER') {
            // future: transfer-specific permission / lock
            // example:
            // abort_if(! $user->can('account-transfers.view'), 403);
        }

        // Load everything needed for modal
        $journalEntry->load([
            'lines.account:id,name',
            'branch:id,name',
            'voucherType:id,name,code',
            'createdBy:id,name',
        ]);

        return view(
            'backend.modules.account.voucher.voucher_view_modal',
            compact('journalEntry')
        );
    }

}
