<?php $pageTitle = 'Kembalikan Buku - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<?php
$tanggalKembali = date('Y-m-d');
$denda = hitungDenda($loan['tanggal_kembali_rencana'], $tanggalKembali);
$isOverdue = $loan['tanggal_kembali_rencana'] < $tanggalKembali;
?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-undo"></i> Kembalikan Buku</h1>
        <a href="index.php?page=admin-loans" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="form-container">
        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> Detail Peminjaman</h3>
            <table class="info-table">
                <tr>
                    <td><strong>Anggota</strong></td>
                    <td>: <?php echo $loan['nama_anggota']; ?></td>
                </tr>
                <tr>
                    <td><strong>Buku</strong></td>
                    <td>: <?php echo $loan['judul_buku']; ?> (<?php echo $loan['kode_buku']; ?>)</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Pinjam</strong></td>
                    <td>: <?php echo formatTanggal($loan['tanggal_pinjam']); ?></td>
                </tr>
                <tr>
                    <td><strong>Batas Kembali</strong></td>
                    <td>: <?php echo formatTanggal($loan['tanggal_kembali_rencana']); ?></td>
                </tr>
                <tr>
                    <td><strong>Tanggal Kembali</strong></td>
                    <td>: <?php echo formatTanggal($tanggalKembali); ?></td>
                </tr>
                <tr>
                    <td><strong>Status</strong></td>
                    <td>: 
                        <?php if($isOverdue): ?>
                            <span class="badge badge-danger">Terlambat</span>
                        <?php else: ?>
                            <span class="badge badge-success">Tepat Waktu</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>Denda</strong></td>
                    <td>: <span class="text-danger"><strong><?php echo formatRupiah($denda); ?></strong></span></td>
                </tr>
            </table>
        </div>
        
        <?php if($denda > 0): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Perhatian!</strong> Anggota harus membayar denda sebesar <strong><?php echo formatRupiah($denda); ?></strong>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-actions">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-check"></i> Konfirmasi Pengembalian
                </button>
                <a href="index.php?page=admin-loans" class="btn btn-danger btn-lg">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
