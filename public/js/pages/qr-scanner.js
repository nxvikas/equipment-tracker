(function() {
    const modal = document.getElementById('qrScannerModal');
    if (!modal) {
        return;
    }
    let scanner = null;
    let isScanning = false;
    function getEquipmentIdFromUrl(url) {
        let match = url.match(/\/equipment\/(\d+)/);
        if (match) return match[1];
        match = url.match(/\/admin\/equipment\/(\d+)/);
        if (match) return match[1];
        return null;
    }

    function stopScanner() {
        if (scanner && isScanning) {
            scanner.stop().then(() => {
                isScanning = false;
            }).catch(() => {});
        }
    }

    function startScanner() {
        const readerElement = document.getElementById('qr-reader');
        if (!readerElement) return;

        readerElement.innerHTML = '';
        scanner = new Html5Qrcode("qr-reader");
        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        };

        scanner.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                const equipmentId = getEquipmentIdFromUrl(decodedText);
                if (equipmentId) {
                    stopScanner();
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                    sessionStorage.setItem('redirect_after_login', '/equipment/' + equipmentId);
                    window.location.href = '/equipment/' + equipmentId;
                } else {
                    alert('Неверный QR-код');
                }
            },
            () => {}
        ).then(() => {
            isScanning = true;
        }).catch(err => {
            let errorMessage = 'Не удалось запустить камеру';

            if (err.name === 'NotFoundError' || err.message?.includes('device not found')) {
                errorMessage = 'Камера не найдена. Подключите камеру или используйте телефон.';
            } else if (err.name === 'NotAllowedError') {
                errorMessage = 'Доступ к камере запрещён. Разрешите использование камеры в настройках браузера.';
            } else if (err.name === 'NotReadableError') {
                errorMessage = 'Камера занята другим приложением. Закройте другие программы, использующие камеру.';
            }

            readerElement.innerHTML = '<div class="text-danger text-center p-3"><i class="bi bi-exclamation-triangle me-2"></i>' + errorMessage + '</div>';
        });
    }

    modal.addEventListener('shown.bs.modal', function() {

        setTimeout(startScanner, 500);
    });


    modal.addEventListener('hidden.bs.modal', function() {
        stopScanner();
        const readerElement = document.getElementById('qr-reader');
        if (readerElement) readerElement.innerHTML = '';
    });
})();
