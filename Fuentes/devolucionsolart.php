<!--
Devolucion de articulo, cargo un combo con los articulos posibles de ser devueltos, es decir que deberían haber sido entregado al menos 1, y este no haber sido ya devuelto.

Se presentó la siguiente situación: Se realizo la entrega de material de una solicitud y por error se hizo entrega de un artículo que no estaba en la lista sustituyendo
a otro que si estaba. El procedimiento correcto hubiera sido realizar la sustitución durante el procedimiento de adjudicación, pero una vez confirmado no hay vuelta atrás. 
La solicitud quedó en estado "Finalizada". Se necesita entonces, devolver el artículo que figura como entregado, registrar el sustituto y su entrega, esto sería un cambio
o una sustitución de mercadería. Desde éste estado el único procedimiento que se podría hacer es la "Devolución de Artículo" faltando registrar al sustituto, debiendo controlar
stock al momento del movimiento. Este cambio de artículo entregado debería de poder realizarse tanto en el estado "Finalizada" como en "Pendiente de entrega" para solicitudes 
parciales de los artículos entregados.
-->
<?php
session_start();
if (!isset($_SESSION['logged']))
{
     echo '<meta http-equiv="refresh" content="0; url=login.php">';
}
else
{
	if ($_SESSION['logged']==2)
	{
	     echo '<meta http-equiv="refresh" content="0; url=login.php">';
	}
}
$fechaGuardada = $_SESSION["ultimoAcceso"];
$ahora = date("Y-n-j H:i:s");
$tiempo_transcurrido = (strtotime($ahora)-strtotime($fechaGuardada));
if($tiempo_transcurrido >= 1800) //30 min en seg.
{
   echo '<meta http-equiv="refresh" content="0; url=login.php">';
}
else
{
  $_SESSION["ultimoAcceso"] = $ahora;
} 
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$solid=$_REQUEST['solid'];
$unidadcall=$_REQUEST['unidadcall'];
$estadocall=$_REQUEST['estadocall'];
$artid=$_POST['artid']; //$artid=$_REQUEST['artid'];

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=="guardar")
{
	if ($_POST['cant']>"0")
	{
		$consultaX="Select * from StkMovArticulos where StkArtId='".$artid."' and StkSolId='".$solid."'";
		$resultadoX=mysqli_query($cn,$consultaX) or die('La consulta fall&oacute;: ' .mysqli_error());
		$entregado=0;
		while($artmov=mysqli_fetch_assoc($resultadoX))
		{
			if($artmov['StkMovArtTpo']=="E")
			{
				$entregado=$entregado-$artmov['StkMovArtCant'];
			}
			else
			{
				$entregado=$entregado+$artmov['StkMovArtCant'];
			}
		}

		if ($entregado<$_POST['cant'])
		{
		       echo 'Ingrese otra cantidad, solo puede devolver '.$entregado;
		}
		else
		{
			//Insertar Movimiento de devolucion de articulo de la solicitud
			$moventrada="E";
			$sentencia="Insert into StkMovArticulos (StkSolId, StkArtId, StkMovArtCant, StkMovArtTpo, StkMovArtFch, StkMovArtUsuCre, StkMovArtFchCre) values ('".$solid."','".$artid."','".$_POST['cant']."','".$moventrada."','".date("Y-m-d H:i")."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
			$articulosol = mysqli_query($cn, $sentencia);
			if (mysqli_affected_rows($cn)==0)
			{
			       echo 'Atenci&oacute;n: No se pudo realizar la devolucion del articulo '.mysqli_error();
			}	
			else
			{
				$consultaXI="Select * from StkArticulos where StkArtId='".$artid."'";
				$resultadoXI=mysqli_query($cn,$consultaXI);
				$articulo=mysqli_fetch_assoc($resultadoXI);
				$sumodevolucion=$articulo['StkArtCantReal']+$_POST['cant'];

				$sentenciaI="Update StkArticulos set StkArtCantReal='".$sumodevolucion."', StkArtUsuMod='".$_COOKIE['usuid']."', StkArtFchMod='".date("Y-m-d H:i")."' where StkArtId=".$artid;
				$devuelvoart = mysqli_query($cn, $sentenciaI);

				echo "<META HTTP-EQUIV='refresh' CONTENT='1 ; URL=index.php?estado=$estadocall&unidad=$unidadcall'>";
			}	
		}
	}
	else
	{
	       echo 'Ingrese cantidad del art&iacute;culo a devolver'.mysqli_error();
	}
}
?>

