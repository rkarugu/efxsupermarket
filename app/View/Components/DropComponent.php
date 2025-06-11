<?php

namespace App\View\Components;

use App\Interfaces\SmsService;
use App\Model\User;
use App\Services\CashierManagementService;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class DropComponent extends Component
{

    public \Illuminate\Contracts\Auth\Authenticatable|null|\App\Model\User $cashier;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->cashier = Auth::user();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $drop_limit = $this->cashier->drop_limit ?? 100000;
        $cash_at_hand = $this->cashier->cashAtHand();
        $selling_allowance =  $drop_limit - $cash_at_hand;
        return view('components.drop-component', compact('cash_at_hand', 'selling_allowance'));
    }

    public function sendOtp()
    {
        $phone = request('phone_number');
        $password = request('password');


        /*get Chief cashier*/
        $chiefCashier = User::where('phone_number', $phone)->first();
        if (!$chiefCashier) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Phone Number'
            ]);
        }

        /*check if user is a chief cashier*/
        $can_continue = can('drop_cash', 'pos-cash-sales', $chiefCashier);

        if (!$can_continue) {
            return response()->json([
                'success' => false,
                'message' => 'User Not a Chief cashier'
            ]);
        }

        if (!Hash::check($password, $chiefCashier->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password'
            ]);
        }


        // Generate and send OTP
        $otp = rand(100000, 999999);
        // Store OTP in session or database
        session([
            'drop_otp' => [
                'code' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(4)->timestamp,
                'phone_number' => $phone
            ]
        ]);

        $smsService = (app(SmsService::class));
        $sms_msg = "OTP to drop cash for Cashier ".$this->cashier->name. ' '.$otp;
        $smsService->sendMessage($sms_msg, $chiefCashier->phone_number);

        return response()->json([
            'success' => true,
            'message'=> $otp
        ]);
    }

    public function verifyOtp(): \Illuminate\Http\JsonResponse
    {
        $otp = request('otp');
        $phone = request('phone_number');

        $stored_otp_data = session('drop_otp');

        // Check if OTP exists in session
        if (!$stored_otp_data) {
            return response()->json([
                'success' => false,
                'message' => 'No OTP found'
            ]);
        }

        // Verify phone number matches
        if ($stored_otp_data['phone_number'] !== $phone) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP request'
            ]);
        }

        // Check if OTP has expired
        if (Carbon::now()->timestamp > $stored_otp_data['expires_at']) {
            session()->forget('drop_otp');
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired'
            ]);
        }

        // Verify OTP
        if (!Hash::check($otp, $stored_otp_data['code'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ]);
        }

        // Clear the OTP from session after successful verification
        session()->forget('drop_otp');

        // Set verification status
        session(['otp_verified' => true]);

        return response()->json(['success' => true]);
    }
    public function dropcash()
    {
        if (!session('otp_verified')) {
            return response()->json([
                'success' => false,
                'message' => 'OTP not verified'
            ]);
        }
        try {

            $chiefcashier  = User::where('phone_number', request('phone_number'))->first();


           $service =  new CashierManagementService();
          $resp =  $service->dropCash($this->cashier, $chiefcashier, ceil($this->cashier->cashAtHand()));
          if (!$resp['status'])
          {
              return response()->json([
                  'success' => false,
                  'message' => $resp['message']
              ]);
          }

            // Clear the session
            session()->forget(['drop_otp', 'otp_verified']);


            return response()->json([
                'success' => true,
                'drop'=> $resp['drop'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function callCashier()
    {
        $this->cashier->dropLimitAlert();
        return response()->json([
            'success' => true,
            'message' => 'Notification sent to Chief cashier'
        ]);
    }

}
