<?php $pageTitle = 'Login - ' . APP_NAME; ?>
<?php include VIEW_PATH . 'layouts/header.php'; ?>

<div class="login-container">
    <div class="login-box">
        <div class="login-header">
            <i class="fas fa-book-open"></i>
            <h2><?php echo APP_NAME; ?></h2>
            <p>Sistem Manajemen Perpustakaan</p>
        </div>
        
        <?php 
        $flash = getFlashMessage();
        if ($flash): 
        ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?page=login">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" class="form-control" required autofocus placeholder="Masukkan username">
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>
        
        <div class="login-footer">
            <small><i class="fas fa-info-circle"></i> Default: admin / admin123</small>
        </div>
    </div>
</div>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>
