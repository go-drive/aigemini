<?php
if (file_exists('config.php')) {
    header("Location: index.php");
    exit;
}
$pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $api_key = trim($_POST['api_key']);
    
    $emails = $_POST['email'] ?? [];
    $tokens = $_POST['token'] ?? [];
    $ai_token_arr = [];

    for ($i = 0; $i < count($tokens); $i++) {
        $e = trim($emails[$i] ?? '');
        $t = trim($tokens[$i] ?? '');
        
        if (!empty($e) && !empty($t)) {
            $ai_token_arr[] = $e . '-->' . $t;
        }
    }
    
    $ai_token = implode(',', $ai_token_arr);

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
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E🤖%3C/text%3E%3C/svg%3E">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.8);
            --border-color: rgba(255, 255, 255, 0.1);
            --accent: #38bdf8;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --input-bg: rgba(15, 23, 42, 0.6);
            --btn-text: #0f172a;
            --bg-gradient: radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.1) 0px, transparent 50%);
        }

        body.light-mode {
            --bg-color: #f1f5f9;
            --card-bg: #ffffff;
            --border-color: rgba(0,0,0,0.1);
            --accent: #0284c7;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --input-bg: #f8fafc;
            --btn-text: #ffffff;
            --bg-gradient: radial-gradient(at 0% 0%, rgba(2, 132, 199, 0.05) 0px, transparent 50%);
        }

        body { 
            background-color: var(--bg-color); 
            background-image: var(--bg-gradient);
            color: var(--text-main); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            transition: background-color 0.4s ease, color 0.4s ease;
            position: relative;
        }

        .theme-wrapper {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .btn-theme {
            background: transparent;
            color: var(--text-main);
            border: 1px solid var(--border-color);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-theme:hover { background: var(--border-color); }

        .card { 
            background: var(--card-bg); 
            padding: 35px 30px; 
            border-radius: 16px; 
            border: 1px solid var(--border-color); 
            width: 100%; 
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
            backdrop-filter: blur(10px);
            transition: background-color 0.4s ease, border-color 0.4s ease;
            max-height: 90vh;
            overflow-y: auto;
        }
        h2 { margin-top: 0; color: var(--accent); text-align: center; margin-bottom: 25px; transition: color 0.4s ease; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; margin-bottom: 8px; color: var(--text-muted); font-weight: 600; transition: color 0.4s ease; }
        
        input[type="text"] { 
            width: 100%; 
            padding: 12px 15px; 
            background: var(--input-bg); 
            border: 1px solid var(--border-color); 
            border-radius: 8px; 
            color: var(--text-main); 
            box-sizing: border-box; 
            font-family: inherit;
            transition: all 0.3s ease;
        }
        input[type="text"]:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2); }
        
        .token-row {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
            align-items: center;
        }
        .token-row input {
            flex: 1;
        }
        .btn-icon {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            border: none;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }
        .btn-icon.add { background: #10b981; color: #fff; }
        .btn-icon.add:hover { background: #059669; }
        .btn-icon.remove { background: #ef4444; color: #fff; }
        .btn-icon.remove:hover { background: #dc2626; }

        .btn { 
            width: 100%; 
            padding: 14px; 
            background: var(--accent); 
            color: var(--btn-text); 
            border: none; 
            border-radius: 8px; 
            font-weight: 700; 
            font-size: 14px;
            cursor: pointer; 
            transition: all 0.3s ease; 
            font-family: inherit;
            margin-top: 10px;
        }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }
        
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
        .note { font-size: 11px; color: var(--text-muted); margin-top: 6px; display: block; transition: color 0.4s ease; }
        
        .note a {
            color: inherit;
            text-decoration: none;
            font-weight: 600;
        }
        
        .note a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="theme-wrapper">
        <button id="themeToggle" class="btn-theme" onclick="toggleTheme()">☀️ Light Mode</button>
    </div>

    <div class="card">
        <h2>Setup AI Gemini</h2>
        <?php if($pesan): ?>
            <div class="alert"><?= $pesan ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>API Key Sistem</label>
                <input type="text" name="api_key" placeholder="Contoh: USER_API_KEY_123" required autocomplete="off">
                <span class="note">Dapatkan API Key di <a href="https://village.elyng.com/page/profile" target="_blank">dasbor Village Payment</a>: <strong>Profil</strong> &rarr; <strong>Pengaturan API</strong>.</span>
            </div>
            
            <div class="form-group" id="token-wrapper">
                <label>Token AI Studio</label>
                <div class="token-row">
                    <input type="text" name="email[]" placeholder="timA@email.com" required autocomplete="off">
                    <input type="text" name="token[]" placeholder="Token sk-xxxx" required autocomplete="off">
                    <button type="button" class="btn-icon add" onclick="addTokenRow()">+</button>
                </div>
            </div>
            
            <button type="submit" class="btn">Simpan & Mulai</button>
        </form>
    </div>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        let isLightMode = localStorage.getItem('ai_theme') === 'light';

        function applyTheme() {
            if (isLightMode) {
                document.body.classList.add('light-mode');
                themeToggle.innerHTML = '🌙 Dark Mode';
            } else {
                document.body.classList.remove('light-mode');
                themeToggle.innerHTML = '☀️ Light Mode';
            }
        }

        function toggleTheme() {
            isLightMode = !isLightMode;
            localStorage.setItem('ai_theme', isLightMode ? 'light' : 'dark');
            applyTheme();
        }
        
        applyTheme();

        function addTokenRow() {
            const wrapper = document.getElementById('token-wrapper');
            const newRow = document.createElement('div');
            newRow.className = 'token-row';
            newRow.innerHTML = `
                <input type="text" name="email[]" placeholder="timB@email.com" required autocomplete="off">
                <input type="text" name="token[]" placeholder="Token sk-xxxx" required autocomplete="off">
                <button type="button" class="btn-icon remove" onclick="this.parentElement.remove()">-</button>
            `;
            wrapper.appendChild(newRow);
        }
    </script>
</body>
</html>
