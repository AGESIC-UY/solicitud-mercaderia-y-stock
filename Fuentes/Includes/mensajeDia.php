  <script language="JavaScript"> 
   function frase(){ 
    var aFrases = new Array("Texto del Domingo",  
                            "A pesar de que sea lunes comienza con buena cara",  
                            "Lo peor ya paso..... el lunes.",  
                            "Ya estamos en la mitad de la semana... y nos falta desarrollar la mayor&iacute;a",  
                            "Ya es jueves...metele pata si no querés quedarte el viernes hasta tarde!!",  
                            "Recuerda: Hoy es viernes termina la semana, as&iacute; que cambia esa cara de amargo.",  
                            "Texto del Sábado"); 
    var fecha = new Date(); 
    var indice = fecha.getDay(); 
    return aFrases[indice]; 
   } 
  </script> 
 
 
  <form name="frm"style="color:blue;font-size:7pt;text-align:left"> 
   <h1> 
    <script language="JavaScript"> 
     document.write(frase()); 
    </script> 
   </h1> 
  </form> 
 