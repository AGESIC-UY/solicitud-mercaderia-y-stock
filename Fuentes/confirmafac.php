<!--
Creación	Alicia Acevedo
Fecha:		04/2010
Algunas Características:
	0.- Este objeto se usa para:
		- Confirmar factura por el usuario registrador(administrador), no siendo esta la confirmación definitiva, sino el pasaje de la factura a la orbita de control por 
			el operador o administrador de inventario. 
		- Rechazar la factura por el operador(o administrador de inventario), pues se han encontrado diferencias. 

	1.- Es decir que esta confirmación de factura, aún NO ALTERA el saldo del Stock, debe ser cotejada por el usuario operador(o administrador de inventario) quien recibe el material.
		Existirá otra instancia donde los movimientos son ingresados por el operador(o administrador de inventario), siendo esto en la segunda confirmación(objeto ingresoartfac.php)
		considerando que la factura se confirmo como OK.
		El atributo "StkPrvFacFin", comienza con valor "0" cuando ingresa la factura, cambia a "1" cuando obtiene su primer confirmación(administrador - quien se encarga

		de registrar la factura al sistema con su detalle de articulos y precios), cambia a "2" cuando esta es confirmada por el operador(o administrador de inventario), quien se 
		encarga que los artículos hayan sido entregados en condiciones. En este ultimo paso se SUMAN al stock las cantidades.
	2.- El rechazo de la factura lo realiza el usuario con rol Operador(o adminstrador de inventario), volviendo la factura a la orbita del usuario con rol Administrador
		para la correspondiente correccion. El usuario operador o administrador de inventario no pueden modificar la información de la factura. 
-->

<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idprv=$_REQUEST['idprv'];
$idfac=$_REQUEST['idfac'];

	$consulta="Select * from StkMovArticulos as m, StkArticulos as a where m.StkPrvFacId='".$idfac."' and m.StkArtId=a.StkartId";
	$resultado=mysqli_query($cn,$consulta);
	$losmov=mysqli_fetch_assoc($resultado);
	$laclase=$losmov['StkArtClsId'];

       if (mysqli_num_rows($resultado)==0)
	{
		echo '<br><center><label> No es posible confirmar la factura, esta no cuenta con articulos ingresados </label></center><br>';
	}
	else
	{
		$fin=1;
		if ($_COOKIE['usuperfil']==2) 
		{//Se trata de un rechazo ejecutado por "Operador""
			$fin=0;
			$Obs=$_COOKIE['usuario']." ha rechazado ingreso al stock, revisar";
			$MsjResultado="La factura a sido rechazada para ingresar al Stock";
		}
		else
		{//Confirmación de Administrador del Stock, quien registra las facturas de proveedores
			$Obs="En espera para ser ingresada al Stock";
			$MsjResultado="La factura ha sido confirmada, queda a la espera del ingreso al Stock ";
		}
		$sentencia="Update StkPrvFacturas set StkPrvFacFin='".$fin."', StkPrvFacObs='".$Obs."', StkPrvFacUsuMod='".$_COOKIE['usuid']."',StkPrvFacFchMod='".date("Y-m-d H:i")."' where StkPrvFacId='".$idfac."'";		
		$update=mysqli_query($cn,$sentencia);

		echo '<br><center><label>'.$MsjResultado.'</label></center><br>';
		if ($_COOKIE['usuperfil']==2)
		{//Con este rol esta rechazando la factura enviando mail a Administrador responsable de su ingreso
	
			$consulta="Select * from SisPflUsuarios as p, Usuarios as u where p.SisId=1 and p.SisPflId=3 and u.UsuId=p.UsuId";
			$resultado=mysqli_query($cn,$consulta);
			while($destinatario=mysqli_fetch_assoc($resultado))
			{
				if($destinatario['UsuFchFin']==NULL)
				{
					$email_to = $destinatario['UsuMail'];
					$email_subject = "Ingreso Factura al Stock rechazada - ".$_COOKIE['usuario'];
					$email_body = "Se ha rechazado factura para ingresar al stock por diferencias, ingrese a la aplicación de Stock y rectifique la Factura";
					$email_from = "From: ".$_COOKIE['usumail']."\r\n";
					if(mail($email_to, $email_subject, $email_body, $email_from))
					{
						echo "Se ha enviado mail por factura rechazada a ($email_to) ";
					}
					else
					{
						echo "El email por factura rechazada a ($email_to) no se ha podido enviar";
					}
				}
			}
			echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=proveedoresfacstk.php'>";
		}
		else
		{//Corresponde al "Administrador" quien registra las facturas, envia mail a Operador. 
			if ($_COOKIE['usuperfil']<>6 and $_COOKIE['usuperfil']<>7)
			{//evito envio mail a 6 consultor financiero, 7 consultor unidad
				//Usuarios de la clase
				$consulta="Select * from Usuarios as u, StkArtClsUsu as c, SisPflUsuarios as p where c.StkArtClsId=$laclase and c.UsuId=u.UsuId and p.UsuId=u.UsuId";
				$resultado=mysqli_query($cn,$consulta);

				$consultaI="Select * from SisPflUsuarios as p, Usuarios as u where p.SisId=1 and p.SisPflId=2 and u.UsuId=p.UsuId";
				$resultadoI=mysqli_query($cn,$consultaI);
				while($destinatario=mysqli_fetch_assoc($resultadoI))
				{
					if($destinatario['UsuFchFin']==NULL)
					{
						$email_to = $destinatario['UsuMail'];
						$email_subject = "Hay Facturas para ingreso de Stock - ".$_COOKIE['usuario'];
						$email_body = "Hay facturas ingresadas para verificar su ingreso al Stock, ingrese a la aplicación de Stock y rectifique los articulos y cantidades";
						$email_from = "From: ".$_COOKIE['usumail']."\r\n";
						if(mail($email_to, $email_subject, $email_body, $email_from))
						{
							echo "Se ha enviado mail por factura a ingresar al ($email_to) ";
						}
						else
						{
							echo "El email por factura a ingresar al ($email_to) no se ha podido enviar";
						}
					}	
				}
				echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=proveedoresfacver.php?idprv=$idprv;'>";
			}
		}

	}
?>
