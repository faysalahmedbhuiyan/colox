<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PoliceStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PoliceStationController extends Controller
{
    public function index()
    {
        return response()->json(['stations' => PoliceStation::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string'],
            'district' => ['required', 'string'],
            'area' => ['nullable', 'string'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $station = PoliceStation::create($request->only(['name', 'phone', 'district', 'area', 'lat', 'lng']));

        return response()->json(['message' => 'Police station added.', 'station' => $station], 201);
    }

    public function update(Request $request, PoliceStation $policeStation)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string'],
            'district' => ['sometimes', 'string'],
            'area' => ['nullable', 'string'],
            'lat' => ['sometimes', 'numeric', 'between:-90,90'],
            'lng' => ['sometimes', 'numeric', 'between:-180,180'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $policeStation->update($request->only(['name', 'phone', 'district', 'area', 'lat', 'lng', 'is_active']));

        return response()->json(['message' => 'Police station updated.', 'station' => $policeStation->fresh()]);
    }

    public function destroy(PoliceStation $policeStation)
    {
        $policeStation->delete();

        return response()->json(['message' => 'Police station removed.']);
    }
}