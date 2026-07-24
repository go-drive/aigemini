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
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E🤖%3C/text%3E%3C/svg%3E">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.8);
            --accent: #38bdf8;
            --text-main: #f8fafc;
            --border-color: rgba(255,255,255,0.1);
            --ai-msg-bg: rgba(255,255,255,0.05);
            --btn-text: #0f172a;
            --bg-gradient: radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.1) 0px, transparent 50%);
        }

        body.light-mode {
            --bg-color: #f1f5f9;
            --card-bg: #ffffff;
            --accent: #0284c7;
            --text-main: #0f172a;
            --border-color: rgba(0,0,0,0.1);
            --ai-msg-bg: #f8fafc;
            --btn-text: #ffffff;
            --bg-gradient: radial-gradient(at 0% 0%, rgba(2, 132, 199, 0.05) 0px, transparent 50%);
        }

        body { 
            background-color: var(--bg-color); 
            background-image: var(--bg-gradient);
            color: var(--text-main); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; 
            padding: 20px; 
            display: flex; 
            flex-direction: column; 
            height: 100vh; 
            box-sizing: border-box;
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(128, 128, 128, 0.3); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(128, 128, 128, 0.5); }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 20px; color: var(--accent); transition: color 0.4s ease; }
        
        .btn-theme {
            background: transparent; color: var(--text-main); border: 1px solid var(--border-color);
            padding: 8px 16px; border-radius: 8px; font-size: 13px; cursor: pointer; transition: 0.3s;
        }
        .btn-theme:hover { background: var(--border-color); }

        .chat-container { 
            flex-grow: 1; background: var(--card-bg); border: 1px solid var(--border-color); 
            border-radius: 12px; padding: 20px; overflow-y: auto; display: flex; 
            flex-direction: column; gap: 15px; margin-bottom: 10px; backdrop-filter: blur(10px);
        }
        .message { padding: 14px 18px; border-radius: 14px; max-width: 80%; line-height: 1.6; font-size: 14px; white-space: pre-wrap; }
        .message.user { background: var(--accent); color: var(--btn-text); align-self: flex-end; border-bottom-right-radius: 4px; font-weight: 500; }
        .message.ai { background: var(--ai-msg-bg); align-self: flex-start; border-bottom-left-radius: 4px; border: 1px solid var(--border-color); }
        
        .input-wrapper { display: flex; flex-direction: column; gap: 8px; }
        .file-preview { font-size: 12px; color: var(--accent); display: none; align-items: center; gap: 8px; padding-left: 5px; }
        .file-preview span { background: var(--ai-msg-bg); padding: 4px 10px; border-radius: 12px; border: 1px solid var(--border-color); }
        .remove-file { cursor: pointer; color: #ef4444; font-weight: bold; }

        .input-area { display: flex; gap: 12px; align-items: center; }
        
        .btn-attach {
            background: var(--card-bg); color: var(--text-main); border: 1px solid var(--border-color);
            border-radius: 12px; padding: 0 16px; height: 50px; cursor: pointer; font-size: 18px; transition: 0.3s;
            display: flex; align-items: center; justify-content: center;
        }
        .btn-attach:hover { border-color: var(--accent); color: var(--accent); }
        
        input[type="text"] { 
            flex-grow: 1; padding: 0 16px; height: 50px; background: var(--card-bg); 
            border: 1px solid var(--border-color); border-radius: 12px; color: var(--text-main); 
            font-family: inherit; font-size: 14px; transition: 0.4s ease; box-sizing: border-box;
        }
        input[type="text"]:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2); }
        
        button.btn-send { 
            height: 50px; padding: 0 28px; background: var(--accent); color: var(--btn-text); 
            border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; font-family: inherit;
        }
        button.btn-send:hover:not(:disabled) { opacity: 0.9; transform: translateY(-1px); }
        button.btn-send:disabled { opacity: 0.5; cursor: not-allowed; }
        
        input[type="file"] { display: none; }
    </style>
