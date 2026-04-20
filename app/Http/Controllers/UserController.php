<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserQuickRequest;
use App\Http\Requests\UpdateUserFullRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['department', 'position', 'role'])
            ->where('id', '!=', auth()->id())
            ->orderByRaw("FIELD(status, 'pending', 'active', 'blocked', 'rejected')")
            ->orderBy('created_at', 'desc');

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

    public function show(User $user)
    {
        $user->load(['department', 'position', 'role']);

        $history = \App\Models\Equipment_history::with(['equipment', 'user', 'toUser', 'fromUser', 'toLocation', 'fromLocation'])
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

        return view('admin.users.show', compact('user', 'history', 'actionTypes', 'departments', 'positions'));
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

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Нельзя удалить самого себя');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Сотрудник удалён');
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
        $user->update(['status' => \App\Http\Enums\UserStatus::BLOCKED->value]);
        session()->flash('success', 'Пользователь заблокирован');
        return redirect()->back();
    }

    public function activate(User $user)
    {
        $user->update(['status' => \App\Http\Enums\UserStatus::ACTIVE->value]);
        session()->flash('success', 'Пользователь активирован');
        return redirect()->back();
    }


}
