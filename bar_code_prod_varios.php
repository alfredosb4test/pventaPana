<?php
//============================================================+
// File name   : example_027.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 027 for TCPDF class
//               1D Barcodes
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: 1D Barcodes.
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf/examples/tcpdf_include.php');
require_once('tcpdf/tcpdf.php'); 

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Punto de Venta');
$pdf->SetTitle('Codigos de Barras');
$pdf->SetSubject('');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 027', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// set margins
$pdf->SetMargins(1, 1, 1);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 2);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------
// set font
$pdf->SetFont('helvetica', '', 11);

// add a page
$pdf->AddPage();

$pdf->SetFont('helvetica', '', 10);
// define barcode style
$style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => 'auto',
    'vpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 3,
    'stretchtext' => 4
);

// EAN 13
//$pdf->Cell(0, 0, $pdf->write1DBarcode('CODE 128 AUTO', 'C128', '', '', '', 18, 0.4, $style, 'N'), 0, 1);
$style['fgcolor'] = array(0,0,0);
$style['position'] = '';
$style['stretch'] = false; // disable stretch
$style['fitwidth'] = false; // disable fitwidth

$style['cellfitalign'] = 'L';
$y = 11;
$x = 1;
$x_code = 1;
$margin_left = array(
					  "1" => "1",
					  "2" => "58",
					  "3" => "114",
					  "4" => "170"
					);
// write1DBarcode ($code, $type, $x='', $y='', $w='', $h='', $xres='', $style='', $align='')
	//$conn = new class_mysqli();
	$json_codigos = $_GET['codigos']; 
	$json_codigos = json_decode($json_codigos);
foreach($json_codigos as $rowj){

	if($x == 11){
		$y -= .8;
	}	
	if($x == 16){
		$y -= 1;
	}	
	if($x_code == 5){
		$x_code = 1;
		$y += 14;
	}			
	$pdf->write1DBarcode($rowj->code, 'C128', $margin_left[$x_code], $y, 40, 11, 0.4, $style, 'N');
	
	$x++;
	$x_code++;

}

// . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .

//Close and output PDF document
$pdf->Output('code_bar.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
?>