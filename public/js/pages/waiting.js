
class WaitingPage {
    constructor() {
        this.checkInterval = null;
        this.init();
    }

    init() {

        this.startAutoCheck();


        const checkBtn = document.getElementById('checkStatusBtn');
        if (checkBtn) {
            checkBtn.addEventListener('click', () => this.manualCheck());
        }


        window.addEventListener('beforeunload', () => this.stopAutoCheck());
    }

    startAutoCheck() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
        }
        this.checkInterval = setInterval(() => this.checkStatus(), 10000);
    }

    stopAutoCheck() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
        }
    }

    async manualCheck() {
        const btn = document.getElementById('checkStatusBtn');
        const originalHtml = btn.innerHTML;

        btn.innerHTML = '<i class="bi bi-arrow-repeat me-1 spinner"></i> Проверка...';
        btn.disabled = true;

        await this.checkStatus();

        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }, 2000);
    }

    async checkStatus() {
        try {
            const response = await fetch('/check-status', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.redirect) {
                this.stopAutoCheck();
                window.location.href = data.redirect;
                return;
            }

            this.handleStatus(data);

        } catch (error) {
            console.error('Ошибка при проверке статуса:', error);
        }
    }

    handleStatus(data) {
        switch (data.status) {
            case 'active':
                this.stopAutoCheck();
                this.showSuccessAndRedirect();
                break;
            case 'rejected':
                this.stopAutoCheck();
                this.showRejected();
                break;
            case 'blocked':
                this.stopAutoCheck();
                this.showBlocked();
                break;
            default:

                break;
        }
    }

    showSuccessAndRedirect() {
        const statusCard = document.querySelector('.status-card');
        statusCard.innerHTML = `
            <div class="text-center">
                <i class="bi bi-check-circle-fill" style="font-size: 2rem; color: var(--accent);"></i>
                <h5 class="mt-2" style="color: var(--text-primary);">Заявка одобрена!</h5>
                <p style="color: var(--text-secondary);">Перенаправление на страницу входа...</p>
            </div>
        `;

        setTimeout(() => {
            window.location.href = '/login';
        }, 2000);
    }

    showRejected() {
        const statusCard = document.querySelector('.status-card');
        statusCard.innerHTML = `
            <div class="text-center">
                <i class="bi bi-x-circle-fill" style="font-size: 2rem; color: var(--danger);"></i>
                <h5 class="mt-2" style="color: var(--text-primary);">В доступе отказано</h5>
                <p style="color: var(--text-secondary);">Свяжитесь с администратором для получения подробной информации</p>
                <a href="/register" class="btn mt-2" style="background: var(--accent); color: #02040a; border-radius: 40px; padding: 8px 20px; font-weight: 600;">Подать новую заявку</a>
            </div>
        `;
    }

    showBlocked() {
        const statusCard = document.querySelector('.status-card');
        statusCard.innerHTML = `
            <div class="text-center">
                <i class="bi bi-shield-exclamation" style="font-size: 2rem; color: var(--danger);"></i>
                <h5 class="mt-2" style="color: var(--text-primary);">Аккаунт заблокирован</h5>
                <p style="color: var(--text-secondary);">Обратитесь к администратору системы</p>
            </div>
        `;
    }
}


const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .spinner {
        animation: spin 1s linear infinite;
        display: inline-block;
    }
`;
document.head.appendChild(style);


document.addEventListener('DOMContentLoaded', () => {
    new WaitingPage();
});
