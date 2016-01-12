<!--
Creación	Alicia Acevedo
Fecha:		04/2010
Algunas Características:
	1.- Los articulos cuentan con un atributo Stock ficto, para cumplir con la funcionalidad de indicar dadas las solicitudes en construccion cual sería el 
		stock durante el proceso. Aún no se implementa esta funcionalidad. No se expone en la grilla tal valor
	2.- Los articulos cuentan con un atributo Stock Critico o minimo, para cumplir con la funcionalidad de indicar cuando debe realizar un pedido de reposición
		al stock por el artículo donde su cantidad del stock esta por debajo del stock critico.
-->
<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$estado=$_COOKIE['estadosol'];
$unidad=$_COOKIE['usuunidadele'];

$clase=$_REQUEST['clase'];
if (isset($_REQUEST['articulo']))
{
$articulo=$_REQUEST['articulo'];
} else {
$articulo= "";
}
$fchhasta=$_REQUEST['fchhasta'];
$dia = date('d');
$mes = date('m');
$year = date('y')+2000;
$fchdesdemov=date("d/m/Y", mktime(0, 0, 0, $mes, 1, $year));
$fchhastamov=date("d/m/Y", mktime(0, 0, 0, $mes, $dia, $year)); 
if (isset($_REQUEST['artbus']))
{
$artbus=$_REQUEST['artbus'];
} else {
$artbus = "";
}
if (isset($_REQUEST['actividad']))
{
$actividad=$_REQUEST['actividad'];
} else {
$actividad= "";
}
$escero=0;
$fchaplicada=$fchhasta;

$consultaI="Select * from StkArtCls where StkArtClsId=$clase";
$resultadoI=mysqli_query($cn,$consultaI);
$laclase=mysqli_fetch_assoc($resultadoI);
if ($clase==99)
{
$clasedsc="Todos";
}
else
{
$clasedsc=$laclase['StkArtClsDsc'];
}
?>

<script type="text/JavaScript" language="javascript">
function confirmDel()
{
	$mensaje="Confirma eliminar el articulo ";
	var agree=confirm($mensaje);
	if (agree) return true ;
	else return false ;
}
</script>

<SCRIPT LANGUAGE="JavaScript">
<!--
////////////////////////////////////////////////////
//Convierte fecha de mysql a normal
////////////////////////////////////////////////////
-->
function cambiaf_a_normal($fecha){
    ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha);
    $lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
    return $lafecha;
}
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript">
<!--
////////////////////////////////////////////////////
//Convierte fecha de normal a mysql
////////////////////////////////////////////////////
-->
function cambiaf_a_mysql(){
	$fecha=$_POST['fecha'];
	$fch=explode("/",$fecha);
	$fecha=$fch[2]."-".$fch[1]."-".$fch[0];
	return $fecha;
} 
</SCRIPT>

