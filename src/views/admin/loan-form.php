<?php $pageTitle = 'Pinjam Buku - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-book-reader"></i> Pinjam Buku</h1>
        <a href="index.php?page=admin-loans" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="form-container">
        <form method="POST" action="">
            <div class="form-group">
                <label>Anggota *</label>
                <select name="user_id" class="form-control" required>
                    <option value="">- Pilih Anggota -</option>
                    <?php foreach($users as $user): ?>
                        <option value="<?php echo $user['user_id']; ?>">
                            <?php echo $user['nama_lengkap']; ?> (<?php echo $user['username']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Buku *</label>
                <select name="book_id" class="form-control" required>
                    <option value="">- Pilih Buku -</option>
                    <?php foreach($books as $book): ?>
                        <option value="<?php echo $book['book_id']; ?>" 
                                <?php echo $book['stok_tersedia'] <= 0 ? 'disabled' : ''; ?>>
                            <?php echo $book['kode_buku']; ?> - <?php echo $book['judul']; ?> 
                            (Stok: <?php echo $book['stok_tersedia']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" 
                          placeholder="Catatan tambahan (opsional)"></textarea>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>Informasi:</strong> Lama peminjaman akan disesuaikan dengan pengaturan sistem.
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Proses Peminjaman
                </button>
                <a href="index.php?page=admin-loans" class="btn btn-danger">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
