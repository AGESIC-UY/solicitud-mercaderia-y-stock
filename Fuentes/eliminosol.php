<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");

$solid=$_REQUEST['solid'];
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];

$sentencia="Select * from StkSolicitudes where StkSolId=".$solid;
$resultado=mysqli_query($cn,$sentencia);
$laSol=mysqli_fetch_assoc($resultado);
$estadosol=$lasol['StkSolEstado'];
$parcialsol=$lasol['StkSolParcial'];


//Si el estado esta en "Pendiente" y el att StkSolParcial=1, hubo material adjudicado y entregado de alguno de los articulos de la solicitud, dejando en espera algun articulo
//en particular, o alguna cantidad que no se pudo cumplir, por lo que la anulación o cancelación de una solicitud en este estado, se debería de considerar como "Finalizada" 
//consecuencia de la existencia de material entregado, quedando el detalle de articulo(entregas y pendientes). Y el StkSolParcial=2 (identifica las sols.Finalizada forzadamente 
//con parcial)
//El StkSolParcial=1 se registra en el proceso de adjudicación de articulos, cuando quedan articulos pendientes y el estado de la sol aún queda en "Pendiente".

$consulta="Select * from StkMovArticulos where StkSolId='".$solid."'";
$resultado=mysqli_query($cn,$consulta);
if (mysqli_num_rows($resultado)==0)
{
	$estado="Cancelada";
	$Parcial=0;
}
else
{
	//Hay movimientos de stock cambia a estado finalizada con entregas parciales
	$estado="Finalizada";
	$Parcial=2;
}

$sentencia="Update StkSolicitudes set StkSolEstado='".$estado."', StkSolParcial=$Parcial, StkSolUsuMod='".$_COOKIE['usuid']."',StkSolFchMod='".date("Y-m-d H:i")."', StkSolFchFin='".date("Y-m-d H:i")."' where StkSolId=".$solid;
if (!mysqli_query($cn,$sentencia))
{
	die('Error al cambiar el estado de la Solicitud'.mysqli_error());
}
if ($estadocall=="Construyendo")
{
	?>
       <script type="text/javascript"> alert ("La Solicitud seleccionada ha sido Cancelada");</script>
	<?php
}
else
{
	?>
	<script type="text/javascript"> alert ("La Solicitud seleccionada a sido Cancelada o Finalizada si hubiese entregas parciales de articulo");</script>
	<?php
}
echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=index.php?estado=$estadocall&unidad=$unidadcall'>";    
?>



