<?php $pageTitle = ($isEdit ? 'Edit' : 'Tambah') . ' Anggota - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1>
            <i class="fas fa-user-<?php echo $isEdit ? 'edit' : 'plus'; ?>"></i> 
            <?php echo $isEdit ? 'Edit' : 'Tambah'; ?> Anggota
        </h1>
        <a href="index.php?page=admin-users" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="form-container">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Username *</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?php echo $user['username'] ?? ''; ?>" 
                               <?php echo $isEdit ? 'readonly' : 'required'; ?>>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Password <?php echo $isEdit ? '(Kosongkan jika tidak diubah)' : '*'; ?></label>
                        <input type="password" name="password" class="form-control" 
                               <?php echo $isEdit ? '' : 'required'; ?>>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" class="form-control" 
                       value="<?php echo $user['nama_lengkap'] ?? ''; ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo $user['email'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>No Telepon</label>
                        <input type="text" name="no_telepon" class="form-control" 
                               value="<?php echo $user['no_telepon'] ?? ''; ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="4"><?php echo $user['alamat'] ?? ''; ?></textarea>
            </div>
            
            <?php if($isEdit): ?>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="aktif" <?php echo ($user['status'] ?? '') == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="nonaktif" <?php echo ($user['status'] ?? '') == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                </select>
            </div>
            <?php endif; ?>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="index.php?page=admin-users" class="btn btn-danger">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
