<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\Location;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function equipment()
    {
        $equipments = Equipment::with(['category', 'currentUser'])->paginate(15);
        $categories = Category::all();
        $locations = Location::all();

        return view('admin.equipment', compact('equipments', 'categories', 'locations'));
    }
}
