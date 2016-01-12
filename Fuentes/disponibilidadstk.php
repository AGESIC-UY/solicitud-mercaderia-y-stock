<!--
Creación	Alicia Acevedo
Fecha:		04/2010
Algunas Características:
	1.- Esta será la lista de artículos de las solicitudes en estado "Pendiente" con entrega pacial o total
	2.- Se visualizará la disponibilidad actual del material en stock
	3.- Tendrá acceso a realizar la funcionalidades o procedimiento de asignación del material a la solicitud y automaticamente determinara el estado
		de la misma según la asignación realizada de material.
	4.- Acceso a modificar adjudicación provisoria de la cantidad, repartida de stock existente entre solicitantes, descisión del Usuario con rol Operador.
	5.- Emisión del Remito de entrega para ser firmado por quien recibe. Con el detalle de lo entregado en ésta instancia e Instancias anteriores

El atributo StkSolArtCantAcred y StksolArtCantPen serán dinámicos, se utilizan como atributos auxiliares, donde se indica la cantidad posible de acreditar y pendiente
éstos estarán determinadados por la disponibilidad de material en el stock, la cantidad solicitada en el pedido y la cantidad ya entregada, en el contexto de este objeto.
Por lo que calculo estos valores al ingresar. Luego desde éste objeto, podriamos realizar el call a "updateartsolcant.php" donde puedo indicar la cantidad que realmente deseo
que se acredite, por lo que voy a guardar cantidad pendiente del pedido alterando el valor en el atributo StkSolArtCantPen. (La cantidad a acreditar estará topeada por el 
stock existente en ese momento y la cantidad solicitada en el pedido)
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
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];
$parcial=$_COOKIE['parcial'];
?>

<script type="text/JavaScript" language="javascript">
function confirmIng()
{
	var agree=confirm(" Confirma ADJUDICAR el material? ");
	if (agree) return true ;
	else return false ;
}
</script>

