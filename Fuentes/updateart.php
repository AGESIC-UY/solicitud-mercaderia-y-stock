<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idart=$_REQUEST['idart'];
$clase=$_REQUEST['clase'];
$fchhasta=$_REQUEST['fchhasta'];
if (isset($_REQUEST['artbus'])){
$artbus=$_REQUEST['artbus'];
} else {
$artbus= "";
}
if (isset($_REQUEST['actividad'])){
$actividad=$_REQUEST['actividad'];
} else {
$actividad= "";
}

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
	if ($_POST['artdsc']=="")
	{
		echo "<font color='#FF0000'>El nombre del art&iactue;culo no puede ser vac&iacute;a</font>";
	}
	else
	{
		$consulta="select * from StkArticulos where StkArtDsc=".$_POST['artdsc'];
		$resultado=mysqli_query($cn, $consulta);
		$otroArticulo=mysqli_fetch_array($resultado);
		if ($otroArticulo['StkArtId']<>$elArticulo['StkArtId'])
		{
			echo 'Ya existe otro c&oacute;digo para la descripci&oacute;n que desea ingresar';
		}
	       else
		{
			if ($_POST['causabaja']=="0")
			{
				$sentencia="Update StkArticulos set StkArtDsc='".$_POST['artdsc']."',StkArtCantMinimo='".$_POST['cantmin']."',StkArtCantReal='".$_POST['cantstk']."', StkArtFchFin=NULL, StkCauBjaId=NULL, StkArtUsuMod='".$_COOKIE['usuid']."', StkArtFchMod='".date("Y-m-d H:i")."', StkArtClsId='".$_POST['clasificacion']."', StkArtIVA='".$_POST['iva']."' where StkArtId=".$idart;
			}
		       else
			{
				$sentencia="Update StkArticulos set StkArtDsc='".$_POST['artdsc']."',StkArtCantMinimo='".$_POST['cantmin']."',StkArtCantReal='".$_POST['cantstk']."', StkArtFchFin='".date("Y-m-d H:i")."', StkCauBjaId='".$_POST['causabaja']."', StkArtUsuMod='".$_COOKIE['usuid']."', StkArtFchMod='".date("Y-m-d H:i")."',StkArtClsId='".$_POST['clasificacion']."', StkArtIVA='".$_POST['iva']."' where StkArtId=".$idart;
			}

			$update= mysqli_query($cn, $sentencia);
			if ($update==0)
			{
				echo 'Atenci&oacute;n: No se pudo modificar ficha del Articulo puede que la descripci&oacute;n ya exista';
			}
			else
			{
				echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=articulos.php?clase=$clase&articulo=$idart&fchhasta=$fchhasta&artbus=$artbus&actividad=$actividad'>";
			} 
		}
	} 
}
$consulta="select * from StkArticulos where StkArtId='".$idart."'";
$resultado = mysqli_query($cn,$consulta);
$elArticulo=mysqli_fetch_array($resultado);

