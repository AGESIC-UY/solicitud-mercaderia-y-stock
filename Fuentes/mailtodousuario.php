<?php
require_once("funcionesbd.php");
echo "entre";

		$sentenciaII="Select * from Usuarios";
		$resultadoII=mysqli_query($cn,$sentenciaII);
		while($unSolicitante=mysqli_fetch_assoc($resultadoII))
		{
			echo "ahhora en el while";
				//envio de mail a usuarios comunicando usuario y contraseña
				$email_to = $unSolicitante['UsuMail'];
				$email_subject = "Usuario Sistema de Proveeduria";
				$email_body = "Su usuario es ".$unSolicitante['UsuUsuario'] y "la contraseña provisoria es 'nueva', cuando realice el ingreso al Sistema, éste le solicitará cambiar por una contraseña personal. 
					Por cualquier inconveniente comunicarse con Magdalena Escayola de adquisiciones ";
				$email_from = "From: ".$_COOKIE['usumail']."\r\n";
				if(mail($email_to, $email_subject, $email_body, $email_from))
				{
					echo "Se ha enviado mail por solicitud autorizada a ($email_to) ";
				}
				else
				{
					echo "El email aviso de solicitud autorizada a ($email_to) no se ha podido enviar";
				}
		}


?>

