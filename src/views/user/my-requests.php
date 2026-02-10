<?php $pageTitle = 'Request Peminjaman Saya - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-clipboard-list"></i> Request Peminjaman Saya</h1>
        <a href="index.php?page=catalog" class="btn btn-primary">
            <i class="fas fa-book"></i> Katalog Buku
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
            <h2><i class="fas fa-list"></i> Daftar Request</h2>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Cover</th>
                    <th>Buku</th>
                    <th>Tanggal Request</th>
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
                                <p style="color:#64748b; margin:10px 0;">Belum ada request peminjaman</p>
                                <a href="index.php?page=catalog" class="btn btn-primary" style="margin-top:15px;">
                                    <i class="fas fa-book"></i> Browse Katalog
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($requests as $req): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <?php if($req['cover_image']): ?>
                                    <img src="<?php echo UPLOAD_URL . 'covers/' . $req['cover_image']; ?>" 
                                         alt="Cover" class="book-cover-small">
                                <?php else: ?>
                                    <div class="no-cover"><i class="fas fa-book"></i></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($req['judul']); ?></strong><br>
                                <small>Kode: <?php echo $req['kode_buku']; ?></small><br>
                                <small>Penulis: <?php echo htmlspecialchars($req['nama_penulis'] ?: '-'); ?></small><br>
                                <small>Kategori: <?php echo htmlspecialchars($req['nama_kategori'] ?: '-'); ?></small>
                            </td>
                            <td>
                                <?php echo formatTanggal(date('Y-m-d', strtotime($req['request_date']))); ?><br>
                                <small><?php echo date('H:i', strtotime($req['request_date'])); ?> WIB</small>
                            </td>
                            <td><?php echo htmlspecialchars($req['keterangan'] ?: '-'); ?></td>
                            <td>
                                <?php if($req['status'] == 'pending'): ?>
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> Menunggu
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
                                    <a href="index.php?page=cancel-request&id=<?php echo $req['request_id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin membatalkan request ini?')">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                <?php elseif($req['status'] == 'approved'): ?>
                                    <a href="index.php?page=my-loans" class="btn btn-success btn-sm">
                                        <i class="fas fa-eye"></i> Lihat Peminjaman
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if(!empty($requests)): ?>
            <div style="margin-top:20px; padding:15px; background:#f8fafc; border-radius:8px;">
                <p style="margin:0; color:#64748b; text-align:center;">
                    <i class="fas fa-info-circle"></i> 
                    Total request: <strong><?php echo count($requests); ?></strong>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
