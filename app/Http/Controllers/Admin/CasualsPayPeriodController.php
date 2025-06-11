<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Casual;
use Carbon\CarbonPeriod;
use App\Model\Restaurant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PayrollSetting;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CasualsPayPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\CasualsPayPeriodDetail;
use App\Services\PesaFlowDisbursementService;
use App\Imports\HR\CasualsPayPeriodRegisterImport;
use App\Exports\HR\CasualsPayPeriodRegisterTemplate;
use Maatwebsite\Excel\Validators\ValidationException;

class CasualsPayPeriodController extends Controller
{
    public function __construct(protected $title = 'Casual Pay', protected $model = 'hr-and-payroll-payroll',)
    {
        // 
    }
    
    public function casualsPayPeriods()
    {
        if (can('view', 'casuals-pay-pay-periods')) {
            $title = $this->title . ' | Pay Periods';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Casuals Pay' => '', 'Pay Periods' => ''];

            $user = Auth::user();

            $branches = Restaurant::all();

            return view('admin.hr.payroll.casuals-pay-periods', compact('title', 'model', 'breadcum', 'user', 'branches'));
        } else {
            return returnAccessDeniedPage();
        }
    }
    
    public function casualsPayPeriodPrint(CasualsPayPeriod $casualsPayPeriod)
    {
        if (!can('print', 'casuals-pay-pay-periods')) {
            return returnAccessDeniedPage();
        }

        $casualsPayPeriod->load([
            'branch',
            'initialApprover',
            'finalApprover',
            'casualsPayPeriodDetails.casual',
            'casualsPayPeriodDetails.disbursement'
        ]);

        $pdf = Pdf::loadView('admin.hr.payroll.casuals-pay-period.print', compact('casualsPayPeriod'))
            ->setPaper('a4', 'landscape');

        $filename = Str::slug("{$casualsPayPeriod->branch->name} $casualsPayPeriod->start_date $casualsPayPeriod->end_date Pay Period", '_');

        return $pdf->stream("$filename.pdf");
    }
    
