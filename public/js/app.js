document.addEventListener('DOMContentLoaded', function () {
    // Confirmación antes de acciones destructivas (botones/enlaces con data-confirm="mensaje")
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const message = el.getAttribute('data-confirm') || '¿Estás seguro?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});
