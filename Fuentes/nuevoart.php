<!--
Obsevaciones:
1.- Objeto para realizar el alta de un articulo, tanto de Stock como de Inventario.  
2.- Solicitará un valor para stock crítico(Representa el mínimo para dar aviso de realizar compra para no pasar por debajo de éste). 
3.- Solicitará valores para: costo basico, costo promedio y tipo de iva
-->
<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$articulo="99";
$clase=$_REQUEST['clase'];
$fchhasta=$_REQUEST['fchhasta'];
$artbus=$_REQUEST['artbus'];
$actividad=$_REQUEST['actividad'];
$escero=0;

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
	if ($_POST['artdsc']=="")
		echo "<font color='#FF0000'>La descripci&oacute;n del art&iacute;culo no puede ser vac&iacute;a</font>";
	else
	{
		if (isset($_POST['cantmin'])){
			$cantmin=$_POST['cantmin'];
		} else {
			$cantmin= "";
		}
		if (isset($_POST['iva'])){
			$iva=$_POST['iva'];
		} else {
			$iva= "";
		}
		$sentencia="Insert into StkArticulos (StkArtDsc, StkArtCantReal, StkArtCantFicto, StkArtCantMinimo, StkArtUsuCre, StkArtFchCre, StkArtClsId, StkArtIVA) values ('".$_POST['artdsc']."','".$_POST['cantini']."','".$_POST['cantini']."','".$cantmin."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."','".$_POST['clasificacion']."','".$iva."')";
		$insert = mysqli_query($cn, $sentencia);
		if (mysqli_affected_rows($cn)==0)
			echo 'Atenci&oacute;n: No se pudo ingresar el Art&iacute;culo por el error: '.mysqli_error();
		else
		{
			$consulta="Select * from StkArticulos where StkArtDsc='".$_POST['artdsc']."'"; //StkArtDsc tiene I.Unique => trae id reciente
			$resultado=mysqli_query($cn,$consulta);
			$elArt=mysqli_fetch_assoc($resultado);
			$idart=$elArt['StkArtId'];

			$lafechanow=date("Y-m-d H:i");
			$lafechahoy=date("Y-m-d");
			$tipomov='E';
			$StkMovArtPorId=17; //id "Saldo inicial"
			$observa="Alta - ingreso de saldo Inicial";

			if ($_POST['cantini']>0)
			{
				$sentencia="Insert into StkMovArticulos (StkArtId,StkMovArtFch,StkMovArtTpo,StkMovArtCant,StkMovArtPorId,StkMovArtUsuCre,StkMovArtFchCre,StkMovArtObs) values ('".$idart."','".$lafechanow."','".$tipomov."','".$_POST['cantini']."','".$StkMovArtPorId."','".$_COOKIE['usuid']."','".$lafechanow."','".$observa."')";
				$insert= mysqli_query($cn, $sentencia);
				if ($insert==0)
					echo 'Atenci&oacute;n: No se pudo ingresar el movimiento intente nuevamente';
				else
				{
				echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=articulos.php?clase=$clase&fchhasta=$fchhasta&artbus=$artbus&actividad=$actividad'>";
				}
			}
		}
	}
}
?>

<SCRIPT LANGUAGE="JavaScript">
function validate(string) {
    if (!string) return false;
    var Chars = "0123456789";  <!--var Chars = "0123456789-"; incluyendo negativos-->
    for (var i = 0; i < string.length; i++)
	{
	if (Chars.indexOf(string.charAt(i)) == -1)
		return false;
	}
		return true;
	}
</SCRIPT>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
<form name="datos" action="nuevoart.php?clase=<?php echo $clase;?>&articulo=<?php echo $articulo;?>&fchhasta=<?php echo $fchhasta;?>&actividad=<?php echo $actividad;?>" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="2">
	<br>
	<font size="5" color="#000066"><img src="Images/nuevo.png" width="30" height="30" alt="Nuevo" border=0/> Registro de Nuevo Art&iacute;culo </font>
	<br>
    </td>
    </tr>
  <tr>
    <td colspan="2">
    <table class="inventario">
      <tr>
        <td align="left"><label>Art&iacute;culo:</label></td>
        <td>
            <input type="text" name="artdsc" maxlength="120" size="70"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Stock inicial:</label></td>
        <td>
            <input type="text" name="cantini" maxlength="70" size="15" onChange="if (!validate(this.value)) alert('Indique un valor num&eacute;rico mayor o igual a cero')"/>
        </td>
      </tr>
      	<tr>
         <td align="left"><label>Stock cr&iacute;tico:</label></td>
      	  <td>
             <input type="text" name="cantmin" maxlength="70" size="15" onChange="if (!validate(this.value)) alert('Indique un valor num&eacute;rico mayor o igual a cero')"/>
      	  </td>
       </tr>
      <tr>
        <td align="left"><label>Clasificaci&oacute;n:</label></td>
        <td>
	       <select name="clasificacion">
		<?php
			$consulta="Select * from StkArtCls as c, StkArtClsUsu as u where c.StkArtClsId=u.StkArtClsId and u.UsuId='".$_COOKIE['usuid']."' order by c.StkArtClsDsc";
			$resultado=mysqli_query($cn,$consulta);
			while($Claseart=mysqli_fetch_assoc($resultado))
			{
				echo '<option value="'.$Claseart['StkArtClsId'].'" selected>'.$Claseart['StkArtClsDsc'].'</option>';
			}
		?>	
	       </select>
        </td>
     </tr>
	      <tr>
       	 <td align="left"><label>Tipo IVA:</label></td>
	        <td>
		       <select name="iva">
			<?php
			$consulta="Select * from IVAS";
			$resultado=mysqli_query($cn,$consulta);
			while($ivas=mysqli_fetch_assoc($resultado))
			{
				echo '<option value="'.$ivas['IVAVal'].'" selected>'.$ivas['IVA'].'</option>';
			}

			?>	
		       </select>
	        </td>
      </tr>
    </table>
	</td>
  </tr>

  <tr>
    <td align="left">&nbsp;&nbsp;&nbsp;
	<a href="articulos.php?clase=<?php echo $clase;?>&articulo=<?php echo $articulo;?>&fchhasta=<?php echo $fchhasta;?>&artbus=<?php echo $artbus;?>&actividad=<?php echo $actividad;?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a>
    </td>
    <td align="left">
	<input type="image" width="40" height="40" src="Images/guardar.png">
	<input type="hidden" name="accion" value="guardar" >
    </td>
  </tr>
</table>
<br>
</form>
</center>
</html>

<?php
require_once("pie.php");
?>