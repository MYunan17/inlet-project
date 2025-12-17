<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    // Definisikan objek global untuk menampung semua konfigurasi PHP
    window.AppConfig = {
        BASE_URL: '<?php echo BASE_URL; ?>',
        GALLERY: {
            INITIAL_LIMIT: <?php echo $this->initial_limit ?? 12; ?>,
            TOTAL_ITEMS: <?php echo $data['total_items'] ?? 0; ?>,
            LOAD_MORE_URL: '<?php echo BASE_URL; ?>galeri/loadMore'
        }
    };
</script>

<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
</body>
</html>