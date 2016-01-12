<?php
require_once("funcionesbd.php");
$usuele=$_REQUEST['usuele'];
$uniusu='99999';
//Se arma envio de los datos del usuario. 
//el reenvio de la contraseña tiene implicito su reinicio, esto cambio por un tema de seguridad no se puede reenvia la contraseña como md5, es ilegible y en realidad debera reiniciarla
$usupass= 'nueva';
$esu=1;
$sentencia="Update Usuarios set UsuPass='".$usupass."', UsuPassInicia='".$esu."' where UsuId=".$usuele;
$usuario = mysqli_query($cn, $sentencia);

$consulta="Select * from sistemas where SisId='".$esu."'";
$resultado=mysqli_query($cn,$consulta);
$elSistema=mysqli_fetch_assoc($resultado);
$subject=$elSistema['SisNom'];
	
$consulta="Select * from Usuarios where UsuId='".$usuele."'";
$resultado=mysqli_query($cn,$consulta);
$unUsuario=mysqli_fetch_assoc($resultado);

//envio de mail a usuario elegido
$email_to = $unUsuario['UsuMail'];
$email_subject = $subject;
$email_body = "Su usuario para el Sistema de " .$subject.": ".$unUsuario['UsuUsuario']. "\n";
$email_body.= "Su contraseña es la palabra: ".$unUsuario['UsuPass']. "\n";
$email_body.= "Compruebe su acceso y cualquier inconveniente comuniquesé con el Área de Planificación y Gestión Financiero Contable";
$email_from = "From: ".$_COOKIE['usumail']."\r\n";
if(mail($email_to, $email_subject, $email_body, $email_from))
{
	echo "Se ha enviado mail al usuario para comunicar sus datos($email_to) ";
}
else
{
	echo "El email para comunicar el usuario ($email_to) no se ha podido enviar";
}
echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=usuarios.php?unidad=$uniusu'>";   
?>
