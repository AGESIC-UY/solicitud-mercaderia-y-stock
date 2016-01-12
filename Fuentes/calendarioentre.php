<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.7.3.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.3/jquery-ui.min.js"></script>
<script type="text/javascript">

jQuery(function($){
	$.datepicker.regional['es'] = {
		closeText: 'Cerrar',
		prevText: '&#x3c;Ant',
		nextText: 'Sig&#x3e;',
		currentText: 'Hoy',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
		dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
		dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['es']);
});    
 
$(document).ready(function() {
   $("#datepicker").datepicker();
 });

$(document).ready(function() {
   $("#datepickerI").datepicker();
 });

</script>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<form action="cnsuniart.php?fchdesde=<?php echo $fchdesde;?>&fchhasta=<?php echo $fchhasta;?>&detallo=<?php echo $detallo;?>&uniele=<?php echo $uniele;?>&artid=<?php echo $artid;?>" method="post">
  <tr>
  <th>Entre Fechas:</th>
  <th align="left">
  <input type="text" name="fchdesde" value="<?php echo $fchdesde; ?>" id="datepicker" readonly="readonly" size="12" />
  <input type="text" name="fchhasta" value="<?php echo $fchhasta; ?>" id="datepickerI" readonly="readonly" size="12" />
  <input name="submit" type="submit" value="Aplicar"/> 
  </th>
  </tr>
</form>