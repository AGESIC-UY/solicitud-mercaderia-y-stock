<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");

$idart=$_REQUEST['idart'];
$clase=$_REQUEST['clase'];
$fchhasta=$_REQUEST['fchhasta'];
$artbus=$_REQUEST['artbus'];
$actividad=$_REQUEST['actividad'];
$escero=0;

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
if ($_POST['cantmov']=="" or $_POST['cantmov']=="0")
	echo "<font color='#FF0000'>La cantidad no puede ser cero</font>";
else
	$movimiento=$_POST['tpomov'];
	$sentencia="select * from StkCausal where StkCauId ='".$_POST['caucorrijo']."'";
	$resultado = mysqli_query($cn,$sentencia);
	$lacausal=mysqli_fetch_array($resultado);
	$cumple=0;
	if ($lacausal['StkCauIn']==1 and $movimiento=='E')
	{
		$cumple=1;
	}
	if ($lacausal['StkCauOut']==1 and $movimiento=='S')
	{
		$cumple=1;
	}
	if ($cumple=='0')
	{
		echo 'El tipo de movimiento no corresponde con la causa';
	}
	else
	{
		$sentencia="select * from StkArticulos where StkArtId='".$idart."'";
		$resultado = mysqli_query($cn,$sentencia);
		$elArticulo=mysqli_fetch_array($resultado);
		if ($movimiento=='S' and $elArticulo['StkArtCantReal']<$_POST['cantmov'])
		{
			echo 'La cantidad en el Stock es menor al movimiento de salida, Imposible realizar el movimiento';
		}
		else
		{
			$lafechanow=date("Y-m-d H:i");
			$lafechahoy=date("Y-m-d");
			$sentencia="Insert into StkMovArticulos (StkArtId,StkMovArtFch,StkMovArtTpo,StkMovArtCant,StkMovArtPorId,StkMovArtUsuCre,StkMovArtFchCre,StkMovArtObs) values ('".$idart."','".$lafechanow."','".$movimiento."','".$_POST['cantmov']."','".$_POST['caucorrijo']."','".$_COOKIE['usuid']."','".$lafechanow."','".$_POST['observa']."')";
			$insert= mysqli_query($cn, $sentencia);
			if ($insert==0)
				echo 'Atenci&oacute;n: No se pudo ingresar el movimiento intente nuevamente';
			else
			{
				if ($movimiento=='E')
				{
				$sentencia="Update StkArticulos set StkArtCantReal=StkArtCantReal+'".$_POST['cantmov']."', StkArtUsuMod='".$_COOKIE['usuid']."', StkArtFchMod='".$lafechanow."' where StkArtId=".$idart;
				}
				else
				{
				$sentencia="Update StkArticulos set StkArtCantReal=StkArtCantReal-'".$_POST['cantmov']."', StkArtUsuMod='".$_COOKIE['usuid']."', StkArtFchMod='".$lafechanow."' where StkArtId=".$idart;
				}
				$update= mysqli_query($cn, $sentencia);
				if ($update==0)
					echo 'Atenci&oacute;n: No se pudo alterar la cantidad del stock, pero si se registro el movimiento avise a informatica';
				else
				{
					echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=articulos.php?clase=$clase&articulo=$idart&fchhasta=$fchhasta&artbus=$artbus&actividad=$actividad'>";
				}
			}
		}
	}
}
$consulta="select * from StkArticulos where StkArtId='".$idart."'";
$resultado = mysqli_query($cn,$consulta);
$elArticulo=mysqli_fetch_array($resultado);
$cantreal=$elArticulo['StkArtCantReal'];
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
<form name="datos" action="updateartstk.php?idart=<?php echo $idart;?>&clase=<?php echo $clase;?>&fchhasta=<?php echo $fchhasta;?>&artbus=<?php echo $artbus;?>&actividad=<?php echo $actividad;?>" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="2">
	<br>
	<font size="5" color="#000066"><img src="Images/derivar.png" width="30" height="30" alt="Nuevo" border=0/> Correcci&oacute;n de Saldo de Art&iacute;culo  </font>
    </td>
    </tr>
  <tr>
    <td colspan="2">
    <table class="inventario">
      <tr>
        <td align="left"><label>Art&iacute;culo:</label></td>
        <td>
            <input type="text" name="artdsc" maxlength="120" size="70" value="<?php echo $elArticulo['StkArtDsc']; ?>" readonly="true"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Stock Actual:</label></td>
        <td>
       	<input type="text" name="cantstk" maxlength="120" size="15" value="<?php echo $elArticulo['StkArtCantReal']; ?>" readonly="true"/>
	 </td>
      </tr>
	<tr>
		<td width="180" align="left"><label>Movimiento:</label></td>
		<td>
       	<select name="tpomov">
			<?php
			echo '<option value="E" selected>Entrada</option>';
			echo '<option value="S" selected>Salida</option>';
			?>	
	       </select>
		</td>
	</tr>
      <tr>
        <td align="left"><label>Cantidad:</label></td>
        <td>
       	<input type="text" name="cantmov" maxlength="120" size="15"/>
	 </td>
      </tr>
      <tr>
        <td align="left"><label>Causa:</label></td>
        <td>
	       <select name="caucorrijo">
		<?php
			$consulta="Select * from StkCausal where StkCauTpo='".Definitivo."' or StkCauTpo='".Articulo."' order by StkCauDsc";
			$resultado=mysqli_query($cn,$consulta);
			while($Causal=mysqli_fetch_assoc($resultado))
			{
				echo "<option value='".$Causal['StkCauId']."'>".$Causal['StkCauDsc']."</option>";
			}
		?>	
	       </select>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Observaci&oacute;n:</label></td>
        <td>
       	<input type="text" name="observa" maxlength="120" size="100"/>
	 </td>
      </tr>
    </table>
    </td>
	<tr>
	<td align="left"><a href="articulos.php?clase=<?php echo $clase;?>&articulo=<?php echo $idart;?>&fchhasta=<?php echo $fchhasta;?>&artbus=<?php echo $artbus;?>&actividad=<?php echo $actividad;?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a></td>
	<td align="left"><input type="image" width="40" height="40" src="Images/guardar.png"><input type="hidden" name="accion" value="guardar" ></td>
  </tr>
</table>
<br>
</form>
</center>
</html>
<?php
require_once("pie.php");
?>