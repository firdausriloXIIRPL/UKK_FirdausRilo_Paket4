<?php $pageTitle = 'Dashboard - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-home"></i> Dashboard</h1>
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
                <div class="number"><?php echo count($activeLoans ?? []); ?></div>
            </div>
        </div>
        
        <div class="card card-green">
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-info">
                <h3>Sudah Dikembalikan</h3>
                <div class="number"><?php echo count($loanHistory ?? []); ?></div>
            </div>
        </div>
        
        <div class="card card-warning">
            <div class="card-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="card-info">
                <h3>Request Pending</h3>
                <div class="number">
                    <?php 
                    $pendingCount = 0;
                    if(isset($requests) && is_array($requests)) {
                        foreach($requests as $req) {
                            if($req['status'] == 'pending') $pendingCount++;
                        }
                    }
                    echo $pendingCount;
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
                    if(!empty($activeLoans)) {
                        foreach($activeLoans as $loan) {
                            $today = new DateTime();
                            $deadline = new DateTime($loan['tanggal_kembali']);
                            if($today > $deadline) $overdueCount++;
                        }
                    }
                    echo $overdueCount;
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-bolt"></i> Menu Cepat</h2>
        </div>
        
        <div class="quick-actions">
            <a href="index.php?page=catalog" class="action-btn action-primary">
                <i class="fas fa-search"></i>
                <span>Cari Buku</span>
            </a>
            <a href="index.php?page=my-loans" class="action-btn action-info">
                <i class="fas fa-book-reader"></i>
                <span>Peminjaman Saya</span>
            </a>
            <a href="index.php?page=my-requests" class="action-btn action-warning">
                <i class="fas fa-clipboard-list"></i>
                <span>Request Saya</span>
            </a>
        </div>
    </div>
    
    <!-- Active Loans -->
    <?php if(!empty($activeLoans)): ?>
    <div class="table-container">
        <div class="table-header">
            <h2><i class="fas fa-book-reader"></i> Peminjaman Aktif</h2>
            <a href="index.php?page=my-loans" class="btn btn-secondary btn-sm">
                <i class="fas fa-list"></i> Lihat Semua
            </a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Deadline</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                $displayCount = 0;
                foreach($activeLoans as $loan): 
                    if($displayCount >= 5) break;
                    $displayCount++;
                    
                    $today = new DateTime();
                    $deadline = new DateTime($loan['tanggal_kembali']);
                    $isOverdue = $today > $deadline;
                    
                    $daysRemaining = 0;
                    $daysLate = 0;
                    
                    if($isOverdue) {
                        $diff = $today->diff($deadline);
                        $daysLate = $diff->days;
                    } else {
                        $diff = $deadline->diff($today);
                        $daysRemaining = $diff->days;
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
                        </td>
                        <td>
                            <?php if($isOverdue): ?>
                                <span class="badge badge-danger">
                                    <i class="fas fa-exclamation-circle"></i> Terlambat
                                </span>
                            <?php elseif($daysRemaining <= 3): ?>
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock"></i> Hampir Jatuh Tempo
                                </span>
                            <?php else: ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if(count($activeLoans) > 5): ?>
            <div class="text-center" style="margin-top:15px;">
                <a href="index.php?page=my-loans" class="btn btn-primary">
                    <i class="fas fa-list"></i> Lihat Semua Peminjaman (<?php echo count($activeLoans); ?>)
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-book-reader"></i>
        <h3>Belum Ada Peminjaman Aktif</h3>
        <p>Mulai cari buku yang ingin Anda pinjam</p>
        <a href="index.php?page=catalog" class="btn btn-primary">
            <i class="fas fa-search"></i> Cari Buku
        </a>
    </div>
    <?php endif; ?>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
