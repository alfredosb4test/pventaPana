<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
// Include the main TCPDF library (search for installation path).
require_once('tcpdf/examples/tcpdf_include.php');
require_once('tcpdf/tcpdf.php'); 


class MYPDF extends TCPDF {
 

    // Page footer
    public function Footer() { 
    }
}
$medidas=array(45,100); 
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $medidas, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 003');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(2, 2, 2);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('times', 'BI', 7);

// add a page
$pdf->AddPage();

// set some text to print
$txt = <<<EOD
TCPDF Example 003

1 Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
2 Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
3 Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
4 Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
5 Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
6 Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
7 Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
8 Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
9 Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
10 Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
EOD;

// print a block of text using Write()
$pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
 
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_003.pdf', 'I');

