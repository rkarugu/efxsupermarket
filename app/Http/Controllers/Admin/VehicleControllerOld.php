<?php
//
//namespace App\Http\Controllers\Admin;
//
//use App\Model\Bodytype;
//use App\Model\Expensehistory;
//use App\Model\Fuelentry;
//use App\Model\InspectionHistory;
//use App\Model\Issues;
//use App\Model\Meterhistory;
//use App\Model\Modal;
//use App\Model\Restaurant;
//use App\Model\ServiceHistory;
//use App\Model\ServiceRemainder;
//use App\Model\User;
//use App\Model\VehicleType;
//use App\Model\WaPoiStockSerialMoves;
//use Carbon\Carbon;
//use Illuminate\Http\JsonResponse;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
//
//class VehicleControllerOld
//{
//
//    protected $model;
//    protected $title;
//    protected $pmodule;
//
//    public function __construct()
//    {
//
//        $this->model = 'vehicle';
//        $this->title = 'Vehicle List';
//        $this->pmodule = 'vehicle';
//    }
//
//    public function modulePermissions($permission, $type)
//    {
//        if (!isset($permission[$this->pmodule . '___' . $type]) && $permission != 'superadmin') {
//            Session::flash('warning', 'Invalid Request');
//            return false;
//        }
//
//        return true;
//    }
//
//
//    public function index()
//    {
//        $pmodule = $this->pmodule;
//        $permission = $this->mypermissionsforAModule();
//        if (!$this->modulePermissions($permission, 'view')) {
//            return redirect()->back()->withErrors(['error' => 'Access Denied']);
//        }
//
//        $vehicles = NewVehicle::with(['device', 'driver'])->get()->map(function (NewVehicle $vehicle) {
//            $vehicle->acquisition_date = Carbon::parse($vehicle->acquisition_date)->toFormattedDateString();
//            return $vehicle;
//        });
//
//        $model = $this->model;
//        $title = 'My Fleet';
//        $googleMapsApiKey = config('app.google_maps_api_key');
//
//        return view('admin.vehicle.index', compact('vehicles', 'model', 'title', 'pmodule', 'permission', 'googleMapsApiKey'));
//    }
//
//    public function exportToPdf($id)
//    {
//        $id = base64_decode($id);
//        $permission = $this->mypermissionsforAModule();
//        $pmodule = $this->pmodule;
//        $title = $this->title;
//        $model = $this->model;
//        if ($permission != 'superadmin' && !isset($permission['vehicle___pdf'])) {
//            Session::flash('warning', 'Invalid Request');
//            return redirect()->back();
//        }
//
//        $data = WaPoiStockSerialMoves::where('vehicle_id', $id)->where('status', 'in_motor_vehicle')->get();
//        //dd($id);
//
//        if (!$data) {
//            Session::flash('warning', 'Invalid Request');
//            return redirect()->back();
//        }
//
//        $pdf = \PDF::loadView('admin.vehicle.print', compact('data', 'title', 'model', 'pmodule', 'permission', 'esd_details'));
//        $report_name = 'pos_cash_sales_' . date('Y_m_d_H_i_A');
//        return $pdf->download($report_name . '.pdf');
//    }
//
//    public function vehicle_dropdown(Request $request)
//    {
//
//        $data = Vehicle::select(['id', 'vin_sn as text']);
//        if ($request->q) {
//            $data->where('vin_sn', 'LIKE', "%$request->q%");
//            $data->orWhere('license_plate', 'LIKE', "%$request->q%");
//            $data->orWhere('registration_state_provine', 'LIKE', "%$request->q%");
//            $data->orWhere('year', 'LIKE', "%$request->q%");
//        }
//        $data = $data->get();
//        return $data;
//    }
//
//    public function create()
//    {
//        $permission = $this->mypermissionsforAModule();
//        $pmodule = $this->pmodule;
//        $title = $this->title;
//        $model = $this->model;
//
//        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
//            $branches = Restaurant::select(['id', 'name'])->get();
//            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
//
//            return view('admin.vehicle.create', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'branches'));
//        } else {
//            Session::flash('warning', 'Access Restricted');
//            return redirect()->back();
//        }
//    }
//
//    public function getDevices(): JsonResponse
//    {
//        try {
//            $vehicles = NewVehicle::whereHas('device')->with(['device', 'driver'])->get();
//            $telematics = new \App\Telematics();
//            $devices_response = $telematics->getDevices();
//            $devices = $devices_response[0]["items"];
//
//            $vehicles = $vehicles->map(function (NewVehicle $vehicle) use ($devices) {
//                $device = collect($devices)->where('id', $vehicle->device->device_id)->first();
//                if ($device) {
//                    $vehicle->device_data = [
//                        'device_id' => $device['id'],
//                        'lat' => $device['lat'],
//                        'lng' => $device['lng'],
//                        'speed' => $device['speed'],
//                        'fuel' => $device['sensors'][0]['value'],
//                    ];
//                }
//
//                return $vehicle;
//            });
//
//            return response()->json(['success' => 1, 'vehicles' => $vehicles]);
//        } catch (\Throwable $e) {
//            return response()->json(['success' => 0, 'message' => $e->getMessage()], 500);
//        }
//    }
//
//
//    public function store(Request $request)
//    {
//        $data['permission'] = $this->mypermissionsforAModule();
//        if (!$this->modulePermissions($data['permission'], 'create')) {
//            return response()->json(['result' => -1, 'message' => 'Restricted! You dont have permissions']);
//        }
//
//        $validator = Validator::make($request->all(), [
//            'license_plate' => 'required|unique:new_vehicles',
//            'name' => 'required',
//            'load_capacity' => 'required',
//        ]);
//
//        if ($validator->fails()) {
//            return redirect()->back()->withErrors($validator->errors());
//        }
//
//        try {
//            NewVehicle::create([
//                'name' => $request->name,
//                'license_plate' => $request->license_plate,
//                'branch_id' => $request->branch_id,
//                'acquisition_date' => $request->acquisition_date,
//                'vin_sn' => $request->vin_sn,
//                'load_capacity' => $request->load_capacity,
//            ]);
//
//            return redirect()->route($this->model . '.index')->with('success', 'Vehicle added successfully');
//        } catch (\Throwable $e) {
//            return redirect()->back()->withErrors(['msg' => 'An error was encountered. Please try again.']);
//        }
//    }
//
//    public function showAttachDeviceForm($id)
//    {
//        $permission = $this->mypermissionsforAModule();
//        $pmodule = $this->pmodule;
//        $title = $this->title;
//        $model = $this->model;
//
//        $vehicle = NewVehicle::find($id);
//        $devices = TelematicsDevice::all()->filter(function (TelematicsDevice $device) {
//            return !$device->vehicle;
//        });
//
//        $breadcum = ['My Fleet' => route($model . '.index'), 'Attach Device' => ''];
//        return view('admin.vehicle.attach-device', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'vehicle', 'devices'));
//
//    }
//
//    public function attachDevice(Request $request, $id)
//    {
//        try {
//            $device = TelematicsDevice::find($request->device_id);
//            $device->update(['vehicle_id' => $id]);
//
//            return redirect()->route('vehicle.index')->with('success', 'Device attached successfully');
//        } catch (\Throwable $e) {
//            return redirect()->back()->withErrors(['error' => 'An error was encountered. Please try again.']);
//        }
//    }
//
//    public function detachDevice(Request $request, $id)
//    {
//        try {
//            $device = TelematicsDevice::find($request->device_id);
//            $device->update(['vehicle_id' => $id]);
//
//            return redirect()->route('vehicle.index')->with('success', 'Device attached successfully');
//        } catch (\Throwable $e) {
//            return redirect()->back()->withErrors(['error' => 'An error was encountered. Please try again.']);
//        }
//    }
//
//    public function showAssignDriverFormForm($id)
//    {
//        $permission = $this->mypermissionsforAModule();
//        $pmodule = $this->pmodule;
//        $title = $this->title;
//        $model = $this->model;
//
//        $vehicle = NewVehicle::find($id);
//        $drivers = User::all()->filter(function (User $user) {
//            $role = $user->userRole;
//            return ($role->slug == 'delivery') && !$user->vehicle;
//        });
//
//        $breadcum = ['My Fleet' => route($model . '.index'), 'Assign Driver' => ''];
//        return view('admin.vehicle.assign-driver', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'vehicle', 'drivers'));
//
//    }
//
//    public function assignDriver(Request $request, $id)
//    {
//        try {
//            $vehicle = NewVehicle::find($id);
//            $driver = User::find($request->driver_id);
//            $vehicle->update(['driver_id' => $driver->id]);
//
//            return redirect()->route('vehicle.index')->with('success', 'Driver assigned successfully');
//        } catch (\Throwable $e) {
//            return redirect()->back()->withErrors(['error' => 'An error was encountered. Please try again.']);
//        }
//    }
//
//
//    public function edit($id)
//    {
//        try {
//            $permission = $this->mypermissionsforAModule();
//            if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
//                $row = Vehicle::where('id', $id)->first();
//                if ($row) {
//                    $title = 'Edit ' . $this->title;
//                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
//                    $model = $this->model;
//                    $vehicle = VehicleType::all();
//                    $make = Make::all();
//                    $modal = Modal::all();
//                    $bodytype = Bodytype::all();
//
//                    $telematics = new \App\Telematics();
//                    $devices_response = $telematics->getDevices();
//                    $devices = $devices_response[0]["items"];
//
//                    return view('admin.vehicle.edit', compact('title', 'model', 'breadcum', 'row', 'vehicle', 'make', 'modal', 'bodytype', 'devices'));
//                } else {
//                    Session::flash('warning', 'Invalid Request');
//                    return redirect()->back();
//                }
//            } else {
//                Session::flash('warning', 'Invalid Request');
//                return redirect()->back();
//            }
//
//        } catch (\Exception $e) {
//
//            $msg = $e->getMessage();
//            Session::flash('warning', $msg);
//            return redirect()->back();
//        }
//    }
//
//
//    public function update(Request $request, $id)
//    {
//        // echo "test"; die;
//        try {
//            $row = Vehicle::where('id', $id)->first();
//            // $row->vehicle_name= $request->vehicle_name;
//            $row->acquisition_date = $request->acquisition_date;
//            $row->vin_sn = $request->vin_sn;
//            $row->license_plate = $request->license_plate;
//            $row->type = $request->type;
//            $row->year = $request->year;
//            $row->make = $request->make;
//            $row->model = $request->model;
//            $row->trim = $request->trim;
//            $row->registration_state_provine = $request->registration_state_provine;
//            if ($request->hasFile('photo')) {
//                $file = $request->file('photo');
//                $image = uploadwithresize($file, 'vehiclelist');
//                $row->photo = $image;
//            }
//            $row->status = $request->status;
//            $row->group = $request->group;
//            $row->operator = $request->operator;
//            $row->ownership = $request->ownership;
//            $row->color = $request->color;
//            $row->body_type = $request->bodytype;
//            $row->msrp = $request->msrp;
//            $row->linked_devices = $request->linked_devices;
//            $row->device_id = $request->device;
//            $row->update();
//            Session::flash('success', 'Record updated successfully.');
//            return redirect()->route($this->model . '.index');
//        } catch (\Exception $e) {
//            $msg = $e->getMessage();
//            Session::flash('warning', $msg);
//            return redirect()->back()->withInput();
//        }
//    }
//
//
//    public function destroy($id)
//    {
//        try {
//            $find = Vehicle::findOrFail($id);
//            $find->status = "archived";
//            $find->save();
//
//
//            $response['result'] = 1;
//            $response['message'] = 'Archived have successfully';
//            return response()->json($response);
//
//            /*Session::flash('success', 'Archived have successfully.');
//            return redirect()->back();*/
//        } catch (\Exception $e) {
//            Session::flash('warning', 'Invalid Request');
//            return redirect()->back();
//        }
//    }
//
//    public function show(Request $request, $id)
//    {
//        $permission = $this->mypermissionsforAModule();
//        if (isset($permission[$this->pmodule . '___view']) || $permission == 'superadmin') {
//            $title = 'Show ' . $this->title;
//            $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
//            $model = $this->model;
//
//            $googleMapsApiKey = config('app.google_maps_api_key');
//            $row = Vehicle::where('id', $id)->first();
//            $device = $row->getDevice();
//
//            return view('admin.vehicle.location', compact('title', 'model', 'breadcum', 'row', 'googleMapsApiKey', 'device'));
//        } else {
//            Session::flash('warning', 'Invalid Request');
//            return redirect()->back();
//        }
//    }
//
//    public function overview(Request $request, $id)
//    {
//
//
//        try {
//            $permission = $this->mypermissionsforAModule();
//            if (isset($permission[$this->pmodule . '___view']) || $permission == 'superadmin') {
//                $row = Vehicle::where('id', $id)->first();
//                if ($row) {
//
//                    $issue_data = Issues::getDataVehicles(99999999999, 0, '', 'issues.id', 'desc', $request, $id);
//
//                    $service_remainder = ServiceRemainder::getDataVehicles(999999999999, 0, '', 'service_remainders.id', 'desc', $request, $id);
//
//                    $insepection_history = InspectionHistory::select('inspection_history_items.item_detail')->leftjoin('inspection_history_items', 'inspection_history.id', '=', 'inspection_history_items.inspection_history_id')->where('vehicle_id', $id)->get();
//
//
//                    $issueOpenCount = 0;
//                    $issueOverDueCount = 0;
//                    if ($issue_data['response']->count() > 0) {
//                        foreach ($issue_data['response'] as $issue) {
//                            if ($issue->resolve == 'open') {
//                                $issueOpenCount++;
//                            }
//                            if ($issue->due_date != NULL) {
//                                $issueOverDueCount++;
//                            }
//                        }
//                    }
//
//
//                    $remaindersOverdueCount = 0;
//                    $remaindersSnoozedCount = 0;
//                    $remaindersDueCount = 0;
//                    if ($service_remainder['response']->count() > 0) {
//                        foreach ($service_remainder['response'] as $service_remainder) {
//                            if ($service_remainder->status == 'overdue') {
//                                $remaindersOverdueCount++;
//                            }
//                            if ($service_remainder->status == 'snoozed') {
//                                $remaindersSnoozedCount++;
//                            }
//                            if ($service_remainder->next_due_date != NULL) {
//                                $remaindersDueCount++;
//                            }
//                        }
//                    }
//
//
//                    $inspectionPassCount = 0;
//                    $inspectionFailCount = 0;
//                    if ($insepection_history->count() > 0) {
//                        foreach ($insepection_history as $item) {
//                            if ($item->item_detail == 'pass') {
//                                $inspectionPassCount++;
//                            }
//                            if ($item->item_detail == 'fail') {
//                                $inspectionFailCount++;
//                            }
//                        }
//                    }
//
//
//                    $title = 'Show ' . $this->title;
//                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
//                    $model = $this->model;
//                    $vehicle = VehicleType::all();
//                    $make = Make::all();
//                    $modal = Modal::all();
//                    $bodytype = Bodytype::all();
//
//
//                    return view('admin.vehicle.show', compact('title', 'model', 'breadcum', 'row', 'vehicle', 'make', 'modal', 'bodytype', 'issueOpenCount', 'issueOverDueCount', 'remaindersOverdueCount', 'remaindersSnoozedCount', 'remaindersDueCount', 'inspectionPassCount', 'inspectionFailCount'));
//                } else {
//                    Session::flash('warning', 'Invalid Request');
//                    return redirect()->back();
//                }
//            } else {
//                Session::flash('warning', 'Invalid Request');
//                return redirect()->back();
//            }
//
//        } catch (\Exception $e) {
//
//            $msg = $e->getMessage();
//            Session::flash('warning', $msg);
//            return redirect()->back();
//        }
//    }
//
//
//    public function fuelentries(Request $request, $id)
//    {
//        $row = Vehicle::where('id', $id)->first();
//        $permission = $this->mypermissionsforAModule();
//        if ($request->ajax()) {
//            $sortable_columns = ['id', 'vehicle', 'vendor_name'];
//            $limit = $request->input('length');
//            $start = $request->input('start');
//            $search = $request['search']['value'];
//            $orderby = $request['order']['0']['column'];
//            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
//            $draw = $request['draw'];
//            $response = Fuelentry::getDataVehicles($limit, $start, $search, $sortable_columns[$orderby], $order, $request, $id);
//            $totalCms = $response['count'];
//            $data = $response['response']->toArray();
//            $total = 0;
//            foreach ($data as $key => $re) {
//                $data[$key]['links'] = '<div style="display:flex">';
//                if (isset($permission['fuelentry___view']) || $permission == 'superadmin') {
//                    $data[$key]['links'] .= '<a href="' . route('fuelentry.show', $re['id']) . '" data-id="' . $re['id'] . '" onclick="openEditForm(this);return false;" class="btn btn-danger btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>';
//                }
//
//                if (isset($permission['fuelentry___edit']) || $permission == 'superadmin') {
//                    $data[$key]['links'] .= ' &nbsp; <a href="' . route('fuelentry.edit', $re['id']) . '" data-id="' . $re['id'] . '" class="btn btn-danger btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>';
//                }
//
//
//                if (isset($permission['fuelentry___delete']) || $permission == 'superadmin') {
//                    $data[$key]['links'] .= '<form action="' . route('fuelentry.destroy', $re['id']) . '" method="POST"  class="deleteMe"><button class="btn btn-sm btn-danger" style="margin-left:4px" type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
//                     <input type="hidden" value="DELETE" name="_method">
//                     ' . csrf_field() . '
//                     </form>';
//                }
//
//                $data[$key]['links'] .= '</div>';
//
//                $data[$key]['dated'] = getDateFormatted($re['created_at']);
//
//                $data[$key]['photos'] = '<img src="' . asset('public/uploads/fuelentry/' . $re['photos']) . '" width="50px" height="50px"alt="image">';
//
//
//                $total += $re['total'];
//
//            }
//            $response['response'] = $data;
//            $return = [
//                "draw" => intval($draw),
//                "recordsFiltered" => intval($totalCms),
//                "recordsTotal" => intval($totalCms),
//                "data" => $response['response'],
//                "total" => manageAmountFormat($total)
//
//            ];
//            return $return;
//        }
//
//        $model = $this->model;
//        $title = 'Fuel History ' . $this->title;
//
//        return view('admin.vehicle.fuelentry', compact('title', 'model', 'breadcum', 'row', 'permission'));
//
//
//    }
//
//
//    public function expensehistory(Request $request, $id)
//    {
//        $row = Vehicle::where('id', $id)->first();
//        $permission = $this->mypermissionsforAModule();
//        if ($request->ajax()) {
//            $sortable_columns = ['id', 'vehicle', 'date', 'expense_type', 'vendor', 'amount'];
//            $limit = $request->input('length');
//            $start = $request->input('start');
//            $search = $request['search']['value'];
//            $orderby = $request['order']['0']['column'];
//            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
//            $draw = $request['draw'];
//            // dd($response);
//
//            $response = Expensehistory::getDataVehicles($limit, $start, $search, $sortable_columns[$orderby], $order, $request, $id);
//            // print_r($responses->toArray());die();
//            $totalCms = $response['count'];
//            $data = $response['response'];
//            $json = json_encode($data);
//            $data = json_decode($json, true);
//            // dd($array);
//            $total = 0;
//            foreach ($data as $key => $re) {
//                $data[$key]['source'] = '-';
//                $data[$key]['links'] = '<div style="display:flex">';
//                if (isset($permission['expensehistory___edit']) || $permission == 'superadmin') {
//                    $data[$key]['links'] .= '<a href="' . route('expensehistory.edit', $re['id']) . '" data-id="' . $re['id'] . '" onclick="openEditForm(this);return false;" class="btn btn-danger btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
//                }
//                if (isset($permission['expensehistory___delete']) || $permission == 'superadmin') {
//                    $data[$key]['links'] .= '<form action="' . route('expensehistory.destroy', $re['id']) . '" method="POST"  class="deleteMe"><button class="btn btn-sm btn-danger" style="margin-left:4px" type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
//                     <input type="hidden" value="DELETE" name="_method">
//                     ' . csrf_field() . '
//                     </form>';
//                }
//                $data[$key]['links'] .= '</div>';
//                $total += $re['amount'];
//
//                $data[$key]['dated'] = getDateFormatted($re['dated']);
//
//                // $data[$key]['photo'] = '<img src="'.asset('public/uploads/expensehistory/'.$re['photo']).'" width="50px" height="50px"alt="image">';
//
//            }
//            $response['response'] = $data;
//            $return = [
//                "draw" => intval($draw),
//                "recordsFiltered" => intval($totalCms),
//                "recordsTotal" => intval($totalCms),
//                "data" => $response['response'],
//                "total" => manageAmountFormat($total)
//            ];
//            return $return;
//        }
//
//        $model = $this->model;
//        $title = 'Fuel History ' . $this->title;
//
//        return view('admin.vehicle.expensehistory', compact('title', 'model', 'breadcum', 'row', 'permission'));
//
//
//    }
//
//
//    public function servicehistory(Request $request, $id)
//    {
//        $row = Vehicle::where('id', $id)->first();
//        $permission = $this->mypermissionsforAModule();
//        if ($request->ajax()) {
//            $sortable_columns = ['id', 'vehicle', 'vendor_name'];
//            $limit = $request->input('length');
//            $start = $request->input('start');
//            $search = $request['search']['value'];
//            $orderby = $request['order']['0']['column'];
//            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
//            $draw = $request['draw'];
//            $response = ServiceHistory::getDataVehicles($limit, $start, $search, $sortable_columns[$orderby], $order, $request, $id);
//            $totalCms = $response['count'];
//            $data = $response['response']->toArray();
//            // $total = 0;
//            foreach ($data as $key => $re) {
//                $data[$key]['links'] = '<div style="display:flex">';
//                if (isset($permission['servicehistory___view']) || $permission == 'superadmin') {
//                    $data[$key]['links'] .= '<a href="' . route('servicehistory.show', $re['id']) . '" data-id="' . $re['id'] . '" onclick="openEditForm(this);return false;" class="btn btn-danger btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>';
//                }
//
//                $data[$key]['links'] .= '</div>';
//
//                $data[$key]['dated'] = getDateFormatted($re['created_at']);
//
//                $data[$key]['photos'] = '<img src="' . asset('public/uploads/servicehistory/' . $re['photos']) . '" width="50px" height="50px"alt="image">';
//                // $total += $re['total'];
//
//            }
//            $response['response'] = $data;
//            $return = [
//                "draw" => intval($draw),
//                "recordsFiltered" => intval($totalCms),
//                "recordsTotal" => intval($totalCms),
//                "data" => $response['response'],
//                // "total"             =>  manageAmountFormat($total)
//
//            ];
//            return $return;
//        }
//
//        $model = $this->model;
//        $title = 'Fuel History ' . $this->title;
//
//        return view('admin.vehicle.servicehistory', compact('title', 'model', 'breadcum', 'row', 'permission'));
//    }
//
//
//    public function issues(Request $request, $id)
//    {
//        $row = Vehicle::where('id', $id)->first();
//        $permission = $this->mypermissionsforAModule();
//        if ($request->ajax()) {
//            $sortable_columns = ['id', 'asset', 'vendor_name', 'reported_date'];
//            $limit = $request->input('length');
//            $start = $request->input('start');
//            $search = $request['search']['value'];
//            $orderby = $request['order']['0']['column'];
//            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
//            $draw = $request['draw'];
//            $response = Issues::getDataVehicles($limit, $start, $search, $sortable_columns[$orderby], $order, $request, $id);
//            $totalCms = $response['count'];
//            $data = $response['response']->toArray();
//            // echo '<pre>';
//            // print_r($data);die;
//            foreach ($data as $key => $re) {
//
//                $buttonText = $re['resolve'] == 'resolve' ? 'Resolve' : 'Open';
//
//                $data[$key]['asset_type'] = "Vehicle";
//                $data[$key]['labels'] = "-";
//
//                $data[$key]['links'] = '<div style="display:flex">';
//                if (isset($permission['issues___view']) || $permission == 'superadmin') {
//                    $data[$key]['links'] .= '<a href="' . route('issues.show', $re['id']) . '" data-id="' . $re['id'] . '" onclick="openEditForm(this);return false;" class="btn btn-warning btn-sm">' . $buttonText . '</a>';
//                }
//                if (isset($permission['issues___delete']) || $permission == 'superadmin') {
//
//                }
//                $data[$key]['id'] = $re['id'] . '#';
//                $data[$key]['links'] .= '</div>';
//
//                $data[$key]['dated'] = getDateFormatted($re['created_at']);
//
//                $data[$key]['photos'] = '<img src="' . asset('public/uploads/issues/' . $re['photos']) . '" width="50px" height="50px"alt="image">';
//
//
//                // echo '<pre>';
//                // print_r($data); die;
//
//            }
//            $response['response'] = $data;
//            $return = [
//                "draw" => intval($draw),
//                "recordsFiltered" => intval($totalCms),
//                "recordsTotal" => intval($totalCms),
//                "data" => $response['response'],
//
//            ];
//            return $return;
//        }
//
//        $model = $this->model;
//        $title = 'Fuel History ' . $this->title;
//
//        return view('admin.vehicle.issues', compact('title', 'model', 'breadcum', 'row', 'permission'));
//    }
//
//
//    public function inspection_history(Request $request, $id)
//    {
//        $row = Vehicle::where('id', $id)->first();
//        $permission = $this->mypermissionsforAModule();
//        if ($request->ajax()) {
//            $sortable_columns = ['inspection_history.vehicle_id'];
//            $limit = $request->input('length');
//            $start = $request->input('start');
//            $search = $request['search']['value'];
//            $orderby = $request['order']['0']['column'];
//            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
//            $draw = $request['draw'];
//            $response = InspectionHistory::getDataVehicles($limit, $start, $search, $sortable_columns[$orderby], $order, $request, $id);
//            $totalCms = $response['count'];
//            $data = $response['response']->toArray();
//            $total = 0;
//            foreach ($data as $key => $re) {
//
//
//                $data[$key]['vehicle_id'] = @$re['vehicle']['vin_sn'];
//                $data[$key]['management'] = '-';
//                $data[$key]['created_at'] = date('j, M d, Y H:ia', strtotime(@$re['created_at']));
//                $data[$key]['inspection_form_id'] = @$re['form']['title'];
//                $data[$key]['duration'] = '-';
//                $data[$key]['user_id'] = @$re['user']['name'];
//                $data[$key]['location'] = '<i class="fa fa-warning"></i>';
//                $data[$key]['failed_item'] = '-';
//                $data[$key]['links'] = '<div style="display:flex">';
//
//                if (isset($permission[$this->pmodule . '___view']) || $permission == 'superadmin') {
//                    $data[$key]['links'] .= '&nbsp; <a href="' . route($this->model . '.show', base64_encode($re['id'])) . '" class="btn btn-danger btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>';
//                }
//                $data[$key]['links'] .= '</div>';
//            }
//            $response['response'] = $data;
//            $return = [
//                "draw" => intval($draw),
//                "recordsFiltered" => intval($totalCms),
//                "recordsTotal" => intval($totalCms),
//                "data" => $response['response'],
//                "total" => manageAmountFormat($total)
//
//            ];
//            return $return;
//        }
//
//        $model = $this->model;
//        $title = 'Fuel History ' . $this->title;
//
//        return view('admin.vehicle.inspection_history', compact('title', 'model', 'breadcum', 'row', 'permission'));
//    }
//
//    public function service_remainder(Request $request, $id)
//    {
//
//        $row = Vehicle::where('id', $id)->first();
//        $permission = $this->mypermissionsforAModule();
//        if ($request->ajax()) {
//            $sortable_columns = ['service_remainders.vehicle_id'];
//            $limit = $request->input('length');
//            $start = $request->input('start');
//            $search = $request['search']['value'];
//            $orderby = $request['order']['0']['column'];
//            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
//            $draw = $request['draw'];
//
//            $response = ServiceRemainder::getDataVehicles($limit, $start, $search, $sortable_columns[$orderby], $order, $request, $id);
//
//
//            $totalCms = $response['count'];
//            $data = $response['response']->toArray();
//            $total = 0;
//            foreach ($data as $key => $re) {
//                $data[$key]['last_completed'] = $re['updated_at'];
//                $data[$key]['links'] = '<div style="display:flex">';
//                if (isset($permission[$this->pmodule . '___view']) || $permission == 'superadmin') {
//                    $data[$key]['links'] .= '&nbsp; <a href="' . route($this->model . '.show', base64_encode($re['id'])) . '" class="btn btn-danger btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>';
//                }
//
//                if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
//                    $data[$key]['links'] .= '&nbsp; <a href="' . route($this->model . '.edit', base64_encode($re['id'])) . '" class="btn btn-danger btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>';
//                }
//                $data[$key]['links'] .= '&nbsp; <a href="' . route($this->model . '.destroy', base64_encode($re['id'])) . '"  class="btn btn-primary delete-confirm" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a>';
//
//                $data[$key]['links'] .= '</div>';
//            }
//            $response['response'] = $data;
//            $return = [
//                "draw" => intval($draw),
//                "recordsFiltered" => intval($totalCms),
//                "recordsTotal" => intval($totalCms),
//                "data" => $response['response'],
//                "total" => manageAmountFormat($total)
//
//            ];
//            return $return;
//        }
//
//        $model = $this->model;
//        $title = 'Service Remainder ' . $this->title;
//
//        return view('admin.vehicle.service_remainder', compact('title', 'model', 'breadcum', 'row', 'permission'));
//    }
//
//
//    public function meter_history(Request $request, $id)
//    {
//
//        $row = Vehicle::where('id', $id)->first();
//        $permission = $this->mypermissionsforAModule();
//        if ($request->ajax()) {
//            $sortable_columns = ['id', 'vehicle'];
//            $limit = $request->input('length');
//            $start = $request->input('start');
//            $search = $request['search']['value'];
//            $orderby = $request['order']['0']['column'];
//            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
//            $draw = $request['draw'];
//            $response = Meterhistory::getDataVehicles($limit, $start, $search, $sortable_columns[$orderby], $order, $request, $id);
//            $totalCms = $response['count'];
//            $data = $response['response']->toArray();
//            foreach ($data as $key => $re) {
//                $data[$key]['links'] = '<div style="display:flex">';
//                if (isset($permission['meterhistory___edit']) || $permission == 'superadmin') {
//                    $data[$key]['links'] .= '<a href="' . route('meterhistory.edit', $re['id']) . '" data-id="' . $re['id'] . '" onclick="openEditForm(this);return false;" class="btn btn-danger btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
//                }
//                if (isset($permission['meterhistory___delete']) || $permission == 'superadmin') {
//                    $data[$key]['links'] .= '<form action="' . route('meterhistory.destroy', $re['id']) . '" method="POST"  class="deleteMe"><button class="btn btn-sm btn-danger" style="margin-left:4px" type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
//                     <input type="hidden" value="DELETE" name="_method">
//                     ' . csrf_field() . '
//                     </form>';
//                }
//                $data[$key]['links'] .= '</div>';
//
//                $data[$key]['dated'] = getDateFormatted($re['created_at']);
//            }
//            $response['response'] = $data;
//            $return = [
//                "draw" => intval($draw),
//                "recordsFiltered" => intval($totalCms),
//                "recordsTotal" => intval($totalCms),
//                "data" => $response['response']
//            ];
//            return $return;
//        }
//
//        $model = $this->model;
//        $title = 'Service Remainder ' . $this->title;
//
//        return view('admin.vehicle.meter_history', compact('title', 'model', 'breadcum', 'row', 'permission'));
//    }
//
//
//    public function financial($id)
//    {
//        try {
//            $permission = $this->mypermissionsforAModule();
//            if (isset($permission[$this->pmodule . '___create']) || $permission == 'superadmin') {
//                $row = Vehicle::where('id', $id)->first();
//                if ($row) {
//                    $title = 'Financial ' . $this->title;
//                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
//                    $model = $this->model;
//                    $vehicle = VehicleType::all();
//                    $make = Make::all();
//                    $modal = Modal::all();
//                    $bodytype = Bodytype::all();
//
//
//                    return view('admin.vehicle.financial', compact('title', 'model', 'breadcum', 'row'));
//                } else {
//                    Session::flash('warning', 'Invalid Request');
//                    return redirect()->back();
//                }
//
//            } else {
//                Session::flash('warning', 'Invalid Request');
//                return redirect()->back();
//            }
//
//        } catch (\Exception $e) {
//
//            $msg = $e->getMessage();
//            Session::flash('warning', $msg);
//            return redirect()->back();
//        }
//    }
//
//    public function getAvailableVehicles(): JsonResponse
//    {
//        try {
//            $vehicles = NewVehicle::with('driver')->whereHas('driver')->get()->map(function (NewVehicle $vehicle) {
//                return [
//                    'id' => $vehicle->id,
//                    'name' => "$vehicle->name $vehicle->license_plate - {$vehicle->driver->name}",
//                ];
//            });
//            return $this->jsonify(['data' => $vehicles], 200);
//        } catch (Throwable $e) {
//            return $this->jsonify(['message' => $e->getMessage()], 500);
//        }
//    }
//}