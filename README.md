# AI Gemini UI 🤖

Antarmuka obrolan (Chat UI) modern, ringan, dan responsif untuk terhubung dengan AI API Gateway. Aplikasi ini dirancang agar sangat mudah diinstal (Plug-and-Play) di server lokal maupun hosting cPanel Anda.

## ✨ Fitur Utama
* **Desain Modern:** Antarmuka bergaya *Dark Mode* yang bersih dan profesional.
* **Auto-Setup Wizard:** Instalasi mudah melalui antarmuka web, tanpa perlu mengedit kode konfigurasi secara manual.
* **Keamanan Privasi:** API Key dan Token AI Anda disimpan secara lokal di server Anda sendiri (`config.php`).
* **Dukungan Multi-Token:** Mendukung penggunaan banyak token AI sekaligus sebagai cadangan (*failover*).

---

## 📋 Persyaratan Sistem
Sebelum menginstal aplikasi ini, pastikan Anda telah menyiapkan hal-hal berikut:
1. **Web Server dengan PHP:** Mendukung PHP 7.4 atau yang lebih baru (XAMPP, Laragon, atau Hosting Panel).
2. **API Key Sistem:** Dapatkan kunci akses Anda di dasbor Village Payment: **Profil** &rarr; **Pengaturan API**.
3. **Token AI Mandiri:** Kunci API / Token rahasia dari penyedia AI Anda (misalnya Gemini atau OpenAI).

---

## 🚀 Cara Instalasi

### Opsi A: Menggunakan Localhost (XAMPP / Laragon)
1. Unduh repositori ini dengan mengklik tombol hijau **Code** > **Download ZIP**, atau melalui terminal:
   `git clone https://github.com/go-drive/aigemini.git`
2. Pindahkan folder `aigemini` ke direktori web server Anda:
   * **XAMPP:** Letakkan di folder `C:\xampp\htdocs\`
   * **Laragon:** Letakkan di folder `C:\laragon\www\`
3. Pastikan servis Apache sudah menyala.
4. Buka browser Anda dan akses: `http://localhost/aigemini`

### Opsi B: Menggunakan Hosting / cPanel
1. Unduh repositori ini dalam bentuk **.ZIP**.
2. Login ke cPanel hosting Anda, lalu buka **File Manager**.
3. Masuk ke folder `public_html` (atau folder domain/subdomain yang Anda inginkan).
4. **Upload** file ZIP tadi, lalu klik kanan dan pilih **Extract**.
5. Buka domain Anda melalui browser (contoh: `https://domain-anda.com/aigemini`).

---

## ⚙️ Setup Wizard (Konfigurasi Pertama Kali)

Saat Anda membuka aplikasi ini untuk pertama kalinya, Anda akan otomatis diarahkan ke halaman Setup Wizard (`install.php`). 

Isi formulir instalasi dengan teliti:
* **API Key Sistem:** Masukkan API Key yang Anda salin dari Village Payment.
* **Token AI (Mandiri):** Masukkan token AI Anda. 
  * *Tips:* Jika Anda memiliki lebih dari satu token, pisahkan dengan koma. (Contoh: `sk-TokenSatu, sk-TokenDua`).

Setelah Anda menekan tombol **Simpan & Mulai**, aplikasi akan otomatis membuat file `config.php` dan Anda siap menggunakan AI Assistant!

---

## 🛡️ Catatan Keamanan
* File `config.php` akan tercipta secara otomatis setelah instalasi berhasil. Pastikan folder aplikasi ini memiliki izin tulis (*write permissions*) saat instalasi.
* **JANGAN PERNAH** mengunggah file `config.php` Anda ke repositori publik, karena file tersebut berisi Token rahasia Anda.
