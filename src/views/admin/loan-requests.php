<?php $pageTitle = 'Request Peminjaman - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-clipboard-list"></i> Request Peminjaman</h1>
        <div class="user-info">
            <span><i class="fas fa-user"></i> <?php echo $_SESSION['nama_lengkap']; ?></span>
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
    
    <div class="table-container">
        <div class="table-header">
            <h2><i class="fas fa-filter"></i> Filter Status</h2>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="index.php?page=admin-requests&status=pending" 
                   class="btn btn-<?php echo ($_GET['status'] ?? 'pending') == 'pending' ? 'warning' : 'secondary'; ?> btn-sm">
                    <i class="fas fa-clock"></i> Pending
                    <?php
                    $stmt = $GLOBALS['db']->query("SELECT COUNT(*) as total FROM loan_requests WHERE status = 'pending'");
                    $pending_count = $stmt->fetch()['total'];
                    if($pending_count > 0):
                    ?>
                        <span style="background:rgba(255,255,255,0.3); padding:2px 8px; border-radius:10px; margin-left:5px;">
                            <?php echo $pending_count; ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="index.php?page=admin-requests&status=approved" 
                   class="btn btn-<?php echo ($_GET['status'] ?? '') == 'approved' ? 'success' : 'secondary'; ?> btn-sm">
                    <i class="fas fa-check"></i> Disetujui
                </a>
                <a href="index.php?page=admin-requests&status=rejected" 
                   class="btn btn-<?php echo ($_GET['status'] ?? '') == 'rejected' ? 'danger' : 'secondary'; ?> btn-sm">
                    <i class="fas fa-times"></i> Ditolak
                </a>
                <a href="index.php?page=admin-requests" 
                   class="btn btn-<?php echo !isset($_GET['status']) ? 'info' : 'secondary'; ?> btn-sm">
                    <i class="fas fa-list"></i> Semua
                </a>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Request</th>
                    <th>Anggota</th>
                    <th>Buku</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($requests)): ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <div style="padding:40px 20px;">
                                <i class="fas fa-inbox" style="font-size:48px; color:#cbd5e1; margin-bottom:15px;"></i>
                                <p style="color:#64748b; margin:0;">
                                    <?php if(isset($_GET['status'])): ?>
                                        Tidak ada request dengan status: <strong><?php echo $_GET['status']; ?></strong>
                                    <?php else: ?>
                                        Belum ada request peminjaman
                                    <?php endif; ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($requests as $req): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <?php echo formatTanggal(date('Y-m-d', strtotime($req['request_date']))); ?><br>
                                <small><?php echo date('H:i', strtotime($req['request_date'])); ?> WIB</small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($req['nama_lengkap']); ?></strong><br>
                                <small><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($req['email']); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($req['judul']); ?></strong><br>
                                <small>Kode: <?php echo $req['kode_buku']; ?></small><br>
                                <small>Penulis: <?php echo htmlspecialchars($req['nama_penulis'] ?: '-'); ?></small>
                            </td>
                            <td>
                                <?php if($req['keterangan']): ?>
                                    <span title="<?php echo htmlspecialchars($req['keterangan']); ?>">
                                        <?php echo mb_substr(htmlspecialchars($req['keterangan']), 0, 50); ?>
                                        <?php echo strlen($req['keterangan']) > 50 ? '...' : ''; ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:#94a3b8;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($req['status'] == 'pending'): ?>
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                <?php elseif($req['status'] == 'approved'): ?>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Disetujui
                                    </span>
                                    <?php if($req['approved_date']): ?>
                                        <br><small><?php echo formatTanggal(date('Y-m-d', strtotime($req['approved_date']))); ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times"></i> Ditolak
                                    </span>
                                    <?php if($req['response_note']): ?>
                                        <br><small title="<?php echo htmlspecialchars($req['response_note']); ?>">
                                            <i class="fas fa-comment"></i> Ada catatan
                                        </small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($req['status'] == 'pending'): ?>
                                    <a href="index.php?page=approve-request&id=<?php echo $req['request_id']; ?>" 
                                       class="btn btn-success btn-sm"
                                       onclick="return confirm('Setujui request ini?\n\nRequest akan otomatis menjadi peminjaman dan stok buku akan berkurang.')">
                                        <i class="fas fa-check"></i> Setujui
                                    </a>
                                    <a href="index.php?page=reject-request&id=<?php echo $req['request_id']; ?>" 
                                       class="btn btn-danger btn-sm">
                                        <i class="fas fa-times"></i> Tolak
                                    </a>
                                <?php elseif($req['status'] == 'approved'): ?>
                                    <a href="index.php?page=admin-loans" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Lihat Peminjaman
                                    </a>
                                <?php else: ?>
                                    <span style="color:#94a3b8;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if(!empty($requests)): ?>
            <div style="margin-top:20px; padding:15px; background:#f8fafc; border-radius:8px; text-align:center;">
                <p style="margin:0; color:#64748b;">
                    <i class="fas fa-info-circle"></i> 
                    Menampilkan <strong><?php echo count($requests); ?></strong> request
                    <?php if(isset($_GET['status'])): ?>
                        dengan status: <strong><?php echo $_GET['status']; ?></strong>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
