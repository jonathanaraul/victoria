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
$('.pagina-cartelera').live("click", function() {
	var valor = $(this).attr('valor');
	var data = 'valor='+valor;
	
	$('.borrar').remove();
	$('#loader').css('display','');

	$.post(paginacionCartelera, data, function(data) {
		
		var obj = JSON.parse(data);
		$('#contenido-pagina').append( obj.variable);
		$('#fila-loader').remove();
		
	});
});

$('.celdanoseleccionada').live("click", function() {
	var valor = $(this).attr('valor');
	var data = 'valor='+valor;
	
	$('.celdaseleccionada').addClass('celdanoseleccionada');
	$('.celdaseleccionada').removeClass('celdaseleccionada');
	$(this).removeClass('celdanoseleccionada');
	$(this).addClass('celdaseleccionada');
	
	$('.borrar').remove();
	$('#loader').css('display','');
	
	$.post(paginacionCartelera, data, function(data) {
		
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
		$('.paginacion-especial:not(.celdanovisible)').first().html('00 X');	
		var fecha = $('.paginacion-especial:not(.celdanovisible)').first().attr('valor');
		fecha = fecha.split("-");
		var myDate=new Date();
		myDate.setFullYear(fecha[0],parseInt(fecha[1])-1,fecha[2]);
		myDate.setDate(myDate.getDate()-1);
		$('.paginacion-especial:not(.celdanovisible)').last().attr('valor',myDate.getUTCFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate());
		
		console.dir(myDate);
			
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
		myDate.setDate(myDate.getDate()+1);
		$('.paginacion-especial:not(.celdanovisible)').last().attr('valor',myDate.getUTCFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate());
		console.dir(myDate);
	}
});