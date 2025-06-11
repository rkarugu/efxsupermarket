<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GrnUpdateUtilityController extends Controller
{
    protected $model = 'update-grn-utility';

    protected $title = 'Update GRN Utility';

    public function edit()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        return view('admin.utility.update_grn_utility', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => [
                'Utilities' => '',
                $this->title => ''
            ]
        ]);
    }
}
