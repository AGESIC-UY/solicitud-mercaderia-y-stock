<html>
<head>
    <style> a {text-decoration: none; color: #000000; } a:hover { color: #555555; }</style>
	<title>Stock</title> 
	<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="Estilos/color.css" title="default">
	<link rel="shortcut icon" href="images/pc.png">
	<Script language='Javascript'>
	</Script>
	<Style type='text/css'>
	</Style>
</head>

<body>
<div id="principal">
<div id="titulo_horizontal">
<img src="Images/LogoOPP.jpg" width="80%" border=0 ><br>
<?php
require_once("funcionesbd.php");
$usuid=$_COOKIE['usuid'];
$usuario=$_COOKIE['usuario'];
$esu=1;
?>
<table cellpadding="7" cellspacing="7" align="left"><tr><td><font size="4" color='#552B00'>&nbsp;&nbsp;&nbsp; <?php echo utf8_encode($titin);?></font></td></tr></table>
<div class="menu_local">
</div>
</div>
</div>
</body>

<body>
<div id="principal">
<div id="titulo_horizontal">
<div class="menu_local">
<?php
$esu=1;
$consulta="Select * from menu where MnuVig='".$esu."' and MnuTpo='".Vertical."' order by MnuOrder";
$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
while($itemmenu=mysqli_fetch_assoc($resultado))
{
	$elref=$itemmenu['MnuLink'].'&var6='.$itemmenu['MnuId'];
	if($itemmenu['MnuSinParam']==1)
	{
		$elref=$itemmenu['MnuLink'];
	}
	echo '<tr><a href="'.$elref.'"><li>'.$itemmenu['MnuItem'].'</li></a></tr>';
}
?>
</div>
</div>
</div>