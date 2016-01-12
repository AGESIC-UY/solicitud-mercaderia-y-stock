<!--
El articulo ingresado a traves de este objeto, es el articulo sustituto de otro, de una solicitud en estado "Pendiente de entrega"
Lo mas prolijo en este procedimiento complejo, sería realizar un insert del articulo a entregar, las cantidades podrían ser diferentes, ya que es otro articulo
y las espcificaciones del articulo son distintas, serán similares.

Si del articulo original no hubo entrega de material alguno, podria eliminarse, pero no si ya existe cantidades entregadas. 
NO VOY A ELMINAR EL ARTICULO ORIGINAL - Se mantiene para conservar la situación original del pedido, por lo que cambiaré las cantidades solicitas, a "0" si no 
hubo entrega o a la cantidad ya entregada si hubo entrega parcial.

Falta controlar en la sustitucion, cuando se quiere sustituir un articulo pendiente por un articulo que ya esta en la solicitud, debo hacer la diferencia si este articulo ya fue entregado en su
totalidad, si aun hay cantidad pendiente no afectaria en nada dado el procedimiento como se resolvio, pues este pasa a un estado finalizado, pero en el otro caso el estado ya esta finalizado, debería
volver a activa (estado pendiente para que vuelva a aparecer en la lista de ariticulos a entregar) e indicar la suma de las cantidades, dejando solo pendiente la cantidad que se esta queriendo ingresar.

-->

<?php
$solid=$_REQUEST['solid'];
$unidadcall=$_REQUEST['unidadcall'];
$estadosol=$_REQUEST['estadocall'];
$estadoart="Pendiente";
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idart=$_REQUEST['idart'];
if (isset($_REQUEST['cantsustituir'])){
$cantsustituir=$_REQUEST['cantsustituir'];
} else {
$cantsustituir= "";
}

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
	if ($_POST['artid']==$idart)
	{
	       echo 'No puede sustituir por el mismo art&iacute;culo'.mysqli_error();
	}
	else
	{
		if ($_POST['cant']>0)
		{
			$consulta="Select * from StkSolArticulos where StkSolId=".$solid." and StkArtId='".$_POST['artid']."'";
			$resultado=mysqli_query($cn,$consulta);
			$artsol=mysqli_fetch_assoc($resultado);
			$pendiente=$artsol['StkSolArtCantSol'];
			if (mysqli_affected_rows($cn)==0)
			{
				$consulta="Select * from StkSolArticulos where StkSolId=".$solid." and StkArtId='".trim($idart)."'";
				$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
				$artsol=mysqli_fetch_assoc($resultado);
				$pendiente=$artsol['StkSolArtCantSol'];

				//Inserto nuevo articulo a la solicitud
				$sentencia="Insert into StkSolArticulos (StkSolId, StkArtId, StkSolArtCantSol, StkSolArtEstado, StkSolArtUsuCre, StkSolArtFchCre) values ('".$solid."','".$_POST['artid']."','".$_POST['cant']."','".$estadoart."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
				$articulosol = mysqli_query($cn, $sentencia);
				if (mysqli_affected_rows($cn)==0)
				       echo 'Atenci&oacute;n: No se pudo ingresar el articulo a la solicitud: '.mysqli_error();
				else
					{//El articulo sustituido le cambio su estado a finalizado. Quedará registrado a la vista del usuario como articulo parcialmente entregado
					 //Si la entrega es cero queda en cero movimientos. Registro en StkSolArtCantCanje la cantidad que se canjea(o suspende en realidad)

					//Busco movimientos del articulo por si hubo parcialmente entregado y debo indicar cuanto se entrego y cuanto se canjea
					$consultaX="Select * from StkMovArticulos where StkArtId='".trim($idart)."' and StkSolId='".$solid."'";
					$resultadoX=mysqli_query($cn,$consultaX);
					$entregado=0;
					while($artmov=mysqli_fetch_assoc($resultadoX))
					{//solo podria haber movimientos de entrega no de devolucion ya que estamos aun en estado "Pendiente de Entrega", la devolucion solo 
					 //se puede efectuar en estado "Finalizada".
						$entregado=$entregado+$artmov['StkMovArtCant'];
					}
					$pendiente=$pendiente-$entregado;

					$sentenciaI="Update StkSolArticulos set StkSolArtEstado='Finalizada', StkSolArtCantAcred='".$entregado."', StkSolArtCantCanje='".$pendiente."' where StkSolId=".$solid." and StkArtId='".trim($idart)."'";
					$resultadoI=mysqli_query($cn,$sentenciaI);

					echo "<META HTTP-EQUIV='refresh' CONTENT='1 ; URL=disponibilidadstk.php?solid=$solid&estadocall=$estadocall&unidadcall=$unidadcall'>";
				}	
			}
			else
			{
			       echo 'El art&iacute;culo ya existe en la lista de pedido, se sumar&aacute a &eacute;ste la cantidad indicada'.mysqli_error();

				$consulta="Select * from StkSolArticulos where StkSolId=".$solid." and StkArtId='".trim($idart)."'";
				$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
				$artsol=mysqli_fetch_assoc($resultado);
				$pendiente=$artsol['StkSolArtCantSol'];

				//El articulo sustituido le cambio su estado a finalizado. Quedará registrado a la vista del usuario como articulo parcialmente entregado
				//Si la entrega es cero queda en cero movimientos. Registro en StkSolArtCantCanje la cantidad que se canjea(o suspende en realidad)
				//Busco movimientos del articulo por si hubo parcialmente entregado y debo indicar cuanto se entrego y cuanto se canjea
				$consultaX="Select * from StkMovArticulos where StkArtId='".trim($idart)."' and StkSolId='".$solid."'";
				$resultadoX=mysqli_query($cn,$consultaX);
				$entregado=0;
				while($artmov=mysqli_fetch_assoc($resultadoX))
				{//solo podria haber movimientos de entrega no de devolucion ya que estamos aun en estado "Pendiente de Entrega", la devolucion solo 
				 //se puede efectuar en estado "Finalizada".
					$entregado=$entregado+$artmov['StkMovArtCant'];
				}
				$pendiente=$pendiente-$entregado;
				$sentenciaI="Update StkSolArticulos set StkSolArtEstado='Finalizada', StkSolArtCantAcred='".$entregado."', StkSolArtCantCanje='".$pendiente."' where StkSolId=".$solid." and StkArtId='".trim($idart)."'";
				$resultadoI=mysqli_query($cn,$sentenciaI);


				$consulta="Select * from StkSolArticulos where StkSolId=".$solid." and StkArtId='".$_POST['artid']."'";
				$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
				$artsol=mysqli_fetch_assoc($resultado);

				$agregocant=$artsol['StkSolArtCantSol']+$_POST['cant'];
				$cantacred=$artsol['StkSolArtCantAcred']+$_POST['cant'];
				$comentario="Se agrega cantidad articulo sustituto";
				$sentenciaI="Update StkSolArticulos set StkSolArtEstado='Pendiente', StkSolArtCantSol='".$agregocant."', StkSolArtCantAcred='".$cantacred."', StkSolArtObs ='".$comentario."' where StkSolId=".$solid." and StkArtId='".$_POST['artid']."'";
				$resultadoI=mysqli_query($cn,$sentenciaI);

			}
		}
		else
		{
		       echo 'Ingrese cantidad del art&iacute;culo'.mysqli_error();
		}
	}

} //finaliza accion guardar if

