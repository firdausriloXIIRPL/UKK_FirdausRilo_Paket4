<?php $pageTitle = 'Riwayat Peminjaman - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-history"></i> Riwayat Peminjaman</h1>
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
    
    <div class="table-container">
        <div class="table-header">
            <h2><i class="fas fa-list"></i> Daftar Buku yang Sudah Dikembalikan</h2>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Anggota</th>
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
                        <td colspan="8" class="text-center">
                            <div style="padding:40px 20px;">
                                <i class="fas fa-inbox" style="font-size:48px; color:#cbd5e1; margin-bottom:15px;"></i>
                                <p style="color:#64748b; margin:0;">
                                    Belum ada riwayat peminjaman
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $no = 1; 
                    foreach($loans as $loan): 
                        // Calculate if it was late
                        $wasLate = false;
                        $daysLate = 0;
                        
                        if($loan['tanggal_pengembalian']) {
                            $deadline = new DateTime($loan['tanggal_kembali']);
                            $returned = new DateTime($loan['tanggal_pengembalian']);
                            
                            if($returned > $deadline) {
                                $wasLate = true;
                                $diff = $returned->diff($deadline);
                                $daysLate = $diff->days;
                            }
                        }
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($loan['nama_anggota']); ?></strong><br>
                                <small><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($loan['email']); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($loan['judul_buku']); ?></strong><br>
                                <small>Kode: <?php echo $loan['kode_buku']; ?></small>
                                <?php if(!empty($loan['nama_penulis'])): ?>
                                    <br><small>Penulis: <?php echo htmlspecialchars($loan['nama_penulis']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo formatTanggal($loan['tanggal_pinjam']); ?></td>
                            <td><?php echo formatTanggal($loan['tanggal_kembali']); ?></td>
                            <td>
                                <?php if($loan['tanggal_pengembalian']): ?>
                                    <strong><?php echo formatTanggal($loan['tanggal_pengembalian']); ?></strong>
                                    <?php if($wasLate): ?>
                                        <br><small class="text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            Terlambat <?php echo $daysLate; ?> hari
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
                                    <span class="text-danger" style="font-weight:600; font-size:14px;">
                                        Rp <?php echo number_format($loan['denda'], 0, ',', '.'); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-success" style="font-weight:600;">
                                        Rp 0
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Dikembalikan
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if(!empty($loans)): ?>
            <div style="margin-top:20px; padding:20px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;">
                <?php
                // Calculate statistics
                $totalDenda = 0;
                $tepatWaktu = 0;
                $terlambat = 0;
                $totalHariTerlambat = 0;
                
                foreach($loans as $loan) {
                    $totalDenda += $loan['denda'];
                    
                    if($loan['tanggal_pengembalian']) {
                        $deadline = new DateTime($loan['tanggal_kembali']);
                        $returned = new DateTime($loan['tanggal_pengembalian']);
                        
                        if($returned > $deadline) {
                            $terlambat++;
                            $diff = $returned->diff($deadline);
                            $totalHariTerlambat += $diff->days;
                        } else {
                            $tepatWaktu++;
                        }
                    }
                }
                
                $total = count($loans);
                $persentaseTepat = $total > 0 ? round(($tepatWaktu / $total) * 100, 1) : 0;
                $rataHariTerlambat = $terlambat > 0 ? round($totalHariTerlambat / $terlambat, 1) : 0;
                ?>
                
                <h3 style="margin:0 0 20px 0; color:#1e293b; font-size:18px;">
                    <i class="fas fa-chart-bar"></i> Statistik Riwayat Peminjaman
                </h3>
                
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:15px;">
                    <!-- Total Riwayat -->
                    <div style="background:white; padding:20px; border-radius:8px; border:1px solid #e2e8f0; text-align:center;">
                        <div style="font-size:32px; font-weight:bold; color:#3b82f6; margin-bottom:5px;">
                            <?php echo $total; ?>
                        </div>
                        <div style="font-size:14px; color:#64748b;">
                            Total Peminjaman
                        </div>
                    </div>
                    
                    <!-- Tepat Waktu -->
                    <div style="background:white; padding:20px; border-radius:8px; border:1px solid #e2e8f0; text-align:center;">
                        <div style="font-size:32px; font-weight:bold; color:#10b981; margin-bottom:5px;">
                            <?php echo $tepatWaktu; ?>
                        </div>
                        <div style="font-size:14px; color:#64748b;">
                            Tepat Waktu
                        </div>
                        <div style="font-size:12px; color:#10b981; margin-top:5px;">
                            <?php echo $persentaseTepat; ?>%
                        </div>
                    </div>
                    
                    <!-- Terlambat -->
                    <div style="background:white; padding:20px; border-radius:8px; border:1px solid #e2e8f0; text-align:center;">
                        <div style="font-size:32px; font-weight:bold; color:#ef4444; margin-bottom:5px;">
                            <?php echo $terlambat; ?>
                        </div>
                        <div style="font-size:14px; color:#64748b;">
                            Terlambat
                        </div>
                        <?php if($rataHariTerlambat > 0): ?>
                            <div style="font-size:12px; color:#ef4444; margin-top:5px;">
                                Rata-rata: <?php echo $rataHariTerlambat; ?> hari
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Total Denda -->
                    <div style="background:white; padding:20px; border-radius:8px; border:1px solid #e2e8f0; text-align:center;">
                        <div style="font-size:24px; font-weight:bold; color:#f59e0b; margin-bottom:5px;">
                            Rp <?php echo number_format($totalDenda, 0, ',', '.'); ?>
                        </div>
                        <div style="font-size:14px; color:#64748b;">
                            Total Denda
                        </div>
                        <?php if($terlambat > 0): ?>
                            <div style="font-size:12px; color:#f59e0b; margin-top:5px;">
                                Rata-rata: Rp <?php echo number_format($totalDenda / $terlambat, 0, ',', '.'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
