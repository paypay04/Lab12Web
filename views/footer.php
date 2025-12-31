    </div> <!-- Tutup .main-content -->

    <!-- Footer -->
    <footer style="background: #343a40; color: white; padding: 30px 0; margin-top: 50px;">
        <div style="width: 90%; max-width: 1200px; margin: 0 auto; text-align: center;">
            <p style="margin: 0 0 10px; font-size: 18px; font-weight: 600;">
                <i class="fas fa-boxes"></i> Sistem Inventaris Barang
            </p>
            <p style="margin: 0 0 10px; opacity: 0.8;">
                &copy; <?= date('Y') ?>, Informatika, Universitas Pelita Bangsa
            </p>
            <p style="margin: 0; font-size: 14px; opacity: 0.7;">
                Praktikum PHP OOP Lanjutan - Framework Modular Sederhana
            </p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Auto hide alerts setelah 5 detik
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Confirm untuk aksi delete
            $(document).on('click', 'a[href*="delete"], .btn-delete', function(e) {
                if (!confirm('Yakin ingin menghapus data ini?')) {
                    e.preventDefault();
                }
            });
            
            // Tooltip
            $('[title]').tooltip();
        });
        
        // Format Rupiah
        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Base URL untuk JavaScript
        const BASE_URL = '<?= BASE_URL ?>';
    </script>
</body>
</html>