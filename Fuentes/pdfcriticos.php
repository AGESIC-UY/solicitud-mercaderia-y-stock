<?php
header('Content-Type: text/html; charset=iso-8859-1); //UTF-8');
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

$consulta="Select * from Sistemas";
$resultado=mysqli_query($cn,$consulta) or die('La consulta del area fall&oacute;: ' .mysqli_error());
$sis=mysqli_fetch_assoc($resultado);
$logo=$sis['SisLogo'];


	include('Includes/class.ezpdf.php');
//	include('Includes/class.backgroundpdf.php');

//	$pdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'Images/alerta.jpg'));
//	$pdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'Images/alerta.jpg','width'=>560, 'height'=>420, 'xpos'=>0, 'ypos'=>200)); 

	$pdf = new Cezpdf();
	$pdf->selectFont('Includes/pdf-related/Helvetica.afm');
	$pdf->ezSetCmMargins(1,1,1.5,1.5);
	$pdf->addJpegFromFile($logo,430,750,150,70);
	$titulo="Articulos activos en Stock por debajo Stock Critico";   
	$pdf->ezText($titulo,14);
	$pdf->ezText('',10);
	$pdf->ezText('',12);
	$pdf->ezText('Fecha: '.$fchhoy,10);
	$pdf->ezText('',10);
	$pdf->ezText('',12);
	$sentencia="Select * from StkArticulos as a, StkArtCls as c where a.StkCauBjaId=0 and a.StkArtCantMinimo>a.StkArtCantReal and a.StkArtClsId=c.StkArtClsId and c.StkArtClsBien='".$_COOKIE['tipobien']."' order by a.StkArtDsc";
	$resultado=mysqli_query($cn,$sentencia);
	$HuboCanje=" ";
	$i=0;
	while( $row=mysqli_fetch_array($resultado) )
	{
		$estadosol="Pendiente de Entrega";
		$sentenciaX="Select * from StkSolArticulos as a, StkSolicitudes as s where s.StkSolEstado='".$estadosol."' and s.StkSolId=a.StkSolId and a.StkArtId='".$row['StkArtId']."' and a.StkSolArtEstado='Pendiente'";
		$resultadoX=mysqli_query($cn,$sentenciaX);
		$Pendiente=0;
		while($unSolArtPen=mysqli_fetch_array($resultadoX))
		{
			//No considero las alteraciones de cantidad por un canje de articulo, ya que el estado del articulo en stksolarticulo es "Finalizada"
			//queda el renglon cerrado y No se incluye en este filtro.
			$Pendiente=$Pendiente+$unSolArtPen['StkSolArtCantSol'];
			$sentenciaXI="Select * from StkMovArticulos where StkArtId='".$row['StkArtId']."' and StkSolId='".$unSolArtPen['StkSolId']."'"; 
			$resultadoXI=mysqli_query($cn,$sentenciaXI);
			while($unSolArtPen=mysqli_fetch_array($resultadoXI))
			{
				//En StkMovArticulos pueden existir movimientos de Entrada con StkSolId<>Null siendo éstos movimientos "devoluciones de articulo",
				//el estado del articulo en stksolarticulos va a ser "Finalizada" por lo que no se incluye en este filtro. Igualmente analizando 
				//esta situación, si hubo devolución por la cantidad que sea es porque al usuario no le intereso conservarlo, no sería una cantidad
				//entonces una cantidad Pendiente.
				if($unSolArtPen['StkMovArtTpo']=='S')
				{
					$Pendiente=$Pendiente-$unSolArtPen['StkMovArtCant'];
				}
			}
		}
		$NombreArticulo=$row['StkArtDsc'];
		$data[$i]=array('Articulo'=>$NombreArticulo,'Cantidad Stock'=>$row['StkArtCantReal'],'Minimo'=>$row['StkArtCantMinimo'],'Pendientes de Entrega'=>$Pendiente);
		$i++;
	}
	$pdf->ezTable($data,"","",array('width'=>500));
	$pdf->ezText('',10);
	$pdf->ezStream();
	exit;
?>
