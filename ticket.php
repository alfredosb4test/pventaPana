<?php
session_start(); 
include('funciones/conexion_class.php');
$conn = new class_mysqli();
// Include the main TCPDF library (search for installation path).
require_once('tcpdf/examples/tcpdf_include.php');
require_once('tcpdf/tcpdf.php'); 

// create new PDF document
$medidas=array(45,100); 
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'mm', $medidas, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PVenta');
$pdf->SetTitle('Ticket');
$pdf->SetSubject(' ');
$pdf->SetKeywords(' ');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, 80, 'Ticket', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins	SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetMargins(1, 1, 1);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
//$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------
$pdf->AddPage();

$array_ids = explode(",",$_GET['array_id']);
$array_cantidad_solicitada = explode(",",$_GET['array_cantidad_solicitada']);
$tr="";
foreach ($array_ids as $key => $id) {
	$tr .= "<tr>
				<td>".$conn->get_nombre_producto($id,30).'</td><td align="center" align="center">'.$array_cantidad_solicitada[$key]."</td>
				
			</tr>";	
}
$fecha = date('Y-m-d H:i:s');
// set font	".$conn->get_nombre_producto($id,30)."
$pdf->SetFont('times', '',6);
// Test fonts nesting
$html = '<table width="147" border="1">
		<tr>
			<th width="100" align="center">Producto</th><th width="25" align="center">Cant.</th>
		</tr>	
		'.$tr."
		</table>";
 

// output the HTML content
$encab = '  
	<table width="147" border="1">
	<tr>
		<td colspan="2" align="center">*** P A S T E L E R I A ***</td>
	</tr>	
	<tr>
		<td width="40">Direccion:</td><td>Teoloyucan Edo. Mex.</td>
	</tr>
	<tr>
		<td>Telefono:</td><td>5599-3443-23 / 5543-2154-3433</td>
	</tr>
	<tr>
		<td colspan="2" align="right">'.$fecha.'</td>
	</tr>	
	<table>
';
$pdf->writeHTML($encab, true, 0, true, 0);
$pdf->writeHTML($html, true, false, true, false, '');
 

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_006.pdf', 'I');
