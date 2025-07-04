<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("
    SELECT d.id, d.data_defesa, d.hora_defesa, d.sala,
           e.nome as estudante, e.matricula ,
           p.titulo as projeto,
           t.titulo as tema,
           o.nome as orientador,
           d.projeto_id,
           est.numero_processo,
           GROUP_CONCAT(DISTINCT mb_u.nome SEPARATOR ', ') as membros_banca
    FROM defesas d
    LEFT JOIN projetos p ON d.projeto_id = p.id
    LEFT JOIN usuarios e ON p.estudante_id = e.id
    LEFT JOIN estudantes est ON est.usuario_id = e.id
    LEFT JOIN usuarios o ON p.orientador_id = o.id
    LEFT JOIN membros_banca mb ON d.id = mb.defesa_id
    LEFT JOIN usuarios mb_u ON mb.professor_id = mb_u.id
    LEFT JOIN inscricoes_tema i ON p.estudante_id = i.estudante_id
    LEFT JOIN temas_tfc t ON i.tema_id = t.id
    WHERE d.status != 'cancelada'
    GROUP BY d.id, d.data_defesa, d.hora_defesa, d.sala, e.nome, e.matricula, p.titulo, t.titulo, o.nome
    ORDER BY d.data_defesa DESC, d.hora_defesa ASC");
$stmt->execute();
$defesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '<div style="width:100%; margin-bottom:20px;">
    <table width="100%" style="border:none;">
        <tr>
            <td style="width:120px; text-align:left; vertical-align:top; border:none;">
                <img src="' . $_SERVER['DOCUMENT_ROOT'] . '/SISTEMATFC/assets/img/logo.png" style="height:100px;">
            </td>
            <td style="text-align:center; border:none; font-size:14px;">
                <div style="font-weight:bold;">GABINETE DO DIRECTOR ADJUNTO PARA ÁREA CIENTÍFICA E PÓS-GRADUAÇÃO</div>
                <div>DEPARTAMENTO DE INVESTIGAÇÃO CIENTÍFICA, INOVAÇÃO, EMPREENDEDORISMO E PÓS-GRADUAÇÃO</div>
                <div>INSTIC, Bairro dos CTT’s km 7, Rangel – Luanda</div>
                <div>Contactos: 222041728, E-mail: instic2020@gmail.com, Distrito Urbano de Rangel, Município de Luanda</div>
                <div>LUANDA – ANGOLA</div>
                <div style="margin-top:10px; font-size:16px; font-weight:bold;">Calendário de Defesa do Projecto de Licenciatura, 2024-2025</div>
                <div style="font-size:15px; font-weight:bold; margin-top:5px;">ENGENHARIA INFORMÁTICA</div>
            </td>
        </tr>
    </table>
</div>';
$html .= '<h2 style="text-align:center;">Agendamentos de Defesa</h2>';
$html .= '<table border="1" cellspacing="0" cellpadding="4" width="100%">';
$html .= '<thead><tr style="background:#f0f0f0;">
            <th>Nº</th>
            <th>Data</th>
            <th>Hora</th>
            <th>Local</th>
            <th>Estudante</th>
            <th>Matrícula</th>
            <th>Tema</th>
            <th>Orientador</th>
            <th>Membros da Banca</th>
          </tr></thead><tbody>';
$contador = 1;
foreach ($defesas as $defesa) {
    $html .= '<tr>';
    $html .= '<td>' . $contador++ . '</td>';
    $html .= '<td>' . date('d/m/Y', strtotime($defesa['data_defesa'])) . '</td>';
    $html .= '<td>' . date('H:i', strtotime($defesa['hora_defesa'])) . '</td>';
    $html .= '<td>' . htmlspecialchars($defesa['sala']) . '</td>';
    $html .= '<td>' . htmlspecialchars($defesa['estudante']) . '</td>';
    $html .= '<td>' . htmlspecialchars($defesa['numero_processo']) . '</td>';
    $html .= '<td>' . htmlspecialchars($defesa['tema']) . '</td>';
    $html .= '<td>' . htmlspecialchars($defesa['orientador']) . '</td>';
    $html .= '<td>' . htmlspecialchars($defesa['membros_banca']) . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('agendamentos_defesa.pdf', ['Attachment' => false]);
exit;