    public function casualsPayPeriodDetails($id)
    {
        if (can('details', 'casuals-pay-pay-periods')) {
            $title = $this->title . ' | Pay Period Details';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Casual Pay' => '', 'Pay Periods' => route('hr.payroll.casuals-pay.pay-periods'), 'Pay Period Details' => ''];

            $user = Auth::user();

            $casualPay = PayrollSetting::where('name', 'Casual Pay')->first()->value;

            return view('admin.hr.payroll.casuals-pay-period-details', compact('title', 'model', 'breadcum', 'user', 'id', 'casualPay'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function casualsPayPeriodRegisterTemplate($id)
    {
        if (can('upload-register', 'casuals-pay-pay-periods')) {

            $payPeriod = CasualsPayPeriod::with('casuals')->find($id);
            
            $data = [];

            foreach($payPeriod->casuals as $casual) {
                $data[] = [
                    $casual->phone_no
                ];
            }

            $export = new CasualsPayPeriodRegisterTemplate(collect($data), $id);
            
            return Excel::download($export, "casuals_pay_period_register.xlsx");
        } else {
            return returnAccessDeniedPage();
        }
    }

    // APIs
    public function casualsPayPeriodsList()
    {
        return response()->json(CasualsPayPeriod::with('branch')->orderBy('start_date', 'desc')->get());
    }

    public function casualsPayPeriodsOpen(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|int|exists:restaurants,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $casualsPayPeriod = CasualsPayPeriod::where('branch_id', $data['branch_id'])
            ->where('start_date', $data['start_date'])
            ->where('end_date', $data['end_date'])
            ->first();

        if ($casualsPayPeriod) {
            return response()->json(['message' => 'Casuals pay period already exists.'], 400);
        }

        DB::beginTransaction();
        try {
            $casualsPayPeriod = CasualsPayPeriod::create($data);

            $casuals = Casual::where('branch_id', $casualsPayPeriod->branch_id)->where('active', true)->get();

            $period = CarbonPeriod::create(Carbon::parse($casualsPayPeriod->start_date), Carbon::parse($casualsPayPeriod->end_date));

            $dates = [];

            foreach ($period as $date) {
                $dates[$date->format('Y-m-d')] = false;
            }

            foreach($casuals as $casual) {
                $casualsPayPeriod->casualsPayPeriodDetails()->create([
                    'casual_id' => $casual->id,
                    'dates' => $dates
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Casuals pay period created successfully.'], 201);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function casualsPayPeriod(CasualsPayPeriod $casualsPayPeriod)
    {
        return response()->json($casualsPayPeriod->load('branch', 'casualsPayPeriodDetails.casual', 'initialApprover', 'finalApprover'));
    }

    public function casualsPayPeriodDetailsUpdate(Request $request, CasualsPayPeriod $casualsPayPeriod)
    {
        $request->validate([
            '*.id' => 'required|int|exists:casuals_pay_period_details,id',
            '*.amount' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $this->updateCasualsPayPeriodDetails($casualsPayPeriod, $request->all());

            DB::commit();

            return response()->json(['message' => 'Casuals pay period details updated successfully.']);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
        
    }

    public function casualsPayPeriodDetailsApprove(Request $request, CasualsPayPeriod $casualsPayPeriod)
    {
        $data = $request->validate([
            'stage' => 'required|in:initial,final',
            'casuals_pay_period_details' => 'required|array',
            'casuals_pay_period_details.*.id' => 'required|int|exists:casuals_pay_period_details,id',
            'casuals_pay_period_details.*.amount' => 'required|numeric',
        ]);

        $casualsPayPeriodDetails = $request->casuals_pay_period_details;

        DB::beginTransaction();
        try {
            $this->updateCasualsPayPeriodDetails($casualsPayPeriod, $casualsPayPeriodDetails);
            
            if ($data['stage'] == 'initial') {
                $casualsPayPeriod->update([
                    'initial_approval' => true,
                    'initial_approver' => $request->user()->id,
                    'initial_approval_date' => now()
                ]);

                DB::commit();

                return response()->json(['message' => 'Casuals pay period approved.']);
                
            } else if ($data['stage'] == 'final') {
                $pesaFlow = new PesaFlowDisbursementService();

                $initiated = 0;
                $totalItems = count($casualsPayPeriodDetails);
                foreach ($casualsPayPeriodDetails as $payPeriodDetailItem) {
                    $payPeriodDetail = CasualsPayPeriodDetail::with('casual')->find($payPeriodDetailItem['id']);

                    $documentNo = getCodeWithNumberSeries('CASUAL_PAY_DISBURSEMENT');
                    $narrative = "Casual pay disbursement for {$payPeriodDetail->casual->full_name} ({$payPeriodDetail->casual->phone_no}). Period: {$casualsPayPeriod->start_date} - {$casualsPayPeriod->end_date}";
                    
                    $disbursement = $payPeriodDetail->casualsPayDisbursements()->create([
                        'document_no' => $documentNo,
                        'amount' => $payPeriodDetailItem['amount'],
                        'narrative' => $narrative,
                    ]);
                    
                    $callBackUrl = env('APP_URL') . "/api/hr/payroll/casual-pay/disbursement/{$disbursement->id}/callback";

                    try {
                        $response = $pesaFlow->initiateWithdrawal($payPeriodDetail->casual->phone_no, $payPeriodDetail->amount, $callBackUrl);

                        if ($response->ok()) {
                            $disbursement->update(['reference' => $response->json()['reference']]);
                
                            updateUniqueNumberSeries('CASUAL_PAY_DISBURSEMENT', $documentNo);

                            $initiated++;
                        } else {
                            $disbursement->delete();
    
                            throw new Exception($response->body());
                        }
                    } catch (Exception $e) {
                        Log::info("Failed: $narrative: " . $e->getMessage());
                    }
                }

                $additionalMessage = '';
                if ($initiated) {
                    $casualsPayPeriod->update([
                        'final_approval' => true,
                        'final_approver' => $request->user()->id,
                        'final_approval_date' => now(),
                        'status' => 'closed'
                    ]);

                    $additionalMessage = 'Casual pay period closed.';
                }

                DB::commit();

                return response()->json(['message' => "Casuals pay period approved. $initiated out of $totalItems disbursements have been initiated. $additionalMessage"]);
            }
        } catch (Exception $e) {
            DB::rollback();
            
            return response()->json(['message' => $e->getMessage()], 500);
        }
            
    }

    public function casualsPayPeriodDetailsUploadRegister(Request $request, $id)
    {
        $request->validate([
            'uploaded_file' => 'required|file|mimes:xlsx'
        ]);
        
        try {
            $import = new CasualsPayPeriodRegisterImport($id);
            $import->import($request->file('uploaded_file'));

            if ($import->failures()->count()) {
                
                $data = [];
                foreach($import->failures() as $failure) {
                    
                    $row = $failure->row();
                    $errors = str_replace('.', '', $failure->errors()[0]);

                    if (array_key_exists($row, $data)) {
                        $data[$row]['errors'] .= ', ' . $errors;
                    } else {
                        $data[$row] = [
                            ...$failure->values(),
                            'errors' => $errors
                        ];
                    }
                }

                $values = array_map(function($dataItem) {
                    return array_values($dataItem);
                }, array_values($data));

                array_push($values, ...[
                    [''],
                    [''],
                    [''],
                    ['{--- Delete this line and everything below it ---}'],
                    ['The above rows have errors. Check the errors column for additional information.'],
                ]);

                $file = Excel::raw(new CasualsPayPeriodRegisterTemplate(collect($values), $id), \Maatwebsite\Excel\Excel::XLSX);

                return response($file, 201);
            }

            return response()->json([
                'message' => 'Data uploaded successfully'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function updateCasualsPayPeriodDetails($casualsPayPeriod, $casualsPayPeriodDetails)
    {
        try {
            $period = CarbonPeriod::create(Carbon::parse($casualsPayPeriod->start_date), Carbon::parse($casualsPayPeriod->end_date));

            foreach ($casualsPayPeriodDetails as $payPeriodDetailItem) {
                $payPeriodDetail = CasualsPayPeriodDetail::find($payPeriodDetailItem['id']);
    
                $dates = [];

                foreach ($period as $date) {
                    $dates[$date->format('Y-m-d')] = $payPeriodDetailItem[$date->format('Y-m-d')];
                }

                $payPeriodDetail->update([
                    'dates' => $dates,
                    'amount' => $payPeriodDetailItem['amount']
                ]);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function casualsPayPeriodDetailsRefreshCasualsList(Request $request, CasualsPayPeriod $casualsPayPeriod)
    {
        $period = CarbonPeriod::create(Carbon::parse($casualsPayPeriod->start_date), Carbon::parse($casualsPayPeriod->end_date));

        $dates = [];

        foreach ($period as $date) {
            $dates[$date->format('Y-m-d')] = false;
        }
        
        try {
            Casual::where('branch_id', $casualsPayPeriod->branch_id)
                ->where('active', true)
                ->get()
                ->each(function ($casual) use ($casualsPayPeriod, $dates) {
                    $casualsPayPeriod->casualsPayPeriodDetails()->firstOrCreate([
                        'casual_id' => $casual->id
                    ], [
                        'dates' => $dates,
                    ]);
                });

            return response()->json(['message' => 'Casuals list refreshed successfully.']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
