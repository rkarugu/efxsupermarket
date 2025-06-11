<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\WaChartsOfAccount;
use App\Models\UserGeneralLedgerAccount;
use Exception;
use Illuminate\Http\Request;

class AssignAccountUserController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'assign-account-view';
        $this->title = 'Assign Account';
        $this->pmodule = 'assign-account-view';
    }

    public function assignAccountView()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Assign Account' => ''];

        $users = User::withCount('usergeneralledgeraccounts')
            ->with(['usergeneralledgeraccounts' => function ($query) {
                $query->with('accounts');
            }])
            ->get();
        $accounts = WaChartsOfAccount::get();
        return view('admin.assign_account_user_utility.index', compact('title', 'model', 'pmodule', 'breadcum', 'permission', 'users', 'accounts'));
    }

    public function createAccountsAndUsers(Request $request)
    {

        $messages = [
            'userId.required' => 'Select a user.',
            'accountIds.required' => 'Select atleast one account.',
        ];
        
        $data = $request->validate([
            'userId' => 'required|exists:users,id',
            'accountIds' => 'required|array',
            'accountIds.*' => 'exists:wa_charts_of_accounts,id'
        ], $messages);

        try {
            $userId = $data['userId'];
            $accountIds = $data['accountIds'];

            $existingAccounts = UserGeneralLedgerAccount::where('user_id', $userId)
                ->whereIn('account_id', $accountIds)
                ->exists();

            if ($existingAccounts) {
                return response()->json(['message' => 'These accounts are already assigned to the user'], 400);
            }

            foreach ($accountIds as $accountId) {
                UserGeneralLedgerAccount::create([
                    'user_id' => $userId,
                    'account_id' => $accountId
                ]);
            }

            return response()->json(['message' => 'Accounts assigned to user successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateAccountsAndUsers(Request $request, $userId)
    {
        $messages = [
            'userId.required' => 'Select a user.',
            'accountIds.required' => 'Select atleast one account.',
        ];

        $data = $request->validate([
            'accountIds' => 'required|array',
            'accountIds.*' => 'exists:wa_charts_of_accounts,id'
        ], $messages);

        try {
            $accountIds = $data['accountIds'];
            UserGeneralLedgerAccount::where('user_id', $userId)->delete();
            foreach ($accountIds as $accountId) {
                UserGeneralLedgerAccount::create([
                    'user_id' => $userId,
                    'account_id' => $accountId
                ]);
            }

            return response()->json(['message' => 'User accounts updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
