<!--
Creación	Alicia Acevedo
Fecha:		09/2010
Algunas Características:
	1.- Consumo de artículo por unidad
	2.- Retire de los filtros el " and StkMovArtTpo='".S."' ", pues ahora tengo que considerar que por la StkSolId podria haber movimiento de entrada, se implemento la 
	devolución de un articulo por la unidad a la proveeduria, por no ser el articulo que les interesa, por las razones que sean.

	3.- Se incorporo el filtro por area, ésta involucra el conjunto de unidades, tiene que ver con el atributo DepIddep de la tabla departamentos.
	Debo considerar que la consulta la hace el administrador, operador, el consultor financiero, el consultor de unidad, el autorizador (puede ser de la unidad y esta a su vez ser
	area - jefe de area) y solicitante. 
		En el caso de administrador, operador y consultor financiero pueden elegir cualquier area y consultarla. 
		En el caso de consultor de unidad(no contamos a la fecha con usuario vinculado a este rol pero debo de considerarlo), autorizador (no jefe de area) y solicitante no debe dar
		 lugar a la eleccion ni del area, ni de la unidad, corresponde al area y unidad del usuario logged..
		En el caso de Autorizador pero si jefe de area, debo permitir seleccionar las unidad del area a la que pertenece el usuario logged.
		En el caso de Articulador o Proveedores ni acceden a este objeto
		En el caso de Administrador - Inventario ??????????????????????????????????????????????????????

10/2011
Al incorporar las areas, tuve que duplicar una buena parte del codigo ya que tengo que hacer un while de los departamentos o unidades subordinados e ir armando la grid, como solucion inmediata
copie el codigo por eso quedo muy grande el objeto.... 

10/11/2011 - Se retira columna de cantidad solicitada, solicitado por Adriana, da lugar a confusión cuando se compara con fecha y sin fecha para aquellos articulos
que hubo devolución, etc. ---------------        	<td align="center">'.$CantArtSol.'</td>
No desarmo la busqueda de esta cantidad por si se arrepienten de retirarlo de la consulta
-->

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
function cambiaf_a_mysql(){
	$fecha=$_POST['fecha']
	$fch=explode("/",$fecha);
	$fecha=$fch[2]."-".$fch[1]."-".$fch[0];
	return $fecha;
} 
</SCRIPT>

<?php
	session_start();
	$menutab=basename(__FILE__, ".php"); 
	require_once("principioseleccion.php");
	require_once("funcionesbd.php");

	$fchdesde=$_REQUEST['fchdesde'];
	$fchhasta=$_REQUEST['fchhasta'];
	$artid=$_REQUEST['artid'];
	$uniele=$_REQUEST['uniele'];

	$data=NULL;
	$i=NULL;

if (isset($_REQUEST['detallo'])){
$detallo=$_REQUEST['detallo'];
} else {
$detallo= "";
}
if (isset($_REQUEST['costos'])){
$costos=$_REQUEST['costos'];
} else {
$costos= "";
}
if (isset($_REQUEST['areaele'])){
$areaele=$_REQUEST['areaele'];
} else {
$areaele= "";
}
if (isset($_REQUEST['artbus'])){
$artbus=$_REQUEST['artbus'];
} else {
$artbus= "";
}
	if ($artid==0)
	{
		$ArtNombre="Todos";
	}
	else
	{
		$consultaI="Select * from StkArticulos where StkArtId=$artid"; 
		$resultadoI=mysqli_query($cn,$consultaI);
		$unArticulo=mysqli_fetch_assoc($resultadoI);
		$ArtNombre=$unArticulo['StkArtDsc'];
	}

?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table class="inventario">
		<tr>
		    <td align="left">
			<?php require_once("filtrosconsultas.php");?>
		    </td>
		</tr>
		<table class="inventario">
			<?php
			$txtdetallo="";
			if ($detallo=="on")
			{
			$txtdetallo=" c/detalle";
			}
			?>
			<th><font size="6" color="#FFFF00" >Consumo de art&iacute;culos </font></th>
			<th><a Target="_blank" href="pdfconsumocantidades.php?fchdesde=<?php echo $fchdesde ?>&fchhasta=<?php echo $fchhasta?>&detallo=<?php echo $detallo?>&costos=<?php echo $costos?>&artid=<?php echo $artid?>&artbus=<?php echo $artbus?>&areaele=<?php echo $areaele?>&uniele=<?php echo $uniele?>&array=<?php echo $tmp?>"><img src="Images/impresora.jpg" height="30" width="30" border=0><br></a>
			<br>
			<th><font size="4">&Aacute;rea: <?php echo $AreaNombre;?></font></th>
			<th><font size="4">Unidad: <?php echo $UniNombre;?></font></th>
			<th><font size="4">Art&iacute;culo: <?php echo $ArtNombre;?></font></th>
			<th><font size="4">Entre fechas: <?php echo $fchdesde;?> - <?php echo $fchhasta;?><?php echo $txtdetallo;?></font></th>
	       <?php

 		//sort($uni); //ordenando con 1 dimension 
		$elLike="%".$artbus."%";

