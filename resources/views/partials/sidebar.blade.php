<nav class="sidebar d-none d-md-block" id="sidebar">
    <div class="sidebar-header">
        <div class="logo flex-column">
            <div>
                <span class="logo-name">{{config('app.company.name')}}</span>
            </div>
            <small>{{config('app.company.description')}}</small>
        </div>
    </div>

    <ul class="nav flex-column">
        @if(auth()->user()->isAdmin())
            <li class="nav-item">
                <a href="" class="nav-link active">
                    <i class="bi bi-graph-up"></i>
                    <span>Главная</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="bi bi-laptop"></i>
                    <span>Оборудование</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span>Сотрудники</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="bi bi-building"></i>
                    <span>Склад</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="bi bi-bar-chart-steps"></i>
                    <span>Аналитика</span>
                </a>
            </li>
        @elseif(auth()->user()->isEmployee())
            <li class="nav-item">
                <a href="" class="nav-link active">
                    <i class="bi bi-laptop"></i>
                    <span>Мое оборудование</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="bi bi-bar-chart-steps"></i>
                    <span>Аналитика</span>
                </a>
            </li>
        @endif
    </ul>

    <div class="sidebar-footer">
        <button class="theme-toggle" id="themeToggle">
            <i class="bi bi-moon"></i>
        </button>
{{--        <form method="POST" action="">--}}
{{--            @csrf--}}
{{--            <button type="submit" class="logout-btn">--}}
{{--                <i class="bi bi-box-arrow-right"></i>--}}
{{--                <span>Выход</span>--}}
{{--            </button>--}}
{{--        </form>--}}
    </div>
</nav>
