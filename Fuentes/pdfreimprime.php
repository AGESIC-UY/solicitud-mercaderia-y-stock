<?php
header('Content-Type: text/html; charset=iso-8859-1); //UTF-8');
$solid=$_REQUEST['solid'];
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];

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
	$pdf->ezText('Estado de entrega a la fecha:  '.$fchhoy,14);
	$pdf->ezText('',3);
	$pdf->ezText('',10);
	$pdf->ezText('Unidad: '.$uniNombre,10);
	$pdf->ezText('',4);
	$laFch=cambiaf_a_normal($laFchSol);
	$pdf->ezText('Fecha solicitud: '.$laFch,10);
	$pdf->ezText('',4);
	$pdf->ezText('Solicitado por: '.$elUsuSol,10);
	$pdf->ezText('',4);
	$pdf->ezText('Estado de Solicitud: '.$laSolicitud['StkSolEstado'],10);
	$pdf->ezText('',14);

	$sentencia="Select * from StkSolArticulos as s, StkArticulos as a where a.StkArtId=s.StkArtId and s.StkSolId='".trim($solid)."' order by a.StkArtDsc";
	$resultado=mysqli_query($cn,$sentencia);
	$consejo=" ";
	$HuboCanje=" ";
	$HuboDevolucion=" ";
	$i=0;
	while( $row=mysqli_fetch_array($resultado) )
	{
		$sentenciaX="Select * from StkMovArticulos where StkArtId='".$row['StkArtId']."' and StkSolId='".trim($solid)."'"; 
		$resultadoX=mysqli_query($cn,$sentenciaX);
		$Devuelto=0;
		$Entregado=0;
		while($unMovArticulo=mysqli_fetch_array($resultadoX))
		{
			if($unMovArticulo['StkMovArtTpo']=='S')
			{
				$Entregado=$Entregado+$unMovArticulo['StkMovArtCant'];			
			}
			else
			{//Movimiento tipo 'E' relacionado a la solid, corresponde a una devolución de articulo.
				$Entregado=$Entregado-$unMovArticulo['StkMovArtCant'];			
				$Devuelto=$Devuelto+$unMovArticulo['StkMovArtCant'];
			}
		}

		$FaltaEntregar=$row['StkSolArtCantSol']-$Entregado;
		$NombreArticulo=$row['StkArtDsc'];
		if($Devuelto>'0')
		{//Hubo devolución de articulo
			$NombreArticulo=$NombreArticulo." (**)";
			$HuboDevolucion="(**) Hubo devolucion de articulo";
			if($Devuelto<>$FaltaEntregar)
			{//Devolución y además hubo canje.
				$NombreArticulo=$NombreArticulo." (*)";
				$HuboCanje="(*) Hubo canje por articulo similar";
			}
		}
		else
		{//Controlo si la entrega fue total o sigue existiendo alguna diferencia que correspondería a canje
			if($row['StkSolArtEstado']=='Finalizada' and $FaltaEntregar>0)
			{//Si el renglon esta considerado en estado finalizado pero aún falta entregar, y no entro por el if de devolución entonces es canje
				$NombreArticulo=$NombreArticulo." (*)";
				$HuboCanje="(*) Por la cantidad pendiente, hubo canje por articulo similar";
			}
		}

		if ($FaltaEntregar>'0' and $estadocall=="Pendiente de Entrega")
		{//Aún hay artículos pendientes de entrega, el consejo corresponde si el estado de la solicitud es pendiente de entrega no es el caso de finalizada
//			$consejo="La Solicitud con articulos pendientes queda en el estado 'Pendiente de Entrega', se aconseja cerrar esta solicitud y volver a pedir el articulo en el mes entrante, de lo contrario quedara a la espera por su resolución.";
			$consejo="La Solicitud con articulos pendientes quedará cerrada en el estado Finalizada, deberá volver a pedir el articulo en una nueva solicitud";
		}


		$data[$i]=array('Articulo'=>$NombreArticulo,'Cantidad'=>$row['StkSolArtCantSol'],'Entregados'=>$Entregado,'Pendientes'=>$FaltaEntregar);
		$i++;

	}

	$pdf->ezTable($data,"","",array('width'=>550));
	$pdf->ezText('',10);
	$pdf->ezText($consejo,10);
	$pdf->ezText('',10);
	$pdf->ezText($HuboCanje,10);
	$pdf->ezText('',12);
	$pdf->ezText($HuboDevolucion,10);
	$pdf->ezText('',10);
	$pdf->ezText('',12);
	$laFch=cambiaf_a_normal(date("Y-m-d H:i"));
	$pdf->ezText('Fecha Entrega:   '.$laFch,10);
	$pdf->ezText('',10);
	$pdf->ezText('',12);
	$pdf->ezText('Entregado por:______________________________________________',10);
	$pdf->ezText('',10);
	$pdf->ezText('',12);
	$pdf->ezText('Recibido por:_______________________________________________',10);
	$pdf->ezText('',10);
	$pdf->ezText('',12);



	$pdf->ezStream();

	exit;

?>
