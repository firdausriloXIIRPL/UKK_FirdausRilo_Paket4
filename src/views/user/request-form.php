<?php $pageTitle = 'Request Peminjaman - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-hand-holding"></i> Request Peminjaman Buku</h1>
        <a href="index.php?page=catalog" class="btn btn-secondary">
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
        <div class="row">
            <div class="col-md-4">
                <div class="cover-preview">
                    <?php if($book['cover_image']): ?>
                        <img src="<?php echo UPLOAD_URL . 'covers/' . $book['cover_image']; ?>" 
                             alt="<?php echo htmlspecialchars($book['judul']); ?>">
                    <?php else: ?>
                        <div class="no-cover-large">
                            <i class="fas fa-book"></i>
                            <p>Tidak ada cover</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="info-box">
                    <h3><i class="fas fa-book"></i> Detail Buku</h3>
                    <table class="info-table">
                        <tr>
                            <td><strong>Kode Buku</strong></td>
                            <td>: <?php echo htmlspecialchars($book['kode_buku']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Judul</strong></td>
                            <td>: <?php echo htmlspecialchars($book['judul']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Penulis</strong></td>
                            <td>: <?php echo htmlspecialchars($book['nama_penulis'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kategori</strong></td>
                            <td>: <?php echo htmlspecialchars($book['nama_kategori'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Penerbit</strong></td>
                            <td>: <?php echo htmlspecialchars($book['nama_penerbit'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tahun Terbit</strong></td>
                            <td>: <?php echo $book['tahun_terbit'] ?: '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Stok Tersedia</strong></td>
                            <td>: <strong style="color: var(--success);"><?php echo $book['stok_tersedia']; ?></strong> dari <?php echo $book['stok_total']; ?> buku</td>
                        </tr>
                    </table>
                </div>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Keterangan / Catatan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" rows="4" 
                                  placeholder="Contoh: Butuh untuk tugas kuliah, dll..."></textarea>
                        <small class="form-text">Tambahkan catatan jika diperlukan</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi Peminjaman:</strong>
                        <ul style="margin:10px 0 0 20px; line-height:1.8;">
                            <li>Request akan diproses oleh petugas perpustakaan</li>
                            <li>Anda akan mendapat konfirmasi setelah disetujui</li>
                            <li>Buku dapat diambil setelah request disetujui</li>
                            <li>Maksimal peminjaman sesuai dengan aturan perpustakaan</li>
                        </ul>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Kirim Request
                        </button>
                        <a href="index.php?page=catalog" class="btn btn-danger">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
