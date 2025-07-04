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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo 'ID de defesa inválido.';
    exit;
}

$id = intval($_GET['id']);
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
    WHERE d.id = :id AND d.status != 'cancelada'
    GROUP BY d.id, d.data_defesa, d.hora_defesa, d.sala, e.nome, e.matricula, p.titulo, t.titulo, o.nome
    LIMIT 1");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$defesa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$defesa) {
    echo 'Defesa não encontrada.';
    exit;
}

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
                <div style="margin-top:10px; font-size:16px; font-weight:bold;">Ata de Defesa do Projecto de Licenciatura, 2024-2025</div>
                <div style="font-size:15px; font-weight:bold; margin-top:5px;">ENGENHARIA INFORMÁTICA</div>
            </td>
        </tr>
    </table>
</div>';
$html .= '<h2 style="text-align:center;">Ata de Defesa</h2>';
$html .= '<div style="margin: 30px 0; font-size:15px;">';
$html .= 'No dia <b>' . date('d/m/Y', strtotime($defesa['data_defesa'])) . '</b>, às <b>' . date('H:i', strtotime($defesa['hora_defesa'])) . '</b>, na sala <b>' . htmlspecialchars($defesa['sala']) . '</b>, realizou-se a defesa do projeto de licenciatura intitulada <b>"' . htmlspecialchars($defesa['tema']) . '"</b>, apresentada pelo estudante <b>' . htmlspecialchars($defesa['estudante']) . '</b> (Matrícula: <b>' . htmlspecialchars($defesa['numero_processo']) . '</b>), sob orientação do(a) professor(a) <b>' . htmlspecialchars($defesa['orientador']) . '</b>.';
$html .= '<br><br>A banca examinadora foi composta por: <b>' . htmlspecialchars($defesa['membros_banca']) . '</b>.';
$html .= '<br><br>Após a apresentação e arguição, a banca deliberou pela aprovação do trabalho.';
$html .= '</div>';
$html .= '<div style="margin-top:40px; font-size:15px;">';
$html .= '<b>Assinaturas dos membros da banca:</b><br><br>';
$membros = explode(',', $defesa['membros_banca']);
foreach ($membros as $membro) {
    $html .= htmlspecialchars(trim($membro)) . ': ___________________________________________<br><br>';
}
$html .= '</div>';

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('ata_defesa.pdf', ['Attachment' => false]);
exit;