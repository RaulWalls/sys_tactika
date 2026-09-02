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

    // Vista previa de imagen al elegir un archivo (input[type=file][data-preview-img])
    document.querySelectorAll('input[type="file"][data-preview-img]').forEach(function (input) {
        input.addEventListener('change', function () {
            if (!input.files || !input.files[0]) {
                return;
            }

            const img = document.getElementById(input.getAttribute('data-preview-img'));
            const placeholder = document.getElementById(input.getAttribute('data-preview-placeholder'));
            const reader = new FileReader();

            reader.onload = function (e) {
                img.src = e.target.result;
                img.style.display = 'block';
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            };

            reader.readAsDataURL(input.files[0]);
        });
    });

    // Autocompletar un campo con el valor sugerido de la opción elegida
    // (select[data-fill-target] + option[data-monto])
    document.querySelectorAll('select[data-fill-target]').forEach(function (select) {
        select.addEventListener('change', function () {
            const option = select.options[select.selectedIndex];
            const monto = option ? option.getAttribute('data-monto') : '';

            if (monto) {
                const target = document.getElementById(select.getAttribute('data-fill-target'));
                if (target) {
                    target.value = monto;
                }
            }
        });
    });

    // Excluir del datalist de rival el equipo ya elegido como "Equipo"
    // (select[data-datalist-target][data-equipos])
    document.querySelectorAll('select[data-datalist-target]').forEach(function (select) {
        const datalist = document.getElementById(select.getAttribute('data-datalist-target'));
        if (!datalist) {
            return;
        }

        let equipos = [];
        try {
            equipos = JSON.parse(select.getAttribute('data-equipos') || '[]');
        } catch (e) {
            equipos = [];
        }

        const render = function () {
            const excluidoId = select.value;
            datalist.innerHTML = '';

            equipos.forEach(function (equipo) {
                if (String(equipo.id) === String(excluidoId)) {
                    return;
                }

                const option = document.createElement('option');
                option.value = equipo.nombre;
                datalist.appendChild(option);
            });
        };

        select.addEventListener('change', render);
        render();
    });

    // Menú desplegable de temas (botón 🎨 junto a notificaciones)
    const themeToggle = document.getElementById('themeToggle');
    const themeMenu = document.getElementById('themeMenu');

    if (themeToggle && themeMenu) {
        themeToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            themeMenu.classList.toggle('show');
        });

        themeMenu.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        document.addEventListener('click', function () {
            themeMenu.classList.remove('show');
        });
    }
});
