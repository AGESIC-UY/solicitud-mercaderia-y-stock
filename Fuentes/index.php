<!--
Creación	Alicia Acevedo
Fecha:		04/2010
Algunas Características:
	1.- El botón de nueva solicitud será visible para perfil "solicitante" y "autorizador"(no importa el filtro de estado en el que se encuentre) siempre va a estar visible
	2.- El combo de "unidades" solo estará visible para perfil "operador" y "administrador" el solicitante solo podrá ver las solicitudes de la unidad a la que pertenece asi como su
		autorizador, no tiene que seleccionar unidad.
	3.- Configuración en grilla: 
		a.- Todos los estados cuentan con el detalle de los articulos de la solicitud accesible para todos los perfiles. 
		b.- En el estado "Construyendo" solo el perfil "solicitante" o "autorizador" puede: 
			1.- Ingresar y/o eliminar articulos
			2.- Cuando considere terminada la configuración de la solicitud DEBE confirmar la misma. Esta confirmación cambia del estado "Construyendo" al estado "Autorizar" si 
				se trata del rol "solicitante" de lo contrario si es el "autorizador" directamente a "Pendiente" (estado donde recien el perfil "operador" puede asignar stock). 
			3.- Puede Anular la solicitud dejandola en estado "cancelada", no existe eliminación fisica. 
			4.- Si el autorizador confirma la solicitud en construcción, salta el estado Autorizar pasando directamente al estado Pendiente de Entrega
			5.- Al confirmar un solicitante, se envia mail a los autorizadores del area, aviasando de solicitud para autorizar.	
		c.- En el estado "Pendiente de Entrega" puede Cerrar el perfil solicitante, el autorizador y el Administrador. La Disponibilidad y Entrega solo tiene acceso el perfil operador. El
			perfil Administrador puede si consultar la disponibilidad pero no adjudicar. Aqui se puede reimprimer remitos con entrega parcial en el mismo formato que el remito de entrega
			de material.
			Cuando el operador adjudica material. La solicitud pasa a "Imprimir Remito" (cuando se imprime vuelve si quedan articulos sin entrega, sino a Finalizada)
		d.- En el estado "Imprimir Remito" solo tiene acceso el perfil operador, quien corresponde realizar entrega de pedido y exigir firma de quien recibe. El Administrador puede consultar
			si existen solicitudes para imprimir. 
			Cuando imprime, envia mail automático a solicitantes del area de la solicitud, avisando de la condición de entrega, y vuelve a Pendiente de entrega o a Finalizada.
		e.- En el estado "Autorizar" cuando confirma autorizador, envia mail a TODOS los solicitantes del area para notificar que la solicitud esta en proceso para ser entregada, Pendiente de Entrega.
		f.- En el estado "Finalizada" se podrá reimprimir el remito dado el estado de entrega. Solo en este estado se puede realizar devolución de material. 
	4.- En la grilla las solicitudes aparecen en orden descendente en cualquiera de los estados. Ya que las primeras de la grilla seran las solicitudes ultimas trabajadas. 
			En breve en otra versión se implementará la busqueda entre rango de dos fechas, por default o solicitudes del mes en curso, o solicitudes de un mes para atras.
	5.- La cancelación de una solicitud cuenta con confirmación.

Ultima modificacion 10/2012 - se retira de pantalla lo correspondiente a la entrega parcial ya que se retiro de los requerimientos. Se hace respaldo de "StkProveedores" con esa versión ya que si
fue implementado y resuelto. si se indica en los estados finalizada la entrega parcial, ya que en realidad si se entrego una parte del pedido se indica para que el usuario tome conocimiento
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
if($tiempo_transcurrido >= 600)
{
   echo '<meta http-equiv="refresh" content="0; url=login.php">';
}
else
{
  $_SESSION["ultimoAcceso"] = $ahora;
} 
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$estado=$_REQUEST['estado'];
$unidad=$_REQUEST['unidad'];
$usuid=$_COOKIE['usuid'];
$parcial=$_COOKIE['parcial'];
$_SESSION['variable'] = "0";
?>

