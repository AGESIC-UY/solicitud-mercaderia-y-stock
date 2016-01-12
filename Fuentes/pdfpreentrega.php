<?php
header('Content-Type: text/html; charset=iso-8859-1); //UTF-8');
$solid=$_REQUEST['solid'];
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];

require_once("Includes/conviertefecha.php");
$desde=$_GET['desde'];
$hasta=$_GET['hasta'];
$agenda=$_GET['agenda'];
//$consejo="La Solicitud con articulos pendientes queda en el estado 'Pendiente de Entrega', se aconseja cerrar esta solicitud y volver a pedir el articulo en el mes entrante, de lo contrario quedara a la espera por su resolución.";
$consejo="La Solicitud con articulos pendientes quedará cerrada en el estado Finalizada, deberá volver a pedir el articulo en una nueva solicitud";
$file = fopen("configbd.conf", "r") or exit("Unable to open file!");
$servidor= trim(str_replace("serv:","", fgets($file)));
$usu=trim(str_replace("user:","", fgets($file)));
$pass=trim(str_replace("pass:","", fgets($file)));
$base=trim(str_replace("db:","", fgets($file)));
fclose($file);
$cn=mysqli_connect($servidor,$usu,$pass,$base) or die (mysqli_connect_error().": ".mysqli_connect_error());
function limpiar($texto){$txt=trim(strip_tags($texto));return $txt;}

	$consulta="Select * from Sistemas";
	$resultado=mysqli_query($cn,$consulta) or die('La consulta del area fall&oacute;: ' .mysqli_error());
	$sis=mysqli_fetch_assoc($resultado);
	$logo=$sis['SisLogo'];

	$sentenciaII="Select * from StkSolicitudes where StkSolId='".$solid."'";
	$resultadoII=mysqli_query($cn,$sentenciaII) or die('La consulta fall&oacute;: ' .mysqli_error());
	$laSolicitud=mysqli_fetch_assoc($resultadoII);
	$laFchSol=$laSolicitud['StkSolFchSol'];

	$sentenciaIII="Select * from Departamentos where DepId='".$laSolicitud['StkSolSecId']."'";
	$resultadoIII=mysqli_query($cn,$sentenciaIII) or die('La consulta fall&oacute;: ' .mysqli_error());
       $uniEle=mysqli_fetch_assoc($resultadoIII);
	$uniNombre=$uniEle['DepNombre'];

	$sentenciaIV="Select * from Usuarios where UsuId='".$laSolicitud['StkSolUsuSol']."'";
	$resultadoIV=mysqli_query($cn,$sentenciaIV) or die('La consulta fall&oacute;: ' .mysqli_error());
       $elUsuario=mysqli_fetch_assoc($resultadoIV);
	$elUsuSol=$elUsuario['UsuNombre']." ".$elUsuario['UsuApellido'];

	include('Includes/class.ezpdf.php');
	$pdf = new Cezpdf();
	$pdf->selectFont('Includes/pdf-related/Helvetica.afm');
	$pdf->ezSetCmMargins(1,1,1.5,1.5);
	$pdf->addJpegFromFile($logo,430,750,150,70);

	$pdf->ezText('Solicitud de Mercaderia:  '.$solid,14);
	$pdf->ezText('',3);
	$pdf->ezText('Unidad: '.$uniNombre,10);
	$pdf->ezText('',4);
	$laFch=cambiaf_a_normal($laFchSol);
	$pdf->ezText('Fecha solicitud: '.$laFch,10);
	$pdf->ezText('',4);
	$pdf->ezText('Solicitado por: '.$elUsuSol,10);
	$pdf->ezText('',4);
	$pdf->ezText('Estado de Solicitud: '.$laSolicitud['StkSolEstado'],20);
	$pdf->ezText('',14);


	$sentencia="Select * from StkSolArticulos as s, StkArticulos as a where a.StkArtId=s.StkArtId and s.StkSolId='".trim($solid)."' order by a.StkArtDsc";
	$resultado=mysqli_query($cn,$sentencia);
	$consejo=" ";
	$HuboCanje=" ";
	$HuboDevolucion=" ";
	$i=0;
	while( $row=mysqli_fetch_array($resultado) )
	{

		$sentenciaX="Select * from StkMovArticulos where StkArtId='".$row['StkArtId']."' and StkSolId='".trim($solid)."' and StkMovArtTpo='".S."'"; 
		$resultadoX=mysqli_query($cn,$sentenciaX);
		$Devuelto=0;
		$Entregado=0;
		while($unMovArticulo=mysqli_fetch_array($resultadoX))
		{
			$Entregado=$Entregado+$unMovArticulo['StkMovArtCant'];			
		}
		$Entregado=$Entregado-$row['StkSolArtCantAcred']; //Descuento el movimiento que estoy imprimiendo ahora como entrega

		$Entregando=$row['StkSolArtCantAcred'];

		$NombreArticulo=$row['StkArtDsc'];
		$FaltaEntregar=$Entregado+$Entregando;
		if($row['StkSolArtEstado']=='Finalizada' and $FaltaEntregar<>$row['StkSolArtCantSol'])
		{
			$NombreArticulo=$NombreArticulo." (*)";
			$HuboCanje="(*) Por la cantidad pendiente, hubo canje por articulo similar";
			$Entregando=0;
		}
		else
		{
		}
//por si luego vuelve requerimiento de entrega parcial
//		$data[$i]=array('Articulo'=>$NombreArticulo,'Cantidad'=>$row['StkSolArtCantSol'],'Entregados'=>$Entregado,'Entregando'=>$Entregando,'Pendientes'=>$row['StkSolArtCantPen']);

		$sentenciaX="Select * from StkArticulos where StkArtId='".$row['StkArtId']."'"; 
		$resultadoX=mysqli_query($cn,$sentenciaX);
		$articulo=mysqli_fetch_array($resultadoX);

		$data[$i]=array('Articulo'=>$NombreArticulo,'Pedido'=>$row['StkSolArtCantSol'],'Stock actual'=>$articulo['StkArtCantReal'],'Si entrega'=>$articulo['StkArtCantReal']-$row['StkSolArtCantSol']);
		$i++;

	}

	$pdf->ezTable($data,"","",array('width'=>400));
	$pdf->ezText('',12);
	$pdf->ezText('',12);
	$pdf->ezStream();

	exit;

?>

