<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaGrn;
use Illuminate\Support\Facades\Session;

class GrnController extends Controller
{
    public function sendDocuments($grnNumber)
    {
        if (!can('view', 'confirmed-receive-purchase-order')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        WaGrn::query()->where('grn_number', $grnNumber)
            ->update([
                'documents_sent' => 1,
                'documents_sent_by' => auth()->user()->id,
                'documents_sent_at' => now(),
            ]);

        Session::flash('success', 'Documents marked as sent successfully');

        return redirect()->back();
    }

    public function receiveDocuments($grnNumber)
    {
        if (!can('process', 'pending-grns')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        WaGrn::query()->where('grn_number', $grnNumber)
            ->update([
                'documents_received' => 1,
                'documents_received_by' => auth()->user()->id,
                'documents_received_at' => now(),
            ]);

        Session::flash('success', 'Documents marked as received successfully');

        return redirect()->back();
    }
}
