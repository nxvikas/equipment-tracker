<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
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
            'name' => ['required', 'string', 'max:255', 'unique:positions,name'],
        ], [
            'name.required' => 'Название должности обязательно',
            'name.unique' => 'Должность с таким названием уже существует',
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

        $position = Position::create($validator->validated());

        session()->flash('success', 'Должность "' . $position->name . '" добавлена');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Должность "' . $position->name . '" добавлена',
                'item' => $position
            ]);
        }

        return redirect()->route('admin.structure.index')->with('success', 'Должность добавлена');
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Position $position)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'unique:positions,name,' . $position->id],
        ], [
            'name.required' => 'Название должности обязательно',
            'name.unique' => 'Должность с таким названием уже существует',
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

        $position->update($validator->validated());

        session()->flash('success', 'Должность обновлена');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Должность обновлена',
                'item' => $position
            ]);
        }

        return redirect()->route('admin.structure.index')->with('success', 'Должность обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Position $position)
    {
        if ($position->users()->exists()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя удалить должность, которую занимают сотрудники'
                ], 400);
            }
            return redirect()->back()->with('error', 'Нельзя удалить должность, которую занимают сотрудники');
        }

        $position->delete();

        session()->flash('success', 'Должность удалена');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Должность удалена'
            ]);
        }

        return redirect()->route('admin.structure.index')->with('success', 'Должность удалена');
    }
}
