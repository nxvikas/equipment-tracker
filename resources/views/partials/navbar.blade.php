<header class="header">
    <div class="search-wrapper">
        <i class="bi bi-search"></i>
        <input type="text" id="globalSearch" class="search-input"
               placeholder="Поиск по инвентарному номеру, названию...">
    </div>
    <div class="user-area">

        <div class="dropdown">
            <button class="notification-btn dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell"></i>
                <span class="notification-dot"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end notification-menu">
                <li class="dropdown-header">Уведомления</li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item notification-item unread" href="#">
                        <i class="bi bi-laptop me-2"></i>
                        <div>
                            <div class="fw-semibold small">Вам выдано оборудование</div>
                            <div class="small text-secondary">MacBook Pro 14" передан вам</div>
                            <div class="small text-muted">5 минут назад</div>
                        </div>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-center" href="#">Все уведомления</a></li>
            </ul>
        </div>


        <div class="dropdown">
            <button class="user-profile dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name ?? 'Не указано' }}</div>
                    <div class="user-role">
                        {{auth()->user()->role->display_name ?? 'Не указано'}}
                    </div>
                </div>
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'НУ', 0, 2)) }}
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end profile-menu">
                <li><a class="dropdown-item" href="#">
                        <i class="bi bi-person me-2"></i> Мой профиль
                    </a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Выход
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
