<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$esc=0;
$usuele=$_REQUEST['usuele'];
$unidad=$_REQUEST['unidad'];
$clsid=$_REQUEST['clsid'];
$hab=$_REQUEST['hab'];
$ele=$_REQUEST['ele'];
$soy=$_REQUEST['soy'];

if ($soy=="hab")
{//
$sentencia="Update StkArtClsUsu set StkArtClsHab='".$hab."' where StkArtClsId='".$clsid."' and UsuId='".$usuele."'";
}
else
{
//Funcionalidad descartada, conservo por si vuelve
//Primero elimino predeterminados anteriores del usuario
$sentencia="Update StkArtClsUsu set StkArtClsEle='".$esc."' where UsuId='".$usuele."'";
$update = mysqli_query($cn, $sentencia);
//Si "True" marco predeterminado
$sentencia="Update StkArtClsUsu set StkArtClsEle='".$ele."' where StkArtClsId='".$clsid."' and UsuId='".$usuele."'";
}
$update = mysqli_query($cn, $sentencia);
echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=updateusu.php?usuele=$usuele&unidad=$unidad'>";    
?>
