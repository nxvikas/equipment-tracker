<?php

namespace App\Http\Controllers;

use App\Exports\admin\CategoryExport;
use App\Exports\admin\DashboardExport;
use App\Exports\admin\EquipmentExport;
use App\Exports\admin\HistoryExport;
use App\Exports\admin\LocationExport;
use App\Exports\admin\StructureExport;
use App\Exports\admin\UserExport;
use App\Http\Enums\StatusEquipment;
use App\Http\Enums\TypeEquipmentHistory;
use App\Models\Category;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\Equipment_history;
use App\Models\Location;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function exportDashboard()
    {
        return Excel::download(new DashboardExport(), 'dashboard-report-' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    public function exportEquipment()
    {
        return Excel::download(new EquipmentExport(), 'equipment-' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportCategories()
    {
        return Excel::download(new CategoryExport(), 'categories-' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportLocations()
    {
        return Excel::download(new LocationExport(), 'locations-' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportUsers()
    {
        return Excel::download(new UserExport(), 'users-' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportStructure()
    {
        return Excel::download(new StructureExport(), 'structure-' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportHistory()
    {
        return Excel::download(new HistoryExport(), 'history-' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    public function dashboard()
    {

        $totalEquipments = Equipment::count();
        $inUseEquipments = Equipment::where('status', 'in_use')->count();
        $inStockEquipments = Equipment::where('status', 'in_stock')->count();
        $inRepairEquipments = Equipment::where('status', 'repair')->count();
        $writtenEquipments = Equipment::where('status', 'written')->count();


        $categories = Category::withCount('equipment')
            ->orderBy('equipment_count', 'desc')
            ->take(6)
            ->get();


        $recentOperations = Equipment_history::with(['equipment', 'user', 'toUser'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();


        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[$date->format('Y-m')] = 0;
        }
        $rawAssigns = Equipment::where('status', 'in_use')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();
        $monthlyAssigns = [];
        $cumulative = Equipment::where('status', 'in_use')
            ->where('created_at', '<', now()->subMonths(6)->startOfMonth())
            ->count();
        foreach ($months as $month => $default) {
            $cumulative += $rawAssigns[$month] ?? 0;
            $monthlyAssigns[] = [
                'month' => $month,
                'count' => $cumulative,
            ];
        }

        $monthlyAssigns = collect($monthlyAssigns);

        $topUsers = User::where('status', 'active')
            ->withCount(['equipment as equipment_count' => function ($query) {
                $query->where('status', 'in_use');
            }])
            ->having('equipment_count', '>', 0)
            ->orderBy('equipment_count', 'desc')
            ->take(5)
            ->get();

        $locationStats = Location::withCount('equipment')
            ->orderBy('equipment_count', 'desc')
            ->get();
        $totalInLocations = $locationStats->sum('equipment_count');

        $chartData = [];
        foreach (StatusEquipment::cases() as $status) {
            $count = match($status->value) {
                'in_use' => $inUseEquipments,
                'in_stock' => $inStockEquipments,
                'repair' => $inRepairEquipments,
                'written' => $writtenEquipments,
                default => 0
            };

            $color = match($status->value) {
                'in_use' => '#bef264',
                'in_stock' => '#3b82f6',
                'repair' => '#f59e0b',
                'written' => '#ef4444',
                default => '#94a3b8'
            };

            $chartData[] = [
                'value' => $status->value,
                'label' => StatusEquipment::ruValues()[$status->value],
                'count' => $count,
                'color' => $color,
            ];
        }




        return view('admin.dashboard', compact(
            'totalEquipments',
            'inUseEquipments',
            'inStockEquipments',
            'inRepairEquipments',
            'writtenEquipments',
            'categories',
            'recentOperations',
            'monthlyAssigns',
            'topUsers',
            'locationStats',
            'totalInLocations',
            'chartData',
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

        $locationsForJs = $locations->map(function($loc) {
            return [
                'id' => $loc->id,
                'name' => $loc->name,
                'type' => $loc->type,
                'typeLabel' => \App\Http\Enums\TypeLocation::ruValues()[$loc->type] ?? $loc->type
            ];
        });


        return view('admin.equipment', compact('equipments', 'categories', 'locations', 'users','locationsForJs'));
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


        $departmentsQuery = Department::withCount('users')
            ->with(['users:id,surname,name,patronymic,email,department_id', 'positions:id,name,department_id']);
        $direction = $request->get('direction', 'desc');
        $departmentsQuery->orderBy('users_count', $direction)->orderBy('name', 'asc');
        $departments = $departmentsQuery->paginate(15, ['*'], 'departments_page')->withQueryString();


        $positionsQuery = Position::with('department')->withCount('users')
            ->with('users:id,surname,name,patronymic,email,position_id');
        $posDirection = $request->get('pos_direction', 'desc');
        $positionsQuery->orderBy('users_count', $posDirection)->orderBy('name', 'asc');
        $positions = $positionsQuery->paginate(15, ['*'], 'positions_page')->withQueryString();

        return view('admin.structure.index', compact('departments', 'positions', 'activeTab'));
    }

    public function globalSearch(Request $request)
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }


        $equipment = Equipment::where('name', 'like', "%{$query}%")
            ->orWhere('inventory_number', 'like', "%{$query}%")
            ->orWhere('serial_number', 'like', "%{$query}%")
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
                    'url' => route('admin.equipment.show', $item->id)
                ];
            });


        $users = collect();
        if (auth()->user()->isAdmin()) {
            $users = User::where('surname', 'like', "%{$query}%")
                ->orWhere('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->get(['id', 'surname', 'name', 'email'])
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'user',
                        'title' => $item->surname . ' ' . $item->name,
                        'subtitle' => $item->email,
                        'url' => route('admin.users.show', $item->id)
                    ];
                });
        }


        $categories = collect();
        if (auth()->user()->isAdmin()) {
            $categories = Category::where('name', 'like', "%{$query}%")
                ->get(['id', 'name'])
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'category',
                        'title' => $item->name,
                        'subtitle' => 'Категория оборудования',
                        'url' => route('admin.categories.index') . '?search=' . $item->name
                    ];
                });
        }


        $locations = collect();
        if (auth()->user()->isAdmin()) {
            $locations = Location::where('name', 'like', "%{$query}%")
                ->get(['id', 'name'])
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'location',
                        'title' => $item->name,
                        'subtitle' => 'Локация',
                        'url' => route('admin.locations.show', $item->id)
                    ];
                });
        }

        $results = $equipment->concat($users)->concat($categories)->concat($locations);

        return response()->json($results);
    }
}
