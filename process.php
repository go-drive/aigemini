<?php
if (!file_exists('config.php')) {
    echo json_encode(['status' => false, 'data' => ['pesan' => 'Aplikasi belum dikonfigurasi.']]);
    exit;
}
require 'config.php';
header('Content-Type: application/json');
$pesan = $_POST['pesan'] ?? '';
if (empty($pesan)) {
    echo json_encode(['status' => false, 'data' => ['pesan' => 'Pesan tidak boleh kosong.']]);
    exit;
}
$post_data = [
    'action'    => 'respon',
    'code'      => API_KEY_SYSTEM,
    'api_token' => AI_TOKENS,
    'pertama'   => 'Kamu adalah asisten AI yang cerdas, ramah, dan sangat membantu.',
    'kedua'     => '',
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
    echo $response;
}
?>
