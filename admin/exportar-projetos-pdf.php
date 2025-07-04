<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar todos os projetos dos estudantes
$query = "SELECT u.id, u.nome, est.curso, est.numero_processo, est.tema_defesa, est.data_defesa, 
                 o.nome as orientador_nome, co.nome as coorientador_nome
          FROM usuarios u
          INNER JOIN estudantes est ON u.id = est.usuario_id
          LEFT JOIN usuarios o ON est.orientador_id = o.id
          LEFT JOIN usuarios co ON est.coorientador_id = co.id
          WHERE u.tipo_usuario = 'estudante'
          ORDER BY u.nome";

$stmt = $conn->query($query);
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConnection();

// Criar novo documento PDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');

// Configurar documento
$pdf->SetCreator('SISTEMA TFC');
$pdf->SetAuthor('Administrador');
$pdf->SetTitle('Relatório de Projetos dos Estudantes');

// Remover cabeçalho e rodapé padrão
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Adicionar página
$pdf->AddPage();

// Configurar fonte
$pdf->SetFont('helvetica', 'B', 14);

// Título do relatório
$pdf->Cell(0, 10, 'Relatório de Projetos dos Estudantes', 0, 1, 'C');
$pdf->Ln(5);

// Cabeçalhos da tabela
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(45, 7, 'Estudante', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Curso', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'Nº Processo', 1, 0, 'C', true);
$pdf->Cell(70, 7, 'Tema da Defesa', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'Data Defesa', 1, 0, 'C', true);
$pdf->Cell(45, 7, 'Orientador', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Coorientador', 1, 1, 'C', true);

// Dados da tabela
$pdf->SetFont('helvetica', '', 8);
$pdf->SetFillColor(255, 255, 255);

foreach ($projetos as $projeto) {
    // Ajustar o texto para caber nas células
    $estudante = substr($projeto['nome'], 0, 30);
    $curso = substr($projeto['curso'], 0, 20);
    $tema = substr($projeto['tema_defesa'], 0, 50);
    $orientador = substr($projeto['orientador_nome'], 0, 30);
    $coorientador = $projeto['coorientador_nome'] ? substr($projeto['coorientador_nome'], 0, 25) : '-';
    $data_defesa = date('d/m/Y', strtotime($projeto['data_defesa']));

    $pdf->Cell(45, 6, $estudante, 1, 0, 'L');
    $pdf->Cell(30, 6, $curso, 1, 0, 'L');
    $pdf->Cell(25, 6, $projeto['numero_processo'], 1, 0, 'C');
    $pdf->Cell(70, 6, $tema, 1, 0, 'L');
    $pdf->Cell(25, 6, $data_defesa, 1, 0, 'C');
    $pdf->Cell(45, 6, $orientador, 1, 0, 'L');
    $pdf->Cell(35, 6, $coorientador, 1, 1, 'L');
}

// Gerar o PDF
$pdf->Output('projetos_estudantes.pdf', 'D');