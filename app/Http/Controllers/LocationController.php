<?php

namespace App\Http\Controllers;


use App\Models\Equipment;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Location::withCount('equipment');


        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }


        $direction = $request->get('direction', 'desc');
        $query->orderBy('equipment_count', $direction);
        $query->orderBy('name', 'asc');

        $locations = $query->paginate(15)->withQueryString();

        return view('admin.locations.index', compact('locations'));
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $location = Location::create($validator->validated());

        session()->flash('success', 'Локация "' . $location->name . '" добавлена');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Локация "' . $location->name . '" добавлена',
                'item' => [
                    'id' => $location->id,
                    'name' => $location->name,
                    'type' => $location->type
                ]
            ]);
        }

        return redirect()->route('admin.locations.index')->with('success', 'Локация добавлена');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:' . implode(',', \App\Http\Enums\TypeLocation::values())],
            'address' => ['nullable', 'string', 'max:500']
        ], [
            'name.required' => 'Название локации обязательно',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $location->update($validator->validated());

        session()->flash('success', 'Локация обновлена');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Локация обновлена',
                'item' => $location
            ]);
        }

        return redirect()->route('admin.locations.index')->with('success', 'Локация обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Location $location)
    {
        if ($location->equipment()->exists()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя удалить локацию, в которой есть оборудование'
                ], 400);
            }
            return redirect()->back()->with('error', 'Нельзя удалить локацию, в которой есть оборудование');
        }

        $location->delete();

        session()->flash('success', 'Локация удалена');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Локация удалена'
            ]);
        }

        return redirect()->route('admin.locations.index')->with('success', 'Локация удалена');
    }
    public function show(Request $request, Location $location)
    {
        $location->loadCount('equipment');

        $equipments = Equipment::where('location_id', $location->id)
            ->with(['category', 'currentUser'])
            ->get();


        $availableEquipments = Equipment::where('location_id', '!=', $location->id)
            ->with(['category', 'location'])
            ->get();

        return view('admin.locations.show', compact('location', 'equipments', 'availableEquipments'));
    }
}
