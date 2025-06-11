<?php
namespace App\Http\Controllers\Admin\CreditSales;

use App\Http\Controllers\Controller;
use App\Models\ChequeBank;
use Illuminate\Http\Request;

class ChequeBankController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected $status;

    public function __construct(Request $request)
    {
         $this->pmodule = 'cheque-bank';
         $this->model = 'cheque-bank';
        $this->title = 'Banks';
    }

    public function index()
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->model;
        $permission =  $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $chequeBanks = ChequeBank::latest()->get();
            return view('admin.CreditSales.chequeBank.index', compact('model','title', 'chequeBanks'));
        }else{
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }

    public function store(Request $request)
    {
        $pmodule = $this->model;
        $permission =  $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___create']) || $permission == 'superadmin') {
            $data = $request->validate([
                'bank' => 'required|string|max:255|unique:cheque_banks,bank',
                'bounce_penalty' => 'required|numeric',
            ]);

            ChequeBank::create($data);
            return response()->json(['success' => 'Bank added successfully.']);
        }{
            return response()->json(['failed' => 'You do not have permission to add bank.'], 419);
        }
    }

    public function update(Request $request, $id)
    {
        $pmodule = $this->model;
        $permission =  $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___create']) || $permission == 'superadmin') {
            $data = $request->validate([
                'bank' => 'required|string|max:255',
                'bounce_penalty' => 'required|numeric',
            ]);

            $promotionType = ChequeBank::findOrFail($id);
            $promotionType->update($data);
            return response()->json(['success' => 'Bank updated successfully.']);
        }

            return response()->json(['failed' => 'You do not have permission to update a bank.'], 419);

    }

    public function destroy($id)
    {
        $pmodule = $this->model;
        $permission =  $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___delete']) || $permission == 'superadmin') {
            $promotionType = ChequeBank::findOrFail($id);
            $promotionType->delete();
            return response()->json(['success' => 'Bank deleted successfully.']);
        }

        return response()->json(['failed' => 'You do not have permission to Delete a bank.'], 419);
    }
}
