<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
    exit;
}

if (!isset($_GET['projeto_id'])) {
    header('Location: gerenciar-banca.php?msg=erro');
    exit;
}

$projeto_id = intval($_GET['projeto_id']);

$db = new Database();
$conn = $db->getConnection();

// Buscar informações do projeto e banca
try {
    $stmt = $conn->prepare(
        "SELECT p.titulo, p.data_defesa, p.hora_defesa, p.local_defesa,
                e.numero_processo,
                u_est.nome as estudante_nome,
                u_ori.nome as orientador_nome,
                u_coori.nome as coorientador_nome
         FROM projetos p
         INNER JOIN estudantes e ON p.estudante_id = e.id
         INNER JOIN usuarios u_est ON e.usuario_id = u_est.id
         LEFT JOIN usuarios u_ori ON e.orientador_id = u_ori.id
         LEFT JOIN usuarios u_coori ON e.coorientador_id = u_coori.id
         WHERE p.id = ?"
    );
    $stmt->execute([$projeto_id]);
    $projeto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$projeto) {
        throw new Exception('Projeto não encontrado');
    }

    // Buscar membros da banca
    $stmt = $conn->prepare(
        "SELECT u.nome, p.titulacao, p.departamento
         FROM banca_avaliadora b
         INNER JOIN usuarios u ON b.professor_id = u.id
         INNER JOIN professores p ON u.id = p.usuario_id
         WHERE b.projeto_id = ?"
    );
    $stmt->execute([$projeto_id]);
    $membros_banca = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($membros_banca) < 3) {
        throw new Exception('Banca incompleta');
    }

    // Gerar PDF da pauta
    require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

    class MYPDF extends TCPDF {
        public function Header() {
            $this->SetFont('helvetica', 'B', 15);
            $this->Cell(0, 15, APP_NAME, 0, false, 'C', 0, '', 0, false, 'M', 'M');
        }

        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistema TFC');
    $pdf->SetTitle('Pauta da Banca Avaliadora');

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    $pdf->AddPage();

    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'PAUTA DA BANCA AVALIADORA', 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('helvetica', '', 12);

    // Informações do Projeto
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Informações do Projeto', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Título: ' . $projeto['titulo'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'Estudante: ' . $projeto['estudante_nome'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'Nº Processo: ' . $projeto['numero_processo'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'Orientador: ' . $projeto['orientador_nome'], 0, 1, 'L');
    if ($projeto['coorientador_nome']) {
        $pdf->Cell(0, 10, 'Coorientador: ' . $projeto['coorientador_nome'], 0, 1, 'L');
    }
    $pdf->Ln(5);

    // Data e Local
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Data e Local da Defesa', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Data: ' . date('d/m/Y', strtotime($projeto['data_defesa'])), 0, 1, 'L');
    $pdf->Cell(0, 10, 'Hora: ' . $projeto['hora_defesa'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'Local: ' . $projeto['local_defesa'], 0, 1, 'L');
    $pdf->Ln(5);

    // Composição da Banca
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Composição da Banca Avaliadora', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    foreach ($membros_banca as $membro) {
        $pdf->Cell(0, 10, $membro['nome'], 0, 1, 'L');
        $pdf->Cell(0, 10, $membro['titulacao'] . ' - ' . $membro['departamento'], 0, 1, 'L');
        $pdf->Ln(5);
    }

    // Espaço para assinaturas
    $pdf->Ln(20);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Assinaturas', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);

    foreach ($membros_banca as $membro) {
        $pdf->Ln(15);
        $pdf->Cell(0, 10, '_____________________________________________', 0, 1, 'C');
        $pdf->Cell(0, 10, $membro['nome'], 0, 1, 'C');
    }

    // Gerar o PDF
    $filename = 'pauta_banca_' . $projeto['numero_processo'] . '_' . date('Ymd') . '.pdf';
    $pdf->Output($filename, 'D');

} catch (Exception $e) {
    header('Location: gerenciar-banca.php?msg=erro_pauta');
} finally {
    $db->closeConnection();
}

exit;