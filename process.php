<?php
session_start();

if (!file_exists('config.php')) {
    echo json_encode(['status' => false, 'data' => ['pesan' => 'Aplikasi belum dikonfigurasi.']]);
    exit;
}

require 'config.php';
header('Content-Type: application/json');

$pesan = trim($_POST['pesan'] ?? '');
$has_file = isset($_FILES['file_lampiran']) && $_FILES['file_lampiran']['error'] === UPLOAD_ERR_OK;

if (empty($pesan) && !$has_file) {
    echo json_encode(['status' => false, 'data' => ['pesan' => 'Pesan atau lampiran tidak boleh kosong.']]);
    exit;
}

$gambarBase64 = '';
$mime_type_aman = '';

if ($has_file) {
    $tmp_name = $_FILES['file_lampiran']['tmp_name'];
    $ukuran = $_FILES['file_lampiran']['size'];

    if ($ukuran > 5 * 1024 * 1024) {
        echo json_encode(['status' => false, 'data' => ['pesan' => 'Ukuran file terlalu besar (Max 5MB).']]);
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $real_mime = finfo_file($finfo, $tmp_name);
    finfo_close($finfo);

    $allowed_mimes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'audio/mpeg',
        'audio/ogg',
        'audio/wav',
        'audio/x-m4a',
        'application/pdf'
    ];

    if (!in_array($real_mime, $allowed_mimes)) {
        echo json_encode(['status' => false, 'data' => ['pesan' => 'Format file tidak valid atau berpotensi bahaya.']]);
        exit;
    }

    $isi_file = file_get_contents($tmp_name);
    $gambarBase64 = "data:{$real_mime};base64," . base64_encode($isi_file);
    $mime_type_aman = $real_mime;
}

if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = "";
}
$konteks_sebelumnya = $_SESSION['chat_history'];

$post_data = [
    'action'    => 'respon',
    'code'      => API_KEY_SYSTEM,
    'api_token' => AI_TOKENS,
    'pertama'   => 'Kamu adalah asisten AI yang cerdas, ramah, dan sangat membantu. Jawablah layaknya manusia.',
    'kedua'     => $konteks_sebelumnya,
    'ketiga'    => $pesan,
    'gambarUrl' => $gambarBase64,
    'mime_type' => $mime_type_aman,
    'requestID' => 'REQ-UI-' . uniqid() 
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, API_GATEWAY_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 70); 

$response = curl_exec($ch);
$curl_err = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo json_encode([
        'status' => false, 
        'data' => ['pesan' => 'Gagal terhubung ke Server API Pusat: ' . $curl_err]
    ]);
} else {
    $result = json_decode($response, true);
    
    if (is_array($result) && isset($result['status']) && $result['status'] === true && !empty($result['data']['pesan'])) {
        $jawaban_ai = $result['data']['pesan'];
        
        $riwayat_tambahan = "\nUser: " . $pesan . "\nAI: " . $jawaban_ai;
        $_SESSION['chat_history'] .= $riwayat_tambahan;
        
        if (strlen($_SESSION['chat_history']) > 2000) {
            $_SESSION['chat_history'] = substr($_SESSION['chat_history'], -2000);
        }
    }
    
    echo $response;
}
?>
