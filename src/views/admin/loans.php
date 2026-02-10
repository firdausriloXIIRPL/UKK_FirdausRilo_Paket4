<?php $pageTitle = 'Peminjaman Buku - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-book-reader"></i> Peminjaman Buku</h1>
        <div style="display:flex; gap:10px;">
            <a href="index.php?page=create-loan" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Peminjaman
            </a>
            <a href="index.php?page=loan-history" class="btn btn-secondary">
                <i class="fas fa-history"></i> Riwayat
            </a>
        </div>
    </div>
    
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <?php echo $flash['message']; ?>
        </div>
    <?php endif; ?>
    
    <div class="table-container">
        <div class="table-header">
            <h2><i class="fas fa-list"></i> Daftar Peminjaman</h2>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="index.php?page=admin-loans" 
                   class="btn btn-<?php echo !isset($_GET['status']) ? 'primary' : 'secondary'; ?> btn-sm">
                    <i class="fas fa-list"></i> Semua
                </a>
                <a href="index.php?page=admin-loans&status=dipinjam" 
                   class="btn btn-<?php echo ($_GET['status'] ?? '') == 'dipinjam' ? 'info' : 'secondary'; ?> btn-sm">
                    <i class="fas fa-book-reader"></i> Dipinjam
                </a>
                <a href="index.php?page=admin-loans&status=terlambat" 
                   class="btn btn-<?php echo ($_GET['status'] ?? '') == 'terlambat' ? 'warning' : 'secondary'; ?> btn-sm">
                    <i class="fas fa-exclamation-triangle"></i> Terlambat
                </a>
                <a href="index.php?page=admin-loans&status=dikembalikan" 
                   class="btn btn-<?php echo ($_GET['status'] ?? '') == 'dikembalikan' ? 'success' : 'secondary'; ?> btn-sm">
                    <i class="fas fa-check"></i> Dikembalikan
                </a>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Anggota</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($loans)): ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <div style="padding:40px 20px;">
                                <i class="fas fa-inbox" style="font-size:48px; color:#cbd5e1; margin-bottom:15px;"></i>
                                <p style="color:#64748b; margin:0;">
                                    <?php if(isset($_GET['status'])): ?>
                                        Tidak ada peminjaman dengan status: <strong><?php echo $_GET['status']; ?></strong>
                                    <?php else: ?>
                                        Belum ada data peminjaman
                                    <?php endif; ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $no = 1; 
                    foreach($loans as $loan): 
                        // Check if overdue
                        $today = new DateTime();
                        $deadline = new DateTime($loan['tanggal_kembali']);
                        $isOverdue = ($loan['status'] != 'dikembalikan') && ($today > $deadline);
                        $rowClass = $isOverdue ? 'row-overdue' : '';
                    ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <td><?php echo $no++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($loan['nama_anggota']); ?></strong><br>
                                <small><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($loan['email']); ?></small>
                                <?php if(!empty($loan['no_telepon'])): ?>
                                    <br><small><i class="fas fa-phone"></i> <?php echo htmlspecialchars($loan['no_telepon']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($loan['judul_buku']); ?></strong><br>
                                <small>Kode: <?php echo $loan['kode_buku']; ?></small>
                                <?php if(!empty($loan['nama_penulis'])): ?>
                                    <br><small>Penulis: <?php echo htmlspecialchars($loan['nama_penulis']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo formatTanggal($loan['tanggal_pinjam']); ?></td>
                            <td>
                                <?php echo formatTanggal($loan['tanggal_kembali']); ?>
                                <?php if($isOverdue): ?>
                                    <?php
                                    $diff = $today->diff($deadline);
                                    $days = $diff->days;
                                    ?>
                                    <br><span class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Terlambat <?php echo $days; ?> hari
                                    </span>
                                <?php elseif($loan['status'] != 'dikembalikan'): ?>
                                    <?php
                                    $diff = $deadline->diff($today);
                                    $daysLeft = $diff->days;
                                    if($daysLeft <= 3): ?>
                                        <br><span style="color:#f59e0b;">
                                            <i class="fas fa-clock"></i> 
                                            Sisa <?php echo $daysLeft; ?> hari
                                        </span>
                                    <?php else: ?>
                                        <br><small style="color:#64748b;">
                                            Sisa <?php echo $daysLeft; ?> hari
                                        </small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($loan['status'] == 'dipinjam'): ?>
                                    <span class="badge badge-info">
                                        <i class="fas fa-book-reader"></i> Dipinjam
                                    </span>
                                <?php elseif($loan['status'] == 'terlambat'): ?>
                                    <span class="badge badge-warning">
                                        <i class="fas fa-exclamation-triangle"></i> Terlambat
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Dikembalikan
                                    </span>
                                    <?php if($loan['tanggal_pengembalian']): ?>
                                        <br><small><?php echo formatTanggal($loan['tanggal_pengembalian']); ?></small>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if($loan['denda'] > 0): ?>
                                    <br><small class="text-danger">
                                        <i class="fas fa-money-bill"></i> 
                                        Denda: Rp <?php echo number_format($loan['denda'], 0, ',', '.'); ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($loan['status'] != 'dikembalikan'): ?>
                                    <a href="index.php?page=return-book&id=<?php echo $loan['loan_id']; ?>" 
                                       class="btn btn-success btn-sm"
                                       title="Proses Pengembalian">
                                        <i class="fas fa-undo"></i> Kembalikan
                                    </a>
                                <?php else: ?>
                                    <span style="color:#94a3b8;">
                                        <i class="fas fa-check-circle"></i> Selesai
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if(!empty($loans)): ?>
            <div style="margin-top:20px; padding:15px; background:#f8fafc; border-radius:8px; text-align:center;">
                <p style="margin:0; color:#64748b;">
                    <i class="fas fa-info-circle"></i> 
                    Menampilkan <strong><?php echo count($loans); ?></strong> peminjaman
                    <?php if(isset($_GET['status'])): ?>
                        dengan status: <strong><?php echo $_GET['status']; ?></strong>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
