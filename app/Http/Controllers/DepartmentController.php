<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
        ], [
            'name.required' => 'Название отдела обязательно',
            'name.unique' => 'Отдел с таким названием уже существует',
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

        $department = Department::create($validator->validated());

        session()->flash('success', 'Отдел "' . $department->name . '" добавлен');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Отдел "' . $department->name . '" добавлен',
                'item' => $department
            ]);
        }

        return redirect()->route('admin.structure.index')->with('success', 'Отдел добавлен');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,' . $department->id],
        ], [
            'name.required' => 'Название отдела обязательно',
            'name.unique' => 'Отдел с таким названием уже существует',
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

        $department->update($validator->validated());

        session()->flash('success', 'Отдел обновлён');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Отдел обновлён',
                'item' => $department
            ]);
        }

        return redirect()->route('admin.structure.index')->with('success', 'Отдел обновлён');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Department $department)
    {
        if ($department->users()->exists()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя удалить отдел, в котором есть сотрудники'
                ], 400);
            }
            return redirect()->back()->with('error', 'Нельзя удалить отдел, в котором есть сотрудники');
        }

        $department->delete();

        session()->flash('success', 'Отдел удалён');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Отдел удалён'
            ]);
        }

        return redirect()->route('admin.structure.index')->with('success', 'Отдел удалён');
    }
}
