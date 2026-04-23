(function() {
    console.log('QR скрипт загружен');

    const modal = document.getElementById('qrScannerModal');
    if (!modal) {
        console.error('Модалка не найдена');
        return;
    }

    let scanner = null;
    let isScanning = false;


    function getEquipmentIdFromUrl(url) {
        let match = url.match(/\/admin\/equipment\/(\d+)/);
        if (match) return match[1];
        match = url.match(/\/equipment\/(\d+)/);
        if (match) return match[1];
        return null;
    }


    function stopScanner() {
        if (scanner && isScanning) {
            scanner.stop().then(() => {
                isScanning = false;
                console.log('Сканер остановлен');
            }).catch(err => console.error('Ошибка остановки:', err));
        }
    }


    function startScanner() {
        const readerElement = document.getElementById('qr-reader');
        if (!readerElement) {
            console.error('Элемент qr-reader не найден');
            return;
        }

        console.log('Запуск сканера...');


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
                console.log('QR отсканирован:', decodedText);
                const equipmentId = getEquipmentIdFromUrl(decodedText);

                if (equipmentId) {
                    stopScanner();
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                    window.location.href = '/admin/equipment/' + equipmentId;
                } else {
                    alert('Неверный QR-код');
                }
            },
            (errorMessage) => {

            }
        ).then(() => {
            isScanning = true;
            console.log('Сканер запущен успешно');
        }).catch(err => {
            console.error('Ошибка запуска камеры:', err);
            readerElement.innerHTML = '<div class="text-danger text-center p-3">Ошибка: ' + err + '</div>';
        });
    }


    modal.addEventListener('shown.bs.modal', function() {
        console.log('Модалка открыта');
        setTimeout(startScanner, 500);
    });


    modal.addEventListener('hidden.bs.modal', function() {
        console.log('Модалка закрыта');
        stopScanner();
        const readerElement = document.getElementById('qr-reader');
        if (readerElement) readerElement.innerHTML = '';
    });
})();
