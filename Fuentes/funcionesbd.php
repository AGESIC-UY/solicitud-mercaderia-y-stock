<?php

$file = fopen("configbd.conf", "r") or exit("Unable to open file!");

$servidor= trim(str_replace("serv:","", fgets($file)));
$usu=trim(str_replace("user:","", fgets($file)));
$pass=trim(str_replace("pass:","", fgets($file)));
$base=trim(str_replace("db:","", fgets($file)));
fclose($file);
$cn=mysqli_connect($servidor,$usu,$pass,$base) or die (mysqli_connect_error().": ".mysqli_connect_error());
function limpiar($texto){$txt=trim(strip_tags($texto));return $txt;}
?>