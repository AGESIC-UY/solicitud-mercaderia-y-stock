<?php
$menutab=basename(__FILE__, ".php"); // devuelve el nombre sin extension .php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$fchhasta=$_REQUEST['fchhasta'];
$fchdesde=$_REQUEST['fchdesde'];
$artid=$_REQUEST['artid'];
$artbus=$_REQUEST['artbus'];
$actividad=$_REQUEST['actividad'];

//vuelvo a articulos.php con los valores por default en las fechas, en este podrian haber sido alteradas y no es de mi 
//interes que se refleje en el filtro de articulos.php
$dia = date('d');
$mes = date('m');
$year = date('y')+2000;
$fdesde=date("d/m/Y", mktime(0, 0, 0, $mes, 1, $year));
$fhasta=date("d/m/Y", mktime(0, 0, 0, $mes, $dia, $year));

$consulta="Select * from StkArticulos where StkArtId='".$artid."'";
$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
$elArticulo=mysqli_fetch_assoc($resultado);
$idartdsc=$elArticulo['StkArtDsc'];

//Averiguo dia anterior al rango solicitado para calcular el saldo anterior
$fdesdemysql=cambiaf_a_mysql($fchdesde);
$fchdesdemenosundia=date("Y-m-d", strtotime("$fdesdemysql-1 day"));  

//Averiguo saldo al dia anterior al rango y saldo final(que no tiene porque ser el dia de hoy por lo tanto debo calcular el saldo a esa fecha hasta)
//recorro movimientos posteriores a la fecha que quiero calcular el saldo e invierto la cantidad restando y sumando 
//como si fuese ingenieria inversa, es decir sumo las salidas y resto las entradas. 
$consultaX="Select * from StkMovArticulos where StkArtId='".$artid."' and StkMovArtFch>'".$fchdesdemenosundia."'";  
$resultadoX=mysqli_query($cn,$consultaX) or die('La consulta fall&oacute;: ' .mysqli_error());
$SaldoFchAnterior=$elArticulo['StkArtCantReal'];
$SaldoFchHasta=$elArticulo['StkArtCantReal'];
while($unMovArticulo=mysqli_fetch_assoc($resultadoX))
{
	if($unMovArticulo['StkMovArtTpo']=="E")
	{
		if ($unMovArticulo['StkPrvFacId']<>NULL )
		{//movimiento de factura, entrada pero solo considero aquellas confirmadas sino altera el saldo en forma incorrecta
			$facconfirmada=2;
			$consultaXI="Select * from StkPrvFacturas where StkPrvFacId='".$unMovArticulo['StkPrvFacId']."'";
			$resultadoXI=mysqli_query($cn,$consultaXI) or die('La consulta fall&oacute;: ' .mysqli_error());
		       $lafac=mysqli_fetch_assoc($resultadoXI);
			if ($lafac['StkPrvFacFin']==$facconfirmada)
			{//factura sumada al saldo
				$SaldoFchAnterior = $SaldoFchAnterior - $unMovArticulo['StkMovArtCant'];
				if($unMovArticulo['StkMovArtFch']>cambiaf_a_mysql($fchhasta))
				{
					$SaldoFchHasta = $SaldoFchHasta - $unMovArticulo['StkMovArtCant'];
				}
			}
		}
		else
		{//Serían movimientos de entrada pero de corrección de saldo
			$SaldoFchAnterior = $SaldoFchAnterior - $unMovArticulo['StkMovArtCant'];
			if($unMovArticulo['StkMovArtFch']>cambiaf_a_mysql($fchhasta))
			{
				$SaldoFchHasta = $SaldoFchHasta - $unMovArticulo['StkMovArtCant'];
			}
		}
	}
	else
	{
		$SaldoFchAnterior = $SaldoFchAnterior + $unMovArticulo['StkMovArtCant'];
		if($unMovArticulo['StkMovArtFch']>cambiaf_a_mysql($fchhasta))
		{
			$SaldoFchHasta = $SaldoFchHasta + $unMovArticulo['StkMovArtCant'];
		}
	}
}

//Averiguo saldo a la fecha hasta
if ($_REQUEST['fchdesde']>$_REQUEST['fchhasta'])
{
      	echo '<br><center><label>Rango de fechas incorrecto</label></center><br>';
}
?>

