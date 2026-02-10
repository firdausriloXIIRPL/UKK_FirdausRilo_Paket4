<?php $pageTitle = ($isEdit ? 'Edit' : 'Tambah') . ' Buku - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1>
            <i class="fas fa-book-<?php echo $isEdit ? 'edit' : 'plus'; ?>"></i> 
            <?php echo $isEdit ? 'Edit' : 'Tambah'; ?> Buku
        </h1>
        <a href="index.php?page=admin-books" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="form-container">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kode Buku *</label>
                                <input type="text" name="kode_buku" class="form-control" 
                                       value="<?php echo $book['kode_buku'] ?? ''; ?>" 
                                       <?php echo $isEdit ? 'readonly' : 'required'; ?>>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ISBN</label>
                                <input type="text" name="isbn" class="form-control" 
                                       value="<?php echo $book['isbn'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Judul Buku *</label>
                        <input type="text" name="judul" class="form-control" 
                               value="<?php echo $book['judul'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Penulis</label>
                                <select name="author_id" class="form-control">
                                    <option value="">- Pilih Penulis -</option>
                                    <?php foreach($authors as $author): ?>
                                        <option value="<?php echo $author['author_id']; ?>"
                                                <?php echo ($book['author_id'] ?? '') == $author['author_id'] ? 'selected' : ''; ?>>
                                            <?php echo $author['nama_penulis']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Penerbit</label>
                                <select name="publisher_id" class="form-control">
                                    <option value="">- Pilih Penerbit -</option>
                                    <?php foreach($publishers as $publisher): ?>
                                        <option value="<?php echo $publisher['publisher_id']; ?>"
                                                <?php echo ($book['publisher_id'] ?? '') == $publisher['publisher_id'] ? 'selected' : ''; ?>>
                                            <?php echo $publisher['nama_penerbit']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="category_id" class="form-control">
                                    <option value="">- Pilih Kategori -</option>
                                    <?php foreach($categories as $category): ?>
                                        <option value="<?php echo $category['category_id']; ?>"
                                                <?php echo ($book['category_id'] ?? '') == $category['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo $category['nama_kategori']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tahun Terbit</label>
                                <input type="number" name="tahun_terbit" class="form-control" 
                                       value="<?php echo $book['tahun_terbit'] ?? ''; ?>" min="1900" max="<?php echo date('Y'); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jumlah Halaman</label>
                                <input type="number" name="jumlah_halaman" class="form-control" 
                                       value="<?php echo $book['jumlah_halaman'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Stok Total *</label>
                                <input type="number" name="stok_total" class="form-control" 
                                       value="<?php echo $book['stok_total'] ?? ''; ?>" required min="0">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Stok Tersedia *</label>
                                <input type="number" name="stok_tersedia" class="form-control" 
                                       value="<?php echo $book['stok_tersedia'] ?? ''; ?>" required min="0">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Rak Lokasi</label>
                                <input type="text" name="rak_lokasi" class="form-control" 
                                       value="<?php echo $book['rak_lokasi'] ?? ''; ?>" placeholder="Contoh: A-01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="4"><?php echo $book['deskripsi'] ?? ''; ?></textarea>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Cover Buku</label>
                        <div class="cover-preview">
                            <?php if($isEdit && $book['cover_image']): ?>
                                <img src="<?php echo UPLOAD_URL . 'covers/' . $book['cover_image']; ?>" 
                                     alt="Cover" id="preview">
                            <?php else: ?>
                                <div class="no-cover-large" id="preview">
                                    <i class="fas fa-book"></i>
                                    <p>Tidak ada cover</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="cover_image" class="form-control" accept="image/*" 
                               onchange="previewImage(this)">
                        <small class="form-text">Format: JPG, PNG, GIF. Max: 5MB</small>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="index.php?page=admin-books" class="btn btn-danger">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
