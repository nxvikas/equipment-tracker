<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('equipment');

        $direction = $request->query('direction', 'desc');
        $query->orderBy('equipment_count', $direction);

        $categories = $query->paginate(15)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }
    public function show(Category $category)
    {
        $category->loadCount('equipment');

        $equipments = Equipment::where('category_id', $category->id)
            ->with(['location', 'currentUser'])
            ->get();

        return view('admin.categories.show', compact('category', 'equipments'));
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
                ]);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $category = Category::create($validator->validated());


        session()->flash('success', 'Категория "' . $category->name . '" добавлена');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Категория "' . $category->name . '" добавлена',
                'item' => $category
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Категория добавлена');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:500']
        ], [
            'name.required' => 'Название категории обязательно',
            'name.unique' => 'Категория с таким названием уже существует',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->toArray()
                ]);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $category->update($validator->validated());


        session()->flash('success', 'Категория обновлена');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Категория обновлена',
                'item' => $category
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Категория обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Category $category)
    {
        if ($category->equipment()->exists()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя удалить категорию, в которой есть оборудование'
                ], 400);
            }
            return redirect()->back()->with('error', 'Нельзя удалить категорию, в которой есть оборудование');
        }

        $category->delete();


        session()->flash('success', 'Категория удалена');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Категория удалена'
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Категория удалена');
    }
}
