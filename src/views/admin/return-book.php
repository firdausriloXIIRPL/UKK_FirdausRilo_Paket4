<?php $pageTitle = 'Pengembalian Buku - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-undo"></i> Pengembalian Buku</h1>
        <a href="index.php?page=admin-loans" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <?php echo $flash['message']; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-info-circle"></i> Detail Peminjaman</h2>
        </div>
        
        <div class="detail-grid">
            <!-- Informasi Anggota -->
            <div class="detail-card">
                <h3><i class="fas fa-user"></i> Informasi Anggota</h3>
                <table class="detail-table">
                    <tr>
                        <td><strong>Nama</strong></td>
                        <td><?php echo htmlspecialchars($loan['nama_anggota']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td><?php echo htmlspecialchars($loan['email']); ?></td>
                    </tr>
                    <?php if(!empty($loan['no_telepon'])): ?>
                    <tr>
                        <td><strong>No. Telepon</strong></td>
                        <td><?php echo htmlspecialchars($loan['no_telepon']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            
            <!-- Informasi Buku -->
            <div class="detail-card">
                <h3><i class="fas fa-book"></i> Informasi Buku</h3>
                <table class="detail-table">
                    <tr>
                        <td><strong>Judul</strong></td>
                        <td><?php echo htmlspecialchars($loan['judul_buku']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Kode Buku</strong></td>
                        <td><?php echo $loan['kode_buku']; ?></td>
                    </tr>
                    <?php if(!empty($loan['nama_penulis'])): ?>
                    <tr>
                        <td><strong>Penulis</strong></td>
                        <td><?php echo htmlspecialchars($loan['nama_penulis']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            
            <!-- Informasi Peminjaman -->
            <div class="detail-card">
                <h3><i class="fas fa-calendar"></i> Informasi Peminjaman</h3>
                <table class="detail-table">
                    <tr>
                        <td><strong>Tanggal Pinjam</strong></td>
                        <td><?php echo formatTanggal($loan['tanggal_pinjam']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Kembali</strong></td>
                        <td><?php echo formatTanggal($loan['tanggal_kembali']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Hari Ini</strong></td>
                        <td><?php echo formatTanggal(date('Y-m-d')); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>
                            <?php if($loan['status'] == 'dipinjam'): ?>
                                <span class="badge badge-info">
                                    <i class="fas fa-book-reader"></i> Dipinjam
                                </span>
                            <?php else: ?>
                                <span class="badge badge-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Terlambat
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Perhitungan Denda -->
            <div class="detail-card">
                <h3><i class="fas fa-calculator"></i> Perhitungan Denda</h3>
                <?php
                $today = new DateTime();
                $deadline = new DateTime($loan['tanggal_kembali']);
                $isOverdue = $today > $deadline;
                
                if($isOverdue):
                    $diff = $today->diff($deadline);
                    $days = $diff->days;
                ?>
                    <div style="padding:20px; background:#fef2f2; border-radius:8px; border:1px solid #fecaca; margin-bottom:15px;">
                        <p style="margin:0 0 10px 0; color:#dc2626; font-weight:600;">
                            <i class="fas fa-exclamation-triangle"></i> TERLAMBAT
                        </p>
                        <table class="detail-table">
                            <tr>
                                <td><strong>Hari Terlambat</strong></td>
                                <td><span class="text-danger"><?php echo $days; ?> hari</span></td>
                            </tr>
                            <tr>
                                <td><strong>Denda per Hari</strong></td>
                                <td>
                                    <?php
                                    $stmt = $GLOBALS['db']->query("SELECT nilai FROM settings WHERE nama_setting = 'denda_perhari' LIMIT 1");
                                    $setting = $stmt->fetch();
                                    $denda_perhari = $setting ? (int)$setting['nilai'] : 1000;
                                    ?>
                                    Rp <?php echo number_format($denda_perhari, 0, ',', '.'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Total Denda</strong></td>
                                <td>
                                    <span style="font-size:20px; font-weight:bold; color:#dc2626;">
                                        Rp <?php echo number_format($denda, 0, ',', '.'); ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="padding:20px; background:#f0fdf4; border-radius:8px; border:1px solid #bbf7d0; margin-bottom:15px;">
                        <p style="margin:0; color:#15803d; font-weight:600;">
                            <i class="fas fa-check-circle"></i> TEPAT WAKTU
                        </p>
                        <p style="margin:10px 0 0 0; color:#15803d;">
                            Tidak ada denda. Buku dikembalikan tepat waktu.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Konfirmasi Pengembalian -->
        <div style="margin-top:30px; padding:20px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;">
            <h3 style="margin:0 0 15px 0;">
                <i class="fas fa-question-circle"></i> Konfirmasi Pengembalian
            </h3>
            <p style="margin:0 0 20px 0; color:#64748b;">
                Pastikan buku dalam kondisi baik. Setelah dikonfirmasi, status peminjaman akan berubah menjadi "Dikembalikan" dan stok buku akan bertambah.
            </p>
            
            <form method="POST" onsubmit="return confirm('Konfirmasi pengembalian buku ini?');">
                <div style="display:flex; gap:10px; justify-content:center;">
                    <button type="submit" class="btn btn-success" style="min-width:150px;">
                        <i class="fas fa-check"></i> Konfirmasi Pengembalian
                        <?php if($denda > 0): ?>
                            <br><small>Denda: Rp <?php echo number_format($denda, 0, ',', '.'); ?></small>
                        <?php endif; ?>
                    </button>
                    <a href="index.php?page=admin-loans" class="btn btn-secondary" style="min-width:150px;">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
        
        <?php if(!empty($loan['keterangan'])): ?>
        <div style="margin-top:20px; padding:15px; background:#fef9c3; border-radius:8px; border:1px solid #fde047;">
            <p style="margin:0; color:#854d0e;">
                <strong><i class="fas fa-sticky-note"></i> Keterangan:</strong><br>
                <?php echo nl2br(htmlspecialchars($loan['keterangan'])); ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.detail-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e2e8f0;
}

.detail-card h3 {
    margin: 0 0 15px 0;
    color: #1e293b;
    font-size: 16px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e2e8f0;
}

.detail-table {
    width: 100%;
    border-collapse: collapse;
}

.detail-table td {
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
}

.detail-table td:first-child {
    width: 40%;
    color: #64748b;
}

.detail-table td:last-child {
    color: #1e293b;
    font-weight: 500;
}

.detail-table tr:last-child td {
    border-bottom: none;
}
</style>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
