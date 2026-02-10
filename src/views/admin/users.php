<?php $pageTitle = 'Kelola Anggota - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>
<?php include VIEW_PATH . 'layouts/navbar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-users"></i> Kelola Anggota</h1>
        <a href="index.php?page=user-form" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Anggota
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
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>No Telepon</th>
                    <th>Status</th>
                    <th>Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($users)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data anggota</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($users as $user): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['nama_lengkap']; ?></td>
                            <td><?php echo $user['email'] ?: '-'; ?></td>
                            <td><?php echo $user['no_telepon'] ?: '-'; ?></td>
                            <td>
                                <?php if($user['status'] == 'aktif'): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($user['tanggal_daftar'])); ?></td>
                            <td>
                                <a href="index.php?page=user-form&id=<?php echo $user['user_id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="index.php?page=delete-user&id=<?php echo $user['user_id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Yakin hapus data ini?')">
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
