<!--	
El att StkSolArtCantPen de la tabla StkSolArticulo puede contener cantidad >0 cantidades pendientes de entrega del articulo. 

El att StkSolArtCantAcred de la tabla StkSolArticulo corresponde a la cantidad que se va a entregar del artículo en esta instancia. Esta cantidad puede
ser el saldo de la cantidad calculada en forma automática o indicado por usuario operador del stock. Considerando la entrega parcial de un artículo 
especifico por causas de distribución equitativa, ésta cantidad a indicar de entrega se "update" en el objeto updateartsol.php called desde disponibilidadstk.php
-->

<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idsol=$_REQUEST['idsol'];
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];

$sumatoriapendientes=0;
$sumatoriaadjudicar=0;
$estadoart="Pendiente";

$sentencia="Select * from StkSolArticulos where StkSolId='".$idsol."' and StkSolArtEstado='".$estadoart."'"; 
$resultado=mysqli_query($cn,$sentencia);
if (mysqli_num_rows($resultado)==0)
{
	echo '<br><center><label>No existen aticulos en la solicitud</label></center><br>';
}
else
{
	while($unSolArt=mysqli_fetch_assoc($resultado))
	{
		$sentenciaI="Select * from StkArticulos where StkArtId='".$unSolArt['StkArtId']."'";
		$resultadoI=mysqli_query($cn,$sentenciaI);
		$unArticulo=mysqli_fetch_assoc($resultadoI);
	       if (mysqli_num_rows($resultadoI)==0)
		{
			echo '<br><center><label>No encontro arti&acute;culo</label></center><br>';
		}
		else
		{
			if($unSolArt['StkSolArtCantPen']==0)
			{
				$StkSolArtEstado="Finalizada/o";
			}
			else
			{
				$StkSolArtEstado="Pendiente";
			}
				
			$sumatoriapendientes=$sumatoriapendientes+$unSolArt['StkSolArtCantPen'];
			if($unSolArt['StkSolArtCantAcred']>0)
			{
				$sumatoriaacreditar=$sumatoriaacreditar+$unSolArt['StkSolArtCantAcred'];
				$StkReal=$unArticulo['StkArtCantReal']-$unSolArt['StkSolArtCantAcred'];
				$sentenciaII="Update StkArticulos set StkArtCantReal='".$StkReal."', StkArtUsuMod='".$_COOKIE['usuid']."',StkArtFchMod='".date("Y-m-d H:i")."' where StkArtId='".$unSolArt['StkArtId']."'";
				$resultadoII=mysqli_query($cn,$sentenciaII);
				$tpomov="S";
				$sentenciaIII="Insert into StkMovArticulos (StkArtId,StkMovArtFch,StkMovArtTpo,StkMovArtCant,StkSolId,StkMovArtUsuCre,StkMovArtFchCre) values ('".$unSolArt['StkArtId']."','".date("Y-m-d H:i")."','".$tpomov."','".$unSolArt['StkSolArtCantAcred']."','".$idsol."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
				$resultadoIII=mysqli_query($cn,$sentenciaIII);
			}
			$sentenciaIV="Update StkSolArticulos set StkSolArtEstado='".$StkSolArtEstado."',StkSolArtUsuMod='".$_COOKIE['usuid']."',StkSolArtFchMod='".date("Y-m-d H:i")."' where StkSolId='".$idsol."' and StkArtId='".$unSolArt['StkArtId']."'";
			$resultadoIV=mysqli_query($cn,$sentenciaIV);
		}//Cierra if no hay articulo                  
	}//Cierra el While de los articulos de la solicitud
	//cambio de estado de la solicitud
	if($sumatoriapendientes==0)
	{
		$EstadoSol="Finalizada/o";
	}
	else
	{
		$EstadoSol="Pendiente de Entrega";
	}
	//No hay cambios de estado para la solicitud
	//echo '<br><center><label>Solicitud conservar&aacute su estado actual, No hubo ninguna adjudicaci&oacute;n de material</label></center><br>';
	//echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=index.php?estado=$estadocall&unidad=$unidadcall'>";
	$sentenciaV="Select StkSolicitudes where StkSolId=".$idsol;
	$resultadoV=mysqli_query($cn,$sentenciaV);
	$laSol=mysqli_fetch_assoc($resultadoV);
	$sentenciaVI="Update StkSolicitudes set StkSolEstado='".$EstadoSol."',StkSolUsuMod='".$_COOKIE['usuid']."',StkSolFchMod='".date("Y-m-d H:i")."', StkSolFchFin='".date("Y-m-d H:i")."' where StkSolId=".$idsol;
	if (!mysqli_query($cn,$sentenciaVI))
	{
		die('Error al adjudicar material a la Solicitud'.mysqli_error());
	}
	else
	{
		if($sumatoriapendientes==0)
		{
			?>
			<script type="text/javascript"> 
			alert ("Adjudicacion de Material confirmada");
			</script> 
			<?php
		}
		else
		{
			?>
			<script type="text/javascript"> 
			alert ("Solicitud queda a la Espera de reposicion de Material al Stock");
			</script> 
			<?php
		}
	}
	echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=index.php?estadocall=$estadocall&unidadcall=$unidadcall'>";
}//Cierra el IF donde se pregunta si hay resultados existencia articulos en la solicitud
?>