<SCRIPT LANGUAGE="JavaScript">
<!-- // Convierte fecha de mysql a normal //-->
function cambiaf_a_normal($fecha)
{
    ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha);
    $lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
    return $lafecha;
}
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript">
<!-- // Convierte fecha de normal a mysql //-->
function cambiaf_a_mysql($fecha){
	$fecha=$_POST['fecha']
	$fch=explode("/",$fecha);
	$fecha=$fch[2]."-".$fch[1]."-".$fch[0];
	return $fecha;
} 
</SCRIPT>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table align="left">
	<?php
		include "calendarioentremovart.php";
	?>
	<tr><td>&nbsp;</td></tr>
	</table>

	<table class="inventario">
		<tr>
		<td align="left"><font size="5" color="#000066"><img src="Images/sustituir.GIF" width="20" height="20" alt="Nuevo" border=0/> Movimientos de Art&iacute;culo </font>
		</tr>
		<tr>
		<td align="left"><font size="4" color="#000066"> <?php echo $idartdsc;?> - Entre fechas: <?php echo $fchdesde;?> - <?php echo $fchhasta;?> </font>
		</tr>
		</td>
		<?php

		?>
	</table>
	<table class="inventario">
		<tr>
		<td align="left"><font size="4" color="#000066">Saldo anterior(<?php echo cambiaf_a_normal($fchdesdemenosundia);?>):   <?php echo $SaldoFchAnterior;?> </font><br></td>
		<td align="left"><font size="4" color="#000066">Total a la fecha(<?php echo $fchhasta;?>):   <?php echo $SaldoFchHasta;?> </font><br></td>
		<td align="left"><a href="articulos.php?clase=<?php echo $clase;?>&articulo=<?php echo $articulo;?>&fchhasta=<?php echo $fchhasta;?>&fchdesde=<?php echo $fchdesde;?>&artbus=<?php echo $artbus;?>&actividad=<?php echo $actividad;?>"><img width="30" height="30" src="Images/volver.jpg" border=0></a></td>
		</tr>
		<?php
		$consultaI="Select * from StkMovArticulos where StkArtId='".$artid."' and StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' order by StkArtId, StkMovArtId";
		$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());

	       $colorlinea='#F3F3F3';
	       if (mysqli_num_rows($resultadoI)==0){
	            	echo '<br><center><label>No existen movimientos para el articulo</label></center><br>';
		}
		else
		{
	        	echo '<br><table class="inventario">
			<tr bgcolor="#MM0077">
                    	<th>Id</th>
                    	<th>Fecha</th>
                    	<th>Entrada</th>
                    	<th>Salida</th>
                    	<th>Tipo</th>
                    	<th>Observaci&oacute;n</th>
			</tr>';
			while($losMovArticulos=mysqli_fetch_assoc($resultadoI))
			{
	      			$colorlinea='#FEFEFE';
				$lineaok=1;
				if ($losMovArticulos['StkMovArtTpo']=="S")
				{
					$consultaIII="Select * from Departamentos as d, StkSolicitudes as s where s.StkSolId='".$losMovArticulos['StkSolId']."' and d.DepId=s.StkSolSecId";
					$resultadoIII=mysqli_query($cn,$consultaIII) or die('La consulta fall&oacute;: ' .mysqli_error());
				       $launidad=mysqli_fetch_assoc($resultadoIII);

					$CantSal=$losMovArticulos['StkMovArtCant'];
					$CantEnt="-";
					$StkMovArtTpo="Solicitud Nro.".$losMovArticulos['StkSolId']." - ".$launidad['DepNombre'];
				}
				else
				{
					$CantSal="-";
					$CantEnt=$losMovArticulos['StkMovArtCant'];

					if ($losMovArticulos['StkPrvFacId']<>NULL )
					{//facturas unicamente confirmadas
						$facconfirmada=2;
						$consultaIII="Select * from StkPrvFacturas as f, StkProveedores as p where f.StkPrvFacId='".$losMovArticulos['StkPrvFacId']."' and f.StkPrvId=p.StkPrvId";
						$resultadoIII=mysqli_query($cn,$consultaIII) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $lasfac=mysqli_fetch_assoc($resultadoIII);
						if ($lasfac['StkPrvFacFin']==$facconfirmada)
						{
							$StkMovArtTpo = "Factura Nro.".$lasfac['StkPrvFacNum']." - ".$lasfac['StkPrvRzoSoc'];
						}
						else
						{
							$lineaok=0;
							$StkMovArtTpo = "Factura en espera para ser confirmada subir al Stock - ".$lasfac['StkPrvFacNum']." - ".$lasfac['StkPrvRzoSoc'];
						}
					}
					else
					{//Si ingresa a este else, será entrada de material sin factura, es devolución de la unidad a la proveeduria, salvo que luego existe un 
					 //$losMovArticulos['StkMovArtPorId']<>NULL que podra ser $losMovArticulos['StkMovArtTpo'] == "S" o "E", por ello esta el if despues. 

						$consultaIII="Select * from Departamentos as d, StkSolicitudes as s where s.StkSolId='".$losMovArticulos['StkSolId']."' and d.DepId=s.StkSolSecId";
						$resultadoIII=mysqli_query($cn,$consultaIII) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $launidad=mysqli_fetch_assoc($resultadoIII);

						$StkMovArtTpo = "Devoluci&oacute;n Solicitud Nro.".$losMovArticulos['StkSolId']." - ".$launidad['DepNombre'];
					}
				}

				if ($losMovArticulos['StkMovArtPorId']<>NULL )
				{
					$consultaIII="Select * from StkCausal where StkCauId='".$losMovArticulos['StkMovArtPorId']."'";
					$resultadoIII=mysqli_query($cn,$consultaIII) or die('La consulta fall&oacute;: ' .mysqli_error());
				       $lascorr=mysqli_fetch_assoc($resultadoIII);

					$StkMovArtTpo= "Correccion Administrador ".$lascorr['StkCauDsc'];
				}

				if ($lineaok==1)
				{
					echo '<tr  align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
						<td>'.$losMovArticulos['StkMovArtId'].'</td>
              	       		<td align="left">'.cambiaf_a_normal($losMovArticulos['StkMovArtFch']).'</td>
						<td>'.$CantEnt.'</td>
						<td>'.$CantSal.'</td>
						<td align="left">'.$StkMovArtTpo.'</td>
						<td align="left">'.$losMovArticulos['StkMovArtObs'].'</td>
					</tr>';
				}
			}//Cierra el WHILE que imprime los resultados obtenidos

		}//Cierra el IF donde se pregunta si hay resultados o no
		echo '</table><br>';
            ?>
	</table>
    <tr>
       <td align="center">&nbsp;</td>
    </tr>
</center>

<?php
require_once("pie.php");
?>

