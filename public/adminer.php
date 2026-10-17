<?php
error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED & ~E_NOTICE);

// Jika pengguna membuka adminer.php pertama kali, auto-submit login ke MySQL spmbknbi
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_COOKIE['adminer_key']) && !isset($_GET['select']) && !isset($_GET['table']) && !isset($_GET['sql'])) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Membuka Database SPMB - SMK KNBI</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background: #f1f5f9;
                color: #1e293b;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
            }
            .card {
                background: #ffffff;
                padding: 40px;
                border-radius: 20px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.06);
                text-align: center;
                max-width: 420px;
                border: 1px solid #e2e8f0;
            }
            .spinner {
                width: 48px;
                height: 48px;
                border: 4px solid #e2e8f0;
                border-top: 4px solid #00a65a;
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
                margin: 0 auto 20px;
            }
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            h2 { margin: 0 0 10px; font-size: 18px; font-weight: 800; color: #0f172a; }
            p { margin: 0 0 20px; font-size: 13px; color: #64748b; }
            button {
                background: #00a65a;
                color: #ffffff;
                border: none;
                padding: 10px 24px;
                font-size: 13px;
                font-weight: 700;
                border-radius: 10px;
                cursor: pointer;
            }
        </style>
    </head>
    <body onload="document.getElementById('autoform').submit();">
        <div class="card">
            <div class="spinner"></div>
            <h2>Membuka Database SPMB</h2>
            <p>Menghubungkan ke MySQL Database <strong>spmbknbi</strong>...</p>
            <form id="autoform" action="adminer.php?server=127.0.0.1&username=root&db=spmbknbi" method="post">
                <input type="hidden" name="auth[driver]" value="server">
                <input type="hidden" name="auth[server]" value="127.0.0.1">
                <input type="hidden" name="auth[username]" value="root">
                <input type="hidden" name="auth[password]" value="">
                <input type="hidden" name="auth[db]" value="spmbknbi">
                <input type="hidden" name="auth[permanent]" value="1">
                <button type="submit">Klik di sini jika tidak otomatis berpindah</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

function adminer_object() {
    class AdminerCustom extends Adminer {
        function name() {
            return 'Database SPMB - SMK Karya Nugraha Boyolali';
        }
        function credentials() {
            return array('127.0.0.1', 'root', '');
        }
        function database() {
            return 'spmbknbi';
        }
        function login($login, $password) {
            return true;
        }
    }
    return new AdminerCustom;
}

include __DIR__ . '/adminer-core.php';