</head>
<body>
    <div class="header">
        <h2>AI Assistant</h2>
        <button id="themeToggle" class="btn-theme" onclick="toggleTheme()">☀️ Light Mode</button>
    </div>

    <div class="chat-container" id="chatBox">
        <div class="message ai">Halo! Aplikasi telah berhasil diinstal. Anda bisa mengetik pesan atau melampirkan gambar, audio, dan dokumen.</div>
    </div>

    <div class="input-wrapper">
        <div class="file-preview" id="filePreview">
            <span id="fileName"></span>
            <div class="remove-file" onclick="removeFile()">✕</div>
        </div>
        <div class="input-area">
            <button class="btn-attach" onclick="document.getElementById('fileInput').click()" title="Lampirkan File">📎</button>
            <input type="file" id="fileInput" accept="image/jpeg, image/png, image/webp, audio/mpeg, audio/ogg, audio/wav, application/pdf" onchange="handleFileSelect(event)">
            <input type="text" id="pesanInput" placeholder="Ketik pesan Anda di sini..." autocomplete="off">
            <button id="sendBtn" class="btn-send" onclick="kirimPesan()">Kirim</button>
        </div>
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

        const chatBox = document.getElementById('chatBox');
        const inputField = document.getElementById('pesanInput');
        const sendBtn = document.getElementById('sendBtn');
        const fileInput = document.getElementById('fileInput');
        const filePreview = document.getElementById('filePreview');
        const fileNameDisplay = document.getElementById('fileName');
        
        let selectedFile = null;

        inputField.addEventListener("keypress", function(event) {
            if (event.key === "Enter" && !sendBtn.disabled) {
                event.preventDefault();
                kirimPesan();
            }
        });

        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert("Ukuran file maksimal 5MB.");
                    removeFile();
                    return;
                }
                selectedFile = file;
                fileNameDisplay.textContent = file.name;
                filePreview.style.display = 'flex';
            }
        }

        function removeFile() {
            selectedFile = null;
            fileInput.value = '';
            filePreview.style.display = 'none';
        }

        function toggleInputs(status) {
            inputField.disabled = !status;
            sendBtn.disabled = !status;
            fileInput.disabled = !status;
            if (status) inputField.focus();
        }

        async function kirimPesan() {
            const pesan = inputField.value.trim();
            if (!pesan && !selectedFile) return;

            let pesanTampil = pesan;
            if (selectedFile) {
                pesanTampil += pesanTampil ? `\n\n[Lampiran: ${selectedFile.name}]` : `[Lampiran: ${selectedFile.name}]`;
            }

            appendMessage('user', pesanTampil);
            inputField.value = '';
            toggleInputs(false);

            const loadingId = 'loading-' + Date.now();
            appendMessage('ai', 'Sedang berpikir...', loadingId);
            chatBox.scrollTop = chatBox.scrollHeight;

            try {
                const formData = new FormData();
                formData.append('pesan', pesan);
                if (selectedFile) {
                    formData.append('file_lampiran', selectedFile);
                }

                removeFile(); 

                const response = await fetch('process.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                const elLoading = document.getElementById(loadingId);
                
                if (result.status === true && result.data && result.data.pesan) {
                    let cleanText = result.data.pesan.replace(/[*#`_]/g, '');
                    elLoading.textContent = '';
                    
                    let i = 0;
                    function typeWriter() {
                        if (i < cleanText.length) {
                            elLoading.textContent += cleanText.charAt(i);
                            i++;
                            chatBox.scrollTop = chatBox.scrollHeight;
                            setTimeout(typeWriter, 15);
                        } else {
                            toggleInputs(true);
                        }
                    }
                    typeWriter();
                    
                } else {
                    elLoading.textContent = result.data?.pesan || 'Terjadi kesalahan pada sistem.';
                    elLoading.style.color = '#ef4444';
                    toggleInputs(true);
                }
            } catch (error) {
                const elLoading = document.getElementById(loadingId);
                elLoading.textContent = 'Gagal terhubung ke backend server lokal.';
                elLoading.style.color = '#ef4444';
                toggleInputs(true);
            }
        }

        function appendMessage(sender, text, id = null) {
            const div = document.createElement('div');
            div.className = 'message ' + sender;
            div.textContent = text;
            if (id) div.id = id;
            chatBox.appendChild(div);
        }
    </script>
</body>
</html>