$clasificacionart=$elArticulo['StkArtClsId'];
$causalbjaart=$elArticulo['StkCauBjaId'];
$ivaart=$elArticulo['StkArtIVA'];
$componentes=$elArticulo['StkArtComp'];
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
<form name="datos" action="updateart.php?idart=<?php echo $idart;?>&clase=<?php echo $clase;?>&fchhasta=<?php echo $fchhasta;?>&artbus=<?php echo $artbus;?>&actividad=<?php echo $actividad;?>" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="2">
	<br>
	<font size="5" color="#000066"><img src="Images/modificar.png" width="30" height="30" alt="Nuevo" border=0/>  Ficha del Art&iacute;culo  </font>
    </td>
    </tr>
    <tr>
    <td colspan="2">
    <table class="inventario">
      <tr>
        <td align="left"><label>Art&iacute;culo:</label></td>
        <td>
            <input type="text" name="artdsc" maxlength="120" size="70" value="<?php echo $elArticulo['StkArtDsc']; ?>"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Stock Actual:</label></td>
        <td>
       	<input type="text" name="cantstk" maxlength="120" size="15" value="<?php echo $elArticulo['StkArtCantReal']; ?>" readonly="true"/>
	 </td>
      </tr>
	<tr>
	<td align="left"><label>Stock Cr&iacute;tico:</label></td>
      	<td>
      		<input type="text" name="cantmin" maxlength="120" size="15" value="<?php echo $elArticulo['StkArtCantMinimo']; ?>" onChange="if (!validate(this.value)) alert('Indique un valor num&eacute;rico')"/>
	</td>
	</tr>
      <tr>
        <td align="left"><label>Estado Art&iacute;culo:</label></td>
        <td>
		<?php
		//facturas pendientes con el articulo
		$consulta="Select * from StkPrvFacturas as f, StkMovArticulos as m where m.StkArtId='".$idart."' and f.StkPrvFacFin<'2' and m.StkPrvFacId=f.StkPrvFacId";
		$resultado=mysqli_query($cn,$consulta);
		$nobjafac="1";
	       if (mysqli_num_rows($resultado)==0)
		{
			$nobjafac="0";
		}

		//solicitudes en tramite con el articulo
		$consulta="Select * from StkSolArticulos as a, StkSolicitudes as s where a.StkArtId='".$idart."' and s.StkSolId=a.StkSolId and (s.StkSolEstado='Construyendo' or s.StkSolEstado='Pendiente de Entrega' or s.StkSolEstado='Autorizar')";
		$resultado=mysqli_query($cn,$consulta);
		$nobjasol="1";
	       if (mysqli_num_rows($resultado)==0)
		{
			$nobjasol="0";
		}
		if ($nobjafac=="1" or $nobjasol=="1")
		{
			echo "Activo - No tiene causal de baja, existen solicitudes o facturas en proceso";
		}
		else
		{
		?>	
		       <select name="causabaja">
			<?php
			$StkCauBjaTpo="Articulo";
			$consulta="Select * from StkCausal where StkCauTpo='".$StkCauBjaTpo."' order by StkCauDsc";
			$resultado=mysqli_query($cn,$consulta);
			echo '<option value="0">-- Activo --</option>';
			while($CausalBaja=mysqli_fetch_assoc($resultado))
			{
				if ($CausalBaja['StkCauId'] == $causalbjaart)
				{
					echo "<option value='".$CausalBaja['StkCauId']."' selected>".$CausalBaja['StkCauDsc']."</option>";
				}
				else
				{
					echo "<option value='".$CausalBaja['StkCauId']."'>".$CausalBaja['StkCauDsc']."</option>";
				}
			}
			?>	
		       </select>
		<?php
		}
		?>	
        </td>
      </tr>
      <tr>
        <td align="left"><label>Clasificaci&oacute;n:</label></td>
        <td>
	       <select name="clasificacion">
		<?php
			$uno=1;
			$consulta="Select * from StkArtCls as c, StkArtClsUsu as u where c.StkArtClsId=u.StkArtClsId and u.UsuId='".$_COOKIE['usuid']."' and u.StkArtClsHab='".$uno."' order by c.StkArtClsDsc";
			$resultado=mysqli_query($cn,$consulta);
			while($ClaseArt=mysqli_fetch_assoc($resultado))
			{
				if ($ClaseArt['StkArtClsId']==$clasificacionart)
				{
					echo "<option value='".$ClaseArt['StkArtClsId']."' selected>".$ClaseArt['StkArtClsDsc']."</option>";
				}
				else
				{
					echo "<option value='".$ClaseArt['StkArtClsId']."'>".$ClaseArt['StkArtClsDsc']."</option>";
				}
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
			while($Ivas=mysqli_fetch_assoc($resultado))
			{
				if ($Ivas['IVAVal']==$ivaart)
				{
					echo "<option value='".$Ivas['IVAVal']."' selected>".$Ivas['IVA']."</option>";
				}
				else
				{
					echo "<option value='".$Ivas['IVAVal']."'>".$Ivas['IVA']."</option>";
				}
			}
			?>	
		       </select>
			</td>
		</tr>
	<tr>
	<font size="4">Altere la descripci&oacute;n del Art&iacute;culo en caso de correcci&oacute;n, No utilice para sustituir un art&iacute;culo por otro, esto afectar&aacute el Hist&oacute;rico del art&iacute;culo Original.</font>
	<br>
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