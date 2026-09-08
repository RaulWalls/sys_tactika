        </section>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_URL ?>/js/app.js"></script>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Acceso no permitido',
            text: <?= json_encode($_SESSION['flash_error']) ?>,
            background: getComputedStyle(document.body).getPropertyValue('--surface').trim() || '#ffffff',
            color: getComputedStyle(document.body).getPropertyValue('--text').trim() || '#1f2937',
            confirmButtonColor: getComputedStyle(document.body).getPropertyValue('--primary').trim() || '#5b3cc4'
        });
    </script>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_exito'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Felicidades!',
            html: <?= json_encode($_SESSION['flash_exito']) ?>,
            background: getComputedStyle(document.body).getPropertyValue('--surface').trim() || '#ffffff',
            color: getComputedStyle(document.body).getPropertyValue('--text').trim() || '#1f2937',
            confirmButtonColor: getComputedStyle(document.body).getPropertyValue('--primary').trim() || '#5b3cc4'
        });
    </script>
    <?php unset($_SESSION['flash_exito']); ?>
<?php endif; ?>
</body>
</html>
