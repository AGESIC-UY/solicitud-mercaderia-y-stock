<!--
Creación	Alicia Acevedo
Fecha:		04/2010
Algunas Características:
	1.- En este objeto y de acuerdo a la cookie grabadas en el login, se establece cual es el menu que muestra. Sería correcto haber configurado un menú dinámico, pero 
		no tenia el suficiente conocimiento de la herramienta para realizarlo. Ver mas adelante si es posible modificarlo. Por ahora funciona. Los menues estan en los
		objetos "principioxxx.php" donde xxx indica el perfil del usuario. 

	2.- El usupermiso="S", corresponde al alcance que tiene el perfil de ver solo su unidad o todas. Observar que el administrador y el operador cuentan con el privilegio
		de acceder a todas las unidades, no asi el solicitante. 

-->

<?php
if (isset($_COOKIE['usuperfil'])){
$usupfl=$_COOKIE['usuperfil'];
} else {
$usupfl = "";
}


//************************************************************************************************************************
	if ($usupfl=="1" or $usupfl =="7") //Solicitante, Consultor - Unidad
	{
	require_once("principiosolicitante.php");
	}
//************************************************************************************************************************
	if ($usupfl =="2") //Operador stock
	{
	require_once("principiooperador.php");
	}
//************************************************************************************************************************
	if ($usupfl =="3" or $usupfl =="6") //Administrador, Consultor - Financiero
	{
	require_once("principioadministrador.php");
	}
//************************************************************************************************************************
	if ($usupfl =="5") //Autorizador
	{
	require_once("principioautorizador.php");
	}
//************************************************************************************************************************
	if ($usupfl =="8") //Articulador
	{
	require_once("principioarticulador.php");
	}
//************************************************************************************************************************
	if ($usupfl =="9") //Proveedores
	{
	require_once("principioproveedores.php");
	}
?>