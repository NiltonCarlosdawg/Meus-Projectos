<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$tipo = $_GET['tipo'] ?? '';
if (!in_array($tipo, ['pdf', 'excel', 'csv'])) {
    redirect('/admin/orientadores.php');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar dados dos orientadores
$stmt = $conn->query("
    SELECT u.nome, u.email, u.departamento, u.area_especializacao,
           (SELECT COUNT(*) FROM estudantes e WHERE e.orientador_id = u.id) as total_orientandos
    FROM usuarios u
    WHERE u.tipo_usuario = 'orientador' AND u.status = TRUE
    ORDER BY u.nome
");
$orientadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConnection();

// Definir o nome do arquivo
$timestamp = date('Y-m-d_H-i-s');
$filename = "orientadores_{$timestamp}";

switch ($tipo) {
    case 'pdf':
        require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';
        
        // Criar PDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator(APP_NAME);
        $pdf->SetAuthor('Administrador');
        $pdf->SetTitle('Lista de Orientadores');
        
        // Configurar página
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Lista de Orientadores', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        
        // Cabeçalho da tabela
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(60, 7, 'Nome', 1);
        $pdf->Cell(60, 7, 'Email', 1);
        $pdf->Cell(40, 7, 'Departamento', 1);
        $pdf->Cell(30, 7, 'Orientandos', 1);
        $pdf->Ln();
        
        // Dados da tabela
        $pdf->SetFont('helvetica', '', 10);
        foreach ($orientadores as $orientador) {
            $pdf->Cell(60, 6, $orientador['nome'], 1);
            $pdf->Cell(60, 6, $orientador['email'], 1);
            $pdf->Cell(40, 6, $orientador['departamento'], 1);
            $pdf->Cell(30, 6, $orientador['total_orientandos'], 1);
            $pdf->Ln();
        }
        
        // Enviar PDF
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename={$filename}.pdf");
        echo $pdf->Output($filename . '.pdf', 'S');
        break;
        
    case 'excel':
        require_once '../vendor/autoload.php';
        
        use PhpOffice\PhpSpreadsheet\Spreadsheet;
        use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
        
        // Criar planilha
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Cabeçalho
        $sheet->setCellValue('A1', 'Nome');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Departamento');
        $sheet->setCellValue('D1', 'Área de Especialização');
        $sheet->setCellValue('E1', 'Total de Orientandos');
        
        // Dados
        $row = 2;
        foreach ($orientadores as $orientador) {
            $sheet->setCellValue('A' . $row, $orientador['nome']);
            $sheet->setCellValue('B' . $row, $orientador['email']);
            $sheet->setCellValue('C' . $row, $orientador['departamento']);
            $sheet->setCellValue('D' . $row, $orientador['area_especializacao']);
            $sheet->setCellValue('E' . $row, $orientador['total_orientandos']);
            $row++;
        }
        
        // Ajustar largura das colunas
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Enviar Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename={$filename}.xlsx");
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        break;
        
    case 'csv':
        // Configurar cabeçalho CSV
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename={$filename}.csv");
        
        // Criar arquivo CSV
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, "\xEF\xBB\xBF");
        
        // Cabeçalho
        fputcsv($output, ['Nome', 'Email', 'Departamento', 'Área de Especialização', 'Total de Orientandos']);
        
        // Dados
        foreach ($orientadores as $orientador) {
            fputcsv($output, [
                $orientador['nome'],
                $orientador['email'],
                $orientador['departamento'],
                $orientador['area_especializacao'],
                $orientador['total_orientandos']
            ]);
        }
        
        fclose($output);
        break;
}

exit;