<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table class="inventario">
		<tr><td align="center" colspan="2"><font size="5" color="#000066">Art&iacute;culos de Stock - <?php echo $clasedsc;?></font></td></tr>
		<tr><td align="center" colspan="2"><font size="5" color="#000066">Resultados con Fecha: <?php echo $fchaplicada;?></font></td></tr>
		<tr><td align="right">
		<?php
	       if ($_COOKIE['usuperfil']<>2 and $_COOKIE['usuperfil']<>5 and $_COOKIE['usuperfil']<>6)			
		{//operador,autorizador y consultor el else corresponde al administrador o personal con perfila para ingresar en este objeto
			echo '<a href="nuevoart.php?clase='.$clase.'&fchhasta='.$fchhasta.'&artbus='.$artbus.'&actividad='.$actividad.'"><img src="Images/nuevo.png" height="30" width="30" border=0><br></a>';
		} 
	       if ($_COOKIE['usuperfil']<>5) //autorizador
		{
			echo '<td align="left"><a Target="_blank" href="pdfcriticos.php?"><img src="Images/alerta.jpg" height="30" width="30" border=0><br></a>';
		} 
		?>
		</td>
		</tr>
	</table>
	<table class="inventario">
		<?php
			require_once("filtrosart.php");
		?>
	</table>
	<?php
	if ($fchhasta=='')
	{
           	echo '<br><center><label>Indique una fecha hasta y Aplique filtro</label></center><br>';
	}
	else
	{
	       if ($clase==99)
		{
			if ($artbus=="")
			{
				if ($actividad=="1")
				{
				$consulta="Select * from StkArticulos as a, StkArtCls as c, StkArtClsUsu as u where a.StkArtClsId=c.StkArtClsId and u.UsuId='".$_COOKIE['usuid']."' and u.StkArtClsId=c.StkArtClsId and a.StkCauBjaId='".$escero."' order by StkArtDsc";
				}
				else
				{
				$consulta="Select * from StkArticulos as a, StkArtCls as c, StkArtClsUsu as u where a.StkArtClsId=c.StkArtClsId and u.UsuId='".$_COOKIE['usuid']."' and u.StkArtClsId=c.StkArtClsId and a.StkCauBjaId>'".$escero."' order by StkArtDsc";
				}
			}
			else
			{
				$elLike="%".$artbus."%";
				if ($actividad=="1")
				{
				$consulta="Select * from StkArticulos as a, StkArtCls as c, StkArtClsUsu as u where a.StkArtDsc LIKE '".$elLike."' and a.StkArtClsId=c.StkArtClsId and u.UsuId='".$_COOKIE['usuid']."' and u.StkArtClsId=c.StkArtClsId and a.StkCauBjaId='".$escero."' order by StkArtDsc";
				}
				else
				{
				$consulta="Select * from StkArticulos as a, StkArtCls as c, StkArtClsUsu as u where a.StkArtDsc LIKE '".$elLike."' and a.StkArtClsId=c.StkArtClsId and u.UsuId='".$_COOKIE['usuid']."' and u.StkArtClsId=c.StkArtClsId and a.StkCauBjaId>'".$escero."' order by StkArtDsc";
				}
			}

		}
		else
		{//Al seleccionar una clase(clasificacion) no tendria porque filtrar el tipo de bien, pues no ofreceria la clasificación en tal caso, indico igual
			if ($artbus=="")
			{
				if ($actividad=="1")
				{
				$consulta="Select * from StkArticulos as a, StkArtCls as c where a.StkArtClsId=$clase and a.StkArtClsId=c.StkArtClsId and a.StkCauBjaId='".$escero."' order by StkArtDsc";
				}
				else
				{
				$consulta="Select * from StkArticulos as a, StkArtCls as c where a.StkArtClsId=$clase and a.StkArtClsId=c.StkArtClsId and a.StkCauBjaId>'".$escero."' order by StkArtDsc";
				}
			}
			else
			{
				$elLike="%".$artbus."%";
				if ($actividad=="1")
				{
				$consulta="Select * from StkArticulos as a, StkArtCls as c where a.StkArtClsId=$clase and a.StkArtDsc LIKE '".$elLike."' and a.StkArtClsId=c.StkArtClsId and a.StkCauBjaId='".$escero."' order by StkArtDsc";
				}
				else
				{
				$consulta="Select * from StkArticulos as a, StkArtCls as c where a.StkArtClsId=$clase and a.StkArtDsc LIKE '".$elLike."' and a.StkArtClsId=c.StkArtClsId and a.StkCauBjaId>'".$escero."' order by StkArtDsc";
				}
			}
		}
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $colorlinea='#F3F3F3';

	       if (mysqli_num_rows($resultado)==0)
		{
	            	echo '<br><center><label>No existen articulos en el stock.</label></center><br>';
		}
		else
		{
	       if ($_COOKIE['usuperfil']==2 or $_COOKIE['usuperfil']==5 or $_COOKIE['usuperfil']==6) //operador, consultor, el else corresponde al administrador o personal con perfil para ingresar en este objeto
		{
	             	echo '<br><table class="inventario">
				<tr>
                     	<th>Art&iacute;culo</th>
                       	<th>Stock real</th>
                        	<th>Stock cr&iacute;tico</th>
                        	<th>Alerta Stk.Cr&iacute;tico</th>
                        	<th>Aplica IVA</th>
                        	<th>Estado</th>
                        	<th>Movimientos</th>
                        	<th>Reservado</th>
			</tr>';
		}
		else
		{

	             	echo '<br><table class="inventario">
				<tr bgcolor="#6495ED">
                     	<th>Art&iacute;culo</th>
                       	<th>Stock real</th>
                        	<th>Stock cr&iacute;tico</th>
                        	<th>Alerta</th>
                        	<th>IVA</th>
                        	<th>Modificar</th>
                        	<th>Corregir Saldo</th>
                        	<th>Estado</th>
                        	<th>Eliminar</th>
                        	<th>Movimientos</th>
		              <th>Reservar</th>
			</tr>';
		}

		while($unArticulo=mysqli_fetch_assoc($resultado))
		{
			$articulobuscado=$unArticulo['StkArtId'];
			//Llevo cantidad real en el stock a la fecha indicada en la variable $fchhasta, para ello busco movimientos posteriores e invierto la cantidad
			//a la cantidadreal, es decir sumo las salidas y resto las entradas. De acuerdo a esta variable comparo contra minimo, etc. 
			$consultaX="Select * from StkMovArticulos where StkArtId='".$articulobuscado."' and StkMovArtFch>'".cambiaf_a_mysql($fchhasta)."'";
			$resultadoX=mysqli_query($cn,$consultaX) or die('La consulta fall&oacute;: ' .mysqli_error());
			$CantRealAlaFch=$unArticulo['StkArtCantReal'];
			while($unMovArticulo=mysqli_fetch_assoc($resultadoX))
			{
				if($unMovArticulo['StkMovArtTpo']=="E")
				{
					$CantRealAlaFch=$CantRealAlaFch-$unMovArticulo['StkMovArtCant'];
				}
				else
				{
					$CantRealAlaFch=$CantRealAlaFch+$unMovArticulo['StkMovArtCant'];
				}
			}

			if($CantRealAlaFch<=$unArticulo['StkArtCantMinimo']){
				$icoalerta="Images/alerta.jpg";
			}
			else
			{
				$icoalerta="Images/blank.gif";
			}

			$CausalBaja="Activo";
			if($unArticulo['StkCauBjaId']>0)
			{
				$consultaI="Select * from StkCausal where StkCauId='".$unArticulo['StkCauBjaId']."'";
				$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
				$CauBja=mysqli_fetch_assoc($resultadoI);
				$CausalBaja=$CauBja['StkCauDsc'];
			}
			

		       if ($_COOKIE['usuperfil']==2 or $_COOKIE['usuperfil']==5 or $_COOKIE['usuperfil']==6) //operador, consultor, el else corresponde al administrador o personal con perfila para ingresar en este objeto
			{
			echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
                     	<td>'.$unArticulo['StkArtDsc'].'</td>
				<td align="center">'.$CantRealAlaFch.'</td>
				<td align="center">'.$unArticulo['StkArtCantMinimo'].'</td>
				<td align="center"><a><img src="'.$icoalerta.'" witdh="15" height="15" border=0></a></td>
                     	<td align="center">'.$unArticulo['StkArtIVA'].'</td>
	                    	<td>'.$CausalBaja.'</td>
	              	<td align="center"><a href="articulosmovver.php?clase='.$clase.'&artid='.$unArticulo['StkArtId'].'&fchhasta='.$fchhastamov.'&fchdesde='.$fchdesdemov.'&artbus='.$artbus.'&actividad='.$actividad.'"><img src="Images/sustituir.GIF" witdh="15" height="15" border=0></a></td>
		              <td align="center"><a href="articulosdepver.php?clase='.$clase.'&artid='.$unArticulo['StkArtId'].'&fchhasta='.$fchhasta.'&fchdesde='.$fchdesde.'&artbus='.$artbus.'&actividad='.$actividad.'"><img src="Images/restringir.jpg" witdh="15" height="15" border=0></a></td>';
			}
			else
			{
			echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
                     	<td>'.$unArticulo['StkArtDsc'].'</td>
				<td align="center">'.$CantRealAlaFch.'</td>
	              	<td align="center">'.$unArticulo['StkArtCantMinimo'].'</td>
				<td align="center"><a><img src="'.$icoalerta.'" witdh="15" height="15" border=0></a></td>
                     	<td align="center">'.$unArticulo['StkArtIVA'].'</td>
				<td align="center"><a href="updateart.php?idart='.$unArticulo['StkArtId'].'&clase='.$clase.'&fchhasta='.$fchhasta.'&artbus='.$artbus.'&actividad='.$actividad.'"><img src="Images/modificar.png" witdh="15" height="15" border=0></a></td>
				<td align="center"><a href="updateartstk.php?idart='.$unArticulo['StkArtId'].'&clase='.$clase.'&fchhasta='.$fchhasta.'&artbus='.$artbus.'&actividad='.$actividad.'"><img src="Images/derivar.png" witdh="15" height="15" border=0></a></td>
	                    	<td>'.$CausalBaja.'</td>';
				$consultaII="Select * from StkSolArticulos where StkArtId='".$unArticulo['StkArtId']."'";
				$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
			       if (mysqli_num_rows($resultadoII)==0)
				{
					//Si hay movimientos de proveedores tampoco permito eliminar 
					$consultaII="Select * from StkMovArticulos where StkArtId='".$unArticulo['StkArtId']."'";
					$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
				       if (mysqli_num_rows($resultadoII)==0)
					{
				              echo '<td onclick="return(confirmDel())" align="center"><a href="eliminoart.php?clase='.$clase.'&articulo='.$unArticulo['StkArtId'].'&fchdesde='.$fchdesde.'&fchhasta='.$fchhasta.'&artbus='.$artbus.'&actividad='.$actividad.'"><img src="Images/cancelar.gif" witdh="15" height="15" border=0></a></td>';
					}
					else
					{
				              echo '<td align="center"><a><img src="Images/blank.gif" witdh="15" height="15" border=0></a></td>';
					}
				}
				else
				{	
			              echo '<td align="center"><a><img src="Images/blank.gif" witdh="15" height="15" border=0></a></td>';
				}
		              echo '<td align="center"><a href="articulosmovver.php?clase='.$clase.'&artid='.$unArticulo['StkArtId'].'&fchhasta='.$fchhastamov.'&fchdesde='.$fchdesdemov.'&artbus='.$artbus.'&actividad='.$actividad.'"><img src="Images/sustituir.GIF" witdh="15" height="15" border=0></a></td>';
		              echo '<td align="center"><a href="articulosdep.php?clase='.$clase.'&idart='.$unArticulo['StkArtId'].'&fchhasta='.$fchhasta.'&fchdesde='.$fchdesde.'&artbus='.$artbus.'&actividad='.$actividad.'"><img src="Images/restringir.jpg" witdh="15" height="15" border=0></a></td>';
			}
			echo '</tr>';
		}//Cierra el WHILE que imprime los resultados obtenidos
		}//Cierra el IF donde se pregunta si hay resultados o no
		echo '</table><br>';
	}
 	?>
	<tr>
       <td align="center">&nbsp;</td>
	</tr>
</center>
<?php
require_once("pie.php");
?>