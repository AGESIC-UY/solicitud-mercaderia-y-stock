<?php
header('Content-Type: text/html; charset=iso-8859-10); //UTF-8');
require_once("Includes/conviertefecha.php");

$file = fopen("configbd.conf", "r") or exit("Unable to open file!");
$servidor= trim(str_replace("serv:","", fgets($file)));
$usu=trim(str_replace("user:","", fgets($file)));
$pass=trim(str_replace("pass:","", fgets($file)));
$base=trim(str_replace("db:","", fgets($file)));
fclose($file);
$cn=mysqli_connect($servidor,$usu,$pass,$base) or die (mysqli_connect_error().": ".mysqli_connect_error());
function limpiar($texto){$txt=trim(strip_tags($texto));return $txt;}

$dia = date('d');
$mes = date('m');
$year = date('y')+2000;
$fchhoy=date("d/m/Y", mktime(0, 0, 0, $mes, $dia, $year));

$fchdesde=$_REQUEST['fchdesde'];
$fchhasta=$_REQUEST['fchhasta'];
$detallo=$_REQUEST['detallo'];
$costos=$_REQUEST['costos'];
$artid=$_REQUEST['artid'];
$artbus=$_REQUEST['artbus'];
$areaele=$_REQUEST['areaele'];
$uniele=$_REQUEST['uniele'];

$consulta="Select * from Sistemas";
$resultado=mysqli_query($cn,$consulta) or die('La consulta del area fall&oacute;: ' .mysqli_error());
$sis=mysqli_fetch_assoc($resultado);
$logo=$sis['SisLogo'];

session_start();

if ($areaele>0)
{
	$sentenciaIII="Select * from Departamentos where DepId='".$areaele."'";
	$resultadoIII=mysqli_query($cn,$sentenciaIII) or die('La consulta del area fall&oacute;: ' .mysqli_error());
       $areaEle=mysqli_fetch_assoc($resultadoIII);
	$areaNombre=$areaEle['DepNombre'];
}
else
{
	$areaNombre="Todas";
}

if ($uniele>0)
{
	$sentenciaIII="Select * from Departamentos where DepId='".$uniele."'";
	$resultadoIII=mysqli_query($cn,$sentenciaIII) or die('La consulta de la unidad fall&oacute;: ' .mysqli_error());
       $unidadEle=mysqli_fetch_assoc($resultadoIII);
	$uniNombre=$unidadEle['DepNombre'];
}
else
{
	$uniNombre="Todas";
}

if ($artid>0)
{
	$sentenciaIV="Select * from StkArticulos where StkArtId='".$artid."'";
	$resultadoIV=mysqli_query($cn,$sentenciaIV) or die('La consulta del articulo fall&oacute;: ' .mysqli_error());
       $elart=mysqli_fetch_assoc($resultadoIV);
	$articulo=$elart['StkArtDsc'];
}
else
{
	$articulo="Todos";
}

if ($artbus==Null)
{
	$artbus="Sin especificar";
}
	include('Includes/class.ezpdf.php');
	$pdf = new Cezpdf('a4');
	$pdf->selectFont('Includes/pdf-related/Times-Roman.afm');
	$pdf->ezSetCmMargins(1,1,1.5,1.5);
	$pdf->addJpegFromFile($logo,400,740,130,60);
	$pdf->ezText('',14);
	$pdf->ezText('',14);
	$pdf->ezText('Información del Sistema de Solicitud de Mercaderia y Stock',14);
	$pdf->ezText('Consumo de articulos',14);
	$pdf->ezText('',10);
	$pdf->ezText('',2);
	$pdf->ezText('Periodo:  '.$fchdesde.' -- '.$fchhasta,10);
	$pdf->ezText('',2);
	$pdf->ezText('Área: '.$areaNombre,10);
	$pdf->ezText('',2);
	$pdf->ezText('Unidad: '.$uniNombre,10);
	$pdf->ezText('',2);
	$pdf->ezText('Articulo: '.utf8_encode($articulo),10);
	$pdf->ezText('',2);
	$pdf->ezText('Articulo con texto: '.$artbus,10);
	$pdf->ezText('',14);
	$pdf->ezText('',14);
	$pdf->ezTable($_SESSION['consumo'],"","",array($value,'width'=>500));
	$pdf->ezText('',10);
	$pdf->ezStream();
	exit;
?>


