<?php
session_start();

if (!file_exists('config.php')) {
    echo json_encode(['status' => false, 'data' => ['pesan' => 'Aplikasi belum dikonfigurasi.']]);
    exit;
}

require 'config.php';
header('Content-Type: application/json');

$pesan = trim($_POST['pesan'] ?? '');
if (empty($pesan)) {
    echo json_encode(['status' => false, 'data' => ['pesan' => 'Pesan tidak boleh kosong.']]);
    exit;
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
    'gambarUrl' => '',
    'requestID' => 'REQ-UI-' . uniqid() 
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, API_GATEWAY_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

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
