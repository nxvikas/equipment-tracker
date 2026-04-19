<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
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
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500']
        ], [
            'name.required' => 'Название категории обязательно для заполнения',
            'name.unique' => 'Категория с таким названием уже существует',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator, 'categoryModal')
                ->with('open_category_modal', true)
                ->withInput();
        }

        $validated = $validator->validated();

        $category = Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Категория "' . $category->name . '" добавлена',
                'item' => $category
            ]);
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
