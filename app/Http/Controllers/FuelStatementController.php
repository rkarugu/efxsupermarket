<?php

namespace App\Http\Controllers;

use App\Model\Restaurant;
use App\Models\FuelStatement;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class FuelStatementController extends Controller
{
    protected string $model = 'fuel-statements';
    protected string $permissionModule = 'fuel-statements';

    public function showListingPage(Request $request): View|RedirectResponse
    {
        if (!can('view', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'Fuel Statements - Listing';
        $model = $this->model;
        $breadcrum = ['Fuel Management' => '', 'Statements' => ''];

        $branches = Restaurant::select('id', 'name')->get();
        $statements = DB::table('fuel_statements')
            ->select(
                'fuel_statements.*',
                'fuel_entries.lpo_number',
                'fuel_entries.fueling_time',
                DB::raw("(quantity * terminal_price) as fuel_total")
            )
            ->leftJoin('fuel_entries', 'fuel_statements.matched_fuel_entry_id', '=', 'fuel_entries.id');

        if ($request->fueling_date) {
            $statements = $statements->whereDate('timestamp', '=', Carbon::parse($request->fueling_date)->toDateString());
        }

        $statements = $statements->orderBy('timestamp');

        if ($request->status == 'Matched') {
            $statements = $statements->havingNotNull('lpo_number');
        }

        if ($request->status == 'Open') {
            $statements = $statements->havingNull('lpo_number');
        }

        $statements = $statements->get()
            ->map(function ($record) {
                $record->status = $record->lpo_number ? 'Matched' : 'Open';
                $record->timestamp = Carbon::parse($record->timestamp)->toDayDateTimeString();
                $record->fueling_time = Carbon::parse($record->fueling_time)->toDayDateTimeString();

                return $record;
            });

        return view('fuel_statements.listing', compact('title', 'model', 'branches', 'breadcrum', 'statements'));
    }

    public function showUploadPage(): View|RedirectResponse
    {
        if (!can('view', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'Fuel Statements - Upload';
        $model = $this->model;
        $breadcrum = ['Fuel Management' => '', 'Statements' => ''];

        $branches = Restaurant::select('id', 'name')->get();

        return view('fuel_statements.upload', compact('title', 'model', 'branches', 'breadcrum'));
    }

    public function upload(Request $request): JsonResponse
    {
        try {
            $reader = new Xlsx();
            $reader->setReadDataOnly(true);
            $fileName = $request->file('upload_file');
            $spreadsheet = $reader->load($fileName);
            $data = $spreadsheet->getActiveSheet()->toArray();

            array_shift($data);

            $branches = Restaurant::select('id', 'name')->get();

            $existingStatements = DB::table('fuel_statements')->get();

            $payload = [];
            foreach ($data as $row) {
                if (!($existingStatement = $existingStatements->where('receipt_number', $row[11])->first())) {
                    $payload[] = [
                        'timestamp' => Carbon::createFromFormat('d.m.y H:i:s', $row[0])->toDateTimeString(),
                        'branch_id' => $request->branch_id,
                        'branch_name' => $branches->where('id', $request->branch_id)->first()->name,
                        'receipt_number' => $row[11],
                        'quantity' => abs((float)$row[5]),
                        'terminal_price' => abs((float)$row[6]),
                        'discount' => abs((float)$row[7]),
                        'narrative' => "$row[2]/$row[3]/$row[4]",
                        'total' => manageAmountFormat(abs((float)$row[10])),
                    ];
                }
            }

            return $this->jsonify($payload);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function save(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->data, true);
            $inserts = [];
            foreach ($data as $record) {
                $inserts[] = [
                    'timestamp' => $record['timestamp'],
                    'receipt_number' => $record['receipt_number'],
                    'branch_id' => $record['branch_id'],
                    'quantity' => $record['quantity'],
                    'terminal_price' => $record['terminal_price'],
                    'discount' => $record['discount'],
                    'narrative' => $record['narrative'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            FuelStatement::insert($inserts);
            return $this->jsonify([]);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
