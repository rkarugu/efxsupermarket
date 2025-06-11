<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Withholding\ProcessWithholding;
use App\Exports\WithholdingVatExport;
use App\Http\Controllers\Controller;
use App\Model\WaBankAccount;
use App\Model\WaSuppTran;
use App\Models\WaBankFile;
use App\Models\WaWithholdingFile;
use App\Models\WaWithholdingFileItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class WithholdingFilesController extends Controller
{
    protected $model;
    protected $title;

    public function __construct()
    {
        $this->model = 'withholding-files';
        $this->title = 'Withholding Files';
    }

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $query = WaWithholdingFile::query()
            ->with([
                'preparedBy',
                'items.bankFile.account',
                'items.bankFile.preparedBy'
            ]);

        if (request()->from && request()->to) {
            $query->whereBetween('created_at', [request()->from . ' 00:00:00', request()->to . '23:59:59']);
        }

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('amount', function ($file) {
                    return manageAmountFormat($file->amount);
                })
                ->editColumn('created_at', function ($file) {
                    return $file->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('actions', function ($file) {
                    return view('admin.withholding_files.action', ['file' => $file]);
                })
                ->toJson();
        }

        $breadcum = [
            'Withholding Files' => route($this->model . '.index')
        ];

        return view('admin.withholding_files.index', [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => $breadcum,
            'accounts' => WaBankAccount::query()
                ->with('getGlDetail')
                ->whereHas('paymentMethod', function ($query) {
                    $query->where('use_for_payments', 1);
                })->get(),
        ]);
    }

    public function create()
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $breadcum = [
            'WithHolding Files' => route($this->model . '.index'),
            'Create' => route($this->model . '.create'),
        ];

        return view('admin.withholding_files.create', [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => $breadcum,
            'accounts' => WaBankAccount::query()
                ->with('getGlDetail')
                ->whereHas('paymentMethod', function ($query) {
                    $query->where('use_for_payments', 1);
                })->get(),
            'files' => WaBankFile::query()
                ->withCount('items')
                ->pendingWithholding()
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, [
            'bank_files' => 'required'
        ]);

        $files = WaBankFile::whereIn('id', $request->bank_files)->get();

        DB::beginTransaction();

        try {
            $withholdingFile = WaWithholdingFile::create([
                'file_no' => getCodeWithNumberSeries('WITHHOLDING_FILES'),
                'prepared_by' => auth()->user()->id,
                'amount' => 0,
            ]);

            updateUniqueNumberSeries('WITHHOLDING_FILES', $withholdingFile->file_no);

            $totalAmount = 0;

            foreach ($files as $file) {
                $itemsAmount = 0;

                foreach ($file->items as $fileItem) {
                    if ($fileItem->voucher->withholding_amount == 0) {
                        continue;
                    }

                    $itemsAmount += $fileItem->voucher->withholding_amount;
                }

                if ($itemsAmount > 0) {
                    WaWithholdingFileItem::create([
                        'wa_withholding_file_id' =>  $withholdingFile->id,
                        'wa_bank_file_id' =>  $file->id,
                        'amount' =>  $itemsAmount,
                    ]);

                    $totalAmount += $itemsAmount;
                }
            }

            $withholdingFile->update([
                'amount' => $totalAmount
            ]);

            DB::commit();

            Session::flash('success', 'WithHolding file created successfully.');

            return redirect()->route('withholding-files.index');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('withholding-files.create')->withErrors($e->getMessage());
        }
    }

    public function download(WaWithholdingFile $file)
    {
        $query = WaSuppTran::query()
            ->select([
                'wa_supp_trans.id',
                'supplier.name',
                'supplier.kra_pin',
                'wa_supp_trans.cu_invoice_number',
                'wa_supp_trans.trans_date',
                DB::raw('wa_supp_trans.total_amount_inc_vat - wa_supp_trans.vat_amount as exclusive'),
                'wa_supp_trans.vat_amount',
                'files.created_at',
                'wa_supp_trans.withholding_amount',
                'vouchers.number',
            ])
            ->with(['notes' => function ($query) {
                $query->where('type', 'CREDIT');
            }])
            ->join('wa_suppliers as supplier', 'supplier.supplier_code', '=', 'wa_supp_trans.supplier_no')
            ->join('payment_voucher_items as items', function ($query) {
                $query->on('items.payable_id', '=', 'wa_supp_trans.id')
                    ->where('payable_type', 'invoice');
            })
            ->join('payment_vouchers as vouchers', 'vouchers.id', '=', 'items.payment_voucher_id')
            ->join('wa_bank_file_items as bitems', 'bitems.payment_voucher_id', '=', 'vouchers.id')
            ->join('wa_bank_files as files', 'files.id', '=', 'bitems.wa_bank_file_id')
            ->join('wa_withholding_file_items as witems', 'witems.wa_bank_file_id', '=', 'files.id')
            ->join('wa_withholding_files as wfiles', 'wfiles.id', '=', 'witems.wa_withholding_file_id')
            ->where('wa_supp_trans.withholding_amount', '>', 0)
            ->where('wfiles.id', $file->id);

        return Excel::download(new WithholdingVatExport($query->get()), $file->file_no . '_withholding_vat_' . date('Y-m-d-H-i-s') . '.csv');
    }

    public function destroy($id)
    {
        if (!can('delete', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $file = WaWithholdingFile::findOrFail($id);
        $file->items()->delete();
        $file->delete();

        Session::flash('success', 'WithHolding file removed successfully.');

        return redirect()->route('withholding-files.index');
    }
}
