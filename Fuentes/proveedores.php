<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
?>
<script type="text/JavaScript" language="javascript">
function confirmDel()
{
	$mensaje="Confirma eliminar el proveedor ?";
	var agree=confirm($mensaje);
	if (agree) return true ;
	else return false ;
}
</script>

<META HTTP-EQUIV='refresh' CONTENT='60; URL=proveedores.php'>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table class="inventario">
		<tr>
		<td align="center"><font size="5" color="#000066">Proveedores de Mercader&iacute;a y/o Servicios</font>
		<?php
		if ($_COOKIE['usuperfil']<>6) //consultor el else corresponde al administrador o personal con perfila para ingresar en este objeto
		{
			echo '&nbsp;&nbsp;<a href="nuevoprv.php"><img src="Images/nuevo.png" height="30" width="30" border=0><br></a>';
		} 
		?>
		</td>
		</tr>
		<tr>
		<td align="center"><font size="5" color="#000066">Registro de Facturas</font></td>
		</tr>
	</table>
            <?php

		$consulta="Select * from StkProveedores order by StkPrvRzoSoc";
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $colorlinea='#F3F3F3';
	       if (mysqli_num_rows($resultado)==0){
	            	echo '<br><center><label>No existen proveedores de mercader&iacute;a.</label></center><br>';
		}
		else
		{
			if ($_COOKIE['usuperfil']<>6) //consultor el else corresponde al administrador o personal con perfil para ingresar en este objeto
			{
	             	echo '<br><table class="inventario">
			<tr bgcolor="#6495ED">
                     	<th>Raz&oacute;n Social</th>
                        	<th>Direcci&oacute;n</th>
                        	<th>Tel&eacute;fono</th>
	              	<th>Fax</th>
                        	<th>Mail</th>         
                        	<th>Ficha</th>
                        	<th>Facturac&iacute;on</th>
                        	<th>Eliminar</th>
                        	<th>A Confirmar</th>
                        	<th>Pendientes Stock</th>
			</tr>';
			}
			else
			{
	             	echo '<br><table class="inventario">
			<tr bgcolor="#6495ED">
                     	<th>Raz&oacute;n Social</th>
                        	<th>Direcci&oacute;n</th>
                        	<th>Tel&eacute;fono</th>
	              	<th>Fax</th>
                        	<th>Mail</th>         
                        	<th>Facturac&iacute;on</th>
                        	<th>A Confirmar</th>
                        	<th>Pendientes Stock</th>
			</tr>';
			}
		while($unProveedor=mysqli_fetch_assoc($resultado))
		{
              	if($colorlinea=='#F3F3F3')
			{
       			$colorlinea='#FEFEFE';
			}
			else
			{
				$colorlinea='#FEFEFE';
			}

			$consultaI="Select * from StkPrvFacturas where StkPrvId=".$unProveedor['StkPrvId'];
			$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
			$aconfirmar=0;
			$astockear=0;
			while($lasFacPrv=mysqli_fetch_assoc($resultadoI))
			{
				//la elección de un articulo dentro de una factura determina la clase o clasificación de artículos, solo con analizar a que clase corresponde uno de los artículos
				//es suficiente para saber a que ambiente pertenece la factura
				$consultaII="Select * from StkMovArticulos as f, StkArticulos as a, StkArtCls as c where f.StkPrvFacId='".$lasFacPrv['StkPrvFacId']."' and f.StkArtId=a.StkArtId and a.StkArtClsId=c.StkArtClsId";
				$resultadoII=mysqli_query($cn, $consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
			       if (mysqli_num_rows($resultadoII)>0)
				{

		              	if($lasFacPrv['StkPrvFacFin']==0)
					{
       					$aconfirmar=$aconfirmar+1;
					}
		              	if($lasFacPrv['StkPrvFacFin']==1)
					{
	       				$astockear=$astockear+1;
					}
				}
			}
			if ($_COOKIE['usuperfil']<>6) //consultor el else corresponde al administrador o personal con perfila para ingresar en este objeto
			{
			echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
                     	<td>'.$unProveedor['StkPrvRzoSoc'].'</td>
				<td>'.$unProveedor['StkPrvDir'].'</td>
				<td>'.$unProveedor['StkPrvTel'].'</td>
				<td>'.$unProveedor['StkPrvFax'].'</td>';
		              echo '<td>'.$unProveedor['StkPrvMail'].'</td>
				<td><a href="updateprv.php?idprv='.$unProveedor['StkPrvId'].'"><img src="Images/modificar.png" witdh="15" height="15" border=0></a></td>
				<td><a href="proveedoresfacver.php?idprv='.$unProveedor['StkPrvId'].'"><img src="Images/pesos.jpg" witdh="15" height="15" border=0></a></td>';

				//Si hay movimientos de proveedores No permito eliminar 
				$consultaII="Select * from StkPrvFacturas where StkPrvId='".$unProveedor['StkPrvId']."'";
				$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
			       if (mysqli_num_rows($resultadoII)==0){
			              echo '<td onclick="return(confirmDel())" align="center"><a href="eliminoprv.php?prvid='.$unProveedor['StkPrvId'].'"><img src="Images/cancelar.gif" witdh="15" height="15" border=0></a></td>';
				}
				else
				{
			              echo '<td align="center"><a><img src="Images/blank.gif" witdh="15" height="15" border=0></a></td>';
				}
	       		echo	'<td>'.$aconfirmar.'</td>
       			<td>'.$astockear.'</td>
			</tr>';
			}
			else
			{
			echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
                     	<td>'.$unProveedor['StkPrvRzoSoc'].'</td>
				<td>'.$unProveedor['StkPrvDir'].'</td>
				<td>'.$unProveedor['StkPrvTel'].'</td>
				<td>'.$unProveedor['StkPrvFax'].'</td>';
	       	       echo '<td>'.$unProveedor['StkPrvMail'].'</td>
				<td><a href="proveedoresfacver.php?idprv='.$unProveedor['StkPrvId'].'"><img src="Images/pesos.jpg" witdh="15" height="15" border=0></a></td>';
       			echo	'<td>'.$aconfirmar.'</td>
       			<td>'.$astockear.'</td>
			</tr>';
			}
		}//Cierra el WHILE que imprime los resultados obtenidos
	}//Cierra el IF donde se pregunta si hay resultados o no
	echo '</table><br>';
       ?>
    <tr>
        <td align="center">&nbsp;</td>
    </tr>
</center>

<?php
require_once("pie.php");
?>