<?php $pageTitle = 'Tolak Request - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-times-circle"></i> Tolak Request Peminjaman</h1>
        <a href="index.php?page=admin-requests" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="form-container">
        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> Detail Request</h3>
            <table class="info-table">
                <tr>
                    <td><strong>Anggota</strong></td>
                    <td>: <?php echo htmlspecialchars($request['nama_lengkap']); ?></td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td>: <?php echo htmlspecialchars($request['email']); ?></td>
                </tr>
                <tr>
                    <td><strong>Buku</strong></td>
                    <td>: <?php echo htmlspecialchars($request['judul']); ?></td>
                </tr>
                <tr>
                    <td><strong>Kode Buku</strong></td>
                    <td>: <?php echo $request['kode_buku']; ?></td>
                </tr>
                <tr>
                    <td><strong>Tanggal Request</strong></td>
                    <td>: <?php echo formatTanggal(date('Y-m-d', strtotime($request['request_date']))); ?></td>
                </tr>
            </table>
        </div>
        
        <form method="POST" action="">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Perhatian:</strong> Request ini akan ditolak dan anggota akan mendapat notifikasi penolakan.
            </div>
            
            <div class="form-group">
                <label>Alasan Penolakan <span style="color:var(--danger);">*</span></label>
                <textarea name="response_note" class="form-control" rows="5" required
                          placeholder="Contoh: Stok buku sedang habis, Buku dalam proses perbaikan, dll..."></textarea>
                <small class="form-text">Berikan alasan yang jelas untuk penolakan</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-times-circle"></i> Tolak Request
                </button>
                <a href="index.php?page=admin-requests" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