// and ($_COOKIE['usuperfil']==2 or $_COOKIE['usuperfil']==3 or $_COOKIE['usuperfil']==6 or $_COOKIE['usuperfil']==10)
		if ($areaele>0)
		{
			$diferencia = 4 - strlen($areaele);
			for($i = 0 ; $i < $diferencia; $i++)
			{
			        $numero_con_ceros = 0;
			}
			$numero_con_ceros = $areaele;

$consultaUOs="Select * from departamentos where DepId = '".$areaele."'";	
$resultado=mysqli_query($cn,$consultaUOs) or die (mysqli_error()); 
$lasUOS=mysqli_fetch_assoc($resultado);
$unilog=$lasUOS['DepNombre'];

$herederosusu="%".$lasUOS['DepHerederos']."%";
$consultaUOs="Select * from departamentos where DepHerederos like '".$herederosusu."'";	
$resultado=mysqli_query($cn,$consultaUOs) or die (mysqli_error()); 
$in="";
while($lasUOS=mysqli_fetch_assoc($resultado))
{
	$in=$in.",".$lasUOS['DepId'];
}
$in=Substr($in,1); //Quito primer coma

//			$consultaK="Select * from Departamentos where DepId='".$areaele."'"; 
//			$resultadoK=mysqli_query($cn,$consultaK);
//			$elarea=mysqli_fetch_assoc($resultadoK);
//			$prefijoherederos=$elarea['DepHerederos'];
//			$prefijolargo=strlen($prefijoherederos);

//			//Va a traer todos los departamentos herederos del area elegida dado el prefijo
//			$consultaK="Select * from Departamentos where SubStr(DepHerederos,1,$prefijolargo)='".$prefijoherederos."' order by DepHerederos"; 
//			$resultadoK=mysqli_query($cn,$consultaK);
//			while($unidadesconsulta=mysqli_fetch_assoc($resultadoK))
//			{
//				$iddepmov=$unidadesconsulta['DepId'];
//				if ($unidadesconsulta['DepNoVigente']==1)
//				{
//				$nomdepmov=$unidadesconsulta['DepNombre']." "."(No Vigente)";
//				}
//				else
//				{
//				$nomdepmov=$unidadesconsulta['DepNombre'];
//				}

				if ($artid==0)
				{//Todos los articulos;
					if ($uniele==0)
					{//Todos los articulos y unidades
						if ($artbus=="")
						{//Todos los articulos, unidades y sin filtro de nombre de articulo
						$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtClsId=c.StkArtClsId and d.DepId in ($in) order by a.StkArtDsc, m.StkMovArtFch"; 
						}
						else
						{//filtro texto like 
						$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and a.StkArtClsId=c.StkArtClsId and d.DepId in ($in) order by a.StkArtDsc, m.StkMovArtFch"; 
						}
					}
					else
					{
						if ($artbus=="")
						{//Todos los articulos de una unidad
						$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=$uniele and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtClsId=c.StkArtClsId and DepId in ($in) order by a.StkArtDsc, m.StkMovArtFch"; 
						}
						else
						{//filtro texto like 
						$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=$uniele and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and a.StkArtClsId=c.StkArtClsId and d.DepId in ($in) order by a.StkArtDsc, m.StkMovArtFch"; 
						}
					}
				}
				else
				{
					if ($uniele==0)
					{
						if ($artbus=="")
						{
						//Todas las unidades de un articulo
						//Todos los movimientos de salida entre las fechas indicadas del articulo filtrado
						$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and m.StkArtId=a.StkArtid and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and d.DepId in ($in) order by m.StkMovArtFch"; 
						}
						else
						{//filtro texto like 
						$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and m.StkArtId=a.StkArtid and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and d.DepId in ($in) order by m.StkMovArtFch"; 
						}
					}	
					else
					{
						if ($artbus=="")
						{
						//Un articulo una unidad
						$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and s.StkSolSecId=$uniele and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and d.DepId in ($in) order by m.StkMovArtFch"; 
						}
						else
						{//filtro texto like 
						$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and s.StkSolSecId=$uniele and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and d.DepId in ($in) order by d.Departamentos, m.StkMovArtFch"; 
						}
					}
				}
			$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
		       $colorlinea='#F3F3F3';
		       if (mysqli_num_rows($resultado)==0)
			{
		            $nomdepmov="No existen movimientos para la unidad ".$nomdepmov;
				echo $nomdepmov;
				echo '<br>';
			}
			else
			{
				//Armado de grilla segun datos especificados en el consulta.
				$CantArtSol="0";
				$CantArtEnt="0";
				$CantArtSolTot="0";
				$CantArtEntTot="0";

				$ValCostoSol="0";
				$ValCostoEnt="0";
				$ValCostoSolTot="0";
				$ValCostoEntTot="0";
				$ValTotal="0";

				$dep="1";  //para reconocer primer registro a acumular
				$fch="1";
				$art="1";
		
				if ($artid==0)
				{//Todos los articulos
					if ($uniele==0)
					{//Todos los articulos y las unidades
						//Todos los movimientos de salida entre las fechas indicadas, ordenada por unidad articulos o por articulos unidad verrrrrrrrrrrrrr 
						if ($detallo<>"on")
						{
							//Sin detalle resultados: Unidad - Articulo - Cantidad solicitada -  cantidad entregada
					             	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Unidad</th>
		       	              	<th>Articulo</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}
	
								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'"; 
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);

								if ($dep=="1" or ($dep==$unMovArticulo['DepNombre'] and $art==$unMovArticulo['StkArtDsc']))
								{
									$dep=$unMovArticulo['DepNombre'];
									$art=$unMovArticulo['StkArtDsc'];
									if ($sumooresto>0)
									{//Es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+($costobasico*$unSolArticulo['StkSolArtCantSol']);
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+($costobasico*($unMovArticulo['StkMovArtCant'])*$sumooresto);
								}
								else
								{
									if($CantArtEnt>0)
									{//este valor en cero o menor a cero es afectada la cantidad por devolución del artículo, no la despliego, pues en realidad no es consumo,
									//este es negativo.
										echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
					       	       	      	<td>'.$dep.'</td>
					              		      	<td>'.$art.'</td>
							                    	<td align="center">'.$CantArtEnt.'</td>';
										if ($costos=="on")
										{
			       			              		echo '<td align="center">'.$ValCostoEnt.'</td>';
											$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
											$i++;
										}
										else
										{
											$datatit[$i]=array('Unidad'=>$dep);
											$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Cantidad'=>$CantArtEnt);
											$i++;
										}
										echo '</tr>';
									
										if ($CantArtEnt>0)
										{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
											$CantArtSolTot=$CantArtSolTot+$CantArtSol;
											$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
										}
										$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
										$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
			
										$art=$unMovArticulo['StkArtDsc'];
										$dep=$unMovArticulo['DepNombre'];
										$CantArtSol="-";
										$ValCostoSol="-";
										if ($sumooresto>0)
										{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
											$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
											$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
										}
										$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
										$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
									}			
								}
							}
							if($CantArtEnt>0)
							{
								echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
				              	      	<td>'.$dep.'</td>
				              	      	<td>'.$art.'</td>
					                    	<td align="center">'.$CantArtEnt.'</td>';
								if ($costos=="on")
								{
		       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
									$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
									$i++;
								}
								else
								{
									$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Cantidad'=>$CantArtEnt);
									$i++;
								}
								echo '</tr>';
								if ($CantArtEnt>0)
								{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
									$CantArtSolTot=$CantArtSolTot+$CantArtSol;
									$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
								}
								$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
								$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
								echo '</table><br>';
							}
						}
						else
						{
							//Con detalle resultados: Unidad - Articulo - Fecha - Cantidad solicitada -  cantidad entregada
					             	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Unidad</th>
		       	              	<th>Articulo</th>
		       	              	<th>Fecha</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

 								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}

								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'";  
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);
								if ($dep=="1" or ($dep==$unMovArticulo['DepNombre'] and $art==$unMovArticulo['StkArtDsc'] and $fch==$unMovArticulo['StkMovArtFch']))
								{
									$dep=$unMovArticulo['DepNombre'];
									$art=$unMovArticulo['StkArtDsc'];
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+($costobasico*$unSolArticulo['StkSolArtCantSol']);
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+($costobasico*($unMovArticulo['StkMovArtCant'])*$sumooresto);
								}
								else
								{
									if($CantArtEnt>0)
									{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
				       	       	      	<td>'.$dep.'</td>
			       	       		      	<td>'.$art.'</td>
			       	       		      	<td>'.$fch.'</td>
					       	             	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
		
									$dep=$unMovArticulo['DepNombre'];
									$art=$unMovArticulo['StkArtDsc'];
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									$CantArtSol="-";
									$ValCostoSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
									}
								}
							}
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$dep.'</td>
			              	      	<td>'.$art.'</td>
			              	      	<td>'.$fch.'</td>
		       		             	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
		      		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
							echo '</table><br>';
						}
					}
					else
					{
						//Todos los articulos de una unidad
						if ($detallo<>"on")
						{
							//Sin detalle resultados: Articulo - Cantidad solicitada -  cantidad entregada
					             	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Articulo</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}

								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'"; 
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);
	
								if ($art=="1" or $art==$unMovArticulo['StkArtDsc'])
								{
									$art=$unMovArticulo['StkArtDsc'];
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+($costobasico*$unSolArticulo['StkSolArtCantSol']);
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+($costobasico*($unMovArticulo['StkMovArtCant'])*$sumooresto);
								}
								else
								{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
					              	      	<td>'.$art.'</td>
						                    	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Articulo'=>$art,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Articulo'=>$art,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;

									$art=$unMovArticulo['StkArtDsc'];
									$CantArtSol="-";
									$ValCostoSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
							}
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$art.'</td>
				                    	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
	       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Articulo'=>$art,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Articulo'=>$art,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}	
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
							echo '</table><br>';
						}
						else
						{
							//Con detalle resultados: Articulo - Fecha - Cantidad solicitada -  cantidad entregada
					             	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Articulo</th>
		       	              	<th>Fecha</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}
	
								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'";  
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);

								if ($art=="1" or ($art==$unMovArticulo['StkArtDsc'] and $fch==$unMovArticulo['StkMovArtFch']))
								{
									$art=$unMovArticulo['StkArtDsc'];
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+($costobasico*$unSolArticulo['StkSolArtCantSol']);
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+($costobasico*($unMovArticulo['StkMovArtCant'])*$sumooresto);
								}
								else
								{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
					              	      	<td>'.$art.'</td>
					              	      	<td>'.$fch.'</td>
					       	             	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
									$art=$unMovArticulo['StkArtDsc'];
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									$CantArtSol="-";
									$ValCostoSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
							}
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$art.'</td>
			              	      	<td>'.$fch.'</td>
			       	             	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
	       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
							echo '</table><br>';
						}
					}
				}
				else
				{
					//Un Articulo 
					if ($uniele==0)
					{
						//Todas las unidades de un articulo
						//Todos los movimientos de salida entre las fechas indicadas del articulo filtrado
						if ($detallo<>"on")
						{
							//Sin detalle resultados: Unidad - Cantidad solicitada -  cantidad entregada
					             	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Unidad</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}
								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}

								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'";  
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);
	
								if ($dep=="1" or $dep==$unMovArticulo['DepNombre'])
								{
									$dep=$unMovArticulo['DepNombre'];
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+($costobasico*$unSolArticulo['StkSolArtCantSol']);
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+($costobasico*($unMovArticulo['StkMovArtCant'])*$sumooresto);
								}
								else
								{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
					              	      	<td>'.$dep.'</td>
					       	             	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Unidad'=>$dep,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Unidad'=>$dep,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;

									$dep=$unMovArticulo['DepNombre'];
									$CantArtSol="-";
									$ValCostoSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
							}
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$dep.'</td>
				                    	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
	       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Unidad'=>$dep,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Unidad'=>$dep,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
							echo '</table><br>';
						}
						else
						{
							//Con detalle resultados: Unidad - Fecha - Cantidad solicitada -  cantidad entregada
					             	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Unidad</th>
		       	              	<th>Fecha</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}

								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'";  
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);
	
								if ($dep=="1" or ($dep==$unMovArticulo['DepNombre'] and $fch==$unMovArticulo['StkMovArtFch']))
								{
									$dep=$unMovArticulo['DepNombre'];
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
								else
								{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
					              	      	<td>'.$dep.'</td>
					              	      	<td>'.$fch.'</td>
					       	             	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Unidad'=>$dep,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Unidad'=>$dep,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;

									$dep=$unMovArticulo['DepNombre'];
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									$ValCostoSol="-";
									$CantArtSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
							}
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$dep.'</td>
			              	      	<td>'.$fch.'</td>
				                    	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
	       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Unidad'=>$dep,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Unidad'=>$dep,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
							echo '</table><br>';
						}
					}
					else
					{
						//Un articulo, Una unidad 
						if ($detallo<>"on")
						{
							//Sin detalle resultados: Cantidad solicitada y cantidad entregada
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}
								//Acumulo cantidades
								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'"; 
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);
								if ($sumooresto>0)
								{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
									$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
									$ValCostoSol=$ValCostoSol+$costobasico*$unSolArticulo['StkSolArtCantSol'];
								}
								$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
								$ValCostoEnt=$ValCostoEnt+$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
							}
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
						}
						else
						{
							//Con detalle resultados: fechas - Cantidad solicitada - cantidad entregada
					       	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Fecha</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}

								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'";  
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);
	
								if ($fch=="1" or $fch==$unMovArticulo['StkMovArtFch'])
								{
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
								else
								{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
					              	      	<td>'.$fch.'</td>
						                    	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;

									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									$CantArtSol="-";
									$ValCostoSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
							}
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$fch.'</td>
				                    	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
		      		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';

							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}	
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
							echo '</table><br>';
						}
					}
			//	}

				if ($artid<>"0")
				{
					//Los totales no justifican si se trata de la consulta de todos los articulos, solo interesa en caso de un articulo en particular.
				       ?>
					<br>
					<font size="6" color="#000033">Total Entregado: <?php echo $CantArtEntTot;?></font>
					<br>
					<?php
				}
			}


			}

		}
		else
		{
			if ($artid==0)
			{//Todos los articulos;
				if ($uniele==0)
				{//Todos los articulos y unidades
					if ($artbus=="")
					{//Todos los articulos, unidades y sin filtro de nombre de articulo
					$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtClsId=c.StkArtClsId order by d.DepNombre, a.StkArtDsc, m.StkMovArtFch"; 
					}
					else
					{//filtro texto like 
					$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and a.StkArtClsId=c.StkArtClsId order by d.DepNombre, a.StkArtDsc, m.StkMovArtFch"; 
					}
				}
				else
				{
					if ($artbus=="")
					{//Todos los articulos de una unidad
					$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=$uniele and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtClsId=c.StkArtClsId order by a.StkArtDsc, m.StkMovArtFch"; 
					}
					else
					{//filtro texto like 
					$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=$uniele and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and a.StkArtClsId=c.StkArtClsId order by a.StkArtDsc, m.StkMovArtFch"; 
					}
				}
			}
			else
			{
				if ($uniele==0)
				{
					if ($artbus=="")
					{
					//Todas las unidades de un articulo
					//Todos los movimientos de salida entre las fechas indicadas del articulo filtrado
					$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and m.StkArtId=a.StkArtid and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' order by d.DepNombre, m.StkMovArtFch"; 
					}
					else
					{//filtro texto like 
					$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and m.StkArtId=a.StkArtid and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' order by d.DepNombre, m.StkMovArtFch"; 
					}
				}
				else
				{
					if ($artbus=="")
					{
					//Un articulo una unidad
					$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and s.StkSolSecId=$uniele and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' order by m.StkMovArtFch"; 
					}
					else
					{//filtro texto like 
					$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and s.StkSolSecId=$uniele and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' order by m.StkMovArtFch"; 
					}
				}
			}
			$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
		       $colorlinea='#F3F3F3';
		       if (mysqli_num_rows($resultado)==0)
			{
		            	echo '<br><center><label>No existen movimientos seg&uacute;n el Filtro especificado</label></center><br>';
			}
			else
			{
				//Armado de grilla segun datos especificados en el consulta.
				$CantArtSol="0";
				$CantArtEnt="0";
				$CantArtSolTot="0";
				$CantArtEntTot="0";

				$ValCostoSol="0";
				$ValCostoEnt="0";
				$ValCostoSolTot="0";
				$ValCostoEntTot="0";
				$ValTotal="0";

				$dep="1";  //para reconocer primer registro a acumular
				$fch="1";
				$art="1";
		
				if ($artid==0)
				{//Todos los articulos
					if ($uniele==0)
					{//Todos los articulos y las unidades
						//Todos los movimientos de salida entre las fechas indicadas, ordenada por unidad articulos o por articulos unidad verrrrrrrrrrrrrr 
						if ($detallo<>"on")
						{
							//Sin detalle resultados: Unidad - Articulo - Cantidad solicitada -  cantidad entregada
					             	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Unidad</th>
		       	              	<th>Articulo</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}
	
								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'"; 
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);

								if ($dep=="1" or ($dep==$unMovArticulo['DepNombre'] and $art==$unMovArticulo['StkArtDsc']))
								{
									$dep=$unMovArticulo['DepNombre'];
									$art=$unMovArticulo['StkArtDsc'];
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+($costobasico*$unSolArticulo['StkSolArtCantSol']);
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+($costobasico*($unMovArticulo['StkMovArtCant'])*$sumooresto);
								}
								else
								{
if($CantArtEnt>0)
{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
				       	       	      	<td>'.$dep.'</td>
				              		      	<td>'.$art.'</td>
						                    	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
								
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
}
	
									$art=$unMovArticulo['StkArtDsc'];
									$dep=$unMovArticulo['DepNombre'];
									$CantArtSol="-";
									$ValCostoSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
							}
if($CantArtEnt>0)
{
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$dep.'</td>
			              	      	<td>'.$art.'</td>
				                    	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
	       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
}
							echo '</table><br>';
						}
						else
						{
							//Con detalle resultados: Unidad - Articulo - Fecha - Cantidad solicitada -  cantidad entregada
					             	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Unidad</th>
		       	              	<th>Articulo</th>
		       	              	<th>Fecha</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

 								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}

								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'";  
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);
								if ($dep=="1" or ($dep==$unMovArticulo['DepNombre'] and $art==$unMovArticulo['StkArtDsc'] and $fch==$unMovArticulo['StkMovArtFch']))
								{
									$dep=$unMovArticulo['DepNombre'];
									$art=$unMovArticulo['StkArtDsc'];
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+($costobasico*$unSolArticulo['StkSolArtCantSol']);
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+($costobasico*($unMovArticulo['StkMovArtCant'])*$sumooresto);
								}
								else
								{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
				       	       	      	<td>'.$dep.'</td>
			       	       		      	<td>'.$art.'</td>
			       	       		      	<td>'.$fch.'</td>
					       	             	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
		
									$dep=$unMovArticulo['DepNombre'];
									$art=$unMovArticulo['StkArtDsc'];
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									$CantArtSol="-";
									$ValCostoSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
							}
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$dep.'</td>
			              	      	<td>'.$art.'</td>
			              	      	<td>'.$fch.'</td>
		       		             	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
		      		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Unidad'=>$dep,'Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
							echo '</table><br>';
						}
					}
					else
					{
						//Todos los articulos de una unidad
						if ($detallo<>"on")
						{
							//Sin detalle resultados: Articulo - Cantidad solicitada -  cantidad entregada
					             	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Articulo</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}

								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'"; 
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);
	
								if ($art=="1" or $art==$unMovArticulo['StkArtDsc'])
								{
									$art=$unMovArticulo['StkArtDsc'];
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+($costobasico*$unSolArticulo['StkSolArtCantSol']);
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+($costobasico*($unMovArticulo['StkMovArtCant'])*$sumooresto);
								}
								else
								{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
					              	      	<td>'.$art.'</td>
						                    	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Articulo'=>$art,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Articulo'=>$art,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;

									$art=$unMovArticulo['StkArtDsc'];
									$CantArtSol="-";
									$ValCostoSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
							}
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$art.'</td>
				                    	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
	       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Articulo'=>$art,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Articulo'=>$art,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}	
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
							echo '</table><br>';
						}
						else
						{
							//Con detalle resultados: Articulo - Fecha - Cantidad solicitada -  cantidad entregada
					             	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Articulo</th>
		       	              	<th>Fecha</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}
	
								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'";  
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);

								if ($art=="1" or ($art==$unMovArticulo['StkArtDsc'] and $fch==$unMovArticulo['StkMovArtFch']))
								{
									$art=$unMovArticulo['StkArtDsc'];
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+($costobasico*$unSolArticulo['StkSolArtCantSol']);
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+($costobasico*($unMovArticulo['StkMovArtCant'])*$sumooresto);
								}
								else
								{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
					              	      	<td>'.$art.'</td>
					              	      	<td>'.$fch.'</td>
					       	             	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
									$art=$unMovArticulo['StkArtDsc'];
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									$CantArtSol="-";
									$ValCostoSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
							}
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$art.'</td>
			              	      	<td>'.$fch.'</td>
			       	             	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
	       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Articulo'=>$art,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
							echo '</table><br>';
						}
					}
				}
				else
				{
					//Un Articulo 
					if ($uniele==0)
					{
						//Todas las unidades de un articulo
						//Todos los movimientos de salida entre las fechas indicadas del articulo filtrado
						if ($detallo<>"on")
						{
							//Sin detalle resultados: Unidad - Cantidad solicitada -  cantidad entregada
					             	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Unidad</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}
								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}

								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'";  
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);
	
								if ($dep=="1" or $dep==$unMovArticulo['DepNombre'])
								{
									$dep=$unMovArticulo['DepNombre'];
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+($costobasico*$unSolArticulo['StkSolArtCantSol']);
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+($costobasico*($unMovArticulo['StkMovArtCant'])*$sumooresto);
								}
								else
								{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
					              	      	<td>'.$dep.'</td>
					       	             	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Unidad'=>$dep,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Unidad'=>$dep,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;

									$dep=$unMovArticulo['DepNombre'];
									$CantArtSol="-";
									$ValCostoSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
							}
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$dep.'</td>
				                    	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
	       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Unidad'=>$dep,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Unidad'=>$dep,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
							echo '</table><br>';
						}
						else
						{
							//Con detalle resultados: Unidad - Fecha - Cantidad solicitada -  cantidad entregada
					             	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Unidad</th>
		       	              	<th>Fecha</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}

								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'";  
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);
	
								if ($dep=="1" or ($dep==$unMovArticulo['DepNombre'] and $fch==$unMovArticulo['StkMovArtFch']))
								{
									$dep=$unMovArticulo['DepNombre'];
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
								else
								{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
					              	      	<td>'.$dep.'</td>
					              	      	<td>'.$fch.'</td>
					       	             	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Unidad'=>$dep,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Unidad'=>$dep,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;

									$dep=$unMovArticulo['DepNombre'];
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									$ValCostoSol="-";
									$CantArtSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
							}
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$dep.'</td>
			              	      	<td>'.$fch.'</td>
				                    	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
	       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Unidad'=>$dep,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Unidad'=>$dep,'Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
							echo '</table><br>';
						}
					}
					else
					{
						//Un articulo, Una unidad 
						if ($detallo<>"on")
						{
							//Sin detalle resultados: Cantidad solicitada y cantidad entregada
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}
								//Acumulo cantidades
								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'"; 
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);
								if ($sumooresto>0)
								{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
									$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
									$ValCostoSol=$ValCostoSol+$costobasico*$unSolArticulo['StkSolArtCantSol'];
								}
								$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
								$ValCostoEnt=$ValCostoEnt+$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
							}
							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
						}
						else
						{
							//Con detalle resultados: fechas - Cantidad solicitada - cantidad entregada
					       	echo '<br><table class="inventario">
							<tr bgcolor="#MM0077">
		       	              	<th>Fecha</th>
		                     		<th>Cantidad</th>';
							if ($costos=="on")
							{
			                     		echo '<th>Gasto</th>';
							}
							echo '</tr>';
							while($unMovArticulo=mysqli_fetch_assoc($resultado))
							{
								$consultaI="Select *  from StkMovArticulos where StkArtId='".$unMovArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
								$resultadoI=mysqli_query($cn,$consultaI);
							       if (mysqli_num_rows($resultadoI)==0)
								{//No hay factura tomo el dato de StkArticulos Costo Basico
									$costobasico=$unMovArticulo['StkArtCostoBasico'];
								}
								else
								{//Tomo precio de unidad, en ultima factura registrada
									$elPrecio=mysqli_fetch_assoc($resultadoI);
									$costobasico=$elPrecio['StkMovArtPrecio']/$elPrecio['StkMovArtCant'];
								}

								if ($unMovArticulo['StkMovArtTpo']=="S")
								{
									 $sumooresto=1;
								}
								else
								{
									 $sumooresto=-1;
								}

								$consultaI="Select * from StkSolArticulos where StkSolId='".$unMovArticulo['StkSolId']."' and StkArtId='".$unMovArticulo['StkArtId']."'";  
								$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
								$unSolArticulo=mysqli_fetch_assoc($resultadoI);
	
								if ($fch=="1" or $fch==$unMovArticulo['StkMovArtFch'])
								{
									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$CantArtSol+$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$ValCostoSol+$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=$CantArtEnt+($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$ValCostoEnt+$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
								else
								{
									echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
					              	      	<td>'.$fch.'</td>
						                    	<td align="center">'.$CantArtEnt.'</td>';
									if ($costos=="on")
									{
			       		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
										$data[$i]=array('Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
										$i++;
									}
									else
									{
										$data[$i]=array('Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
										$i++;
									}
									echo '</tr>';
									if ($CantArtEnt>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSolTot=$CantArtSolTot+$CantArtSol;
										$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
									}
									$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
									$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;

									$fch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
									$CantArtSol="-";
									$ValCostoSol="-";
									if ($sumooresto>0)
									{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
										$CantArtSol=$unSolArticulo['StkSolArtCantSol'];
										$ValCostoSol=$costobasico*$unSolArticulo['StkSolArtCantSol'];
									}
									$CantArtEnt=($unMovArticulo['StkMovArtCant']*$sumooresto);
									$ValCostoEnt=$costobasico*($unMovArticulo['StkMovArtCant']*$sumooresto);
								}
							}
							echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
			              	      	<td>'.$fch.'</td>
				                    	<td align="center">'.$CantArtEnt.'</td>';
							if ($costos=="on")
							{
		      		              		echo '<td align="center">'.$ValCostoEnt.'</td>';
								$data[$i]=array('Fecha'=>$fch,'Cantidad'=>$CantArtEnt,'Gasto'=>$ValCostoEnt);
								$i++;
							}
							else
							{
								$data[$i]=array('Fecha'=>$fch,'Cantidad'=>$CantArtEnt);
								$i++;
							}
							echo '</tr>';

							if ($CantArtEnt>0)
							{//es el caso de una devolución no tengo que considerar la cantidad solicitada, estaria duplicando
								$CantArtSolTot=$CantArtSolTot+$CantArtSol;
								$ValCostoSolTot=$ValCostoSolTot+$ValCostoSol;
							}	
							$CantArtEntTot=$CantArtEntTot+$CantArtEnt;
							$ValCostoEntTot=$ValCostoEntTot+$ValCostoEnt;
							echo '</table><br>';
						}
					}
				}

				if ($artid<>"0")
				{
					//Los totales no justifican si se trata de la consulta de todos los articulos, solo interesa en caso de un articulo en particular.
				       ?>
					<br>
					<font size="6" color="#000033">Total Entregado: <?php echo $CantArtEntTot;?></font>
					<br>
					<?php
				}
			}

		}