<?php
$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
	$tpomov="S";
	$hayentrega=0;
	$parciales=0;
	$estadoart="Pendiente";	//Tratamos articulos pendientes con o sin cantidad a acreditar
	$estadosol=$estadocall;	//Sino hubiese impresión de remito con esta varible volverá al estado Pendiente sino a Impresion remito
	$consultaVI="Select * from StkSolArticulos where StkSolId='".$solid."' and StkSolArtEstado='$estadoart'";
	$resultadoVI=mysqli_query($cn,$consultaVI) or die('La consulta fall&oacute;: ' .mysqli_error());
	while($unSolArticulo=mysqli_fetch_assoc($resultadoVI))
	{
		$consultaVII="Select * from StkArticulos where StkArtId='".$unSolArticulo['StkArtId']."'";
		$resultadoVII=mysqli_query($cn,$consultaVII) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $unArticulo=mysqli_fetch_assoc($resultadoVII);
	       if ($unSolArticulo['StkSolArtCantAcred']>0)
		{
			$hayentrega=1;
			$StkReal=$unArticulo['StkArtCantReal']-$unSolArticulo['StkSolArtCantAcred'];
			$consultaVIII="Update StkArticulos set StkArtCantReal=".$StkReal.", StkArtUsuMod='".$_COOKIE['usuid']."',StkArtFchMod='".date("Y-m-d H:i")."' where StkArtId='".$unSolArticulo['StkArtId']."'";
			$resultadoVIII=mysqli_query($cn,$consultaVIII);

			$consultaIX="Insert into StkMovArticulos (StkArtId,StkMovArtFch,StkMovArtTpo,StkMovArtCant,StkSolId,StkMovArtUsuCre,StkMovArtFchCre) values ('".$unSolArticulo['StkArtId']."','".date("Y-m-d H:i")."','".$tpomov."','".$unSolArticulo['StkSolArtCantAcred']."','".$solid."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
			$resultadoIX=mysqli_query($cn,$consultaIX);
			
			if($unArticulo['StkArtCantMinimo']>$StkReal)
			{//Se pide 03/2013 "Magdalena Escayola" que se envie mail por cada articulo que se entrega con stock critico por debajo del mínimo, así se preveen las compras.
			 //Se pidio especificamente se envie mail a ella Magdalena Escayola y a Rosario Martínez, así que implementare un att en la tabla de usuarios para marcar internamente quien recibe
			 //marco articulo entregado y minimo en critico para enviar mail con todos los articulos en esta situacion entregados en esta solicitud	
				$esuno=1;
				$consultaVIII="Update StkArticulos set StkArtCritico=".$esuno." where StkArtId='".$unSolArticulo['StkArtId']."'";
				$resultadoVIII=mysqli_query($cn,$consultaVIII);
			}
		}
		//Calculo nuevamente lo entregado, si la cantidad Pendiente la actualizo antes de esta acción guardar se me descuenta lo que estoy acreditando
		//dos veces, por esta razón lo actualizo aqui, pero debo recalcular lo ya entregado. Considerar que el movimiento de este proc ya esta inserto
		$consultaX="Select * from StkMovArticulos where StkSolId=$solid and StkArtId='".$unSolArticulo['StkArtId']."'";
		$resultadoX=mysqli_query($cn,$consultaX);
		$Entregado=0;
	       if (mysqli_num_rows($resultadoX)==0)
		{
			//La cantidad entregable será la solicitada y los Entregado es cero
		}
		else
		{
			while($unMovArticulo=mysqli_fetch_assoc($resultadoX))
			{
				$Entregado=$Entregado+$unMovArticulo['StkMovArtCant'];			
			}
		}
		//Actualizo la cantidad pendiente para despliegue en el remito de entrega
		$Pendiente=$unSolArticulo['StkSolArtCantSol']-$Entregado;

		if($Pendiente==0)
		{
			$StkSolArtEstado="Finalizada";
		}
		else
		{
			$StkSolArtEstado="Pendiente";
			$parciales=1; //Si al menos un articulo con pendiente, la solicitud esta con entrega parcial
		}
		$consultaXI="Update StkSolArticulos set StkSolArtEstado='".$StkSolArtEstado."', StkSolArtCantPen=$Pendiente, StkSolArtUsuMod='".$_COOKIE['usuid']."', StkSolArtFchMod='".date("Y-m-d H:i")."' where StkSolId='".$solid."' and StkArtId='".$unSolArticulo['StkArtId']."'";
		$resultadoXI=mysqli_query($cn,$consultaXI);
	}
	if($hayentrega==1)
	{
		$consultaX="Select * from StkArticulos where StkArtCritico='".$esuno."'";
		$resultadoX=mysqli_query($cn,$consultaX) or die('La consulta fall&oacute;: ' .mysqli_error());
		$haycriticos=0;
		$email_body = "Articulos entregados que se encuentra por debajo del stock minimo"."\n";
		while($uncritico=mysqli_fetch_assoc($resultadoX))
		{
			$haycriticos=1;
			$email_body.= " ".$uncritico['StkArtDsc'].", Stock actual: ".$uncritico['StkArtCantReal'].", Stock minimo: ".$uncritico['StkArtCantMinimo']."\n";
		}
		if ($haycriticos==1)
		{
			$consultaXX="Update StkArticulos set StkArtCritico=NULL where StkArtCritico='".$esuno."'";
			$resultadoXX=mysqli_query($cn,$consultaXX);

			$consultaXX="Select * from usuarios where UsuStkMin='".$esuno."'";
			$resultadoXX=mysqli_query($cn,$consultaXX) or die('La consulta fall&oacute;: ' .mysqli_error());
			while($usuariomail=mysqli_fetch_assoc($resultadoXX))
			{
				$email_to = $usuariomail['UsuMail'];
				$email_subject = "Stock Minimo";
				$email_from = "From: ".$_COOKIE['usumail']."\r\n";
				if(mail($email_to, $email_subject, $email_body, $email_from))
				{//Para eva transparente!!!
//						echo "Se ha enviado mail de aviso por debajo de stock minimo ($email_to) ";
				}
				else
				{
//						echo "El email ($email_to) no se ha podido enviar";
				}
			}
		}

		//La solicitud queda en un estado transitorio "Imprimir Remito" cuando esta impresión se realiza, vuelve a cambiar su estado. A "Pendiente" si existen articulos
		//con cantidades aún sin entregar o a "Finalizada" si las cantidades de los articulos han sido entregados en su totalidad. (objeto "cambioestado.php" called desde
		//index.php con el filtro de solicitudes "Imprimir Remito")
		$estadosol="Imprimir Remito";
		$consultaXII="Update StkSolicitudes set StkSolEstado='".$estadosol."', StkSolImprimiendo=1, StkSolParcial=$parciales, StkSolUsuMod='".$_COOKIE['usuid']."',StkSolFchMod='".date("Y-m-d H:i")."', StkSolFchFin='".date("Y-m-d H:i")."' where StkSolId=".$solid;
		$resultadoXII=mysqli_query($cn,$consultaXII);
	}
	echo "<META HTTP-EQUIV='refresh' CONTENT='1 ; URL=index.php?estado=$estadosol&unidad=$unidadcall'>";
	//La impresion la resolvi como un estado transitorio de "Imprimir Remito ", para que este proc no se considere implicita en el tramite de entrega y eviten imprimir 
	//en forma voluntaria o involuntaria. Por lo que desde aquí si confirma la entrega vuelvo al index.php con el estado de impresión. Luego de imprimir se reconstruye
	//según las entregas, el estado en el que debe quedar la solicitud si "Pendiente" o "Finalizada". Desde estado "Imprimir Remito" haré el call al siguiente 
	//pdfentrega.php (con target_blank)
}
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
<form name="datos" action="disponibilidadstk.php?solid=<?php echo $solid;?>&estadocall=<?php echo $estadocall;?>&unidadcall=<?php echo $unidadcall;?>" method="POST" >
	<table class="inventario">
		<tr>
			<td align="center" colspan="3"><font size="6" color="#000066">Disponibilidad y Entrega</font><br></td>
		</tr>
		<tr>
			<td align="center" colspan="3"><font size="6" color="#000066"> de Art&iacute;culos pendientes de la Solicitud</font><br></td>
		</tr>
	       <?php
			$consulta="Select * from StkSolicitudes where StkSolId='".$solid."'";
			$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
			$laSolicitud=mysqli_fetch_assoc($resultado);
			$laUnidad=$laSolicitud['StkSolSecId'];

			//La unidad buscada es la indicada en la tabla de solicitudes, no paso por parametro pues podría estar filtrando 
			//por unidad=0 ("Todas") dato que no nos serviría en esta instancia.
			$consultaI="Select * from Departamentos where DepId='".$laUnidad."'";
			$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
		       $uniEle=mysqli_fetch_assoc($resultadoI);
			$uniNombre=$uniEle['DepNombre'];
		?>
		<tr>
			<td align="center" colspan="3"><font size="4" color="#000066">Solicitud:  <?php echo $solid;?> - Unidad:  <?php echo $uniNombre;?></font><br></td>
		</tr>
		<tr>
			<td align="right">
				<a href="index.php?estado=<?php echo $estadocall;?>&unidad=<?php echo $unidadcall;?>">
				<img src="Images/volver.jpg" height="40" width="40" border=0></a>
			</td>
			<td align="center">
			       <?php
					if ($_COOKIE['usuperfil']=="2") //Operador
					{
				?>
					<input type="hidden" name="accion" value="guardar" />
					<input type="image" width="40" height="40" src="Images/carritoentrega.GIF" onclick="return(confirmIng())">
			       <?php
					}
				?>
				<br>
			</td>
			<td align="left">
				<a Target="_blank" href="pdfpreentrega.php?solid=<?php echo $solid; ?>&estadocall=<?php echo $estado; ?>&unidadcall=<?php echo $unidadcall; ?>">
				<img src="Images/impresora.jpg" witdh="40" height="40" border=0></a>
			</td>
		</tr>
	</table>

