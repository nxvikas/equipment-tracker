<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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
            'name' => 'required|string|max:255|unique:categories,name'
        ]);


        if ($validator->fails()) {

            if ($request->input('return_to') === 'equipment') {
                return redirect()->back()
                    ->withErrors($validator, 'categoryModal')
                    ->with('open_category_modal', true)
                    ->withInput();
            }


            return redirect()->back()->withErrors($validator)->withInput();
        }


        $category = Category::create([
            'name' => $request->name
        ]);


        if ($request->input('return_to') === 'equipment') {
            return redirect()->route('admin.equipment')
                ->with('success', 'Категория "' . $category->name . '" успешно добавлена')
                ->with('reopen_equipment_modal', true)
                ->with('new_category_id', $category->id);
        }

        return redirect()->back()->with('success', 'Категория добавлена');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
