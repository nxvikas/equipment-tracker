<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Equipment_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
