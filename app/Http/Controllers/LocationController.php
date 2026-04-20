<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:' . implode(',', \App\Http\Enums\TypeLocation::values())],
            'address' => ['nullable', 'string', 'max:500']
        ], [
            'name.required' => 'Название локации обязательно для заполнения',
            'type.required' => 'Тип локации обязателен для выбора',
            'type.in' => 'Выбран недопустимый тип локации',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator, 'locationModal')
                ->with('open_location_modal', true)
                ->withInput();
        }

        $validated = $validator->validated();

        $location = Location::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'address' => $validated['address'] ?? null
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Локация "' . $location->name . '" добавлена',
                'item' => $location
            ]);
        }

        return redirect()->back()->with('success', 'Локация добавлена');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        //
    }
}
