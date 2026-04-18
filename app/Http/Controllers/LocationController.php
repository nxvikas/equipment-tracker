<?php

namespace App\Http\Controllers;

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
            'name' => 'required|string|max:255',
            'type' => 'required|in:office,warehouse,service'
        ]);

        if ($validator->fails()) {
            if ($request->input('return_to') === 'equipment') {
                return redirect()->back()
                    ->withErrors($validator, 'locationModal')
                    ->with('open_location_modal', true)
                    ->withInput();
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $location = Location::create([
            'name' => $request->name,
            'type' => $request->type
        ]);

        if ($request->input('return_to') === 'equipment') {
            return redirect()->route('admin.equipment')
                ->with('success', 'Локация "' . $location->name . '" успешно добавлена')
                ->with('reopen_equipment_modal', true)
                ->with('new_location_id', $location->id);
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
