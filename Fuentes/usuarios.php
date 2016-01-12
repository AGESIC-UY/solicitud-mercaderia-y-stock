<!--
Creación	Alicia Acevedo
Fecha:		12/2010
-->
<?php
require_once("Includes/conviertefecha.php");
$menutab=basename(__FILE__, ".php"); // devuelve el nombre sin extension .php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$unidad=$_REQUEST['unidad'];
if (isset($_REQUEST['apebus'])){
$apebus=$_REQUEST['apebus'];
} else {
$apebus= "";
}

$consulta="Select * from StkArtClsUsu as u, StkArtCls as c where u.StkArtClsId=c.StkArtClsId and u.UsuId='".$_COOKIE['usuid']."'";
$resultado=mysqli_query($cn,$consulta);
$eltit=mysqli_fetch_assoc($resultado);
$titulo='Usuarios del Sistema de Stock';
$claseusuconectado=$eltit['StkArtClsId'];
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table class="inventario">
		<tr>
			<?php
			if ($_COOKIE['usuperfil']<>6) //consultor el else corresponde al administrador o personal con perfila para ingresar en este objeto
			{
				echo '<td align="center"><font size="5" color="#000066">'.$titulo.'</font>&nbsp;&nbsp;<a href="nuevousu.php"><img src="Images/nuevo.png" height="30" width="30" border=0><br></a></td>';
			}
			else
			{
				echo '<td align="center"><font size="5" color="#000066"><?php echo $titulo;?></font></td>';
			}
		       ?>
		</tr>
	</table>
	<table class="inventario">
	<?php
		require_once("filtrosusr.php");
	       if ($unidad=='99999'){
			if ($apebus=="")
			{
				$consulta="Select * from Usuarios as u, SisPflUsuarios as f, SisPerfiles as p, Departamentos as d where u.UsuId=f.UsuId and f.SisPflId=p.SisPflId and u.SeccionesId=d.DepId order by u.UsuApellido,u.UsuNombre";
			}
			else
			{
				$elLike="%".$apebus."%";
				$consulta="Select * from Usuarios as u, SisPflUsuarios as f, SisPerfiles as p, Departamentos as d where u.UsuId=f.UsuId and f.SisPflId=p.SisPflId and u.SeccionesId=d.DepId and u.UsuApellido LIKE '".$elLike."' order by u.UsuApellido,u.UsuNombre";
			}

			$DepEle='Todos';
		}
		else
		{
			if ($apebus=="")
			{
				$consulta="Select * from Usuarios as u, SisPflUsuarios as f, SisPerfiles as p, Departamentos as d where u.UsuId=f.UsuId and f.SisPflId=p.SisPflId and u.SeccionesId=d.DepId and u.SeccionesId='".$unidad."' order by u.UsuApellido,u.UsuNombre";
			}
			else
			{
				$elLike="%".$apebus."%";
				$consulta="Select * from Usuarios as u, SisPflUsuarios as f, SisPerfiles as p, Departamentos as d where u.UsuId=f.UsuId and f.SisPflId=p.SisPflId and u.SeccionesId=d.DepId and u.SeccionesId='".$unidad."' and u.UsuApellido LIKE '".$elLike."' order by u.UsuApellido,u.UsuNombre";
			}

		}
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $colorlinea='#F3F3F3';
	       if (mysqli_num_rows($resultado)==0){
	            	echo '<br><center><label>No existen usuarios registrados para el sistema.</label></center><br>';
		}
		else
		{
			if ($_COOKIE['usuperfil']<>6) //consultor el else corresponde al administrador o personal con perfil para ingresar en este objeto
			{
	      	      		echo '<br><table class="inventario">
				<tr bgcolor="#6495ED">
                     	<th>Persona</th>
                     	<th>Usuario</th>
                       	<th>Rol</th>
                       	<th>Repartici&oacute;n principal</th>
	                     <th>Autoriza otras Areas</th>
	                     <th>Modificar</th>
                        	<th>Envia Mail Usu/Pass</th>
                        	<th>Inactividad</th>
				</tr>';
			}
			else
			{
	      	      		echo '<br><table class="inventario">
				<tr bgcolor="#6495ED">
                     	<th>Persona</th>
                     	<th>Usuario</th>
                       	<th>Rol</th>
                       	<th>Repartici&oacute;n principal</th>
                     	<th>Mail</th>
                     	<th>Inactividad</th>
				</tr>';
			}
			while($unUsuario=mysqli_fetch_assoc($resultado)){
       	      		if($colorlinea=='#F3F3F3')
				{
       				$colorlinea='#FEFEFE';
				}
				else
				{
					$colorlinea='#FEFEFE';
				}
				$DepEle=$unUsuario['DepNombre'];

//				$fini=cambiaf_a_normal($unUsuario['SisPflFchCre']);
//				$ffin=cambiaf_a_normal($unUsuario['SisPflFchFin']);
//				<td align="center">'.$fini.'</td>
//				<td align="center">'.$ffin.'</td>
				$elusu=$unUsuario['UsuId'];
				$nomape=$unUsuario['UsuApellido']." ".$unUsuario['UsuNombre'];
				if ($_COOKIE['usuperfil']<>6) //consultor el else corresponde al administrador o personal con perfila para ingresar en este objeto
				{
					echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
	                     	<td>'.$nomape.'</td>
      		              	<td>'.$unUsuario['UsuUsuario'].'</td>
					<td align="center">'.$unUsuario['SisPflDsc'].'</td>
					<td align="center">'.$unUsuario['DepNombre'].'</td>';
					if ($unUsuario['SisPflId']==5) //consultor el else corresponde al administrador o personal con perfil para ingresar en este objeto
					{
						echo '<td align="center"><a href="usuautorizadep.php?usuele='.$elusu.'&unidad='.$unUsuario['DepId'].'"><img src="Images/candado.gif" witdh="15" height="15" border=0></a></td>';
					}
					else
					{
			       	       echo '<td align="center"><a><img src="Images/blank.gif" witdh="15" height="15" border=0></a></td>';
					}
					echo '<td align="center"><a href="updateusu.php?usuele='.$elusu.'&unidad='.$unidad.'"><img src="Images/modificar.png" witdh="15" height="15" border=0></a></td>';
					echo '<td align="center"><a href="usuenviomail.php?usuele='.$elusu.'"><img src="Images/icono_mail.gif" witdh="15" height="15" border=0></a></td>
      		              	<td>'.cambiaf_a_normal($unUsuario['UsuFchFin']).'</td>
					</tr>';
				}
				else
				{
					echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
              	       	<td>'.$nomape.'</td>
                     		<td>'.$unUsuario['UsuUsuario'].'</td>
					<td align="center">'.$unUsuario['SisPflDsc'].'</td>
					<td align="center">'.$unUsuario['DepNombre'].'</td>
					<td>'.$unUsuario['UsuMail'].'</td>
      		              	<td>'.$unUsuario['UsuFchFin'].'</td>
					</tr>';

				}
			}//Cierra el WHILE que imprime los resultados obtenidos
		}//Cierra el IF donde se pregunta si hay resultados o no
		echo '</table><br>';
       ?>
	</table>
</center>

<?php
require_once("pie.php");
?>