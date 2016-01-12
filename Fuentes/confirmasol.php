<!--
Algunas características:
	1.- Este objeto es called desde el rol solicitante y desde el rol autorizador. Ambos called lo que hacen es cambiar el estado de la solicitud de "Construyendo" para ser 
		autorizada y desde "Autorizar" al estado de "Pendiente" para continuar con la adjudicación del pedido. 
	2.- Como existirá el rol compuesto que conjuga los permisos de solicitante y autorizador, si el usuario que esta realizando la solicitud tambien debe autorizar. Le evitaría 
		el procedimiento de pasar por las dos etapas. Es decir cuando el usuario con este rol confirma la solicitud que está construyendo, automaticamente pasaría a "pendiente"
		salteando el procedimiento de autorización. Quedando automaticamente autorizado.
	3.- Si la solicitud fue alterada por el autorizador, se comunica tal situación al solicitante. Esto se detecta con el atributo StkSolCambio que será update en el procedimiento de 
		autorización. 
-->

<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$solid=$_REQUEST['solid'];
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];

	$sentenciaI="Select * from StkSolArticulos where StkSolId='".$solid."'";
	$resultadoI=mysqli_query($cn,$sentenciaI);
       if (mysqli_num_rows($resultadoI)==0)
	{
		//Es posible que el autorizador elimine los articulos, ya que puede trabajar con la solicitud, por esta razón realizo el siguiente control, ya que cuenta
		//con la posibilidad de eliminar todos los articulos de la solicitud ocasionando este posible problema.
            	echo '<br><center><label>No es posible confirmar, la Solicitud no contiene articulos ingresados</label></center><br>';
	}
	else
	{

		$consulta="select * from StkSolicitudes as s, Usuarios as u where s.StkSolUsuCre=u.usuId and s.StkSolId='".$solid."'";
		$resultado = mysqli_query($cn,$consulta);
		$elUsuario=mysqli_fetch_array($resultado);
		$usunomcompleto=$elUsuario['UsuNombre'].' '.$elUsuario['UsuApellido'];

		if ($estadocall=="Construyendo")
		{
		       if ($_COOKIE['usuperfil']==5)
			{	//Este corresponde al perfil autorizador, pasa directo a pendiente de entrega y NO habría envio de mail no corresponde ya que el mismo 
				//autorizador esta construyendo la solicitud, no es neceario el envio del mail a si mismo, por lo que automaticamente queda autorizada.
				$estado="Pendiente de Entrega";
				$Autoriza=$_COOKIE['usuid'];
				$sentencia="Update StkSolicitudes set StkSolEstado='".$estado."',StkSolUsuMod='".$_COOKIE['usuid']."',StkSolFchMod='".date("Y-m-d H:i")."', StkSolUsuAut=$Autoriza, StkSolFchAut='".date("Y-m-d H:i")."' where StkSolId=".$solid;
				if (!mysqli_query($cn,$sentencia))
				{
					die('Error al confirmar pedido de Solicitud'.mysqli_error());
				}
				else
				{
					echo '<br><center><label>Su pedido a sido confirmado se encuentra ahora en el estado Pendiente de Entrega</label></center><br>';
				}
			}
			else
			{
				//Este corresponde al perfil solicitante, y esta pasando solicitud para ser autorizada antes de que cambie a su estado pendiente para entrega, envia mail a 
				//usuarios de la unidad con rol Autorizador		
				$estado="Autorizar";
				$sentencia="Update StkSolicitudes set StkSolEstado='".$estado."',StkSolUsuMod='".$_COOKIE['usuid']."',StkSolFchMod='".date("Y-m-d H:i")."' where StkSolId=".$solid;

				if (!mysqli_query($cn,$sentencia))
				{
					die('Error al confirmar pedido de Solicitud'.mysqli_error());
				}
				else
				{
					echo '<br><center><label>Su pedido a sido confirmado para Autorizar</label></center><br>';
					//La solicitud esta ahora en estado "Autorizar", envio mail a interesados de autorizar
					$sentenciaII="Select * from SisPflUsuarios as p, Usuarios as u where p.SisId=1 and p.SisPflId=5 and u.SeccionesId='".$unidadcall."' and u.UsuId=p.UsuId and isnull(u.UsuFchFin)";
					$resultadoII=mysqli_query($cn,$sentenciaII);
					while($unAutorizador=mysqli_fetch_assoc($resultadoII))
					{
						//envio de mail a usuarios con rol autorizador
						//busco autorizadores de la unidad y envio mail a todos ellos informando de la existencia de solicitudes de pedido para autorizar
						$email_to = $unAutorizador['UsuMail'];
						$email_subject = "Solicitud de Stock para autorizar - ".$_COOKIE['usuario'];
						$email_body = "Hay una nueva Solicitud al Stock para Autorizar,";
						$email_body .= " Solicitada por: ".$usunomcompleto."\n";
						$email_body .= " Acceda a la Aplicación desde la intranet,";
						$email_body .= " ó a través del siguiente link";
						$email_body .= " http://intranet/aplicaciones/StkProveedores/login.php";

						$email_from = "From: ".$_COOKIE['usumail']."\r\n";
						if(mail($email_to, $email_subject, $email_body, $email_from))
						{
							echo "Se ha enviado mail para autorizar a ($email_to) ";
						}
						else
						{
							echo "El email para autorizar la solicitud a ($email_to) no se ha podido enviar";
						}
					}
				}
			}

		}
		else
		{	//Este corresponde al perfil autorizador, y esta confirmando la solicitud(solicitudes creadas por otro usuario de la unidad no las que podría
			//haber creado el), cambiandola al estado pendiente, donde se adjudicará el material y envia mail a solicitantes de la unidad para que tomen 
			//en cuenta que la solicitud esta esperando por prveedurida
			$estado="Pendiente de Entrega";
			$Autoriza=$_COOKIE['usuid'];
			$sentencia="Update StkSolicitudes set StkSolEstado='".$estado."',StkSolUsuMod='".$_COOKIE['usuid']."',StkSolFchMod='".date("Y-m-d H:i")."', StkSolUsuAut=$Autoriza, StkSolFchAut='".date("Y-m-d H:i")."' where StkSolId=".$solid;

			if (!mysqli_query($cn,$sentencia))
			{
				die('Error al confirmar pedido de Solicitud'.mysqli_error());
			}
			else
			{
				echo '<br><center><label>Su pedido a sido confirmado se encuentra ahora en el estado Pendiente de Entrega</label></center><br>';

				$sentenciaII="Select * from SisPflUsuarios as p, Usuarios as u where p.SisId=1 and p.SisPflId=1 and u.SeccionesId='".$unidadcall."' and u.UsuId=p.UsuId and isnull(u.UsuFchFin)";
				$resultadoII=mysqli_query($cn,$sentenciaII);
				while($unSolicitante=mysqli_fetch_assoc($resultadoII))
				{
					//envio de mail a usuarios con rol solicitante
					//busco solicitantes de la unidad y envio mail a todos ellos informando de la existencia de solicitudes de pedido para autorizar
					//Además debo considerar si la solicitud sufrió alteraciones por parte del autorizador, se comunica tal situación.
					$sentenciaIII="Select * from StkSolicitudes where StkSolId=".$solid;
					$resultadoIII=mysqli_query($cn,$sentenciaIII);
					$laSol=mysqli_fetch_assoc($resultadoIII);
	
					$email_to = $unSolicitante['UsuMail'];
					$email_subject = "Autorizacion de Solicitud - ".$_COOKIE['usuario'];

					$email_body = "Se ha autorizado Solicitud de Stock,";
					$email_body .= " Solicitada por: ".$usunomcompleto."\n";
					if ($laSol['StkSolCambio']=='S')
					{
						$email_body .= " La misma a sido alterada por la persona que la Autorizo"."\n";
					}

					$email_body .= " Acceda a la Aplicación desde la intranet,";
					$email_body .= " ó a través del siguiente link";
					$email_body .= " http://intranet/aplicaciones/StkProveedores/login.php";

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


			}
		}
	}
	echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=index.php?estado=$estadocall&unidad=$unidadcall'>";    
?>

