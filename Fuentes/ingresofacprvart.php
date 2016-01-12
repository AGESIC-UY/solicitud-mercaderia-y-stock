<!--
Creación	Alicia Acevedo
Fecha:		04/2010
Algunas Características:
	1.- Este objeto corresponde al ingreso del detalle de factura articulo por articulo. El cabezal fue creado en el objeto called. Creando ya la existencia del id 
		de la factura.
	2.- Todo articulo guardado, genera un movimiento de Entrada, vinculado al Id de la factura, este objeto aún NO IMPACTA los saldos en el stock. Queda en manos
		del usuario la confirmación del ingreso de factura. Y durante esta confirmación se impacta el saldo Objeto "confirmafac.php"
-->

<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idprv=$_REQUEST['idprv'];
$idfac=$_REQUEST['idfac'];
$tpomov="E";
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
////////////////////////////////////////////////////
//Convierte fecha de normal a mysql
////////////////////////////////////////////////////
-->
function cambiaf_a_mysql(){
	$fecha=$_POST['fecha']
	$fch=explode("/",$fecha);
	$fecha=$fch[2]."-".$fch[1]."-".$fch[0];
	return $fecha;
} 
</SCRIPT>

<?php
$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
	if ($_POST['cant']>0 and $_POST['precio']>0)
	{
		$consulta="Select * from StkArticulos where StkArtId='".$_POST['artid']."'";
		$resultado=mysqli_query($cn,$consulta);
		$elArt=mysqli_fetch_assoc($resultado);
		$eliva=$elArt['StkArtIVA'];
		$preciociva=$_POST['precio']*$eliva;

		$consulta="Select * from StkMovArticulos as m, StkArticulos as a where m.StkPrvFacId='".$idfac."' and m.StkArtId=a.StkArtId";
		$resultado=mysqli_query($cn,$consulta);
		$elArtPri=mysqli_fetch_assoc($resultado);
		$laClasificacion=$elArtPri['StkArtClsId'];

		$tipomov='E';
		$consulta="Select * from StkMovArticulos where StkPrvFacId='".$idfac."' and StkArtId='".$_POST['artid']."' and StkMovArtTpo='".$tipomov."'";
		$resultado=mysqli_query($cn,$consulta);
	       if (mysqli_num_rows($resultado)==0)
		{
			//Insertar movimiento del articulo
			//obtener el autonumerico de la factura creada StkPrvFacId para indicar en el insert que le sigue
			$sentencia="Insert into StkMovArticulos (StkArtId,StkMovArtFch,StkMovArtTpo,StkMovArtCant,StkPrvFacId,StkMovArtPrecio,StkMovArtPrecioCIva,StkMovArtUsuCre,StkMovArtFchCre) values ('".$_POST['artid']."','".date("Y-m-d H:i")."','".$tpomov."','".$_POST['cant']."','".$idfac."','".$_POST['precio']."','".$preciociva."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
			$cantidad = mysqli_query($cn, $sentencia);
		}
		else
		{
			echo "<font color='#FF0000'>El art&iacute;culo ya esta registrado en &eacute;sta Factura, sume cantidades si corresponde a otro rengl&oacute;n e ingrese nuevamente</font>";
		}
	}  //fin if cantidad
	else
	{
		echo "Falta ingresar cantidad y/o precio del art&iacute;culo para registrar el INGRESO";
	} 
}  //fin if accion guardar	

$sentenciaI="Select * from StkPrvFacturas where StkPrvFacId=".$idfac;
$resultadoI=mysqli_query($cn,$sentenciaI);
$laFac=mysqli_fetch_assoc($resultadoI);
$facnum=$laFac['StkPrvFacNum'];

$sentenciaII="Select * from StkMovArticulos where StkPrvFacId=".$idfac;
$resultadoII=mysqli_query($cn,$sentenciaII);
$preciocargado=0;
$precioconiva=0;
while($FacArt=mysqli_fetch_assoc($resultadoII))
{
	$preciocargado=$preciocargado+$FacArt['StkMovArtPrecio'];
	$precioconiva=$precioconiva+$FacArt['StkMovArtPrecioCIva'];;
}

$sentenciaIII="Select * from StkProveedores where StkPrvId=".$idprv;
$resultadoIII=mysqli_query($cn,$sentenciaIII);
$elPrv=mysqli_fetch_assoc($resultadoIII);
$prvnom=$elPrv['StkPrvRzoSoc'];
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
<form name="datos" action="ingresofacprvart.php?idprv=<?php echo $idprv;?>&idfac=<?php echo $idfac;?>" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="2">
	<br>
	<font size="5" color="#000066"><img src="Images/modificar.png" width="30" height="30" alt="Nuevo"/> Ingresar Art&iacute;culo de Factura <?php echo $facnum;?></font>
	<br>
	<font size="5" color="#000066"><?php echo $prvnom;?></font>
	<br>
    </td>
    </tr>
    <tr>
    <td colspan="2">
    <table class="inventario">
      <tr>
        <td width="102" align="left"><label>Art&iacute;culo:</label></td>
	 <td colspan="3">
        <select name="artid">
          <?php
		$consulta="Select * from StkArticulos as a, StkArtCls as c, StkArtClsUsu as u where a.StkCauBjaId=0 and a.StkArtClsId=c.StkArtClsId and u.UsuId='".$_COOKIE['usuid']."' and a.StkArtClsId=u.StkArtClsId order by a.StkArtDsc";
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
		while($unArt=mysqli_fetch_assoc($resultado))
			{
			$artid=$unArt['StkArtId'];
			$artdsc=$unArt['StkArtDsc'];
			echo '<option value="'.$artid.'">'.$artdsc.'</option>';
	              }
          ?>
        </select>
	 </td>
      </tr>
      <tr>
        <td width="180" align="left"><label>Cantidad compra:</label></td>
	 <td><input type="text" name="cant" maxlength="120" size="15"/></td>
      </tr>
      <tr>
        <td width="180" align="left"><label>Monto por cantidad:</label></td>
	 <td><input type="text" name="precio" maxlength="120" size="15"/></td>
      </tr>

      <tr>
        <td width="102" align="left"><label>Subtotal s/iva:</label></td>
	 <td><input type="text" name="stkcant" maxlength="120" size="15" value="<?php echo $preciocargado; ?>" " disabled="true" readonly="readonly"/></td>
      </tr>
      <tr>
        <td width="102" align="left"><label>Total iva inc.:</label></td>
	 <td><input type="text" name="stkcant" maxlength="120" size="15" value="<?php echo $precioconiva; ?>" " disabled="true" readonly="readonly"/></td>
      </tr>
    </table>
    </td>
    </tr>

  <tr>
    <td align="left">
	<a href="proveedoresfacver.php?idprv=<?php echo $idprv;?>"><img width="40" height="40" src="Images/volver.jpg"></a>
    </td>
    <td align="left">
	<input type="image" width="40" height="40" src="Images/guardar.png" >
	<input type="hidden" name="accion" value="guardar"/>
    </td>
  </tr>

</table>
<br>
</form>
</center>

<?php
require_once("articulosfac.php");
require_once("pie.php");
?>