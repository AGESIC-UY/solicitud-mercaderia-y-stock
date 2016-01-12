<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$usuele=$_REQUEST['usuele'];
$unidad=$_REQUEST['unidad'];
?>

<center>
	<table class="inventario">
	<tr>
            <?php
         	echo '<th>Clases del usuario</font><br></th></tr>';
            ?>
	</table>
            <?php
		$consulta="Select * from StkArtCls where 1 order by StkArtClsDsc";
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $colorlinea='#F3F3F3';

             	echo '<br><table class="inventario">
		<tr bgcolor="#MM0077">
            	<th>Ambiente</th>
             	<th>Habilitar</th>
		</tr>';
		while($unAmbiente=mysqli_fetch_assoc($resultado))
		{
			$consultaI="Select * from StkArtClsUsu where UsuId='".$usuele."' and StkArtClsId='".$unAmbiente['StkArtClsId']."'";
			$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
			$claseusuario=mysqli_fetch_assoc($resultadoI);
			$icohab="Images/tilde.gif";
			$icoele="Images/tilde.gif";
			$hab=0;
			$ele=0;
			if (mysqli_num_rows($resultadoI)==0)
			{//Hay registros que no se cargaron, por defecto sino estan lo inserto deshabilitado 
				$hab=0;
				$icohab="Images/pausa2.png";
				$ele=0;
				$icoele="Images/pausa2.png";
				$sentenciaI="Insert into StkArtClsUsu (UsuId, StkArtClsId, StkArtClsHab) values('".$usuele."','".$unAmbiente['StkArtClsId']."','".$hab."')";
				$clsusu= mysqli_query($cn, $sentenciaI);
			}
			else
			{
				if($claseusuario['StkArtClsHab']==0)
				{//y hay registros cargados deshabilitados
				$hab=1;
				$icohab="Images/pausa2.png";
				}
				if($claseusuario['StkArtClsEle']==0)
				{
				$ele=1;
				$icoele="Images/pausa2.png";
				}
			}
			echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
                    	<td>'.$unAmbiente['StkArtClsDsc'].'</td>
			<td align="center"><a href="habdeshabamb.php?usuele='.$usuele.'&unidad='.$unidad.'&clsid='.$unAmbiente['StkArtClsId'].'&hab='.$hab.'&soy='.hab.'"><img src="'.$icohab.'" witdh="15" height="15" border=0></a></td>';
			echo '</tr>';
		}
		echo '</table><br>';
            ?>
    <tr><td align="center">&nbsp;</td></tr>
</center>

<?php
require_once("pie.php");
?>