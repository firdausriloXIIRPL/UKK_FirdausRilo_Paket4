<?php $pageTitle = 'Dashboard Admin - ' . APP_NAME; ?>
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
    
    <div class="dashboard-cards">
        <div class="card card-blue">
            <div class="card-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="card-info">
                <h3>Total Buku</h3>
                <div class="number"><?php echo $totalBooks; ?></div>
            </div>
        </div>
        
        <div class="card card-green">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-info">
                <h3>Total Anggota</h3>
                <div class="number"><?php echo $totalMembers; ?></div>
            </div>
        </div>
        
        <div class="card card-orange">
            <div class="card-icon">
                <i class="fas fa-book-reader"></i>
            </div>
            <div class="card-info">
                <h3>Buku Dipinjam</h3>
                <div class="number"><?php echo $totalLoans; ?></div>
            </div>
        </div>
        
        <div class="card card-red">
            <div class="card-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="card-info">
                <h3>Terlambat</h3>
                <div class="number"><?php echo $totalOverdue; ?></div>
            </div>
        </div>
    </div>
    
    <div class="table-container">
        <div class="table-header">
            <h2><i class="fas fa-list"></i> Peminjaman Aktif</h2>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Anggota</th>
                    <th>Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Batas Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($recentLoans)): ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada peminjaman aktif</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($recentLoans as $loan): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $loan['nama_anggota']; ?></td>
                            <td><?php echo $loan['judul_buku']; ?></td>
                            <td><?php echo formatTanggal($loan['tanggal_pinjam']); ?></td>
                            <td><?php echo formatTanggal($loan['tanggal_kembali_rencana']); ?></td>
                            <td>
                                <?php if($loan['tanggal_kembali_rencana'] < date('Y-m-d')): ?>
                                    <span class="badge badge-danger">
                                        <i class="fas fa-exclamation-circle"></i> Terlambat
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Aktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?page=return-book&id=<?php echo $loan['loan_id']; ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-undo"></i> Kembalikan
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
