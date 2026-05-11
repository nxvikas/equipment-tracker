<?php

namespace App\Http\Controllers;

use App\Exports\employee\EmployeeEquipmentExport;
use App\Models\Category;
use App\Models\Location;
use App\Models\Equipment;
use App\Models\Equipment_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $totalMyEquipment = Equipment::where('current_user_id', $user->id)
            ->where('status', 'in_use')
            ->count();

        $totalRepairEquipment = Equipment::where('current_user_id', $user->id)
            ->where('status', 'repair')
            ->count();

        $recentAssigns = Equipment_history::where('to_user_id', $user->id)
            ->where('action_type', 'assigned')
            ->with(['equipment'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->translatedFormat('F');

            $assigned = Equipment_history::where('to_user_id', $user->id)
                ->where('action_type', 'assigned')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $returned = Equipment_history::where('from_user_id', $user->id)
                ->where('action_type', 'returned')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $monthlyStats[] = [
                'month' => $monthName,
                'assigned' => $assigned,
                'returned' => $returned,
            ];
        }

        $chartMonths = array_column($monthlyStats, 'month');
        $chartAssigned = array_column($monthlyStats, 'assigned');
        $chartReturned = array_column($monthlyStats, 'returned');


        return view('employee.dashboard', compact(
            'user',
            'totalMyEquipment',
            'totalRepairEquipment',
            'recentAssigns',
            'monthlyStats',
            'chartMonths',
            'chartAssigned',
            'chartReturned',

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


        $direction = $request->query('direction', 'desc');

        $equipments = $query->orderBy(function ($query) use ($user) {
            $query->select('created_at')
                ->from('equipment_histories')
                ->whereColumn('equipment_histories.equipment_id', 'equipment.id')
                ->where('equipment_histories.action_type', 'assigned')
                ->where('equipment_histories.to_user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(1);
        }, $direction)
            ->paginate(15)
            ->withQueryString();

        $categories = Category::whereHas('equipment', function ($q) use ($user) {
            $q->where('current_user_id', $user->id)
                ->whereIn('status', ['in_use', 'repair']);
        })->orderBy('name')->get();

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

    public function globalSearch(Request $request)
    {
        $query = $request->query('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $user = Auth::user();


        $equipment = Equipment::where('current_user_id', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('inventory_number', 'like', "%{$query}%")
                    ->orWhere('serial_number', 'like', "%{$query}%");
            })
            ->get(['id', 'name', 'inventory_number', 'serial_number'])
            ->map(function ($item) {
                $subtitle = $item->inventory_number;
                if ($item->serial_number) {
                    $subtitle .= ' | SN: ' . $item->serial_number;
                }
                return [
                    'id' => $item->id,
                    'type' => 'equipment',
                    'title' => $item->name,
                    'subtitle' => $subtitle,
                    'url' => route('public.equipment', ['id' => $item->id, 'from' => 'employee_equipment'])
                ];
            });

        return response()->json($equipment);
    }

    public function returnEquipment($id)
    {
        $user = Auth::user();

        $equipment = Equipment::where('id', $id)
            ->where('current_user_id', $user->id)
            ->where('status', 'in_use')
            ->firstOrFail();

        $oldUserId = $equipment->current_user_id;
        $oldLocationId = $equipment->location_id;

        $equipment->update([
            'status' => 'in_stock',
            'current_user_id' => null,
        ]);

        Equipment_history::create([
            'equipment_id' => $equipment->id,
            'action_type' => 'returned',
            'user_id' => auth()->id(),
            'from_user_id' => $oldUserId,
            'from_location_id' => $oldLocationId,
            'new_status' => 'in_stock',
            'comment' => 'Возвращено сотрудником на склад'
        ]);

        return redirect()->back()->with('success', 'Оборудование "' . $equipment->name . '" возвращено на склад');
    }
}
