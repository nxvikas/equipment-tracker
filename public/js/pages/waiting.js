
let checkCount = 0;
const maxChecks = 60;
let interval = null;

function checkStatus() {
    const checkUrl = document.getElementById('checkStatusBtn')?.dataset.checkUrl;

    if (!checkUrl) {
        console.error('URL для проверки статуса не найден');
        return;
    }

    fetch(checkUrl)
        .then(response => response.json())
        .then(data => {

            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }


            if (data.status === 'active') {
                handleActiveStatus(data);
            }

            else if (data.status === 'rejected') {
                handleRejectedStatus(data);
            }

            else if (data.status === 'blocked') {
                handleBlockedStatus(data);
            }
        })
        .catch(error => console.error('Ошибка при проверке статуса:', error));
}


function handleActiveStatus(data) {
    const statusBadge = document.getElementById('statusBadge');
    const statusText = document.getElementById('statusText');

    if (statusBadge) {
        statusBadge.style.background = 'rgba(40, 167, 69, 0.12)';
        statusBadge.style.color = '#28a745';
    }

    if (statusText) {
        statusText.innerHTML = '<i class="bi bi-check-circle me-1"></i> Одобрено! Перенаправление...';
    }

    if (interval) {
        clearInterval(interval);
        interval = null;
    }


    setTimeout(() => {
        if (data.redirect) {
            window.location.href = data.redirect;
        }
    }, 1500);
}


function handleRejectedStatus(data) {
    const statusBadge = document.getElementById('statusBadge');
    const statusText = document.getElementById('statusText');
    const checkBtn = document.getElementById('checkStatusBtn');

    if (statusBadge) {
        statusBadge.style.background = 'rgba(220, 53, 69, 0.12)';
        statusBadge.style.color = '#dc3545';
    }

    if (statusText) {
        statusText.innerHTML = '<i class="bi bi-x-circle me-1"></i> Отклонено';
    }


    if (checkBtn) {
        checkBtn.disabled = true;
        checkBtn.style.opacity = '0.5';
        checkBtn.style.cursor = 'not-allowed';
    }


    if (interval) {
        clearInterval(interval);
        interval = null;
    }


    setTimeout(() => {
        if (data.redirect) {
            window.location.href = data.redirect;
        }
    }, 3000);
}


function handleBlockedStatus(data) {
    const statusBadge = document.getElementById('statusBadge');
    const statusText = document.getElementById('statusText');
    const checkBtn = document.getElementById('checkStatusBtn');

    if (statusBadge) {
        statusBadge.style.background = 'rgba(255, 193, 7, 0.12)';
        statusBadge.style.color = '#ffc107';
    }

    if (statusText) {
        statusText.innerHTML = '<i class="bi bi-shield-exclamation me-1"></i> Заблокирован';
    }


    if (checkBtn) {
        checkBtn.disabled = true;
        checkBtn.style.opacity = '0.5';
        checkBtn.style.cursor = 'not-allowed';
    }


    if (interval) {
        clearInterval(interval);
        interval = null;
    }


    setTimeout(() => {
        if (data.redirect) {
            window.location.href = data.redirect;
        }
    }, 3000);
}


function stopChecking() {
    if (interval) {
        clearInterval(interval);
        interval = null;
    }
}


function manualCheck() {
    checkCount = 0;
    checkStatus();
}


function initWaitingPage() {
    const checkBtn = document.getElementById('checkStatusBtn');
    if (checkBtn) {
        checkBtn.addEventListener('click', manualCheck);
    }


    checkStatus();


    interval = setInterval(() => {
        checkCount++;


        if (checkCount >= maxChecks) {
            stopChecking();
            const statusText = document.getElementById('statusText');
            if (statusText) {
                statusText.innerHTML = '<i class="bi bi-hourglass me-1"></i> Проверка приостановлена. Нажмите "Проверить статус"';
            }
        } else {
            checkStatus();
        }
    }, 5000);


    window.addEventListener('beforeunload', stopChecking);
}


if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWaitingPage);
} else {
    initWaitingPage();
}
