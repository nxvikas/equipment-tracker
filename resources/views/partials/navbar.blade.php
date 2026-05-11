<header class="header">
    <button class="burger-btn d-md-none" id="burgerBtn" type="button">
        <i class="bi bi-list"></i>
    </button>

    <div class="search-wrapper">
        <i class="bi bi-search"></i>
        <input type="text" id="globalSearch" class="search-input"
               placeholder="@if(auth()->user()->isAdmin())Название, инв.№, SN, ФИО...@else Название, инв.№...@endif">


        <button type="button" class="search-hint-btn"
                data-bs-toggle="popover"
                data-bs-html="true"
                data-bs-placement="right"
                data-bs-container="body"
                data-bs-title="<i class='bi bi-search' style='color: var(--accent);'></i> <span style='color: var(--text-primary);'>Что можно искать?</span>"
                data-bs-content="@if(auth()->user()->isAdmin())
                <div class='popover-content-wrapper'>
                    <div class='popover-items'>
                        <div class='popover-item'><i class='bi bi-laptop'></i> <strong>Оборудование</strong> — название, инв.№, SN</div>
                        <div class='popover-item'><i class='bi bi-person'></i> <strong>Сотрудники</strong> — ФИО, email</div>
                        <div class='popover-item'><i class='bi bi-tags'></i> <strong>Категории</strong> — название</div>
                        <div class='popover-item'><i class='bi bi-geo-alt'></i> <strong>Локации</strong> — название</div>
                    </div>

                </div>
            @else
                <div class='popover-content-wrapper'>
                    <div class='popover-section-title'>Что можно искать:</div>
                    <div class='popover-items'>
                        <div class='popover-item'><i class='bi bi-laptop'></i> <strong>Моё оборудование</strong> — название, инв.№</div>
                    </div>
                </div>
            @endif">
            <i class="bi bi-info-circle-fill"></i>
        </button>
    </div>


    <div class="user-area">


        <div class="dropdown">
            <button class="user-profile dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
    <span class="user-info">
        <span class="user-name">{{ auth()->user()->name ?? 'Не указано' }}</span>
        <span class="user-role">
    {{ auth()->user()->role->display_name ?? 'Не указано' }}
            @if(auth()->user()->position)
                | {{ auth()->user()->position->name }}
            @endif
</span>
    </span>
                <span class="user-avatar">
        @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                             alt="Аватар"
                             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'НУ', 0, 2)) }}
                    @endif
    </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end profile-menu">
                <li><a class="dropdown-item" href="{{route('profile.show')}}">
                        <i class="bi bi-person me-2"></i> Мой профиль
                    </a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>

                    <a class="dropdown-item text-danger" href="{{route('logout')}}">
                        <i class="bi bi-box-arrow-right me-2"></i> Выход
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('globalSearch');
            if (!searchInput) return;

            let searchTimeout;
            let searchResults = document.createElement('div');
            searchResults.className = 'global-search-results';
            searchInput.parentNode.appendChild(searchResults);


            const searchUrl = @json(auth()->user()->isAdmin() ? route('admin.global.search') : route('employee.global.search'));

            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    searchResults.classList.remove('show');
                    searchResults.innerHTML = '';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length === 0) {
                                searchResults.innerHTML = '<div class="search-result-item">Ничего не найдено</div>';
                                searchResults.classList.add('show');
                                return;
                            }

                            let html = '';
                            data.forEach(item => {
                                html += `
                                    <a href="${item.url}" class="search-result-item">
                                        <div class="search-result-info">
                                            <div class="search-result-title">${item.title}</div>
                                            <div class="search-result-subtitle">${item.subtitle}</div>
                                        </div>
                                    </a>
                                `;
                            });
                            searchResults.innerHTML = html;
                            searchResults.classList.add('show');
                        })
                        .catch(() => {
                        });
                }, 300);
            });

            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.remove('show');
                }
            });
        });

        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    </script>
@endpush
