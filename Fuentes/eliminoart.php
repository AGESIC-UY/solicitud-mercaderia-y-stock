<!--
Observaciones:
	1.- El ingreso a este objeto fue permitido pues el control en el objeto anterior lo permitio.
	2.- En el caso de articulos de consumo:
		a.- Si los StkArtId existen en StkSolArticulos y StkMovArticulos no son Delete pues no debería de permitirse eliminar el articulo si hay registros en estas dos tablas. 
			Una no excluye a la otra, pues es posible que exista movimientos por ingreso al stock por facturas, o corrección de saldo y no habiendo nunca emitido una solicitud.
	3.- En el caso de inventario:
		a.- La eliminación de un articulo se permitirá si no existe información registrada para etiquetas(de especificaciones y/o adjudicaciones)
		b.- Debo eliminar StkArtBCEsp(especificaciones de las etiquetas y de las etiquetas que son componentes, estos ultimos no son inventario al estar vinculados a la etiqueta primaria,
			por lo que tambien debo eliminar toda su estructura de datos). 
		c.- Nunca ingreso a este objeto si el articulo que deseo eliminar es componente en otro. Primero debería poder desvincularlo de la etiqueta primaria. 
		d.- Procedimiento de eliminación:
			1.- Elimino todo lo correspondiente a etiquetas, buscaré etiquetas secundarias de las primarias,primero elimino las estructuras de especificaciones de las secundarias 
				y luego elimino la secundarias. 
			2.- Eliminada la estructura de las Etiquetas secundarias, paso a eliminar lo correspondiente a la primaria.
			3.- Elimino lo correspondiente al articulo, primero desvinculo articulos componentes si corresponde(vinculación generica)
			4.- Elimino especificaciones genericas y sus valores, valores posibles de todas las especificaciones del articulo y elimino especificaciones
			5.- El ambiente inventario, genera al menos un movimiento en StkMovArticulos cuando se registra un nuevo articulo, pues este ingreso ejecuta la generación de etiquetas
				que se deben vincular a un movimiento de dicha tabla. Si la cantidadini=0 no genero etiquetas pero este movimiento igualmente existe. Debiendo ser eliminado si
				el articulo es eliminado.
			6.- El ambiente inventario no cuenta con reserva de articulo, obvio control sobre esta tabla como lo hace el de consumo
			7.- Por ultimo elimino articulo
-->

<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$clase=$_REQUEST['clase'];
$idart=$_REQUEST['articulo'];
$fchdesde=$_REQUEST['fchdesde'];
$fchhasta=$_REQUEST['fchhasta'];
$artbus=$_REQUEST['artbus'];

//Vuelvo a controlar que el articulo no haya sido seleccionado por alguna solicitud en proceso.
//ya que trabajando en tiempo real podría suceder. (Error que sucedio!!!) 
	$consultaII="Select * from StkSolArticulos where StkArtId='".$idart."'";
	$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
       if (mysqli_num_rows($resultadoII)==0)
	{
		$consultaII="Select * from StkMovArticulos where StkArtId='".$idart."'";
		$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
	       if (mysqli_num_rows($resultadoII)==0)
		{
			$sentencia="Delete from StkArtDep where StkArtId='".$idart."'";
			if (!mysqli_query($cn,$sentencia))
			{
				die('Error al eliminar el Articulos reservados'.mysqli_error());
			}	
			else
			{
				$sentencia="Delete from StkArticulos where StkArtId='$idart'";
				if (!mysqli_query($cn,$sentencia))
				{
					die('Error al eliminar el Articulo'.mysqli_error());
				}
				else
				{
					echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=articulos.php?clase=$clase&fchdesde=$fchdesde&fchhasta=$fchhasta&artbus=$artbus&actividad=$actividad'>";    
				}
			}
		}
	}
?>
