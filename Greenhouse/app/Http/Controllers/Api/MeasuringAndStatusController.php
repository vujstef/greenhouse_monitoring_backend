<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Greenhouse;
use App\Models\measuring_status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeasuringAndStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
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

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(MeasuringAndStatusController $measuringAndStatus)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function showLastData($greenhouse_id)
    {
        $user_id = Auth::id();
        $greenhouse = Greenhouse::where('id', $greenhouse_id)
            ->where('user_id', $user_id)
            ->first();

        if (!$greenhouse) {
            return response()->json([
                'message' => 'You do not have access to this greenhouse'
            ], 403);
        }

        $measuring_status = $greenhouse->status()
            ->orderBy('time', 'desc')
            ->first();

        return response()->json([
            'greenhouse_name' => $greenhouse->name,
            'measuring_status' => $measuring_status,
        ]);
    }

    public function getDisplayedMeasurementStatusesByTime(Request $request, $greenhouse_id)
    {
        $user = auth()->user();
        $greenhouse = $user->greenhouse()->find($greenhouse_id);

        if (!$greenhouse) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $displayedMeasurementStatuses = [];
        $measuringStatuses = $greenhouse->status()->wherePivot('id', true)->get();

        foreach ($measuringStatuses as $measuringStatus) {
            if (!empty($measuringStatus->name)) {
                $displayedMeasurementStatuses[] = $measuringStatus->name;
            }
        }

        $greenhouseAccesses = $greenhouse->greenhouse_accesses()->latest()->first();

        foreach ($greenhouseAccesses->toArray() as $key => $value) {
            if ($value === 1) {
                $displayedMeasurementStatuses[] = $key;
            }
        }

        $query = $greenhouse->status()->select($displayedMeasurementStatuses)->orderBy('time');

        if ($request->has('last_day')) {
            $query->where('time', '>=', Carbon::now()->subDay());
        }

        if ($request->has('last_week')) {
            $query->where('time', '>=', Carbon::now()->subWeek());
        }

        if ($request->has('last_month')) {
            $query->where('time', '>=', Carbon::now()->subMonth());
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('time', [
                Carbon::parse($request->input('start_date')),
                Carbon::parse($request->input('end_date'))->endOfDay()
            ]);
        }

        $results = $query->get();

        return response()->json(['data' => $results]);
    }

}
