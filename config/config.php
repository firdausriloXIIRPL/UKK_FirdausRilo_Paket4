<?php
// Timezone
date_default_timezone_set('Asia/Jakarta');

// Base URL
define('BASE_URL', 'http://localhost/perpusrlo/public/');
define('ASSET_URL', BASE_URL . 'assets/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

// Paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('SRC_PATH', ROOT_PATH . 'src/');
define('VIEW_PATH', SRC_PATH . 'views/');
define('UPLOAD_PATH', ROOT_PATH . 'public/uploads/');

// App Settings
define('APP_NAME', 'Perpustakaan RLO');
define('APP_VERSION', '1.0.0');

// Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Autoload classes
spl_autoload_register(function($class) {
    $paths = [
        SRC_PATH . 'controllers/' . $class . '.php',
        SRC_PATH . 'models/' . $class . '.php',
        SRC_PATH . 'helpers/' . $class . '.php'
    ];
    
    foreach($paths as $path) {
        if(file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Include helpers
require_once SRC_PATH . 'helpers/functions.php';
require_once SRC_PATH . 'helpers/session.php';
require_once SRC_PATH . 'helpers/validation.php';
?>
