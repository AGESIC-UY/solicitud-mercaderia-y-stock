<?php
$estado="Autorizar";
$unidad=0; //$_COOKIE['usuunidad'];  //para que cuando click en "consulta" traiga por default la consulta de todas las unidades en el caso de jefe de area
$clase="99";
$articulo="99";
$dia = date('d');
$mes = date('m');
$year = date('y')+2000;
$fchhasta=date("d/m/Y", mktime(0, 0, 0, $mes, $dia, $year));
$fchdesde=date("d/m/Y", mktime(0, 0, 0, $mes, 1, $year));
$detallo='off';
$uniele=$_COOKIE['usuunidad'];
$artid=0;


if(!isset($_COOKIE['usuario']))
	header("Location: login.php");
	function mostrarFecha()
	{
	    $dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
	    $mes= array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
	    return "Hoy es ".$dias[date('w')].' '.date(d).' de '.$mes[date("n")-1].' de '.date("Y") ;
	}
	require_once("funcionesbd.php");
	require_once("Includes/conviertefecha.php");
	$hoy=Date("d/m/Y",strtotime("-0 weeks"));
	$maniana=Date("d/m/Y",strtotime("+1 day"));
	$hora=date("H:i");

	$consulta="Select * from Sistemas";
	$resultado=mysqli_query($cn,$consulta) or die('La consulta del area fall&oacute;: ' .mysqli_error());
	$sis=mysqli_fetch_assoc($resultado);
	$logo=$sis['SisLogo'];

?>

<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<title>Stock de Mercader&iacute;a</title> 
	<style> a {text-decoration: none; color: #000000; } a:hover { color: #555555; }</style>
	<link rel="stylesheet" type="text/css" href="Estilos/color.css" title="default">
	<link rel="shortcut icon" href="Images/pc.png">
	<Script language='Javascript'>
	</Script>
	<Style type='text/css'>
	</Style>
</head>

<body style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
<table style="height: 100%;" border="0" cellpadding="0" cellspacing="0" width="100%">
<!--	DWLayoutTable	--><tbody>

<tr style="height: 8%;" valign="top">
<td width="15%" align="center">
<?php
echo '<img src="'.$logo.'" alt="OPP" height="120" width="220" border=0><br>';
?>
<table align="center">
    <tr>
	<td align="center">
	<form action="buscar.php" method="post" name="miform">
	       <input type="text" name="findsol" size="24" maxlength="50" >
       	<input type="submit" name='boton' value='Buscar Solicitud' onMouseOver='change(this,"btnFocus")' onMouseOut='change(this,"normBtn")' id='normBtn'/>
	</form>
	</td>
    </tr>
    <tr>
    <td align="center">
	<br><a href="index.php?estado=<?php echo $estado;?>&unidad=<?php echo $uniele;?>"><br><font style="font-family: 'Arial', Times, serif;font-size: 16px; font-weight: bold; color: #000066">Solicitudes</font></a>
    </td>
    </tr>
    <tr>
    <tr>
    <td align="center">
	<br><a href="cnsuniart.php?fchdesde=<?php echo $fchdesde;?>&fchhasta=<?php echo $fchhasta;?>&detallo=<?php echo $detallo;?>&uniele=<?php echo $unidad;?>&artid=<?php echo $artid;?>"><br><font style="font-family: 'Arial', Times, serif;font-size: 16px;font-weight: bold; color: #000066">Consultas</font></a>
    </td>
    </tr>
    <tr>
	<?php
	//Si el Autorizador cuenta al menos con 1 registro en StkArtClsUsu, es un autorizador con derechos a ver la n�mina de articulos de las clases all� registradas
	$consulta="Select * from StkArtClsUsu where UsuId='".$_COOKIE['usuid']."'";
	$resultado=mysqli_query($cn,$consulta);
       if (mysqli_num_rows($resultado)==0)
	{
	}
	else
	{
		$unaClase=mysqli_fetch_assoc($resultado);
		$clase=$unaClase['StkArtClsId'];
	?>
		<td align="center"><br><a href="articulos.php?clase=<?php echo $clase;?>&articulo=<?php echo $articulo;?>&fchdesde=<?php echo $fchdesde;?>&fchhasta=<?php echo $fchhasta;?>">
		<br><font style="font-family: 'Arial', Times, serif;font-size: 16px;font-weight: bold; color: #000066">Art&iacute;culos</font></a><br>
		</td>
	<?php
	}
	?>
    </tr>
    <tr>
    <td align="center">
	<br><a href="nuevopass.php"><br><font style="font-family: 'Arial', Times, serif;font-size: 16px;font-weight: bold; color: #000066">Contrase&ntilde;a</font></a>
    </td>
    </tr>
    <tr>
    <td align="center">
        <br align="center"><a href="docs/Manual_Autorizador.pdf" target="_blank" >Descargar ->> Manual</a>
    </td>
    </tr>
    <tr>
    <td align="center">
	<br>
	<br>
	<a href="login.php"><img width="30" height="30" src="Images/icono_salir.jpg" border=0></a>
    </td>
    </tr>
</table>
</td>

<td >
<table align="left" width="100%">
	<tr>
	<td align="right"  style="color:white;font-size:13pt">
	<?php
	require_once("menu.php");
	require_once("filtroII.php");
	?>
	</td>
	</tr>
</table>
<br>