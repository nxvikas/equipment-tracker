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
            <li class="nav-item {{ request()->routeIs('admin.employees*') ? 'active' : '' }}">
                <a href="" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span>Сотрудники</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.stock*') ? 'active' : '' }}">
                <a href="" class="nav-link">
                    <i class="bi bi-building"></i>
                    <span>Склад</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                <a href="" class="nav-link">
                    <i class="bi bi-bar-chart-steps"></i>
                    <span>Аналитика</span>
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
