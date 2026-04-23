<?php

namespace App\Http\Controllers;

use App\Exports\employee\EmployeeEquipmentExport;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Equipment_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();


        $myEquipment = Equipment::where('current_user_id', $user->id)
            ->where('status', 'in_use')
            ->with('category')
            ->get();


        $totalMyEquipment = $myEquipment->count();


        $repairEquipment = Equipment::where('current_user_id', $user->id)
            ->where('status', 'repair')
            ->with('category')
            ->get();

        $totalRepairEquipment = $repairEquipment->count();


        $recentHistory = Equipment_history::where(function ($query) use ($user) {
            $query->where('to_user_id', $user->id)
                ->orWhere('from_user_id', $user->id);
        })
            ->with(['equipment', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('employee.dashboard', compact(
            'user',
            'myEquipment',
            'totalMyEquipment',
            'repairEquipment',
            'totalRepairEquipment',
            'recentHistory'
        ));
    }
    public function myEquipment(Request $request)
    {
        $user = Auth::user();

        $query = Equipment::where('current_user_id', $user->id)
            ->with(['category', 'location'])
            ->whereIn('status', ['in_use', 'repair']);


        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }


        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }


        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('inventory_number', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $equipments = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $categories = Category::orderBy('name')->get();
        $statuses = [
            'in_use' => 'В использовании',
            'repair' => 'В ремонте',
        ];

        return view('employee.equipment', compact('equipments', 'categories', 'statuses', 'user'));
    }

    public function exportMyEquipment()
    {
        $user = Auth::user();
        $equipments = Equipment::where('current_user_id', $user->id)
            ->whereIn('status', ['in_use', 'repair'])
            ->with('category', 'location')
            ->get();

        $fileName = 'report_' . $user->surname . '_' . $user->name . '_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new EmployeeEquipmentExport($equipments, $user), $fileName);
    }
}
