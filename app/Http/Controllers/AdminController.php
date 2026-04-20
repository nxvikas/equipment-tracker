<?php

namespace App\Http\Controllers;

use App\Http\Enums\TypeEquipmentHistory;
use App\Models\Category;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\Equipment_history;
use App\Models\Location;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {

        $totalEquipments = Equipment::count();
        $inUseEquipments = Equipment::where('status', 'in_use')->count();
        $inStockEquipments = Equipment::where('status', 'in_stock')->count();
        $inRepairEquipments = Equipment::where('status', 'repair')->count();
        $writtenEquipments = Equipment::where('status', 'written')->count();


        $categories = Category::withCount('equipment')->orderBy('equipment_count', 'desc')->take(6)->get();


        $recentOperations = Equipment_history::with(['equipment', 'user', 'toUser'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();


        $statusData = [
            'in_stock' => $inStockEquipments,
            'in_use' => $inUseEquipments,
            'repair' => $inRepairEquipments,
        ];


        $monthlyAssigns = Equipment_history::where('action_type', 'assigned')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'totalEquipments',
            'inUseEquipments',
            'inStockEquipments',
            'inRepairEquipments',
            'writtenEquipments',
            'categories',
            'recentOperations',
            'statusData',
            'monthlyAssigns'
        ));
    }

    public function equipment(Request $request)
    {
        $query = Equipment::query()->with(['category', 'currentUser']);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $equipments = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $categories = Category::all();
        $locations = Location::all();
        $users = User::where('status', 'active')->orderBy('name')->get();

        return view('admin.equipment', compact('equipments', 'categories', 'locations', 'users'));
    }
    public function history(Request $request)
    {
        $query = Equipment_history::with(['equipment', 'user', 'toUser', 'fromUser', 'toLocation', 'fromLocation'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        $operations = $query->paginate(20)->withQueryString();

        $actionTypes = TypeEquipmentHistory::ruValues();

        return view('admin.history', compact('operations', 'actionTypes'));
    }
    public function structure(Request $request)
    {
        $activeTab = $request->get('tab', 'departments');

        // Отделы
        $departmentsQuery = Department::withCount('users');
        $direction = $request->get('direction', 'desc');
        $departmentsQuery->orderBy('users_count', $direction)->orderBy('name', 'asc');
        $departments = $departmentsQuery->paginate(15, ['*'], 'departments_page')->withQueryString();

        // Должности
        $positionsQuery = Position::withCount('users');
        $posDirection = $request->get('pos_direction', 'desc');
        $positionsQuery->orderBy('users_count', $posDirection)->orderBy('name', 'asc');
        $positions = $positionsQuery->paginate(15, ['*'], 'positions_page')->withQueryString();

        return view('admin.structure.index', compact('departments', 'positions', 'activeTab'));
    }
}