<META HTTP-EQUIV='refresh' CONTENT='60; URL=index.php?estado=<?php echo $estado;?>&unidad=<?php echo $unidad;?>'>

<script type="text/JavaScript" language="javascript">
function confirmCan()
{
	var agree=confirm(" Confirma Cancelar la solicitud? ");
	if (agree) return true ;
	else return false ;
}
</script>

<script type="text/JavaScript" language="javascript">
function confirmCerr()
{
	var agree=confirm(" Confirma Cerrar la solicitud? ");
	if (agree) return true ;
	else return false ;
}
</script>

<script type="text/JavaScript" language="javascript">
function confirmAut()
{
	var agree=confirm(" Confirma Autorizar la solicitud? ");
	if (agree) return true ;
	else return false ;
}
</script>

<script type="text/JavaScript" language="javascript">
function confirmSol()
{
	var agree=confirm(" Confirma la solicitud para ser Autorizada? ");
	if (agree) return true ;
	else return false ;
}
</script>

<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<form name="datos" action="index.php?estado=<?php echo $estado;?>&unidad=<?php echo $unidad;?>" method="post">
<center>
	<table class="inventario">
	       <?php
			if ($unidad=="0"){
				$uniNombre="Todas";
			}
			else
			{
				$consultaIII="Select * from Departamentos where DepId='".$unidad."'";
				$resultadoIII=mysqli_query($cn,$consultaIII) or die('La consulta fall&oacute;: ' .mysqli_error());
			       $uniEle=mysqli_fetch_assoc($resultadoIII);
				$uniNombre=$uniEle['DepNombre'];
			}

			$consultaV="Select * from StkEstados where StkEstId='".$estado."'"; 
			$resultadoV=mysqli_query($cn,$consultaV) or die('La consulta fall&oacute;: ' .mysqli_error());
		       $EstDsc=mysqli_fetch_assoc($resultadoV);
			$EstDscEsp=$EstDsc['StkEstDsc'];

		?>
			<tr>
				<td align="center">
				<font size="6" color="#000066">Solicitudes de Mercader&iacute;a - <?php echo $estado;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
				<?php
		       	if ($_COOKIE['usuperfil']==1 or $_COOKIE['usuperfil']==5) //Solicitante o Autorizador
				{
					$unidad=$_COOKIE['usuunidad'];
	       			echo '<table class="inventario">
					<a href="nuevasol.php?unidadcall='.$unidad.'"><img src="Images/nuevo.png" height="30" width="30" border=0><br></a>
					</tr>';
				}
		       	if ($_COOKIE['usuperfil']==2 or $_COOKIE['usuperfil']==3 or $_COOKIE['usuperfil']==6) //Operador de Proveduria, Administrador de Stock y Consultor Financiero
				{
		 		      	echo '<table class="inventario">
					<a href="articulossolpen.php?estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/alerta.jpg" height="30" width="30" border=0><br></a>
					</tr>';
				}
				?>
				</td>
			</tr>

			<tr>
				<td align="center">
				<font size="4" color="#000066"><?php echo $EstDscEsp;?></font>
				<br>
				<font size="4" color="#000066">Unidad:  <?php echo $uniNombre;;?></font>
				<br>
				</td>
			</tr>
	</table>
            <?php
		if ($_COOKIE['usuperfil']==1 or $_COOKIE['usuperfil']==5) //Solicitante o Autorizador
		{//Unidad para el solicitante, unidades para el autorizador pues puede ser sustituto de otro autorizador o representar varias unidades. Ambos vinculados a tabla UsuDep.
			$consulta="Select * from StkSolicitudes as s, UsuDep as d where s.StkSolEstado='".$estado."' and s.StkSolSecId=d.DepId and d.UsuId='".$usuid."' and d.UsuDepFchFin is null order by s.StkSolId DESC;";
		}
		else
		{
			//usuario con perfil operador, puede ver todas las unidades
			if ($unidad=="0")
			{//Todas
				$consulta="Select * from StkSolicitudes where StkSolEstado='".$estado."' order by StkSolId DESC;";
			}
			else
			{
				$consulta="Select * from StkSolicitudes where StkSolEstado='".$estado."' and StkSolSecId='".$uniEle['DepId']."' order by StkSolId DESC;";
			}
		}
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $colorlinea='#F3F3F3';
	       if (mysqli_num_rows($resultado)==0)
		{
	            	echo '<br><center><label>No existen solicitudes para el Estado seleccionado</label></center><br>';
		}
		else
		{
	       	if ($estado=="Finalizada" or $estado=="Cancelada")
			{
		       	echo '<br><table class="inventario">
				<tr bgcolor="#6495ED">
			             	<th>Fecha</th>
		                    	<th>Nro.Solicitud</th>
		                    	<th>Solicitante</th>
		                    	<th>Autoriza</th>
		                    	<th>Fch.Autoriza</th>
					<th>Oficina</th>';
				if ($estado=="Cancelada")
				{
				      	echo '<th>Cancelo</th>';
				}
				else
				{
	              	      	echo '<th>Estado entrega</th>';
				}
	                    	echo '<th>Fecha Fin</th>
		                    	<th>Detalle</th>';
				if ($estado=="Finalizada" and ($_COOKIE['usuperfil']==2 or $_COOKIE['usuperfil']==3))
				{
				      	echo '<th>Devoluci&oacute;n articulo</th>
		              		<th>Ultimo Remito</th>';
				}
				echo '</tr>';
				while($unaSolicitud=mysqli_fetch_assoc($resultado))
				{
	              		if($colorlinea=='#F3F3F3')
					{
		       			$colorlinea='#FEFEFE';
					}
					else
					{
						$colorlinea='#F3F3F3';
					}

					$consultaI="Select * from Usuarios where UsuId='".$unaSolicitud['StkSolUsuSol']."'";
					$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
				       $elUsuario=mysqli_fetch_assoc($resultadoI);
					$solicitante=$elUsuario['UsuNombre']." ".$elUsuario['UsuApellido'];

					$consultaI="Select * from Usuarios where UsuId='".$unaSolicitud['StkSolUsuAut']."'";
					$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
				       $elAutoriza=mysqli_fetch_assoc($resultadoI);
					$autoriza=$elAutoriza['UsuNombre']." ".$elAutoriza['UsuApellido'];
	
					$consultaII="Select * from Departamentos where DepId='".$unaSolicitud['StkSolSecId']."'";
					$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
				       $laUnidad=mysqli_fetch_assoc($resultadoII);

					//Si el estado es cancelado, el usuario en StkSolUsuMod es el responsable de la cancelacion 
					$consultaIII="Select * from Usuarios where UsuId='".$unaSolicitud['StkSolUsuMod']."'";
					$resultadoIII=mysqli_query($cn,$consultaIII) or die('La consulta fall&oacute;: ' .mysqli_error());
				       $elUsuarioCancela=mysqli_fetch_assoc($resultadoIII); 
					$cancelo=$elUsuarioCancela['UsuNombre']." ".$elUsuarioCancela['UsuApellido'];


					$Fecha=cambiaf_a_normal($unaSolicitud['StkSolFchSol']);
					$FechaFin=cambiaf_a_normal($unaSolicitud['StkSolFchFin']);
					echo '<tr  align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
       	       		       <td>'.$Fecha.'</td>
						<td>'.$unaSolicitud['StkSolId'].'</td>
						<td align=left>'.$solicitante.'</td>
						<td align=left>'.$autoriza.'</td>
						<td>'.cambiaf_a_normal($unaSolicitud['StkSolFchAut']).'</td>
						<td align=left>'.$laUnidad['DepNombre'].'</td>';
					if ($estado=="Cancelada")
					{
					      	echo '<td align=left>'.$cancelo.'</td>';
					}
					else
					{
						if($unaSolicitud['StkSolParcial']==0)
						{
						echo '<td align="center"><a><img src="Images/icono_check.gif" witdh="15" height="15" border=0></a></td>';
						}
						else
						{
						echo '<td align="center"><a><img src="Images/icono_check_rojo.gif" witdh="15" height="15" border=0></a></td>';
						}
					}
				      	echo '<td>'.$FechaFin.'</td>
						<td><a href="articulossolver.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>';
					if ($estado=="Finalizada" and ($_COOKIE['usuperfil']==2 or $_COOKIE['usuperfil']==3))
					{
						echo '<td><a href="devolucionsolart.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/devolucion.JPG" witdh="15" height="15" border=0></a></td>
							<td ><a Target="_blank" href="pdfreimprime.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/impresora.jpg" witdh="15" height="15" border=0></a></td>';
					}
					echo '</tr>';
				}
			}//Cierra Finalizada, Cancelada

			if ($estado=="Pendiente de Entrega")
			{
				if ($_COOKIE['usuperfil']=="1" or $_COOKIE['usuperfil']=="5")
				{//Solicitante o Autorizador 
			             	echo '<br><table class="inventario">
					<tr bgcolor="#6495ED">
			                    	<th>Fecha</th>
			                    	<th>Nro.Solicitud</th>
			                    	<th>Solicitante</th>
			                    	<th>Autoriza</th>
			                    	<th>Fch.Autoriza</th>
				  		<th>Oficina</th>
		                    		<th>Detalle</th>
			                    	<th>Cerrar</th>
					</tr>';
					While($unaSolicitud=mysqli_fetch_assoc($resultado))
					{
              				if($colorlinea=='#F3F3F3')
						{
		       				$colorlinea='#FEFEFE';
						}
						else
						{
							$colorlinea='#F3F3F3';
						}
						$consultaI="Select * from Usuarios where UsuId='".$unaSolicitud['StkSolUsuSol']."'";
						$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $elUsuario=mysqli_fetch_assoc($resultadoI);
						$solicitante=$elUsuario['UsuNombre']." ".$elUsuario['UsuApellido'];

						$consultaI="Select * from Usuarios where UsuId='".$unaSolicitud['StkSolUsuAut']."'";
						$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $elAutoriza=mysqli_fetch_assoc($resultadoI);
						$autoriza=$elAutoriza['UsuNombre']." ".$elAutoriza['UsuApellido'];

						$consultaII="Select * from Departamentos where DepId='".$unaSolicitud['StkSolSecId']."'";
						$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $laUnidad=mysqli_fetch_assoc($resultadoII);
	
						$Fecha=cambiaf_a_normal($unaSolicitud['StkSolFchSol']);
						echo '<tr  align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			                     	<td>'.$Fecha.'</td>
							<td>'.$unaSolicitud['StkSolId'].'</td>
							<td>'.$solicitante.'</td>
							<td>'.$autoriza.'</td>
							<td>'.cambiaf_a_normal($unaSolicitud['StkSolFchAut']).'</td>
							<td>'.$laUnidad['DepNombre'].'</td>
				       	       <td><a href="articulossolver.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>
							<td onclick="return(confirmCerr())"><a href="eliminosol.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/cerrarnaran.jpg" witdh="15" height="15" border=0></a></td></tr>';
						}//Cierra el WHILE
				}
				else //Operador-2, Administrador-3, Consultor financiero-6 y de unidad-7
				{
					echo '<br><table class="inventario">
					<tr bgcolor="#6495ED">
			                    	<th>Fecha</th>
		       	             	<th>Nro.Solicitud</th>
		              	      	<th>Solicitante</th>
			                    	<th>Autoriza</th>
			                    	<th>Fch.Autoriza</th>
			  			<th>Oficina</th>
			                    	<th>Detalle</th>';
					if ($_COOKIE['usuperfil']=="2")
					{//Operador
			                    	echo '<th>Disponibilidad y Entrega</th></tr>';
					}
					if ($_COOKIE['usuperfil']=="6") 
					{//Consultor financiero
			                    	echo '<th>Disponibilidad</th></tr>';
					}
					if ($_COOKIE['usuperfil']=="3") 
					{//Administrador
			                    	echo '<th>Disponibilidad</label>
							</td><th>Cerrar</th></tr>';
					}
					while($unaSolicitud=mysqli_fetch_assoc($resultado))
					{
		              		if($colorlinea=='#F3F3F3')
						{
		       				$colorlinea='#FEFEFE';
						}
						else
						{
							$colorlinea='#F3F3F3';
						}
						$consultaI="Select * from Usuarios where UsuId='".$unaSolicitud['StkSolUsuSol']."'";
						$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $elUsuario=mysqli_fetch_assoc($resultadoI);
						$solicitante=$elUsuario['UsuNombre']." ".$elUsuario['UsuApellido'];

						$consultaI="Select * from Usuarios where UsuId='".$unaSolicitud['StkSolUsuAut']."'";
						$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $elAutoriza=mysqli_fetch_assoc($resultadoI);
						$autoriza=$elAutoriza['UsuNombre']." ".$elAutoriza['UsuApellido'];

						$consultaII="Select * from Departamentos where DepId='".$unaSolicitud['StkSolSecId']."'";
						$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $laUnidad=mysqli_fetch_assoc($resultadoII);

						$Fecha=cambiaf_a_normal($unaSolicitud['StkSolFchSol']);
	
						echo '<tr align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			                     	<td>'.$Fecha.'</td>
							<td>'.$unaSolicitud['StkSolId'].'</td>
							<td>'.$solicitante.'</td>
							<td>'.$autoriza.'</td>
							<td>'.cambiaf_a_normal($unaSolicitud['StkSolFchAut']).'</td>
							<td>'.$laUnidad['DepNombre'].'</td>
							<td><a href="articulossolver.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>';
						if ($_COOKIE['usuperfil']=="2" or $_COOKIE['usuperfil']=="6" or $_COOKIE['usuperfil']=="3")//Operador, consultor fin, administrador
						{
				                    	echo '<td><a href="eliminovalaux.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/derivar.png" witdh="15" height="15" border=0></a></td>';
						}
						if ($_COOKIE['usuperfil']=="3") //Administrador
						{
							echo '<td onclick="return(confirmCerr())"><a href="eliminosol.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/cancelar.gif" witdh="15" height="15" border=0></a></td>';
						}
						echo '</tr>';

						//El php eliminovalaux se creo previo al acceso a la disponibilidad para que inicialice algunos valores de la solicitud para que se refresque el stock disponible
					}
				}
			}

			if ($estado=="Construyendo") 
			{
				if ($_COOKIE['usuperfil']=="1" or $_COOKIE['usuperfil']=="5")
				{
			             	echo '<br><table class="inventario">
					<tr bgcolor="#6495ED">
			                    	<th>Fecha</th>
			                    	<th>Nro.Solicitud</th>
			                    	<th>Solicitante</th>
				  		<th>Oficina</th>
	       	       	      	<th>Registro de Art&iacute;culos</th>
	              	      		<th>Detalle</th>
		                    		<th>Cancelar</th>
			                    	<th>Confirme Pedido</th></tr>';
					while($unaSolicitud=mysqli_fetch_assoc($resultado))
					{
	              			if($colorlinea=='#F3F3F3')
						{
	       				$colorlinea='#FEFEFE';
						}
						else
						{
						$colorlinea='#F3F3F3';
						}
						$consultaI="Select * from Usuarios where UsuId='".$unaSolicitud['StkSolUsuSol']."'";
						$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $elUsuario=mysqli_fetch_assoc($resultadoI);
						$solicitante=$elUsuario['UsuNombre']." ".$elUsuario['UsuApellido'];

						$consultaII="Select * from Departamentos where DepId='".$unaSolicitud['StkSolSecId']."'";
						$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $laUnidad=mysqli_fetch_assoc($resultadoII);
	
						$Fecha=cambiaf_a_normal($unaSolicitud['StkSolFchSol']);

						echo '<tr align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			                     	<td>'.$Fecha.'</td>
							<td>'.$unaSolicitud['StkSolId'].'</td>
							<td>'.$solicitante.'</td>
							<td>'.$laUnidad['DepNombre'].'</td>
				              	<td><a href="ingresoartsol.php?solid='.$unaSolicitud['StkSolId'].'&unidadcall='.$unidad.'&estadocall='.$estado.'"><img src="Images/modificar.png" witdh="15" height="15" border=0></a></td>
							<td><a href="articulossolver.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>
							<td onclick="return(confirmCan())"><a href="eliminosol.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/cancelar.gif" witdh="15" height="15" border=0></a></td>';
						if ($_COOKIE['usuperfil']=="5")
						{
					              echo '<td onclick="return(confirmAut())"><a href="confirmasol.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/guardar.gif" witdh="15" height="15" border=0></a></td>';
						}
						else
						{
					              echo '<td onclick="return(confirmSol())"><a href="confirmasol.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/guardar.gif" witdh="15" height="15" border=0></a></td>';
						}
						echo '</tr>';
					}
				}
				else
				{
			             	echo '<br><table class="inventario">
					<tr bgcolor="#6495ED">
			                    	<th>Fecha</th>
			                    	<th>Nro.Solicitud</th>
			                    	<th>Solicitante</th>
				  		<th>Oficina</th>
	                    			<th>Detalle</th>
					</tr>';
					while($unaSolicitud=mysqli_fetch_assoc($resultado))
					{
		              		if($colorlinea=='#F3F3F3')
						{
		       				$colorlinea='#FEFEFE';
						}
						else
						{
							$colorlinea='#F3F3F3';
						}
						$consultaI="Select * from Usuarios where UsuId='".$unaSolicitud['StkSolUsuSol']."'";
						$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $elUsuario=mysqli_fetch_assoc($resultadoI);
						$solicitante=$elUsuario['UsuNombre']." ".$elUsuario['UsuApellido'];

						$consultaII="Select * from Departamentos where DepId='".$unaSolicitud['StkSolSecId']."'";
						$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $laUnidad=mysqli_fetch_assoc($resultadoII);
		
						$Fecha=cambiaf_a_normal($unaSolicitud['StkSolFchSol']);
	
						echo '<tr align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			                     	<td>'.$Fecha.'</td>
							<td>'.$unaSolicitud['StkSolId'].'</td>
							<td>'.$solicitante.'</td>
							<td>'.$laUnidad['DepNombre'].'</td>
				              	<td><a href="articulossolver.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>
						</tr>';
					}
				}
			}

			if ($estado=="Autorizar") 
			{
				if ($_COOKIE['usuperfil']<>"5")
				{
			             	echo '<br><table class="inventario">
					<tr bgcolor="#6495ED">
			                    	<th>Fecha</th>
			                    	<th>Nro.Solicitud</th>
			                    	<th>Solicitante</th>
				  		<th>Oficina</th>
	                    			<th>Detalle</th>
					</tr>';
					while($unaSolicitud=mysqli_fetch_assoc($resultado))
					{
		              		if($colorlinea=='#F3F3F3')
						{
		       				$colorlinea='#FEFEFE';
						}
						else
						{
							$colorlinea='#F3F3F3';
						}
						$consultaI="Select * from Usuarios where UsuId='".$unaSolicitud['StkSolUsuSol']."'";
						$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $elUsuario=mysqli_fetch_assoc($resultadoI);
						$solicitante=$elUsuario['UsuNombre']." ".$elUsuario['UsuApellido'];
	
						$consultaII="Select * from Departamentos where DepId='".$unaSolicitud['StkSolSecId']."'";
						$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $laUnidad=mysqli_fetch_assoc($resultadoII);
		
						$Fecha=cambiaf_a_normal($unaSolicitud['StkSolFchSol']);
	
						echo '<tr align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
		                     		<td>'.$Fecha.'</td>
							<td>'.$unaSolicitud['StkSolId'].'</td>
							<td>'.$solicitante.'</td>
							<td>'.$laUnidad['DepNombre'].'</td>
				              	<td><a href="articulossolver.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>
						</tr>';
					}
				}
				else
				{
			             	echo '<br><table class="inventario">
					<tr bgcolor="#6495ED">
			                    	<th>Fecha</th>
			                    	<th>Nro.Solicitud</th>
			                    	<th>Solicitante</th>
				  		<th>Oficina</th>
	       	       	      	<th>Ing/Egr Art&iacute;culos</th>
	              	      		<th>Detalle</th>
		                    		<th>Cancelar Solicitud</th>
			                    	<th>Autorizar</th>
					</tr>';
					while($unaSolicitud=mysqli_fetch_assoc($resultado))
					{
	              			if($colorlinea=='#F3F3F3')
						{
		      				$colorlinea='#FEFEFE';
						}
						else
						{
						$colorlinea='#F3F3F3';
						}
						$consultaI="Select * from Usuarios where UsuId='".$unaSolicitud['StkSolUsuSol']."'";
						$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $elUsuario=mysqli_fetch_assoc($resultadoI);
						$solicitante=$elUsuario['UsuNombre']." ".$elUsuario['UsuApellido'];

						$consultaII="Select * from Departamentos where DepId='".$unaSolicitud['StkSolSecId']."'";
						$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $laUnidad=mysqli_fetch_assoc($resultadoII);
	
						$Fecha=cambiaf_a_normal($unaSolicitud['StkSolFchSol']);

						echo '<tr align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			                     	<td>'.$Fecha.'</td>
							<td>'.$unaSolicitud['StkSolId'].'</td>
							<td>'.$solicitante.'</td>
							<td>'.$laUnidad['DepNombre'].'</td>
				             		<td><a href="ingresoartsol.php?solid='.$unaSolicitud['StkSolId'].'&unidadcall='.$unidad.'&estadocall='.$estado.'"><img src="Images/modificar.png" witdh="15" height="15" border=0></a></td>
							<td><a href="articulossolver.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>
							<td onclick="return(confirmCan())"><a href="eliminosol.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/cancelar.gif" witdh="15" height="15" border=0></a></td>
							<td onclick="return(confirmAut())"><a href="confirmasol.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/guardar.gif" witdh="15" height="15" border=0></a></td>
						</tr>';
					}
				}
			}
			if ($estado=="Imprimir Remito") 
			{
				if ($_COOKIE['usuperfil']=="1" or $_COOKIE['usuperfil']=="5")
				{
					echo 'Su perfil no tiene permiso para acceder a este Estado';
				}
				else
				{
			             	echo '<br><table class="inventario">
					<tr bgcolor="#6495ED">
			                    	<th>Fecha</th>
			                    	<th>Nro.Solicitud</th>
			                    	<th>Solicitante</th>
				  		<th>Oficina</th>
	              		      	<th>Detalle</th>';
					if ($_COOKIE['usuperfil']=="2") //Operador
					{
			                    	echo '<td><label>Imprimir Remito</th>';
						//no se permite imprimir el remito a otro roles. Al imprimir la solicitud vuelve a su estado anterior. Podría haber confusión luego si el remito fue o no impreso
						//para ser firmado por el operador y el solicitante o quien retira. El unico rol que necesita imprimir el remito es el operador quien es quien entrega
						//el pedido de material.
					}
					echo '</tr>';
					while($unaSolicitud=mysqli_fetch_assoc($resultado))
					{
	              			if($colorlinea=='#F3F3F3')
						{
		       			$colorlinea='#FEFEFE';
						}
						else
						{
						$colorlinea='#F3F3F3';
						}
						$consultaI="Select * from Usuarios where UsuId='".$unaSolicitud['StkSolUsuSol']."'";
						$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $elUsuario=mysqli_fetch_assoc($resultadoI);
						$solicitante=$elUsuario['UsuNombre']." ".$elUsuario['UsuApellido'];

						$consultaII="Select * from Departamentos where DepId='".$unaSolicitud['StkSolSecId']."'";
						$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
					       $laUnidad=mysqli_fetch_assoc($resultadoII);
	
						$Fecha=cambiaf_a_normal($unaSolicitud['StkSolFchSol']);

						echo '<tr  align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
		                     		<td>'.$Fecha.'</td>
							<td>'.$unaSolicitud['StkSolId'].'</td>
							<td>'.$solicitante.'</td>
							<td>'.$laUnidad['DepNombre'].'</td>
					             <td><a href="articulossolver.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unidad.'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>';
						if ($_COOKIE['usuperfil']=="2") //Operador
						{
							echo '<td ><a Target="_blank" href="cambioestado.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$estado.'&unidadcall='.$unaSolicitud['StkSolSecId'].'"><img src="Images/impresora.jpg" witdh="15" height="15" border=0></a></td>';
						}
						echo '</tr>';
					}
				}
			}
		}
		echo '</table><br>';
		?>
</center>
</form>
<?php
require_once("pie.php");
?>