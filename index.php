<?php
if (!file_exists('config.php')) {
    header("Location: install.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat Assistant</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.8);
            --accent: #38bdf8;
            --text-main: #f8fafc;
        }
        body { 
            background-color: var(--bg-dark); 
            background-image: radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.1) 0px, transparent 50%);
            color: var(--text-main); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; 
            padding: 20px; 
            display: flex; 
            flex-direction: column; 
            height: 100vh; 
            box-sizing: border-box;
        }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 20px; color: var(--accent); }
        .chat-container { 
            flex-grow: 1; 
            background: var(--card-bg); 
            border: 1px solid rgba(255,255,255,0.1); 
            border-radius: 12px; 
            padding: 20px; 
            overflow-y: auto; 
            display: flex; 
            flex-direction: column; 
            gap: 15px; 
            margin-bottom: 20px; 
            backdrop-filter: blur(10px);
        }
        .message { padding: 14px 18px; border-radius: 14px; max-width: 80%; line-height: 1.6; font-size: 14px; white-space: pre-wrap; }
        .message.user { background: var(--accent); color: var(--bg-dark); align-self: flex-end; border-bottom-right-radius: 4px; font-weight: 500; }
        .message.ai { background: rgba(255,255,255,0.05); align-self: flex-start; border-bottom-left-radius: 4px; border: 1px solid rgba(255,255,255,0.1); }
        .input-area { display: flex; gap: 12px; }
        input[type="text"] { 
            flex-grow: 1; 
            padding: 16px; 
            background: var(--card-bg); 
            border: 1px solid rgba(255,255,255,0.1); 
            border-radius: 12px; 
            color: #fff; 
            font-family: inherit; 
            font-size: 14px;
            transition: 0.3s;
        }
        input[type="text"]:focus { outline: none; border-color: var(--accent); }
        button { 
            padding: 16px 28px; 
            background: var(--accent); 
            color: var(--bg-dark); 
            border: none; 
            border-radius: 12px; 
            font-weight: 700; 
            cursor: pointer; 
            transition: 0.2s;
            font-family: inherit;
        }
        button:hover { background: #0284c7; color: #fff;}
        button:disabled { opacity: 0.5; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="header">
        <h2>AI Assistant</h2>
    </div>
    <div class="chat-container" id="chatBox">
        <div class="message ai">Halo! Aplikasi telah berhasil diinstal. Ada yang bisa saya bantu hari ini?</div>
    </div>
    <div class="input-area">
        <input type="text" id="pesanInput" placeholder="Ketik pesan Anda di sini..." autocomplete="off">
        <button id="sendBtn" onclick="kirimPesan()">Kirim</button>
    </div>
    <script>
        const chatBox = document.getElementById('chatBox');
        const inputField = document.getElementById('pesanInput');
        const sendBtn = document.getElementById('sendBtn');
        inputField.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                kirimPesan();
            }
        });
        async function kirimPesan() {
            const pesan = inputField.value.trim();
            if (!pesan) return;
            appendMessage('user', pesan);
            inputField.value = '';
            inputField.disabled = true;
            sendBtn.disabled = true;
            const loadingId = 'loading-' + Date.now();
            appendMessage('ai', 'Sedang berpikir...', loadingId);
            chatBox.scrollTop = chatBox.scrollHeight;
            try {
                const formData = new FormData();
                formData.append('pesan', pesan);
                const response = await fetch('process.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                const elLoading = document.getElementById(loadingId);
                if (result.status === true && result.data && result.data.pesan) {
                    elLoading.innerText = result.data.pesan;
                } else {
                    elLoading.innerText = result.data?.pesan || 'Terjadi kesalahan pada sistem pusat.';
                    elLoading.style.color = '#ef4444';
                }
            } catch (error) {
                const elLoading = document.getElementById(loadingId);
                elLoading.innerText = 'Gagal terhubung ke backend server lokal.';
                elLoading.style.color = '#ef4444';
            }
            inputField.disabled = false;
            sendBtn.disabled = false;
            inputField.focus();
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        function appendMessage(sender, text, id = null) {
            const div = document.createElement('div');
            div.className = 'message ' + sender;
            div.innerText = text;
            if (id) div.id = id;
            chatBox.appendChild(div);
        }
    </script>
</body>
</html>
