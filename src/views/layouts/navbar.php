<div class="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-book"></i> <?php echo APP_NAME; ?></h3>
        <small><?php echo isAdmin() ? 'Admin Panel' : 'Member Area'; ?></small>
    </div>
    <ul class="sidebar-menu">
        <?php if(isAdmin()): ?>
            <!-- ADMIN MENU -->
            <li>
                <a href="index.php?page=admin-dashboard" class="<?php echo ($_GET['page'] ?? '') == 'admin-dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="index.php?page=admin-users" class="<?php echo ($_GET['page'] ?? '') == 'admin-users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Kelola Anggota
                </a>
            </li>
            <li>
                <a href="index.php?page=admin-books" class="<?php echo ($_GET['page'] ?? '') == 'admin-books' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i> Kelola Buku
                </a>
            </li>
            <li>
                <a href="index.php?page=admin-requests" class="<?php echo ($_GET['page'] ?? '') == 'admin-requests' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i> Request Peminjaman
                    <?php
                    // Badge notifikasi untuk pending requests
                    try {
                        $stmt = $GLOBALS['db']->query("SELECT COUNT(*) as total FROM loan_requests WHERE status = 'pending'");
                        $pending = $stmt->fetch();
                        if($pending['total'] > 0):
                    ?>
                        <span style="background:#f59e0b; color:white; padding:2px 8px; border-radius:10px; font-size:11px; margin-left:5px;">
                            <?php echo $pending['total']; ?>
                        </span>
                    <?php 
                        endif;
                    } catch(Exception $e) {}
                    ?>
                </a>
            </li>
            <li>
                <a href="index.php?page=admin-loans" class="<?php echo ($_GET['page'] ?? '') == 'admin-loans' ? 'active' : ''; ?>">
                    <i class="fas fa-book-reader"></i> Peminjaman
                </a>
            </li>
            <li>
                <a href="index.php?page=loan-history" class="<?php echo ($_GET['page'] ?? '') == 'loan-history' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i> Riwayat
                </a>
            </li>
            
        <?php else: ?>
            <!-- USER/MEMBER MENU -->
            <li>
                <a href="index.php?page=user-dashboard" class="<?php echo ($_GET['page'] ?? '') == 'user-dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="index.php?page=catalog" class="<?php echo ($_GET['page'] ?? '') == 'catalog' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i> Katalog Buku
                </a>
            </li>
            <li>
                <a href="index.php?page=my-requests" class="<?php echo ($_GET['page'] ?? '') == 'my-requests' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i> Request Saya
                    <?php
                    // Badge untuk pending requests user
                    if(isset($_SESSION['user_id'])):
                        try {
                            $user_id = $_SESSION['user_id'];
                            $stmt = $GLOBALS['db']->prepare("SELECT COUNT(*) as total FROM loan_requests WHERE user_id = :user_id AND status = 'pending'");
                            $stmt->bindParam(':user_id', $user_id);
                            $stmt->execute();
                            $pending = $stmt->fetch();
                            if($pending['total'] > 0):
                    ?>
                        <span style="background:#f59e0b; color:white; padding:2px 8px; border-radius:10px; font-size:11px; margin-left:5px;">
                            <?php echo $pending['total']; ?>
                        </span>
                    <?php 
                            endif;
                        } catch(Exception $e) {}
                    endif;
                    ?>
                </a>
            </li>
            <li>
                <a href="index.php?page=my-loans" class="<?php echo ($_GET['page'] ?? '') == 'my-loans' ? 'active' : ''; ?>">
                    <i class="fas fa-book-reader"></i> Peminjaman Saya
                </a>
            </li>
        <?php endif; ?>
        
        <!-- LOGOUT (untuk semua) -->
        <li style="margin-top:20px; padding-top:20px; border-top:1px solid #e2e8f0;">
            <a href="index.php?page=logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>