$consulta="Select * from StkSolicitudes where StkSolId='".$solid."'";
$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
$unaSol=mysqli_fetch_assoc($resultado);
$elestadosol=$unaSol['StkSolEstado'];

$consultaI="Select * from StkArticulos where StkArtId='".$idart."'";
$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
$elArticulo=mysqli_fetch_assoc($resultadoI);
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
<form name="datos" action="sustituyoartsol.php?idart=<?php echo $idart;?>&solid=<?php echo $solid;?>&unidadcall=<?php echo $unidadcall;?>&estadocall=<?php echo $elestadosol;?>" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="2">
	<br>
	<font size="5" color="#000066"><img src="Images/sustituir.GIF" width="30" height="30" alt="Nuevo" border=0/> Sustituci&oacute;n de Art&iacute;culo en Solicitud <?php echo $solid;?></font>
	<hr style="color: rgb(69, 106, 221);">
	<br>
    </td>
    </tr>
  <tr>
    <td colspan="2">
    <table class="inventario">
      <tr>
        <td width="102" align="left"><label>Art&iacute;culo:</label></td>
        <td>
            <input type="text" name="artdsc" maxlength="120" size="60" value="<?php echo $elArticulo['StkArtDsc']; ?>" readonly="readonly"/>
        </td>
      </tr>
      <tr>
        <td width="102" align="left"><label>Sustituir por:</label></td>
	 <td>
        <select name="artid">
          <?php
		$consulta="Select * from StkArticulos as a, StkArtCls as c where a.StkCauBjaId=0 and a.StkArtClsId=c.StkArtClsId and c.StkArtClsBien='".$_COOKIE['tipobien']."' order by StkArtDsc";
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
		while($unArt=mysqli_fetch_assoc($resultado))
		{
			$ArtId=$unArt['StkArtId'];
			$ArtDsc=$unArt['StkArtDsc'];
			if ($ArtId==$idart)
			{
				echo "<option value='".$ArtId."' selected>".$ArtDsc."</option>";
			}
			else
			{
				echo "<option value='".$ArtId."'>".$ArtDsc."</option>";
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
    </table>
    </td>
  </tr>

  <tr>
    <td align="left">
	<?php
	$estadocalled=$_REQUEST['estadocall'];
	?>
	<a href="disponibilidadstk.php?solid=<?php echo $solid; ?>&estadocall=<?php echo $estadosol; ?>&unidadcall=<?php echo $unidadcall; ?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a>
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
require_once("pie.php");
?>