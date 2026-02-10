<?php $pageTitle = 'Peminjaman Saya - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-book-reader"></i> Peminjaman Saya</h1>
        <div class="user-info">
            <span><i class="fas fa-user-circle"></i> <?php echo $_SESSION['nama_lengkap']; ?></span>
            <a href="index.php?page=logout" class="btn btn-danger btn-sm">
                <i class="fas fa-sign-out-alt"></i> Logout
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
    
    <!-- Statistics Cards -->
    <div class="dashboard-cards">
        <div class="card card-blue">
            <div class="card-icon">
                <i class="fas fa-book-reader"></i>
            </div>
            <div class="card-info">
                <h3>Sedang Dipinjam</h3>
                <div class="number">
                    <?php 
                    $activeCount = 0;
                    if(!empty($loans)) {
                        foreach($loans as $loan) {
                            if($loan['status'] != 'dikembalikan') $activeCount++;
                        }
                    }
                    echo $activeCount;
                    ?>
                </div>
            </div>
        </div>
        
        <div class="card card-green">
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-info">
                <h3>Sudah Dikembalikan</h3>
                <div class="number">
                    <?php 
                    $returnedCount = 0;
                    if(!empty($loans)) {
                        foreach($loans as $loan) {
                            if($loan['status'] == 'dikembalikan') $returnedCount++;
                        }
                    }
                    echo $returnedCount;
                    ?>
                </div>
            </div>
        </div>
        
        <div class="card card-danger">
            <div class="card-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="card-info">
                <h3>Terlambat</h3>
                <div class="number">
                    <?php 
                    $overdueCount = 0;
                    if(!empty($loans)) {
                        foreach($loans as $loan) {
                            if($loan['status'] != 'dikembalikan') {
                                $today = new DateTime();
                                $deadline = new DateTime($loan['tanggal_kembali']);
                                if($today > $deadline) $overdueCount++;
                            }
                        }
                    }
                    echo $overdueCount;
                    ?>
                </div>
            </div>
        </div>
        
        <div class="card card-warning">
            <div class="card-icon">
                <i class="fas fa-money-bill"></i>
            </div>
            <div class="card-info">
                <h3>Total Denda</h3>
                <div class="number" style="font-size:20px;">
                    <?php 
                    $totalDenda = 0;
                    if(!empty($loans)) {
                        foreach($loans as $loan) {
                            $totalDenda += $loan['denda'] ?? 0;
                        }
                    }
                    echo 'Rp ' . number_format($totalDenda, 0, ',', '.');
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter Tabs -->
    
    <!-- Loans Table -->
    <div class="table-container">
        <div class="table-header">
            <h2><i class="fas fa-list"></i> Daftar Peminjaman</h2>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Deadline</th>
                    <th>Tgl Pengembalian</th>
                    <th>Denda</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($loans)): ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <div style="padding:40px 20px;">
                                <i class="fas fa-inbox" style="font-size:48px; color:#cbd5e1; margin-bottom:15px;"></i>
                                <p style="margin:0; color:#64748b;">
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
                        // Calculate status
                        $today = new DateTime();
                        $deadline = new DateTime($loan['tanggal_kembali']);
                        $isOverdue = ($loan['status'] != 'dikembalikan') && ($today > $deadline);
                        
                        $daysRemaining = 0;
                        $daysLate = 0;
                        
                        if($loan['status'] != 'dikembalikan') {
                            if($isOverdue) {
                                $diff = $today->diff($deadline);
                                $daysLate = $diff->days;
                            } else {
                                $diff = $deadline->diff($today);
                                $daysRemaining = $diff->days;
                            }
                        }
                    ?>
                        <tr class="<?php echo $isOverdue ? 'row-overdue' : ''; ?>">
                            <td><?php echo $no++; ?></td>
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
                                <?php if($loan['status'] != 'dikembalikan'): ?>
                                    <?php if($isOverdue): ?>
                                        <br><span class="text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            Terlambat <?php echo $daysLate; ?> hari
                                        </span>
                                    <?php elseif($daysRemaining <= 3): ?>
                                        <br><span class="text-warning">
                                            <i class="fas fa-clock"></i> 
                                            Sisa <?php echo $daysRemaining; ?> hari
                                        </span>
                                    <?php else: ?>
                                        <br><small class="text-muted">
                                            Sisa <?php echo $daysRemaining; ?> hari
                                        </small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($loan['tanggal_pengembalian']): ?>
                                    <strong><?php echo formatTanggal($loan['tanggal_pengembalian']); ?></strong>
                                    <?php
                                    // Check if was late when returned
                                    $deadlineDate = new DateTime($loan['tanggal_kembali']);
                                    $returnedDate = new DateTime($loan['tanggal_pengembalian']);
                                    if($returnedDate > $deadlineDate):
                                        $lateDiff = $returnedDate->diff($deadlineDate);
                                    ?>
                                        <br><small class="text-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Terlambat <?php echo $lateDiff->days; ?> hari
                                        </small>
                                    <?php else: ?>
                                        <br><small class="text-success">
                                            <i class="fas fa-check-circle"></i>
                                            Tepat waktu
                                        </small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color:#94a3b8;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($loan['denda'] > 0): ?>
                                    <span class="text-danger" style="font-weight:600;">
                                        Rp <?php echo number_format($loan['denda'], 0, ',', '.'); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-success" style="font-weight:600;">
                                        Rp 0
                                    </span>
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

<style>
/* Filter Tabs */
.filter-tabs {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 10px 20px;
    border-radius: 8px;
    background: white;
    border: 2px solid #e2e8f0;
    color: #1e293b;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.filter-tab:hover {
    background: #f8fafc;
    border-color: #2563eb;
    transform: translateY(-2px);
}

.filter-tab.active {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
}

@media (max-width: 768px) {
    .filter-tabs {
        flex-direction: column;
    }
    
    .filter-tab {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
