<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('/admin/gerenciar-defesas.php');
}

$projeto_id = intval($_GET['id']);

$db = new Database();
$conn = $db->getConnection();

// Buscar informações da defesa
$stmt = $conn->prepare("
    SELECT 
        e.nome as estudante,
        e.matricula,
        p.titulo as projeto,
        p.data_defesa,
        p.hora_defesa,
        p.sala,
        o.nome as orientador,
        o.departamento as dept_orientador,
        GROUP_CONCAT(
            DISTINCT CONCAT(m.nome, ' (', m.departamento, ')')
            ORDER BY m.nome ASC SEPARATOR '\n'
        ) as membros_banca
    FROM projetos p
    JOIN estudantes e ON p.estudante_id = e.id
    JOIN usuarios o ON e.orientador_id = o.id
    LEFT JOIN membros_banca mb ON p.id = mb.projeto_id
    LEFT JOIN usuarios m ON mb.professor_id = m.id
    WHERE p.id = ?
    GROUP BY p.id");
$stmt->execute([$projeto_id]);
$defesa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$defesa) {
    redirect('/admin/gerenciar-defesas.php');
}

// Criar PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

// Configurar documento
$pdf->SetCreator(APP_NAME);
$pdf->SetAuthor('Coordenação de TFC');
$pdf->SetTitle('Pauta de Defesa - ' . $defesa['estudante']);

// Remover cabeçalho e rodapé padrão
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Adicionar página
$pdf->AddPage();

// Definir fonte
$pdf->SetFont('helvetica', '', 12);

// Cabeçalho do documento
$pdf->Image('../assets/img/logo.png', 10, 10, 30);
$pdf->Cell(0, 10, 'UNIVERSIDADE FEDERAL DO AMAZONAS', 0, 1, 'C');
$pdf->Cell(0, 10, 'INSTITUTO DE COMPUTAÇÃO', 0, 1, 'C');
$pdf->Cell(0, 10, 'COORDENAÇÃO DE TRABALHO FINAL DE CURSO', 0, 1, 'C');

$pdf->Ln(20);

// Título
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'PAUTA DE DEFESA DE TRABALHO FINAL DE CURSO', 0, 1, 'C');

$pdf->Ln(10);

// Informações do estudante e projeto
$pdf->SetFont('helvetica', '', 12);

$data = date('d/m/Y', strtotime($defesa['data_defesa']));
$hora = date('H:i', strtotime($defesa['hora_defesa']));

$info = "Estudante: {$defesa['estudante']}\nMatrícula: {$defesa['matricula']}\n\n";
$info .= "Título do Trabalho: {$defesa['projeto']}\n\n";
$info .= "Data: {$data}\nHorário: {$hora}\nLocal: Sala {$defesa['sala']}\n\n";
$info .= "Orientador(a): {$defesa['orientador']}\nDepartamento: {$defesa['dept_orientador']}\n\n";
$info .= "Banca Examinadora:\n{$defesa['membros_banca']}";

$pdf->MultiCell(0, 10, $info, 0, 'L');

$pdf->Ln(10);

// Tabela de avaliação
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'CRITÉRIOS DE AVALIAÇÃO', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 10);

// Cabeçalho da tabela
$header = array('Critério', 'Peso', 'Nota (0-10)', 'Total');
$w = array(100, 20, 30, 30);

foreach($header as $i => $h) {
    $pdf->Cell($w[$i], 7, $h, 1, 0, 'C');
}
$pdf->Ln();

// Critérios de avaliação
$criterios = array(
    array('Apresentação (clareza, domínio, organização)', '2,0'),
    array('Trabalho Escrito (formatação, organização, referências)', '2,0'),
    array('Conteúdo Técnico/Científico', '4,0'),
    array('Resultados e Contribuições', '2,0')
);

foreach($criterios as $row) {
    $pdf->Cell($w[0], 7, $row[0], 1);
    $pdf->Cell($w[1], 7, $row[1], 1, 0, 'C');
    $pdf->Cell($w[2], 7, '', 1);
    $pdf->Cell($w[3], 7, '', 1);
    $pdf->Ln();
}

// Linha do total
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell($w[0] + $w[1], 7, 'NOTA FINAL', 1, 0, 'R');
$pdf->Cell($w[2] + $w[3], 7, '', 1);
$pdf->Ln(15);

// Espaço para observações
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'OBSERVAÇÕES', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);
$pdf->MultiCell(0, 30, '', 1, 'L');

$pdf->Ln(10);

// Espaço para assinaturas
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Manaus, _____ de __________________ de ' . date('Y'), 0, 1, 'C');

$pdf->Ln(15);

// Linhas para assinaturas
$pdf->Cell(0, 10, '_________________________________', 0, 1, 'C');
$pdf->Cell(0, 5, 'Orientador(a)', 0, 1, 'C');

$pdf->Ln(10);

$pdf->Cell(0, 10, '_________________________________', 0, 1, 'C');
$pdf->Cell(0, 5, 'Membro da Banca', 0, 1, 'C');

$pdf->Ln(10);

$pdf->Cell(0, 10, '_________________________________', 0, 1, 'C');
$pdf->Cell(0, 5, 'Membro da Banca', 0, 1, 'C');

// Gerar arquivo
$filename = 'pauta_defesa_' . preg_replace('/[^a-zA-Z0-9]/', '_', $defesa['estudante']) . '.pdf';
$pdf->Output($filename, 'D');