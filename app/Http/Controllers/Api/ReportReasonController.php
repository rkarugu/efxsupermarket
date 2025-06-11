<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\ReportReason;
use App\SalesmanReportingReason;
use Illuminate\Http\Request;

class ReportReasonController extends Controller
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

    public function apiGetReasons()
    {
        $reasons = SalesmanReportingReason::with(['salesReportingReasons' => function ($query) {
            return $query->select('reporting_reason_id', 'reason_option', 'data_type', 'reason_option_key_name');
        }])->select('id', 'name')->get();

        $transformedReasons = $reasons->map(function ($reason) {
            $fields = $reason->salesReportingReasons->map(function ($field) {
                return [
                    'field_label' => $field->reason_option,
                    'field_type' => $field->data_type,
                    'field_key_name' => $field->reason_option_key_name,
                ];
            });

            return [
                'id' => $reason->id,
                'name' => $reason->name,
                'fields' => $fields,
            ];
        });

        return response()->json(['status' => true, 'data' => $transformedReasons]);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ReportReason $reportReason
     * @return \Illuminate\Http\Response
     */
    public function show(ReportReason $reportReason)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ReportReason $reportReason
     * @return \Illuminate\Http\Response
     */
    public function edit(ReportReason $reportReason)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\ReportReason $reportReason
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReportReason $reportReason)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ReportReason $reportReason
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReportReason $reportReason)
    {
        //
    }
}
