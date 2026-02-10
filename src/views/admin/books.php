<?php $pageTitle = 'Kelola Buku - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-book"></i> Kelola Buku</h1>
        <a href="index.php?page=book-form" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Buku
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
            <form method="GET" action="" class="search-form">
                <input type="hidden" name="page" value="admin-books">
                <div class="search-box">
                    <input type="text" name="search" placeholder="Cari judul atau kode buku..." 
                           value="<?php echo $_GET['search'] ?? ''; ?>" class="form-control">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Cover</th>
                    <th>Kode Buku</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($books)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data buku</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($books as $book): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <?php if($book['cover_image']): ?>
                                    <img src="<?php echo UPLOAD_URL . 'covers/' . $book['cover_image']; ?>" 
                                         alt="Cover" class="book-cover-small">
                                <?php else: ?>
                                    <div class="no-cover"><i class="fas fa-book"></i></div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $book['kode_buku']; ?></td>
                            <td><strong><?php echo $book['judul']; ?></strong></td>
                            <td><?php echo $book['nama_penulis'] ?: '-'; ?></td>
                            <td>
                                <span class="badge badge-info"><?php echo $book['nama_kategori'] ?: '-'; ?></span>
                            </td>
                            <td>
                                <span class="stock-badge">
                                    <?php echo $book['stok_tersedia']; ?> / <?php echo $book['stok_total']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?page=book-form&id=<?php echo $book['book_id']; ?>" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="index.php?page=delete-book&id=<?php echo $book['book_id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Yakin hapus buku ini?')">
                                    <i class="fas fa-trash"></i> Hapus
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
