<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigurationCommandAccessRequest;
use App\Http\Requests\ConfigurationCommandRequest;
use App\Http\Requests\ManagementCommandAccessRequest;
use App\Http\Requests\ThingSpeakRequest;
use App\Models\Greenhouse;
use App\Models\GreenhouseAccess;
use App\Models\Thingspeak;
use Illuminate\Http\Request;
use App\Http\Requests\GreenhouseRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\GreenhouseAccessRequest;
use Illuminate\Support\Facades\DB;

class GreenhouseController extends Controller
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    public function createGreenhouse(GreenhouseRequest $request, $id)
    {
        if (auth()->user()->role !== 1) {
            return response()->json(['error' => 'Forbidden access'], 403);
        }

        if ($id && User::find($id)->role === 1) {
            return response()->json(['error' => 'Admin users cannot own greenhouses'], 403);
        }

        $greenhouse = new Greenhouse([
            'name' => $request->input('name'),
            'created_by' => auth()->user()->id,
            'description' => $request->input('description')
        ]);

        if ($id) {
            $greenhouse->user_id = $id;
        }

        $greenhouse->save();

        return response()->json(['message' => 'Greenhouse created successfully']);
    }

    public function getUserGreenhouses()
    {
        $user = auth()->user();
        if ($user->role === 1) {
            return response()->json(['error' => 'Forbidden access'], 403);
        }
        $greenhouses = $user->greenhouse;
        foreach ($greenhouses as $greenhouse) {
            $greenhouse->makeHidden('user_id');
        }
        return response()->json(['greenhouses' => $greenhouses]);
    }

    public function greenhouseCreatedByAdmin()
    {
        $admin = auth()->user();

        if ($admin->role !== 1) {
            return response()->json(['error' => 'Forbidden access'], 403);
        }

        $greenhouses = Greenhouse::where('created_by', $admin->id)->get();

        return response()->json(['greenhouses' => $greenhouses]);
    }

    public function deleteGreenhouse($id)
    {
        $user = auth()->user();
        $greenhouse = Greenhouse::findOrFail($id);

        if ($user->role === 1 && $greenhouse->created_by !== $user->id) {
            return response()->json(['error' => 'Forbidden access'], 403);
        }

        $greenhouse->delete();
        return response()->json(['message' => 'Greenhouse deleted successfully']);
    }


    public function updateGreenhouse(GreenhouseRequest $request, $id)
    {
        $greenhouse = Greenhouse::find($id);
        if (!$greenhouse) {
            return response()->json(['error' => 'Greenhouse not found'], 404);
        }

        if (auth()->user()->role !== 1 && $greenhouse->created_by !== auth()->user()->id) {
            return response()->json(['error' => 'Forbidden access'], 403);
        }

        if ($request->user_id && User::find($request->user_id)->role === 1) {
            return response()->json(['error' => 'Admin users cannot own greenhouses'], 403);
        }

        if (auth()->user()->role === 1 && $greenhouse->created_by !== auth()->user()->id) {
            return response()->json(['error' => 'Admin users can only update greenhouses they created'], 403);
        }

        $greenhouse->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Greenhouse updated successfully']);
    }

    public function showAllGreenhouseAndMeasuringAndStatus()
    {
        $user_id = Auth::id();
        $greenhouses = Greenhouse::where('created_by', $user_id)->get();

        $result = [];

        foreach ($greenhouses as $greenhouse) {
            $greenhouse_data = [
                'id' => $greenhouse->id,
                'name' => $greenhouse->name,
                'status_data' => []
            ];

            $measuring_status = $greenhouse->status()
                ->orderBy('time', 'desc')
                ->get();

            foreach ($measuring_status as $status) {
                $greenhouse_data['status_data'][] = $status->toArray();
            }

            $result[] = $greenhouse_data;
        }

        return response()->json([
            'greenhouses' => $result
        ]);
    }

    public function assignParameters(GreenhouseAccessRequest $request, $id)
    {
        $admin_id = auth()->user()->id;

        $greenhouse = Greenhouse::find($id);

        if (!$greenhouse) {
            return response()->json(['message' => 'Greenhouse not found'], 404);
        }

        if ($greenhouse->created_by !== $admin_id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $request->validated($request->all());

        $greenhouse_access = $greenhouse->greenhouse_accesses()->create([
            'admin_id' => $admin_id,
            'air_temperature' => $request->air_temperature,
            'relative_air_humidity' => $request->relative_air_humidity,
            'soil_temperature' => $request->soil_temperature,
            'relative_humidity_of_the_soil' => $request->relative_humidity_of_the_soil,
            'lighting_intensity' => $request->lighting_intensity,
            'outside_air_temperature' => $request->outside_air_temperature,
            'wind_speed' => $request->wind_speed,
            'water_level' => $request->water_level,
            'opening' => $request->opening,
            'closing' => $request->closing,
            'opened' => $request->opened,
            'closed' => $request->closed,
            'filling' => $request->filling,
            'emptying' => $request->emptying,
            'full' => $request->full,
            'empty' => $request->empty,
            'remote_mode' => $request->remote_mode,
        ]);

        return response()->json($greenhouse_access);
    }

    public function getDisplayedMeasurementStatuses($id)
    {
        $user = auth()->user();
        $greenhouse = Greenhouse::findOrFail($id);

        if (($greenhouse->created_by !== $user->id || $user->role !== 1) && $greenhouse->user_id !== $user->id) {
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

        if ($greenhouseAccesses) {
            foreach ($greenhouseAccesses->toArray() as $key => $value) {
                if ($value === 1) {
                    $displayedMeasurementStatuses[] = $key;
                }
            }
        }

        $lastStatus = $greenhouse->status()
            ->select($displayedMeasurementStatuses)
            ->withPivot('created_at')
            ->withTimestamps()
            ->latest('greenhouse_measuring_status.created_at')
            ->first($displayedMeasurementStatuses);

        $lastStatusData = $lastStatus ? [
            [
                'time' => $lastStatus->pivot->created_at->format('Y-m-d H:i:s'),
                'parameters' => $lastStatus->toArray()
            ]
        ] : [];

        return $lastStatusData;
    }

    public function assignConfigurationCommand(ConfigurationCommandAccessRequest $request, $id)
    {
        $admin_id = auth()->user()->id;

        $greenhouse = Greenhouse::find($id);

        if (!$greenhouse) {
            return response()->json(['message' => 'Greenhouse not found'], 404);
        }

        if ($greenhouse->created_by !== $admin_id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $request->validated($request->all());

        $configuration_command_access = $greenhouse->configuration_access()->create([
            'min_air_temp' => $request->min_air_temp,
            'min_wind_speed' => $request->min_wind_speed,
            'max_soil_temp' => $request->max_soil_temp,
            'max_soil_humidity' => $request->max_soil_humidity,
            'admin_id' => $admin_id,
        ]);

        return response()->json($configuration_command_access);
    }

    public function getConfigurationCommandAccess($id)
    {
        $user = auth()->user();
        $greenhouse = Greenhouse::find($id);

        if (!$greenhouse) {
            return response()->json(['message' => 'Greenhouse not found'], 404);
        }

        if (($greenhouse->created_by !== $user->id || $user->role !== 1) && $greenhouse->user_id !== $user->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $configuration_command_access = $greenhouse->configuration_access()
            ->where('admin_id', $user->id)
            ->latest()
            ->first();

        return response()->json($configuration_command_access);
    }

    public function assignManagementCommand(ManagementCommandAccessRequest $request, $id)
    {
        $admin_id = auth()->user()->id;

        $greenhouse = Greenhouse::find($id);

        if (!$greenhouse) {
            return response()->json(['message' => 'Greenhouse not found'], 404);
        }

        if ($greenhouse->created_by !== $admin_id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $request->validated($request->all());

        $configuration_command_access = $greenhouse->management_access()->create([
            'admin_id' => $admin_id,
            'opening_command' => $request->opening_command,
            'closing_command' => $request->closing_command,
            'filling_command' => $request->filling_command,
            'emptying_command' => $request->emptying_command,
            'remote_mode' => $request->remote_mode
        ]);

        return response()->json($configuration_command_access);
    }

    public function assignThingspeak(ThingSpeakRequest $request, $id)
    {
        $user = auth()->user();

        if ($user->role !== 1) {
            return response()->json(['error' => 'Forbidden access'], 403);
        }

        $greenhouse = Greenhouse::where('id', $id)
            ->where('created_by', $user->id)
            ->first();

        if (!$greenhouse) {
            return response()->json(['error' => 'Access denied'], 404);
        }

        $thingspeak = new Thingspeak([
            'channel_id' => $request->channel_id,
            'read_key' => $request->read_key,
            'write_key' => $request->write_key,
            'greenhouse_id' => $greenhouse->id
        ]);

        $thingspeak->save();

        return response()->json(['message' => 'ThingSpeak data created successfully']);
    }

    public function updateThingspeak(ThingSpeakRequest $request, $greenhouse_id)
    {
        if (auth()->user()->role !== 1) {
            return response()->json(['error' => 'Forbidden access'], 403);
        }

        $thingspeak = Thingspeak::where('greenhouse_id', $greenhouse_id)->first();

        if (!$thingspeak) {
            return response()->json(['error' => 'Thingspeak data not found'], 404);
        }

        $thingspeak->channel_id = $request->channel_id;
        $thingspeak->read_key = $request->read_key;
        $thingspeak->write_key = $request->write_key;
        $thingspeak->save();

        return response()->json(['message' => 'Thingspeak data updated successfully']);
    }

    public function deleteThingspeak($id)
    {
        $thingspeak = Thingspeak::find($id);
        if (!$thingspeak) {
            return response()->json(['error' => 'Thingspeak data not found'], 404);
        }

        if (auth()->user()->role !== 1 && $thingspeak->greenhouse->created_by !== auth()->user()->id) {
            return response()->json(['error' => 'Forbidden access'], 403);
        }

        $thingspeak->delete();

        return response()->json(['message' => 'Thingspeak data deleted successfully']);
    }

    public function readThingspeak($greenhouseId)
    {
        $user = auth()->user();

        if ($user->role !== 1) {
            return response()->json(['error' => 'Forbidden access'], 403);
        }

        $greenhouse = Greenhouse::where('id', $greenhouseId)
            ->where('created_by', $user->id)
            ->first();

        if (!$greenhouse) {
            return response()->json(['error' => 'Access denied'], 404);
        }

        $thingspeak = Thingspeak::where('greenhouse_id', $greenhouseId)->first();

        if (!$thingspeak) {
            return response()->json(['data' => []]); // return empty array
        }

        $results = $request->results ?? 10;

        $data = DB::table('thingspeaks')
            ->where('greenhouse_id', $greenhouseId)
            ->orderBy('created_at', 'desc')
            ->limit($results)
            ->get();

        return response()->json(['data' => $data]);
    }
}
