<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\ReportShop;
use App\Model\RouteReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RouteReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function report(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'reason_id' => 'required',
            'comments' => 'required',
            'shop_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error]);
        }



        $updatedfileName = '';
        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $filename = time() . '_' . $uploadedFile->getClientOriginalName();
            $path = $uploadedFile->storeAs('uploads', $filename); // Store the file in the 'storage/app/uploads' directory

            $updatedfileName = $filename;

            // You can save the file path in the database or perform other actions as needed

        }


        ReportShop::create([
            'wa_route_customer_id' => $request->route_id,
            'report_reason_id' => $request->reason_id,
            'comments' => $request->comments,
            'image' => $updatedfileName
        ]);

        return response()->json(['status' => true, 'message' => 'Report sent successfully']);
    }


    public function reportRoute(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'reason_id' => 'required',
            'comments' => 'required',
            'route_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages());
            return response()->json(['status' => false, 'message' => $error]);
        }



        $updatedfileName = '';
        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $filename = time() . '_' . $uploadedFile->getClientOriginalName();
            $path = $uploadedFile->storeAs('uploads', $filename); // Store the file in the 'storage/app/uploads' directory

            $updatedfileName = $filename;

            // You can save the file path in the database or perform other actions as needed

        }


        RouteReport::create([
            'route_id' => $request->route_id,
            'report_reason_id' => $request->reason_id,
            'comments' => $request->comments,
            'image' => $updatedfileName
        ]);

        return response()->json(['status' => true, 'message' => 'Report sent successfully']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RouteReport  $routeReport
     * @return \Illuminate\Http\Response
     */
    // public function show(RouteReport $routeReport)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RouteReport  $routeReport
     * @return \Illuminate\Http\Response
     */
    // public function edit(RouteReport $routeReport)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RouteReport  $routeReport
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, RouteReport $routeReport)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RouteReport  $routeReport
     * @return \Illuminate\Http\Response
     */
    // public function destroy(RouteReport $routeReport)
    // {
    //     //
    // }
}
