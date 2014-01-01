/**
 * @author Jonathan Araul
 *
 */
$('.pagina-noticias').live("click", function() {
	var valor = $(this).attr('valor');
	var data = 'valor='+valor;
	
	$('.borrar').remove();
	$('#loader').css('display','');

	$.post(paginacionNoticias, data, function(data) {
		
		var obj = JSON.parse(data);
		$('#contenido-pagina').append( obj.variable);
		$('#fila-loader').remove();
		
	});
});

$('.celdanoseleccionada').live("click", function() {
	var valor = $(this).attr('valor');
	var listado = $(this).attr('listado');
	var data = 'valor='+valor;
	var direccion ='';
	
	$('.celdaseleccionada').addClass('celdanoseleccionada');
	$('.celdaseleccionada').removeClass('celdaseleccionada');
	$(this).removeClass('celdanoseleccionada');
	$(this).addClass('celdaseleccionada');
	
	$('.borrar').remove();
	$('#loader').css('display','');
	
	if(listado == 'cartelera'){
		direccion = paginacionCartelera;
	}
	else if(listado == 'talleres'){
		direccion = paginacionTalleres;		
	}
	else if(listado == 'calendario'){
		direccion = paginacionCalendario;		
	}
	
	$.post(direccion, data, function(data) {
		
		var obj = JSON.parse(data);
		$('#fila-loader').remove();
		$('#paginacion-especial').after( obj.variable);
		
	});
});

$('#pagina-especial-izquierda').live("click", function() {
	$('.paginacion-especial:not(.celdanovisible)').first().next().next().next().next().next().addClass('celdanovisible');
	
	if($(".paginacion-especial:not(.celdanovisible)").prev().first().hasClass('celdanovisible')){
		$(".paginacion-especial:not(.celdanovisible)").prev().first().removeClass('celdanovisible');
	}
	else{
		$('.paginacion-especial:not(.celdanovisible)').first().clone().insertAfter("#celda-paginador-izquierda");
		$('.paginacion-especial:not(.celdanovisible)').first().removeClass('celdaseleccionada');
		$('.paginacion-especial:not(.celdanovisible)').first().addClass('celdanoseleccionada');
		$('.paginacion-especial:not(.celdanovisible)').first().html('07 X');	
		var fecha = $('.paginacion-especial:not(.celdanovisible)').first().next().attr('valor');
		fecha = fecha.split("-");
		var myDate=new Date();
		myDate.setFullYear(fecha[0],parseInt(fecha[1])-1,fecha[2]);
		console.log('La fecha original a usar es '+myDate);
		myDate.setDate(myDate.getDate()-1);
		
		var m = myDate.getMonth() + 1;
		var d = myDate.getDate();
		var nDay = myDate.getDay();
		var dias =new Array('D','L','M','M','J','V','S');
		m = m > 9 ? m : "0"+m;
		d = d > 9 ? d : "0"+d;
		var prettyDate =(myDate.getUTCFullYear() +'-'+ m) +'-'+ d;	
	
		$('.paginacion-especial:not(.celdanovisible)').first().attr('valor',prettyDate);
		$('.paginacion-especial:not(.celdanovisible)').first().html(d+' '+dias[nDay]);	
			
	}
});
$('#pagina-especial-derecha').live("click", function() {
	
	$('.paginacion-especial:not(.celdanovisible)').last().prev().prev().prev().prev().prev().addClass('celdanovisible');
	
	if($(".paginacion-especial:not(.celdanovisible)").next().last().hasClass('celdanovisible')){
		$(".paginacion-especial:not(.celdanovisible)").next().last().removeClass('celdanovisible');
	}
	else{
		$('.paginacion-especial:not(.celdanovisible)').first().clone().insertBefore( "#celda-paginador-derecha" );
		$('.paginacion-especial:not(.celdanovisible)').last().removeClass('celdaseleccionada');
		$('.paginacion-especial:not(.celdanovisible)').last().addClass('celdanoseleccionada');
		$('.paginacion-especial:not(.celdanovisible)').last().removeClass('celdanovisible');
		$('.paginacion-especial:not(.celdanovisible)').last().html('00 X');
		
		var fecha = $('.paginacion-especial:not(.celdanovisible)').last().attr('valor');
		fecha = fecha.split("-");
		var myDate=new Date();
		myDate.setFullYear(fecha[0], parseInt(fecha[1])-1,fecha[2]);
		myDate.setDate(myDate.getDate()+5);
		
		var m = myDate.getMonth() + 1;
		var d = myDate.getDate();
		m = m > 9 ? m : "0"+m;
		d = d > 9 ? d : "0"+d;
		var prettyDate =(myDate.getUTCFullYear() +'-'+ m) +'-'+ d;
		var nDay = myDate.getDay();
		var dias =new Array('D','L','M','M','J','V','S');
		
		$('.paginacion-especial:not(.celdanovisible)').last().html(d+' '+dias[nDay]);	
		
		$('.paginacion-especial:not(.celdanovisible)').last().attr('valor',prettyDate);

	}
});