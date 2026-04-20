// ОБЩИЕ УТИЛИТНЫЕ ФУНКЦИИ ДЛЯ АДМИН-ПАНЕЛИ

window.showToast = (message, type = 'success') => {
    const toast = Object.assign(document.createElement('div'), {
        className: `alert alert-${type} position-fixed bottom-0 end-0 m-3`,
        textContent: message,
        style: `
            z-index: 9999;
            background-color: ${type === 'success' ? 'rgba(190, 242, 100, 0.9)' : 'rgba(239, 68, 68, 0.9)'};
            color: ${type === 'success' ? '#02040a' : '#fff'};
            border-radius: 12px;
            padding: 12px 20px;
        `
    });
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
};


window.showFormErrors = (form, errors) => {
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

    Object.entries(errors).forEach(([field, messages]) => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');
            const feedback = Object.assign(document.createElement('div'), {
                className: 'invalid-feedback',
                textContent: Array.isArray(messages) ? messages[0] : messages
            });
            (input.closest('.mb-3') || input.parentNode).appendChild(feedback);
        }
    });
};


window.submitAjaxForm = async (form, modalId, options = {}) => {
    const { selectName = null, reloadOnSuccess = false, onSuccess = null } = options;

    try {
        const formData = new FormData(form);


        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) {
            formData.append('_method', methodInput.value);
        }

        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
            }

            if (selectName) {
                const select = document.querySelector(`select[name="${selectName}"]`);
                if (select && data.item) {
                    select.add(new Option(data.item.name, data.item.id, true, true));
                }
                window.showToast(data.message || 'Успешно сохранено', 'success');
            }

            form.reset();
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            if (onSuccess) {
                onSuccess(data);
            }

            if (reloadOnSuccess) {
                window.location.reload();
            }
        } else {
            if (data.errors) {
                window.showFormErrors(form, data.errors);
            }
        }
    } catch (error) {
        console.error('Ошибка:', error);
        window.showToast('Ошибка соединения с сервером', 'danger');
    }
};


window.initCustomSelects = () => {
    document.querySelectorAll('.custom-select').forEach(select => {
        const btn = select.querySelector('.custom-select-btn');
        const items = select.querySelectorAll('.dropdown-item');
        const hiddenInput = select.querySelector('.custom-select-input');
        const directionInput = select.querySelector('.custom-direction-input');
        const posDirectionInput = select.querySelector('.custom-pos-direction-input');
        const selectedText = btn.querySelector('.selected-text');

        items.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                selectedText.textContent = item.textContent;

                if (hiddenInput) hiddenInput.value = item.dataset.value || '';
                if (directionInput && item.dataset.direction) directionInput.value = item.dataset.direction;
                if (posDirectionInput && item.dataset.posDirection) posDirectionInput.value = item.dataset.posDirection;

                items.forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            });
        });
    });
};
