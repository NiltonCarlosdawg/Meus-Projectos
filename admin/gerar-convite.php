<?php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<div style="margin:40px auto;max-width:400px;padding:20px;border:1px solid #e74c3c;background:#f9eaea;color:#c0392b;text-align:center;font-family:sans-serif;">Parâmetro obrigatório ausente.<br>Por favor, acesse esta página a partir do sistema corretamente.</div>';
    exit;
}