<?php
	//Solo tratamos los articulos pendientes de entrega.
	$estadoart="Pendiente"; 
	$consultaII="Select * from StkSolArticulos where StkSolArtEstado='".$estadoart."' and StkSolId=".$solid;
	$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
       if (mysqli_num_rows($resultadoII)==0){
		//Esta If no debería de ocurrir pero....nos protejemos.
            	echo '<br><center><label>No existen articulos para la solicitud</label></center><br>';
	}
	else
	{
             	echo '<br><table class="inventario"><tr bgcolor="#MM0077">
	             	<th>Art&iacute;culo</label></td>
	             	<th>Solicita</label></td>';
			if ($parcial==1)
			{
	       	echo '<th>Entregas</th>
	             		<th>Pendiente</th>';
			}
		       echo '<th>Entregar&aacute</th>
	       	     	<th>Stock</th>
	             		<th>Observaci&oacute;n</th>';
			if ($_COOKIE['usuperfil']=="2") //Operador
			{
		             echo '<th>Modificar cantidad</th>';
		             echo '<th>Sustituir articulo</th>';
			}
			echo '</tr>';
		while($unSolArticulo=mysqli_fetch_assoc($resultadoII))
		{
             		if($colorlinea=='#F3F3F3')
			{
       			$colorlinea='#FEFEFE';
			}
			else
			{
				$colorlinea='#F3F3F3';
			}
			$tipomov="S";
			$consultaIII="Select * from StkMovArticulos where StkArtId='".$unSolArticulo['StkArtId']."' and StkSolId='".$solid."' and StkMovArtTpo='".$tipomov."'";
			$resultadoIII=mysqli_query($cn,$consultaIII);
			$Entregas=0;
		       if (mysqli_num_rows($resultadoIII)==0)
			{
				//No hubo entregas parciales 
			}
			else
			{
				while($unMovArticulo=mysqli_fetch_assoc($resultadoIII))
				{
					$Entregas=$Entregas+$unMovArticulo['StkMovArtCant'];			
				}
			}
			//Posiblemente se pueda entregar la siguiente cantidad ahora debería comparar contra cantidad en Stock
			$Pendiente=$unSolArticulo['StkSolArtCantSol']-$Entregas;
			$Entregable=$unSolArticulo['StkSolArtCantSol']-$Entregas;

			$consultaIV="Select * from StkArticulos where StkArtId='".$unSolArticulo['StkArtId']."'";
			$resultadoIV=mysqli_query($cn,$consultaIV) or die('La consulta fall&oacute;: ' .mysqli_error());
		       $unArticulo=mysqli_fetch_assoc($resultadoIV);

			if($unArticulo['StkArtCantReal']<=$Entregable)
			{
				if($unArticulo['StkArtCantReal']<$Entregable)
				{
					$Comentario="Stock Insuficiente, quedar&aacute en Cero";
				}
				else
				{
					$Comentario="Se puede entregar cantidad indicada, pero Stock quedar&aacute en Cero";
				}

				if($unArticulo['StkArtCantReal']==0)
				{
					$Comentario="No hay Stock";
				}
				$Entregar=$unArticulo['StkArtCantReal'];
			}
			else
			{
				$Entregar=$Entregable;
				$Comentario="Se puede entregar cantidad indicada";
			}

			if($Entregar==0)
			{
				//No permito entrar a Updateartsolcant.php no corresponde alterar cantidades no es posible cumplir con ninguna entrega de material
				echo '<tr  align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
	                		<td align="left">'.$unArticulo['StkArtDsc'].'</td>
					<td>'.$unSolArticulo['StkSolArtCantSol'].'</td>';
					if ($parcial==1)
					{
					echo '<td>'.$Entregas.'</td>
					<td>'.$Pendiente.'</td>';
					}
					echo '<td>'.$Entregar.'</td>
					<td>'.$unArticulo['StkArtCantReal'].'</td>
					<td align="left">'.$Comentario.'</td>';
					if ($_COOKIE['usuperfil']=="2") //Operador
					{
						echo '<td>---</td>';
						$cantsustituir=$unSolArticulo['StkSolArtCantSol']-$Entregado;
						echo '<td><a href="sustituyoartsol.php?idart='.$unSolArticulo['StkArtId'].'&solid='.$solid.'&estadocall='.$estadocall.'&unidadcall='.$unidadcall.'&cantsustituir='.$cantsustituir.'"><img src="Images/sustituir.GIF" witdh="15" height="15" border=0></a></td>';
					}
					echo '</tr>';
			}
			else	
			{
				//Analizo si se distorciono StkSolArtCantAcred en updateartsolcant.php, en cuyo caso es el valor a acreditar
				$Acreditar=$unSolArticulo['StkSolArtCantAcred'];
				$Pendiente=$unSolArticulo['StkSolArtCantPen'];

				if($Acreditar>0)
				{
					$Entregar=$Acreditar;
					$Comentario="Se puede entregar cantidad indicada";
				}
				else	
				{
					//en el caso en que hubiese una distorisión del StkSolArtCantAcred y el valor es cero lo distingo por el dato en el 
					//StkSolArtCantPen mayor a cero. De otra forma se hace lio.
					if($Pendiente>0) 
					{
						$Entregar=0;
						$Comentario="Se considero no entregar material";
					}
					else
					{
//						$Comentario="Se puede entregar cantidad indicada";
					}

				}
				echo '<tr  align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			             	<td align="left">'.$unArticulo['StkArtDsc'].'</td>
					<td>'.$unSolArticulo['StkSolArtCantSol'].'</td>';
					if ($parcial==1)
					{
					echo '<td>'.$Entregas.'</td>
					<td>'.$Pendiente.'</td>';
					}
					echo '<td>'.$Entregar.'</td>
					<td>'.$unArticulo['StkArtCantReal'].'</td>
					<td align="left">'.$Comentario.'</td>';
					if ($_COOKIE['usuperfil']=="2") //Operador
					{
						$cantsustituir=$unSolArticulo['StkSolArtCantSol']-$Entregado;
						echo '<td><a href="updateartsolcant.php?idart='.$unSolArticulo['StkArtId'].'&solid='.$solid.'&estadocall='.$estadocall.'&unidadcall='.$unidadcall.'"><img src="Images/modificar.png" witdh="15" height="15" border=0></a></td>';
						echo '<td><a href="sustituyoartsol.php?idart='.$unSolArticulo['StkArtId'].'&solid='.$solid.'&estadocall='.$estadocall.'&unidadcall='.$unidadcall.'&cantsustituir='.$cantsustituir.'"><img src="Images/sustituir.GIF" witdh="15" height="15" border=0></a></td>';
					}
					echo '</tr>';
			}
			if($laSolicitud['StkSolImprimiendo']==0)
			{
				//Recargo Acreditar según stock y contexto actual
				$consultaV="Update StkSolArticulos set StkSolArtCantAcred='".$Entregar."' where StkSolId='".$solid."' and StkArtId='".$unSolArticulo['StkArtId']."'";
				$resultadoV=mysqli_query($cn,$consultaV);
			}
		}
	}
	echo '</table><br>';
?>
    <tr>
       <td align="center">&nbsp;</td>
    </tr>
</form>
</center>

<?php
require_once("pie.php");
?>