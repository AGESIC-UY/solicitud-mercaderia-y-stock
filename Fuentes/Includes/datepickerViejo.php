
		<!-- firebug lite -->
		<SCRIPT type="text/javascript" src="./jquery-datepicker/firebug.js"></SCRIPT>

        <!-- jQuery -->
		<SCRIPT type="text/javascript" src="./jquery-datepicker/jquery.min.js"></SCRIPT>

        <!-- required plugins -->
		<SCRIPT type="text/javascript" src="./jquery-datepicker/date.js"></SCRIPT>
		<!--[if IE]><script type="text/javascript" src="scripts/jquery.bgiframe.min.js"></script><![endif]-->

        <!-- jquery.datePicker.js -->
		<SCRIPT type="text/javascript" src="./jquery-datepicker/jquery.datePicker.js"></SCRIPT>

        <!-- datePicker required styles -->
		<LINK rel="stylesheet" type="text/css" media="screen" href="./jquery-datepicker/datePicker.css">
	
        <!-- page specific scripts -->
		<SCRIPT type="text/javascript" charset="utf-8">
            $(function()
            {
				$('.date-pick').datePicker({clickInput:true})
                $('.date-pick').datePicker().val(new Date().asString()).trigger('change');
            });
		</SCRIPT>