<SCRIPT LANGUAGE="JavaScript">
function validate(string)
{if (!string) return false;
    var Chars = "0123456789";  <!--var Chars = "0123456789-"; incluyendo negativos-->
    for (var i = 0; i < string.length; i++)
	{if (Chars.indexOf(string.charAt(i)) == -1)
		return false;
	}
return true;
}
</SCRIPT>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
<form name="datos" action="devolucionsolart.php?solid=<?php echo $solid;?>&unidadcall=<?php echo $unidadcall;?>&estadocall=<?php echo $estadocall;?>" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="2">
	<br>
	<font size="5" color="#000066"><img src="Images/devolucion.JPG" width="30" height="30" alt="Nuevo"/> Solicitud <?php echo $solid;?> - Devoluci&oacute;n de Art&iacute;culo </font>
	<br>
    </td>
    </tr>
  <tr>
    <td colspan="2">
    <table class="inventario">
      <tr>
        <td align="left"><label>Art&iacute;culos:</label></td>
        <td>
	       <select name="artid">
		<?php
			$consulta="Select * from StkSolArticulos as s, StkArticulos as a where s.StkArtId=a.StkArtId and s.StkSolId='".$solid."' order by a.StkArtDsc";
			$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
			while($ArtSol=mysqli_fetch_assoc($resultado))
			{
				$consultaX="Select * from StkMovArticulos where StkArtId='".$ArtSol['StkArtId']."' and StkSolId='".$solid."'";
				$resultadoX=mysqli_query($cn,$consultaX) or die('La consulta fall&oacute;: ' .mysqli_error());
				$entregado=0;
				$CantPen=$ArtSol['StkSolArtCantSol'];

				while($artmov=mysqli_fetch_assoc($resultadoX))
				{
					if($artmov['StkMovArtTpo']=="E")
					{
						$entregado= $entregado-$artmov['StkMovArtCant'];
					}
					else
					{
						$entregado= $entregado+$artmov['StkMovArtCant'];
					}
					$CantPen=$CantPen-$entregado;
				}
				if ($entregado==0)
				{//Hubo canje de articulo y no tuvo ninguna entrega de parcial, por lo tanto no permito seleccionar para devolver
				 //Hubo entrega y devolucion total
				 //No hubo entrega alguno por lo tanto no hay devolucion
				}
				else
				{
					$artcant=$ArtSol['StkArtCantReal'];
					echo "<option value='".$ArtSol['StkArtId']."'>".$ArtSol['StkArtDsc']."</option>";
				}
			}
		?>	
	       </select>
        </td>
      </tr>
      <tr>
        <td width="180" align="left"><label>Cantidad:</label></td>
	 <td>
		<input type="text" name="cant" maxlength="120" size="15" onChange="if (!validate(this.value)) alert('Indique un valor num&eacute;rico mayor a cero')"/>
        </td>
      </tr>
     <tr>
	<font size="4">En la lista de art&iacute;culos a devolver, solo aparecer&aacute;n los art&iacute;culos entregados y solo se permitir&aacute devolver como m&aacute;ximo la cantidad entregada.</font><br>
	<br>
     </tr>

    </table>
    </td>
  </tr>

  <tr>
    <td align="left">
	<a href="index.php?estado=<?php echo $estadocall; ?>&unidad=<?php echo $unidadcall; ?>"><img width="40" height="40" src="Images/volver.jpg"></a>
    </td>
    <td align="left">
	<input type="image" width="40" height="40" src="Images/guardar.png">
	<input type="hidden" name="accion" value="guardar" >
    </td>
  </tr>
</table>
</form>
</center>
<?php
require_once("devarticulossolver.php");
require_once("pie.php");
?>