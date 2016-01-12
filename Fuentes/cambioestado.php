<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$solid=$_REQUEST['solid'];
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];

//La impresión se basa en el atributo StkSolArtCantAcred con valor mayor a cero para saber que se trata del articulo al cual se le asigno cantidad
//durante él proceso "Entrega de material o disponibilidad". Por lo que Los artículos deben aparecer en una nueva acreditación con pendiente y
//acreditación en cero. Por lo tanto llevo a cero las cantidades acred y pend para que no afecten los resultados en la nueva impresión y/o entrega. 

//Ahora bien esta inicialización solo va a suceder cuando quedaron cantidades pendientes de algun articulo de la solicitud, y deben volver a tramitar
//una entrega de material. De lo contrario estas cantidades podrían quedar con valor. Ya que para que se realice el call a este objeto deberia hacer 
//click en el icono de disponibilidad para que se ejecute.

	//Si se trata de una reimpresión debe llevar al estado en el que se encontraba antes de "Imprimir Remito" osea "Finalizada" o "Pendiente"  pero este estado
	//no lo indican los articulos pendientes podría ser finalizada en forma forzada con articulos pen, se identifica con el parcial = 2

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
		//De lo contrario si es Primer impresión analizo los datos.
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
			{//Estado Adjudicar aún en desarrollo
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
		$email_subject = "Solicitud de Mercadería ".$solid;
		$email_body = "La Solicitud de Stock esta lista, pase a retirar material y a firmar Remito "."\n";
		$email_body.= "Mercadería solicitada por ".$usunomcompleto;
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
	//Cuando ingresa en disponibilidastk.php se recalcula el valor StkSolArtCantAcred y el StkSolArtCantPen cuando guardo o aplico la generación del remito.
?>