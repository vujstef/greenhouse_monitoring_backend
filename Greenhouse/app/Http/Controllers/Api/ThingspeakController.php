<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigurationCommandRequest;
use App\Models\Configuration;
use App\Models\ConfigurationAccess;
use App\Models\Greenhouse;
use App\Models\Management;
use App\Models\Thingspeak;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ThingspeakController extends Controller
{
    public function readDataFromThingSpeakConfiguration(Request $request, $greenhouse_id)
    {
        $user = auth()->user();
        $greenhouse = Greenhouse::findOrFail($greenhouse_id);

        if (($greenhouse->created_by !== $user->id || $user->role !== 1) && $greenhouse->user_id !== $user->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        /*$greenhouse = Greenhouse::where('id', $greenhouse_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        */

        if (!$greenhouse) {
            abort(403, 'Access denied: You do not possess the selected greenhouse.');
        }

        if (!$greenhouse) {
            abort(404, 'The greenhouse could not be found.');
        }

        $thingspeak = ThingSpeak::where('greenhouse_id', $greenhouse_id)->firstOrFail();
        $channel_id = $thingspeak->channel_id;
        $read_key = $thingspeak->read_key;

        $client = new Client();
        $response = $client->request('GET', 'https://api.thingspeak.com/channels/' . $channel_id . '/feeds.json', [
            'query' => [
                'api_key' => $read_key,
                'results' => 1,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        return response()->json($data['feeds']);
    }

    /*
    public function writeDataToThingSpeakConfiguration(ConfigurationCommandRequest $request, $greenhouse_id)
    {
        $user = auth()->user();
        if ($user->role === 1) {
            return response()->json(['error' => 'Forbidden access'], 403);
        }

        $greenhouse = Greenhouse::where('id', $greenhouse_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (!$greenhouse) {
            abort(403, 'Access denied: You do not possess the selected greenhouse.');
        }

        $thingspeak = Thingspeak::where('greenhouse_id', $greenhouse_id)->firstOrFail();
        $write_key = $thingspeak->write_key;

        $config_params = [];

        $configurationAccesses = $greenhouse->configuration_access()->latest()->first();

        foreach ($request->all() as $key => $value) {
            if (isset($configurationAccesses->$key)) {
                if ($configurationAccesses->$key === 1) {
                    $config_params[$key] = $value;
                } else {
                    $config_params[$key] = null;
                }
            }
        }

        $write_client = new Client();
        $write_response = $write_client->request('POST', 'https://api.thingspeak.com/update', [
            'form_params' => [
                'api_key' => $write_key,
                'field1' => $config_params['min_air_temp'] ?? null,
                'field2' => $config_params['min_wind_speed'] ?? null,
                'field3' => $config_params['max_soil_temp'] ?? null,
                'field4' => $config_params['max_soil_humidity'] ?? null,
            ],
        ]);
        $write_data = (string)$write_response->getBody();

        $config = new Configuration;
        $config->greenhouse_id = $greenhouse_id;
        $config->min_air_temp = $config_params['min_air_temp'] ?? null;
        $config->min_wind_speed = $config_params['min_wind_speed'] ?? null;
        $config->max_soil_temp = $config_params['max_soil_temp'] ?? null;
        $config->max_soil_humidity = $config_params['max_soil_humidity'] ?? null;
        $config->save();

        return $write_data;
    }
     */
    public function updateDataToThingSpeakConfiguration(ConfigurationCommandRequest $request, $id)
    {
        $user = auth()->user();
        $greenhouse = Greenhouse::findOrFail($id);
        if (($greenhouse->created_by !== $user->id || $user->role !== 1) && $greenhouse->user_id !== $user->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $greenhouse = Greenhouse::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (!$greenhouse) {
            abort(403, 'Access denied: You do not possess the selected greenhouse.');
        }

        $thingspeak = Thingspeak::where('greenhouse_id', $id)->firstOrFail();
        $write_key = $thingspeak->write_key;

        $config_params = ['min_air_temp' => $request->min_air_temp,
            'min_wind_speed' => $request->min_wind_speed,
            'max_soil_temp' => $request->max_soil_temp,
            'max_soil_humidity' => $request->max_soil_humidity,];
        $configurationAccesses = $greenhouse->configuration_access()->latest()->first();
        $management_params = ['opening_command' => $request->opening_command,
            'closing_command' => $request->closing_command,
            'filling_command' => $request->filling_command,
            'emptying_command' => $request->emptying_command,
            'remote_mode' => $request->remote_mode,];
        $managementAccesses = $greenhouse->management_access()->latest()->first();

        foreach ($request->all() as $key => $value) {
            if (isset($configurationAccesses->$key)) {
                if ($configurationAccesses->$key === 1) {
                    $config_params[$key] = $value;
                } else {
                    $config_params[$key] = null;
                }
            }
        }

        foreach ($request->all() as $key => $value) {
            if (isset($managementAccesses->$key)) {
                if ($managementAccesses->$key === 1) {
                    $management_params[$key] = $value;
                } else {
                    $management_params[$key] = null;
                }
            }
        }

        $field6_data = '';
        if ($management_params['opening_command'] !== null) {
            $field6_data .= $management_params['opening_command'] . ', ';
        }
        if ($management_params['closing_command'] !== null) {
            $field6_data .= $management_params['closing_command'] . ', ';
        }
        if ($management_params['filling_command'] !== null) {
            $field6_data .= $management_params['filling_command'] . ', ';
        }
        if ($management_params['emptying_command'] !== null) {
            $field6_data .= $management_params['emptying_command'] . ', ';
        }
        $field6_data = rtrim($field6_data, ', ');

        $write_client = new Client();
        $write_response = $write_client->request('POST', 'https://api.thingspeak.com/update', [
            'form_params' => [
                'api_key' => $write_key,
                'field1' => $config_params['min_air_temp'] ?? null,
                'field2' => $config_params['min_wind_speed'] ?? null,
                'field3' => $config_params['max_soil_temp'] ?? null,
                'field4' => $config_params['max_soil_humidity'] ?? null,
                'field6' => $field6_data,
                'field8' => $management_params['remote_mode'] ?? null,
            ],
        ]);
        $write_data = $write_response->getBody();

        $config = Configuration::where('greenhouse_id', $id)->first();
        $management = Management::where('greenhouse_id', $id)->first();

        if (!$config || !$management) {
            $config = new Configuration;
            $management = new Management;
            $config->greenhouse_id = $id;
            $management->greenhouse_id = $id;
        }

        $config->min_air_temp = $config_params['min_air_temp'] ?? null;
        $config->min_wind_speed = $config_params['min_wind_speed'] ?? null;
        $config->max_soil_temp = $config_params['max_soil_temp'] ?? null;
        $config->max_soil_humidity = $config_params['max_soil_humidity'] ?? null;
        $config->save();

        $management = new Management;
        $management->greenhouse_id = $id;
        $management->opening_command = $management_params['opening_command'] ?? null;
        $management->closing_command = $management_params['closing_command'] ?? null;
        $management->filling_command = $management_params['filling_command'] ?? null;
        $management->emptying_command = $management_params['emptying_command'] ?? null;
        $management->remote_mode = $management_params['remote_mode'] ?? null;
        $management->save();

        return $write_data;
    }

    public function updateConfiguration(ConfigurationCommandRequest $request, $id)
    {
        $user = auth()->user();
        $greenhouse = Greenhouse::findOrFail($id);

        if (($greenhouse->created_by !== $user->id || $user->role !== 1) && $greenhouse->user_id !== $user->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        if (!$greenhouse) {
            abort(403, 'Access denied: You do not possess the selected greenhouse.');
        }

        $thingspeak = Thingspeak::where('greenhouse_id', $id)->firstOrFail();
        $write_key = $thingspeak->write_key;

        $config_params = [
            'min_air_temp' => $request->min_air_temp,
            'min_wind_speed' => $request->min_wind_speed,
            'max_soil_temp' => $request->max_soil_temp,
            'max_soil_humidity' => $request->max_soil_humidity,];
        $configurationAccesses = $greenhouse->configuration_access()->latest()->first();

        foreach ($request->all() as $key => $value) {
            if (isset($configurationAccesses->$key)) {
                if ($configurationAccesses->$key === 1) {
                    $config_params[$key] = $value;
                } else {
                    $config_params[$key] = null;
                }
            }
        }

        $write_client = new Client();
        $write_response = $write_client->request('POST', 'https://api.thingspeak.com/update', [
            'form_params' => [
                'api_key' => $write_key,
                'field1' => $config_params['min_air_temp'] ?? null,
                'field2' => $config_params['min_wind_speed'] ?? null,
                'field3' => $config_params['max_soil_temp'] ?? null,
                'field4' => $config_params['max_soil_humidity'] ?? null,
            ],
        ]);
        $write_data = $write_response->getBody();

        $config = Configuration::where('greenhouse_id', $id)->first();

        if (!$config) {
            $config = new Configuration;
            $config->greenhouse_id = $id;
        }

        $config->min_air_temp = $config_params['min_air_temp'] ?? null;
        $config->min_wind_speed = $config_params['min_wind_speed'] ?? null;
        $config->max_soil_temp = $config_params['max_soil_temp'] ?? null;
        $config->max_soil_humidity = $config_params['max_soil_humidity'] ?? null;
        $config->save();

        return $write_data;
    }

    public function updateManagement(ConfigurationCommandRequest $request, $id)
    {
        $user = auth()->user();
        $greenhouse = Greenhouse::findOrFail($id);

        if (($greenhouse->created_by !== $user->id || $user->role !== 1) && $greenhouse->user_id !== $user->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        if (!$greenhouse) {
            abort(403, 'Access denied: You do not possess the selected greenhouse.');
        }

        if (!$greenhouse) {
            abort(403, 'Access denied: You do not possess the selected greenhouse.');
        }

        $thingspeak = Thingspeak::where('greenhouse_id', $id)->firstOrFail();
        $write_key = $thingspeak->write_key;

        $management_params = [
            'opening_command' => $request->opening_command,
            'closing_command' => $request->closing_command,
            'filling_command' => $request->filling_command,
            'emptying_command' => $request->emptying_command,
            'remote_mode' => $request->remote_mode,
        ];
        $managementAccesses = $greenhouse->management_access()->latest()->first();

        foreach ($request->all() as $key => $value) {
            if (isset($managementAccesses->$key)) {
                if ($managementAccesses->$key === 1) {
                    $management_params[$key] = $value;
                } else {
                    $management_params[$key] = null;
                }
            }
        }

        $field6Data = [];
        $enteredData = [];

        foreach (['opening_command', 'closing_command', 'filling_command', 'emptying_command'] as $command) {
            if (isset($management_params[$command])) {
                $field6Data[] = $management_params[$command];
                $enteredData[$command] = $management_params[$command];
            }
        }

        $field6_data = implode(', ', $field6Data);

        $write_client = new Client();
        $write_response = $write_client->request('POST', 'https://api.thingspeak.com/update', [
            'form_params' => [
                'api_key' => $write_key,
                'field6' => $field6_data,
                'field8' => $management_params['remote_mode'] ?? null,
            ],
        ]);

        $write_data = $write_response->getBody();
        $management = Management::where('greenhouse_id', $id)->first();

        if (!$management) {
            $management = new Management;
            $management->greenhouse_id = $id;
        }

        $management->opening_command = $management_params['opening_command'] ?? null;
        $management->closing_command = $management_params['closing_command'] ?? null;
        $management->filling_command = $management_params['filling_command'] ?? null;
        $management->emptying_command = $management_params['emptying_command'] ?? null;
        $management->remote_mode = $management_params['remote_mode'] ?? null;
        $management->save();

        return $write_data;
    }

    public function updateRemoteMode(ConfigurationCommandRequest $request, $id)
    {
        $user = auth()->user();
        $greenhouse = Greenhouse::findOrFail($id);

        if (($greenhouse->created_by !== $user->id || $user->role !== 1) && $greenhouse->user_id !== $user->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        if (!$greenhouse) {
            abort(403, 'Access denied: You do not possess the selected greenhouse.');
        }

        if (!$greenhouse) {
            abort(403, 'Access denied: You do not possess the selected greenhouse.');
        }

        $write_client = new Client();
        $thingspeak = Thingspeak::where('greenhouse_id', $id)->firstOrFail();
        $write_key = $thingspeak->write_key;
        $remote_mode = $request->remote_mode;

        $write_response = $write_client->request('POST', 'https://api.thingspeak.com/update', [
            'form_params' => [
                'api_key' => $write_key,
                'field8' => $remote_mode,
            ],
        ]);

        $write_data = $write_response->getBody();

        return $write_data;
    }

    public function readLastDataConfiguration($id)
    {
        $user = auth()->user();
        $greenhouse = Greenhouse::findOrFail($id);

        if (($greenhouse->created_by !== $user->id || $user->role !== 1) && $greenhouse->user_id !== $user->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $lastStatus = $greenhouse->configuration()
            ->select()
            ->latest()
            ->first();

        $lastStatusData = $lastStatus ? [
            [
                'parameters' => $lastStatus->toArray()
            ]
        ] : [];

        return $lastStatusData;
    }

    /*
     * public function updateConfiguration(ConfigurationCommandRequest $request, $id)
{
    $user = auth()->user();
    $greenhouse = Greenhouse::findOrFail($id);

    // Check user access
    if (($greenhouse->created_by !== $user->id || $user->role !== 1) && $greenhouse->user_id !== $user->id) {
        return response()->json(['message' => 'Access denied'], 403);
    }

    $greenhouse = Greenhouse::where('id', $id)
        ->where('user_id', $request->user()->id)
        ->firstOrFail();

    // Check greenhouse ownership
    if (!$greenhouse) {
        abort(403, 'Access denied: You do not possess the selected greenhouse.');
    }

    // Update configuration parameters
    $config_params = [
        'min_air_temp' => $request->min_air_temp,
        'min_wind_speed' => $request->min_wind_speed,
        'max_soil_temp' => $request->max_soil_temp,
        'max_soil_humidity' => $request->max_soil_humidity,
    ];

    // Update configuration model
    $config = Configuration::updateOrCreate(['greenhouse_id' => $id], $config_params);

    return response()->json($config, 200);
}

public function updateManagement(ManagementCommandRequest $request, $id)
{
    $user = auth()->user();
    $greenhouse = Greenhouse::findOrFail($id);

    // Check user access
    if (($greenhouse->created_by !== $user->id || $user->role !== 1) && $greenhouse->user_id !== $user->id) {
        return response()->json(['message' => 'Access denied'], 403);
    }

    $greenhouse = Greenhouse::where('id', $id)
        ->where('user_id', $request->user()->id)
        ->firstOrFail();

    // Check greenhouse ownership
    if (!$greenhouse) {
        abort(403, 'Access denied: You do not possess the selected greenhouse.');
    }

    // Update management parameters
    $management_params = [
        'opening_command' => $request->opening_command,
        'closing_command' => $request->closing_command,
        'filling_command' => $request->filling_command,
        'emptying_command' => $request->emptying_command,
        'remote_mode' => $request->remote_mode,
    ];

    // Update management model
    $management = Management::updateOrCreate(['greenhouse_id' => $id], $management_params);

    return response()->json($management, 200);
}

     */


}
