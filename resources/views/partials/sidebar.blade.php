<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo flex-column">
            <div>
                <span class="logo-name">{{ config('app.company.name') }}</span>
            </div>
            <small>{{ config('app.company.description') }}</small>
        </div>
    </div>

    <ul class="nav flex-column">
        @if(auth()->user()->isAdmin())
            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="bi bi-graph-up"></i>
                    <span>Главная</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.equipment*') ? 'active' : '' }}">
                <a href="{{ route('admin.equipment') }}" class="nav-link">
                    <i class="bi bi-laptop"></i>
                    <span>Оборудование</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <a href="{{ route('admin.categories.index') }}" class="nav-link">
                    <i class="bi bi-tags"></i>
                    <span>Категории</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.locations*') ? 'active' : '' }}">
                <a href="{{ route('admin.locations.index') }}" class="nav-link">
                    <i class="bi bi-geo-alt"></i>
                    <span>Локации</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span>Сотрудники</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.structure*') ? 'active' : '' }}">
                <a href="{{ route('admin.structure.index') }}" class="nav-link">
                    <i class="bi bi-diagram-3"></i>
                    <span>Структура</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.history*') ? 'active' : '' }}">
                <a href="{{ route('admin.history') }}" class="nav-link">
                    <i class="bi bi-clock-history"></i>
                    <span>История операций</span>
                </a>
            </li>

        @elseif(auth()->user()->isEmployee())
            <li class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                <a href="{{ route('employee.dashboard') }}" class="nav-link">
                    <i class="bi bi-graph-up"></i>
                    <span>Главная</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('employee.equipment*') ? 'active' : '' }}">
                <a href="" class="nav-link">
                    <i class="bi bi-laptop"></i>
                    <span>Моё оборудование</span>
                </a>
            </li>
        @endif
    </ul>

    <div class="sidebar-footer">
        <button class="theme-toggle" id="themeToggle">
            <i class="bi bi-moon"></i>
        </button>
    </div>
</nav>
