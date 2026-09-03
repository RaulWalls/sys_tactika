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
            confirmButtonColor: getComputedStyle(document.body).getPropertyValue('--primary').trim() || '#5b3cc4'
        });
    </script>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>
</body>
</html>
