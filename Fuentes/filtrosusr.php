<!--
Creación:	Alicia Acevedo
Fecha:		11/2010
-->

<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
?>

<center>
<form name="filtrosusr" action="usuarios.php?unidad=<?php echo $unidad;?>" method="post">
<tr>
<td align="left"><label>Filtrar usuarios de Unidad:</label></td>
<td>
       <select name="unidad">
	<?php
	if ($_COOKIE['usupermiso']='S')
	{
		$consulta="Select * from Departamentos order by DepNombre";
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
		while($unDep=mysqli_fetch_assoc($resultado))
		{
			$DepId=$unDep['DepId'];
			$DepDsc=$unDep['DepNombre'];
			echo '<option value="'.$DepId.'" selected>'.$DepDsc.'</option>';
		}
		echo '<option value="99999" selected>Todos</option>';
	}
	?>	
       </select>
</td>
</tr>
<tr>
	<td align="left"><label>Filtrar parte del Apellido: </label></td>
	<td><input type="text" name="apebus" size="54" maxlength="54" value="<?php echo $apebus;?>"/></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input name="submit" type="submit" value="Aplicar"/></td> 
</tr>
</form>
</center>
