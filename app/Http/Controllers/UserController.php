<?php

namespace App\Http\Controllers;

use App\Http\Enums\TypeEquipmentHistory;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\Equipment_history;
use App\Http\Enums\StatusEquipment;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserQuickRequest;
use App\Http\Requests\UpdateUserFullRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $direction = $request->query('direction', 'desc');

        $query = User::with(['department', 'position', 'role'])
            ->where('id', '!=', auth()->id())
            ->orderByRaw("FIELD(status, 'pending', 'active', 'blocked', 'rejected')")
            ->orderBy('created_at', $direction);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        $users = $query->paginate(15)->withQueryString();
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        $statuses = \App\Http\Enums\UserStatus::ruValues();

        return view('admin.users.index', compact('users', 'departments', 'positions', 'statuses'));
    }

    public function makeAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Нельзя изменить свою роль');
        }


        $statusValue = $user->status->value ?? $user->status;
        if ($statusValue === 'pending') {
            return redirect()->back()->with('error', 'Сначала активируйте пользователя');
        }

        $user->update(['role_id' => 1]);

        return redirect()->back()->with('success', 'Пользователь назначен администратором');
    }

    public function removeAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Нельзя изменить свою роль');
        }

        $statusValue = $user->status->value ?? $user->status;
        if ($statusValue === 'pending') {
            return redirect()->back()->with('error', 'Сначала активируйте пользователя');
        }

        $adminsCount = User::where('role_id', 1)->where('status', 'active')->count();
        if ($adminsCount <= 1) {
            return redirect()->back()->with('error', 'Нельзя снять права у последнего администратора');
        }

        $user->update(['role_id' => 2]);

        return redirect()->back()->with('success', 'Права администратора сняты');
    }

    public function show(User $user)
    {
        $user->load(['department', 'position', 'role']);

        $assignedEquipments = Equipment::where('current_user_id', $user->id)
            ->where('status', 'in_use')
            ->with('category')
            ->get();
        $availableEquipments = Equipment::where('status', 'in_stock')
            ->with('category', 'location')
            ->get();

        $history = Equipment_history::with(['equipment', 'user', 'toUser', 'fromUser', 'toLocation', 'fromLocation'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('to_user_id', $user->id)
                    ->orWhere('from_user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $actionTypes = \App\Http\Enums\TypeEquipmentHistory::ruValues();
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();

        return view('admin.users.show',
            compact('user',
                'assignedEquipments',
                'availableEquipments',
                'history',
                'actionTypes',
                'departments',
                'positions'));
    }

    public function updateQuick(UpdateUserQuickRequest $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя редактировать самого себя'
            ], 403);
        }

        $user->update($request->validated());

        session()->flash('success', 'Данные обновлены');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Данные обновлены']);
        }

        return redirect()->route('admin.users.index');
    }

    public function updateFull(UpdateUserFullRequest $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Нельзя редактировать самого себя');
        }

        $user->update($request->validated());

        session()->flash('success', 'Данные сотрудника обновлены');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Данные обновлены']);
        }

        return redirect()->route('admin.users.show', $user);
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Нельзя удалить самого себя');
        }

        if ($user->role_id === 1) {
            $adminsCount = User::where('role_id', 1)->where('status', 'active')->count();
            if ($adminsCount <= 1) {
                return redirect()->back()->with('error', 'Нельзя удалить последнего администратора');
            }
        }

        if (!$request->has('confirm_delete')) {
            return redirect()->back()->with('confirm_delete_user', $user->id);
        }

        $equipments = Equipment::where('current_user_id', $user->id)->get();

        foreach ($equipments as $equipment) {
            Equipment_history::create([
                'equipment_id' => $equipment->id,
                'action_type' => TypeEquipmentHistory::RETURNED->value,
                'user_id' => Auth::id(),
                'from_user_id' => $user->id,
                'new_status' => StatusEquipment::IN_STOCK->value,
                'comment' => 'Автоматический возврат при удалении сотрудника',
            ]);
        }

        Equipment::where('current_user_id', $user->id)->update([
            'status' => StatusEquipment::IN_STOCK->value,
            'current_user_id' => null,
        ]);

        Equipment_history::where('user_id', $user->id)->update(['user_id' => null]);
        Equipment_history::where('from_user_id', $user->id)->update(['from_user_id' => null]);
        Equipment_history::where('to_user_id', $user->id)->update(['to_user_id' => null]);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Сотрудник удалён. Оборудование возвращено на склад.');
    }

    public function approve(User $user)
    {
        $user->update(['status' => \App\Http\Enums\UserStatus::ACTIVE->value]);
        session()->flash('success', 'Пользователь активирован');
        return redirect()->back();
    }

    public function reject(User $user)
    {
        $user->update(['status' => \App\Http\Enums\UserStatus::REJECTED->value]);
        session()->flash('success', 'Пользователь отклонён');
        return redirect()->back();
    }

    public function block(User $user)
    {
        $equipments = Equipment::where('current_user_id', $user->id)->get();

        foreach ($equipments as $equipment) {
            Equipment_history::create([
                'equipment_id' => $equipment->id,
                'action_type' => TypeEquipmentHistory::RETURNED->value,
                'user_id' => Auth::id(),
                'from_user_id' => $user->id,
                'new_status' => StatusEquipment::IN_STOCK->value,
                'comment' => 'Автоматический возврат при блокировке сотрудника',
            ]);
        }

        Equipment::where('current_user_id', $user->id)->update([
            'status' => StatusEquipment::IN_STOCK->value,
            'current_user_id' => null,
        ]);

        $user->update(['status' => \App\Http\Enums\UserStatus::BLOCKED->value]);
        session()->flash('success', 'Пользователь заблокирован. Оборудование возвращено на склад.');
        return redirect()->back();
    }

    public function activate(User $user)
    {
        $user->update(['status' => \App\Http\Enums\UserStatus::ACTIVE->value]);
        session()->flash('success', 'Пользователь активирован');
        return redirect()->back();
    }


}