if($costos=="on")
{
             ?>

<table class="inventario">
	<?php
	$precio="0";
	$tipomov="S";
	if ($artid==0)
	{//Todos los articulos
		if ($uniele==0)
		{//Todos los articulos y unidades
			if ($areaele>0)
			{
				if ($artbus=="")
				{//Todos los articulos, unidades y sin filtro de nombre de articulo
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' and a.StkArtClsId=c.StkArtClsId and (d.DepIdDep='".$areaele."' or d.DepId='".$areaele."') order by a.StkArtDsc"; 
				}
				else
				{//filtro texto like 
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' and a.StkArtClsId=c.StkArtClsId and (d.DepIdDep='".$areaele."' or d.DepId='".$areaele."') order by a.StkArtDsc"; 
				}
			}
			else
			{
				if ($artbus=="")
				{//Todos los articulos, unidades y sin filtro de nombre de articulo
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."'  and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' and a.StkArtClsId=c.StkArtClsId order by a.StkArtDsc"; 
				}
				else
				{//filtro texto like 
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' and a.StkArtClsId=c.StkArtClsId order by a.StkArtDsc"; 
				}
			}
		}
		else
		{//Todos los articulos de una unidad
			if ($areaele>0)
			{
				if ($artbus=="")
				{//Todos los articulos de una unidad
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=$uniele and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' and a.StkArtClsId=c.StkArtClsId and (d.DepIdDep='".$areaele."' or d.DepId='".$areaele."') order by a.StkArtDsc"; 
				}
				else
				{//filtro texto like 
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=$uniele and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' and a.StkArtClsId=c.StkArtClsId and (d.DepIdDep='".$areaele."' or d.DepId='".$areaele."') order by a.StkArtDsc"; 
				}
			}
			else
			{
				if ($artbus=="")
				{//Todos los articulos de una unidad
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=$uniele and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."'  and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."'and a.StkArtClsId=c.StkArtClsId order by a.StkArtDsc"; 
				}
				else
				{//filtro texto like 
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, StkArtCls as c, Departamentos as d where s.StkSolId=m.StkSolId and a.StkArtId=m.StkArtId and s.StkSolSecId=$uniele and s.StkSolSecId=d.DepId and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' and a.StkArtClsId=c.StkArtClsId order by a.StkArtDsc"; 
				}
			}
		}
	}
	else
	{
		if ($uniele==0)
		{
			if ($areaele>0)
			{
				if ($artbus=="")
				{
				//Todas las unidades de un articulo
				//Todos los movimientos de salida entre las fechas indicadas del articulo filtrado
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and m.StkArtId=a.StkArtid and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' and (d.DepIdDep='".$areaele."' or d.DepId='".$areaele."') order by a.StkArtDsc"; 
				}
				else
				{//filtro texto like 
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and m.StkArtId=a.StkArtid and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' and (d.DepIdDep='".$areaele."' or d.DepId='".$areaele."') order by a.StkArtDsc"; 
				}
			}
			else
			{
				if ($artbus=="")
				{
				//Todas las unidades de un articulo
				//Todos los movimientos de salida entre las fechas indicadas del articulo filtrado
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and m.StkArtId=a.StkArtid and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' order by a.StkArtDsc"; 
				}
				else
				{//filtro texto like 
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, StkArticulos as a, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and m.StkArtId=a.StkArtid and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' order by a.StkArtDsc"; 
				}
			}
		}
		else
		{
			if ($areaele>0)
			{
				if ($artbus=="")
				{
				//Un articulo una unidad
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and s.StkSolSecId=$uniele and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' and (d.DepIdDep='".$areaele."' or d.DepId='".$areaele."') order by a.StkArtDsc"; 
				}
				else
				{//filtro texto like 
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and s.StkSolSecId=$uniele and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' and (d.DepIdDep='".$areaele."' or d.DepId='".$areaele."') order by a.StkArtDsc"; 
				}
			}
			else
			{
				if ($artbus=="")
				{
				//Un articulo una unidad
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and s.StkSolSecId=$uniele and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."'  and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' order by a.StkArtDsc"; 
				}
				else
				{//filtro texto like 
				$consulta="Select * from StkMovArticulos as m, StkSolicitudes as s, Departamentos as d where s.StkSolId=m.StkSolId and s.StkSolSecId=d.DepId and s.StkSolSecId=$uniele and m.StkArtId=$artid and m.StkMovArtFch>='".cambiaf_a_mysql($fchdesde)."' and m.StkMovArtFch<='".cambiaf_a_mysql($fchhasta)."' and a.StkArtDsc LIKE '".$elLike."' and m.StkMovArtTpo='".$tipomov."' and a.StkArtCostoBasico='".$precio."' order by a.StkArtDsc"; 
				}
			}
		}
	}
	$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
	if (mysqli_affected_rows($cn)==0)
	{
		//no hay articulos sin precio
	}
	else
	{
		$art=1;
		while($unArticulo=mysqli_fetch_assoc($resultado))
		{
			$consultaI="Select *  from StkMovArticulos where StkArtId='".$unArticulo['StkArtId']."' and StkMovArtPrecio>0 order by StkMovArtFch desc"; 
			$resultadoI=mysqli_query($cn,$consultaI);
	       	if (mysqli_num_rows($resultadoI)==0)
			{//Ademas de no contar con costo basico, tampoco hay factura con ultimo valor
				if ($art=="1")
				{
				      	echo '<br><table class="inventario">
					<tr bgcolor="#MM0077">
				      	<th>Articulo</th>
					</tr>';
				?>
				<hr style="color: rgb(69, 106, 221);">
				<font size="4" color="#000066">Art&iacute;culos sin Factura y sin Precio B&aacute;sico,  afectando los resultados del Gasto</font><br>
				<?php
				}

				if ($art=="1" or $art<>$unArticulo['StkArtDsc'])
				{
					$art=$unArticulo['StkArtDsc'];
					echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
				    	<td>'.$art.'</td>
					</tr>';
				}

			}
		}
	}
	?>
</table>
<?php
} //si muestro listas de articulos sin valor en caso que incluya costos en la consulta
//Cargo el array de los valores obtenidos a la session, por si decide imprimir el pdf, en pdfconsumocantidades.php, muestra este array ya cargado
$_SESSION['titulo']=$datatit;
$_SESSION['consumo']=$data;


?>
</table>
</td>
</tr>
</table>
</center>
<?php
require_once("pie.php");
?>