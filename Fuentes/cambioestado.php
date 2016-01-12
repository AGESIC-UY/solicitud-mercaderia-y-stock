<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$solid=$_REQUEST['solid'];
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];

//La impresi�n se basa en el atributo StkSolArtCantAcred con valor mayor a cero para saber que se trata del articulo al cual se le asigno cantidad
//durante �l proceso "Entrega de material o disponibilidad". Por lo que Los art�culos deben aparecer en una nueva acreditaci�n con pendiente y
//acreditaci�n en cero. Por lo tanto llevo a cero las cantidades acred y pend para que no afecten los resultados en la nueva impresi�n y/o entrega. 

//Ahora bien esta inicializaci�n solo va a suceder cuando quedaron cantidades pendientes de algun articulo de la solicitud, y deben volver a tramitar
//una entrega de material. De lo contrario estas cantidades podr�an quedar con valor. Ya que para que se realice el call a este objeto deberia hacer 
//click en el icono de disponibilidad para que se ejecute.

	//Si se trata de una reimpresi�n debe llevar al estado en el que se encontraba antes de "Imprimir Remito" osea "Finalizada" o "Pendiente"  pero este estado
	//no lo indican los articulos pendientes podr�a ser finalizada en forma forzada con articulos pen, se identifica con el parcial = 2

	$consulta="select * from StkSolicitudes as s, Usuarios as u where s.StkSolUsuCre=u.usuId and s.StkSolId='".$solid."'";
	$resultado = mysqli_query($cn,$consulta);
	$elUsuario=mysqli_fetch_array($resultado);
	$usunomcompleto=$elUsuario['UsuNombre'].' '.$elUsuario['UsuApellido'];

	if ($laSolicitud['StkSolParcial']==2)
	{
		$estadosol="Finalizada";
		$parcial=2;
	}
	else
	{
		//De lo contrario si es Primer impresi�n analizo los datos.
		$consulta="Select * from Sistemas";
		$resultado=mysqli_query($cn,$consulta);
		$sistema=mysqli_fetch_array($resultado);
		if ($sistema['SisStkEntParcial']==1)
		{//Se integra este atributo indicando politica de F.Contable se desea dejar o no pendiente material no entregado cuando no hay stock suficiente, etc. 
		 //cuando se ejecuta la entrega de material de la solicitud
			$estadoart="Pendiente";
			$consulta="Select * from StkSolArticulos where StkSolArtEstado='".$estadoart."' and StkSolId=".$solid;
			$resultado=mysqli_query($cn,$consulta);
			//Un sol articulo en Pendiente determina el estado de la solicitud
		       if (mysqli_num_rows($resultado)==0)
			{
				$estadosol="Finalizada";
				$parcial=0;
			}
			else
			{//Estado Adjudicar a�n en desarrollo
				$estadosol="Pendiente de Entrega";
				$parcial=1;
			}
		}
		else
		{
			$estadosol="Finalizada";
			$parcial=0;
		}
	}

	$consultaI="Update StkSolicitudes set StkSolImprimiendo=0, StkSolEstado='".$estadosol."', StkSolParcial=$parcial where StkSolId=".$solid;
	$resultadoI=mysqli_query($cn,$consultaI);

	//Envio mail a los usuarios solicitantes de la unidad que estoy imprimiendo el remito para hacer entrega del material
	$sentenciaII="Select * from SisPflUsuarios as p, Usuarios as u where p.SisId=1 and p.SisPflId=1 and u.SeccionesId='".$unidadcall."' and u.UsuId=p.UsuId and isnull(u.UsuFchFin)";
	$resultadoII=mysqli_query($cn,$sentenciaII);
	while($unSolicitante=mysqli_fetch_assoc($resultadoII))
	{
		//envio de mail a usuarios con rol solicitante
		//busco solicitantes de la unidad y envio mail a todos ellos informando de la Entrega del material
		$email_to = $unSolicitante['UsuMail'];
		$email_subject = "Solicitud de Mercader�a ".$solid;
		$email_body = "La Solicitud de Stock esta lista, pase a retirar material y a firmar Remito "."\n";
		$email_body.= "Mercader�a solicitada por ".$usunomcompleto;
		$email_from = "From: ".$_COOKIE['usumail']."\r\n";
		if(mail($email_to, $email_subject, $email_body, $email_from))
		{
			echo "Se ha enviado mail para entregar material a ($email_to) ";
		}
		else
		{
			echo "El email para entregar material ($email_to) no se ha podido enviar";
		}
	}
	echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=pdfentrega.php?solid=$solid&estadocall=$estadocall&unidadcall=$unidadcall'>";   
	//Cuando ingresa en disponibilidastk.php se recalcula el valor StkSolArtCantAcred y el StkSolArtCantPen cuando guardo o aplico la generaci�n del remito.
?>