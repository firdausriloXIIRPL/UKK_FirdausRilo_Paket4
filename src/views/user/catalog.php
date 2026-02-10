<?php $pageTitle = 'Katalog Buku - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-book"></i> Katalog Buku</h1>
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
            <form method="GET" action="" class="search-form">
                <input type="hidden" name="page" value="catalog">
                <div class="search-box">
                    <input type="text" name="search" placeholder="Cari judul atau kode buku..." 
                           value="<?php echo $_GET['search'] ?? ''; ?>" class="form-control">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
                        <a href="index.php?page=catalog" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="book-grid">
            <?php if(empty($books)): ?>
                <div style="text-align:center; padding:60px 20px; width:100%;">
                    <i class="fas fa-book-open" style="font-size:64px; color:#cbd5e1; margin-bottom:20px;"></i>
                    <p style="color:#64748b; font-size:18px;">Tidak ada buku ditemukan</p>
                    <?php if(isset($_GET['search'])): ?>
                        <a href="index.php?page=catalog" class="btn btn-primary" style="margin-top:15px;">
                            <i class="fas fa-arrow-left"></i> Kembali ke Katalog
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach($books as $book): ?>
                    <div class="book-card">
                        <div class="book-cover">
                            <?php if($book['cover_image']): ?>
                                <img src="<?php echo UPLOAD_URL . 'covers/' . $book['cover_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($book['judul']); ?>">
                            <?php else: ?>
                                <div class="no-cover-card">
                                    <i class="fas fa-book"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h3 title="<?php echo htmlspecialchars($book['judul']); ?>">
                                <?php echo htmlspecialchars($book['judul']); ?>
                            </h3>
                            <p class="book-author">
                                <i class="fas fa-user"></i> 
                                <?php echo htmlspecialchars($book['nama_penulis'] ?: 'Tidak diketahui'); ?>
                            </p>
                            <p class="book-category">
                                <i class="fas fa-tag"></i> 
                                <?php echo htmlspecialchars($book['nama_kategori'] ?: 'Tidak berkategori'); ?>
                            </p>
                            
                            <?php if(!empty($book['tahun_terbit'])): ?>
                                <p class="book-year">
                                    <i class="fas fa-calendar"></i> 
                                    <?php echo $book['tahun_terbit']; ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="book-stock">
                                <span class="stock-label">Stok:</span>
                                <span class="stock-value <?php echo $book['stok_tersedia'] > 0 ? 'available' : 'unavailable'; ?>">
                                    <?php echo $book['stok_tersedia']; ?> / <?php echo $book['stok_total']; ?>
                                </span>
                            </div>
                            
                            <!-- Tombol Request Peminjaman -->
                            <div class="book-actions">
                                <?php if($book['stok_tersedia'] > 0): ?>
                                    <a href="index.php?page=request-loan&book_id=<?php echo $book['book_id']; ?>" 
                                       class="btn btn-primary btn-sm btn-block">
                                        <i class="fas fa-hand-holding"></i> Request Pinjam
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm btn-block" disabled>
                                        <i class="fas fa-times-circle"></i> Stok Habis
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if(!empty($books)): ?>
            <div style="text-align:center; margin-top:30px; padding:20px; background:#f8fafc; border-radius:8px;">
                <p style="color:#64748b; margin:0;">
                    <i class="fas fa-info-circle"></i> 
                    Menampilkan <?php echo count($books); ?> buku
                    <?php if(isset($_GET['search'])): ?>
                        untuk pencarian: <strong>"<?php echo htmlspecialchars($_GET['search']); ?>"</strong>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
