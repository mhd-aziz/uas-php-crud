<?php
// user/generate_pdf.php

session_start();
require '../includes/config.php';
require '../includes/auth_user.php';

define('FPDF_FONTPATH', __DIR__ . '/../libraries/fpdf/font/');
require_once '../libraries/fpdf/fpdf.php';

class PDF extends FPDF
{
    protected $headerColor = [41, 128, 185];
    protected $footerColor = [41, 128, 185];
    protected $tableHeaderColor = [52, 152, 219];
    protected $tableRowColor1 = [245, 245, 245];
    protected $tableRowColor2 = [255, 255, 255];
    protected $itemsPerPage = 7;

    function Header()
    {
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor($this->headerColor[0], $this->headerColor[1], $this->headerColor[2]);
        $this->Cell(0, 20, 'Daftar Direktori Buku', 0, 1, 'C', true);
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('helvetica', 'I', 10);
        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor($this->footerColor[0], $this->footerColor[1], $this->footerColor[2]);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C', true);
    }

    function drawHeader($header, $w, $aligns)
    {
        $h_header = 10;
        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetFillColor($this->tableHeaderColor[0], $this->tableHeaderColor[1], $this->tableHeaderColor[2]);
        $this->SetDrawColor(255, 255, 255);
        $this->SetLineWidth(.3);
        $this->SetFont('helvetica', 'B', 10);
        $this->SetTextColor(255, 255, 255);
        $this->Rect($x, $y, array_sum($w), $h_header, 'F');

        for ($i = 0; $i < count($header); $i++) {
            $this->SetXY($x + array_sum(array_slice($w, 0, $i)), $y);
            $this->Cell($w[$i], $h_header, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $header[$i]), 0, 0, 'C', false);
        }

        $this->SetDrawColor(255, 255, 255);
        for ($i = 0; $i < count($header); $i++) {
            $cellX = $x + array_sum(array_slice($w, 0, $i));
            $this->Line($cellX, $y, $cellX, $y + $h_header);
        }

        $this->SetXY($x, $y + $h_header);
    }

    function Row($data, $widths, $aligns, $imgPath = null, $rowColor)
    {
        $nb = 0;
        foreach ($data as $i => $val) {
            if ($i != 1) {
                $convertedVal = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $val);
                $nb = max($nb, $this->NbLines($widths[$i], $convertedVal));
            }
        }
        $h_text = 5 * $nb;
        $h_image = 30;
        $h = max($h_text, $h_image + 4);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetFillColor($rowColor[0], $rowColor[1], $rowColor[2]);
        $this->Rect($x, $y, array_sum($widths), $h, 'F');
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.2);
        for ($i = 0; $i < count($data); $i++) {
            $cellX = $x + array_sum(array_slice($widths, 0, $i));
            $this->Rect($cellX, $y, $widths[$i], $h);
        }
        for ($i = 0; $i < count($data); $i++) {
            $cellX = $x + array_sum(array_slice($widths, 0, $i));
            $cellY = $y + 2;

            $this->SetXY($cellX, $cellY);
            if ($i == 1 && $imgPath) {
                $imageWidth = $widths[$i] - 4;
                $imageHeight = $h_image;
                $currentY = $y + ($h - $imageHeight) / 2;
                $this->Image($imgPath, $cellX + 2, $currentY, $imageWidth, $imageHeight);
            } else {
                $this->SetFont('helvetica', '', 10);
                $this->SetTextColor(50, 50, 50);
                $convertedText = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $data[$i]);
                $this->MultiCell($widths[$i], 5, $convertedText, 0, $aligns[$i]);
            }
        }

        $this->SetXY($x, $y + $h);
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

$search = '';
$params = [];
$where = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $where = "WHERE title LIKE :search OR author LIKE :search OR genre LIKE :search";
    $params[':search'] = "%" . $search . "%";
}
$sql = "SELECT * FROM books $where ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

$header = array('No.', 'Cover', 'Judul', 'Pengarang', 'Jenis Buku', 'Hal', 'Tahun');
$w = array(10, 35, 40, 40, 35, 15, 15);
$aligns = array('C', 'C', 'C', 'C', 'C', 'C', 'C');

$pdf->drawHeader($header, $w, $aligns);
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(50, 50, 50);
$fill = false;
$itemCount = 0;

if ($books) {
    foreach ($books as $index => $book) {
        $no = $index + 1;
        $data = array(
            $no,
            '',
            $book['title'],
            $book['author'],
            $book['genre'],
            $book['page_count'],
            $book['publish_year']
        );
        $imgPath = ($book['cover_image'] && file_exists(__DIR__ . '/../uploads/cover_images/' . $book['cover_image'])) ? realpath(__DIR__ . '/../uploads/cover_images/' . $book['cover_image']) : null;
        $rowColor = $fill ? [255, 255, 255] : [245, 245, 245];
        $pdf->Row($data, $w, $aligns, $imgPath, $rowColor);
        $fill = !$fill;
        $itemCount++;
        if ($itemCount == 7 && ($index + 1) < count($books)) {
            $pdf->AddPage();
            $pdf->drawHeader($header, $w, $aligns);
            $itemCount = 0;
        }
    }
} else {
    $pdf->SetFont('helvetica', 'I', 12);
    $pdf->SetTextColor(150, 150, 150);
    $pdf->Cell(array_sum($w), 10, 'Tidak ada data buku ditemukan.', 0, 1, 'C');
}

$pdf->Output('I', 'Daftar_Direktori_Buku.pdf');
