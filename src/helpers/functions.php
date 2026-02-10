<?php
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function formatTanggal($tanggal) {
    if (empty($tanggal)) return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function uploadImage($file, $folder = 'covers') {
    $targetDir = UPLOAD_PATH . $folder . '/';
    
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
    $targetFile = $targetDir . $fileName;
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File terlalu besar'];
    }
    
    if (!in_array($fileExtension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Format file tidak didukung'];
    }
    
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'filename' => $fileName];
    }
    
    return ['success' => false, 'message' => 'Gagal upload file'];
}

function hitungDenda($tanggal_kembali_rencana, $tanggal_kembali_aktual) {
    global $db;
    
    $stmt = $db->prepare("SELECT nilai FROM settings WHERE nama_setting = 'denda_per_hari'");
    $stmt->execute();
    $setting = $stmt->fetch();
    $denda_per_hari = $setting['nilai'];
    
    $date1 = new DateTime($tanggal_kembali_rencana);
    $date2 = new DateTime($tanggal_kembali_aktual);
    
    if ($date2 > $date1) {
        $diff = $date2->diff($date1);
        return $diff->days * $denda_per_hari;
    }
    
    return 0;
}
?>
