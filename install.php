<?php
if (file_exists('config.php')) {
    header("Location: index.php");
    exit;
}
$pesan = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $api_key = trim($_POST['api_key']);
    $ai_token = trim($_POST['ai_token']);
    if (!empty($api_key) && !empty($ai_token)) {
        $config_content = "<?php\n";
        $config_content .= "define('API_KEY_SYSTEM', '" . addslashes($api_key) . "');\n";
        $config_content .= "define('AI_TOKENS', '" . addslashes($ai_token) . "');\n";
        $config_content .= "define('API_GATEWAY_URL', 'https://village.elyng.com/api/ai.php');\n";
        $config_content .= "?>";
        if (file_put_contents('config.php', $config_content)) {
            header("Location: index.php");
            exit;
        } else {
            $pesan = "Gagal menyimpan konfigurasi. Pastikan folder aplikasi ini memiliki izin tulis (write permissions).";
        }
    } else {
        $pesan = "Semua kolom wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi AI Gemini</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.8);
            --border: rgba(255, 255, 255, 0.1);
            --accent: #38bdf8;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }
        body { 
            background-color: var(--bg-dark); 
            background-image: radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.1) 0px, transparent 50%);
            color: var(--text-main); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .card { 
            background: var(--card-bg); 
            padding: 35px 30px; 
            border-radius: 16px; 
            border: 1px solid var(--border); 
            width: 100%; 
            max-width: 400px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
            backdrop-filter: blur(10px);
        }
        h2 { margin-top: 0; color: var(--accent); text-align: center; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; margin-bottom: 8px; color: var(--text-muted); font-weight: 600;}
        input[type="text"] { 
            width: 100%; 
            padding: 12px 15px; 
            background: rgba(15, 23, 42, 0.6); 
            border: 1px solid #334155; 
            border-radius: 8px; 
            color: #fff; 
            box-sizing: border-box; 
            font-family: inherit;
            transition: 0.3s;
        }
        input[type="text"]:focus { outline: none; border-color: var(--accent); }
        .btn { 
            width: 100%; 
            padding: 14px; 
            background: var(--accent); 
            color: var(--bg-dark); 
            border: none; 
            border-radius: 8px; 
            font-weight: 700; 
            font-size: 14px;
            cursor: pointer; 
            transition: 0.3s; 
            font-family: inherit;
        }
        .btn:hover { background: #0284c7; color: #fff; }
        .alert { 
            background: rgba(239, 68, 68, 0.1); 
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 12px; 
            border-radius: 8px; 
            font-size: 13px; 
            margin-bottom: 20px; 
            text-align: center; 
        }
        .note { font-size: 11px; color: var(--text-muted); margin-top: 6px; display: block; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Setup AI Gemini</h2>
        <?php if($pesan): ?>
            <div class="alert"><?= $pesan ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>API Key Sistem</label>
                <input type="text" name="api_key" placeholder="Contoh: USER_API_KEY_123" required autocomplete="off">
                <span class="note">Dapatkan API Key di dasbor Village Payment: <strong>Profil</strong> &rarr; <strong>Pengaturan API</strong>.</span>
            </div>
            <div class="form-group">
                <label>Token AI (Mandiri)</label>
                <input type="text" name="ai_token" placeholder="sk-xxxx atau timA@email.com-->sk-xxxx" required autocomplete="off">
                <span class="note">Pisahkan dengan koma (,) jika memiliki lebih dari satu token.</span>
            </div>
            <button type="submit" class="btn">Simpan & Mulai</button>
        </form>
    </div>
</body>
</html>
