/**
 * @author Jonathan Araul
 *
 */
$(function(){
		$('#menu').slicknav({
		prependTo:'#menuobligado'
});

	});
$( document ).ready(function() {
  // Handler for .ready() called.
  if ($('#paginacion-especial').length>0){
  	paginadorEspecial();
  }
});

function paginadorEspecial(){
	console.log('Se ejecuto paginador especial');
	//$('.paginacion-especial:not(.celdanovisible)').first().removeClass('celdanovisible');
	var dimension = $( window ).width();
	if(dimension > 271){
		if($('.paginacion-especial:not(.celdanovisible)').length==1){
			$('.paginacion-especial:not(.celdanovisible)').last().next().removeClass('celdanovisible');
		}
	}
	if(dimension > 311){
		if($('.paginacion-especial:not(.celdanovisible)').length==2){
			$('.paginacion-especial:not(.celdanovisible)').last().next().removeClass('celdanovisible');
		}
	}
	if(dimension > 361){
		if($('.paginacion-especial:not(.celdanovisible)').length==3){
			$('.paginacion-especial:not(.celdanovisible)').last().next().removeClass('celdanovisible');
		}
	}

	if(dimension > 406){
		if($('.paginacion-especial:not(.celdanovisible)').length==4){
			$('.paginacion-especial:not(.celdanovisible)').last().next().removeClass('celdanovisible');
		}
	}
	if(dimension > 453){
		if($('.paginacion-especial:not(.celdanovisible)').length==5){
			$('.paginacion-especial:not(.celdanovisible)').last().next().removeClass('celdanovisible');
		}
	}
	return false;
}
$( window ).resize(function() {
//453 borra uno
// 406 borra dos
// 361 borra tres
// 311 borra cuatro
// 261 borra cinco
// 271 borra seis
	var dimension = $( window ).width();
	if(dimension <= 453){
		if($('.paginacion-especial:not(.celdanovisible)').length==6){
			$('.paginacion-especial:not(.celdanovisible)').last().addClass('celdanovisible');
		}
		
	}
	else{
		if($('.paginacion-especial:not(.celdanovisible)').length==5){
			$('.paginacion-especial:not(.celdanovisible)').last().next().removeClass('celdanovisible');
		}
	}
	
	if(dimension <= 406){
		if($('.paginacion-especial:not(.celdanovisible)').length==5){
			$('.paginacion-especial:not(.celdanovisible)').last().addClass('celdanovisible');
		}
		
	}
	else{
		if($('.paginacion-especial:not(.celdanovisible)').length==4){
			$('.paginacion-especial:not(.celdanovisible)').last().next().removeClass('celdanovisible');
		}
	}
	if(dimension <= 361){
		if($('.paginacion-especial:not(.celdanovisible)').length==4){
			$('.paginacion-especial:not(.celdanovisible)').last().addClass('celdanovisible');
		}
		
	}
	else{
		if($('.paginacion-especial:not(.celdanovisible)').length==3){
			$('.paginacion-especial:not(.celdanovisible)').last().next().removeClass('celdanovisible');
		}
	}
	if(dimension <= 311){
		if($('.paginacion-especial:not(.celdanovisible)').length==3){
			$('.paginacion-especial:not(.celdanovisible)').last().addClass('celdanovisible');
		}
		
	}
	else{
		if($('.paginacion-especial:not(.celdanovisible)').length==2){
			$('.paginacion-especial:not(.celdanovisible)').last().next().removeClass('celdanovisible');
		}
	}
	if(dimension <= 271){
		if($('.paginacion-especial:not(.celdanovisible)').length==2){
			$('.paginacion-especial:not(.celdanovisible)').last().addClass('celdanovisible');
		}
		
	}
	else{
		if($('.paginacion-especial:not(.celdanovisible)').length==1){
			$('.paginacion-especial:not(.celdanovisible)').last().next().removeClass('celdanovisible');
		}
	}
	
  console.log('Se redimensiono a '+$( window ).width());
});
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
	
	var dimension = $( window ).width();
	var esconder = $('.paginacion-especial:not(.celdanovisible)').first();
	var anterior = null;

	if(dimension > 271){
		esconder = $('.paginacion-especial:not(.celdanovisible)').first().next();
	}
	if(dimension > 311){
		esconder = $('.paginacion-especial:not(.celdanovisible)').first().next().next();;
	}
	if(dimension > 361){
		esconder = $('.paginacion-especial:not(.celdanovisible)').first().next().next().next();
	}
	if(dimension > 406){
		esconder = $('.paginacion-especial:not(.celdanovisible)').first().next().next().next().next();
	}
	if(dimension > 453){
		esconder = $('.paginacion-especial:not(.celdanovisible)').first().next().next().next().next().next();
	}
		
	
	if($(".paginacion-especial:not(.celdanovisible)").prev().first().hasClass('celdanovisible')){
		$(".paginacion-especial:not(.celdanovisible)").prev().first().removeClass('celdanovisible');
		esconder.addClass('celdanovisible');
	}
	else{
		$('.paginacion-especial:not(.celdanovisible)').first().clone().insertAfter("#celda-paginador-izquierda");
		esconder.addClass('celdanovisible');
		$('.paginacion-especial:not(.celdanovisible)').first().removeClass('celdaseleccionada');
		$('.paginacion-especial:not(.celdanovisible)').first().addClass('celdanoseleccionada');
		$('.paginacion-especial:not(.celdanovisible)').first().html('07 X');	
		var fecha = $('.paginacion-especial:not(.celdanovisible)').first().attr('valor');
		fecha = fecha.split("-");
		var myDate=new Date();
		myDate.setFullYear(fecha[0],parseInt(fecha[1])-1,fecha[2]);
		myDate.setDate(myDate.getDate()-1);
		
		var m = myDate.getMonth() + 1;
		var d = myDate.getDate();
		var nDay = myDate.getDay();
		var dias = new Array('S','M','T','W','T','F','S');
		if($('#lang-es-selector').hasClass('lang-sel')){
			dias =new Array('D','L','M','M','J','V','S');
		}
		
		m = m > 9 ? m : "0"+m;
		d = d > 9 ? d : "0"+d;
		var prettyDate =(myDate.getUTCFullYear() +'-'+ m) +'-'+ d;	
	
		$('.paginacion-especial:not(.celdanovisible)').first().attr('valor',prettyDate);
		$('.paginacion-especial:not(.celdanovisible)').first().html(d+' '+dias[nDay]);		
	}
	cambiaDeMes();
});
$('#pagina-especial-derecha').live("click", function() {
	var dimension = $( window ).width();
	var esconder = $('.paginacion-especial:not(.celdanovisible)').last();
	var siguiente = null;

	if(dimension > 271){
		esconder = $('.paginacion-especial:not(.celdanovisible)').last().prev();
	}
	if(dimension > 311){
		esconder = $('.paginacion-especial:not(.celdanovisible)').last().prev().prev();;
	}
	if(dimension > 361){
		esconder = $('.paginacion-especial:not(.celdanovisible)').last().prev().prev().prev();
	}
	if(dimension > 406){
		esconder = $('.paginacion-especial:not(.celdanovisible)').last().prev().prev().prev().prev();
	}
	if(dimension > 453){
		esconder = $('.paginacion-especial:not(.celdanovisible)').last().prev().prev().prev().prev().prev();
	}
	siguiente = esconder.next();
	
	
	if($(".paginacion-especial:not(.celdanovisible)").next().last().hasClass('celdanovisible') || siguiente.hasClass('celdanovisible')){
		if($(".paginacion-especial:not(.celdanovisible)").next().last().hasClass('celdanovisible')){
			$(".paginacion-especial:not(.celdanovisible)").next().last().removeClass('celdanovisible');			
		}
		else{
			 siguiente.removeClass('celdanovisible');
		}
		esconder.addClass('celdanovisible');
	}
	else{
		$('.paginacion-especial:not(.celdanovisible)').last().clone().insertBefore( "#celda-paginador-derecha" );
		esconder.addClass('celdanovisible')
		$('.paginacion-especial:not(.celdanovisible)').last().removeClass('celdaseleccionada');
		$('.paginacion-especial:not(.celdanovisible)').last().addClass('celdanoseleccionada');
		$('.paginacion-especial:not(.celdanovisible)').last().removeClass('celdanovisible');
		$('.paginacion-especial:not(.celdanovisible)').last().html('00 X');
		
		var fecha = $('.paginacion-especial:not(.celdanovisible)').last().attr('valor');
		fecha = fecha.split("-");
		var myDate=new Date();
		myDate.setFullYear(fecha[0], parseInt(fecha[1])-1,fecha[2]);
		myDate.setDate(myDate.getDate()+1);
		
		var m = myDate.getMonth() + 1;
		var d = myDate.getDate();
		m = m > 9 ? m : "0"+m;
		d = d > 9 ? d : "0"+d;
		var prettyDate =(myDate.getUTCFullYear() +'-'+ m) +'-'+ d;
		var nDay = myDate.getDay();
		var dias = new Array('S','M','T','W','T','F','S');
		if($('#lang-es-selector').hasClass('lang-sel')){
			dias =new Array('D','L','M','M','J','V','S');
		}
		
		$('.paginacion-especial:not(.celdanovisible)').last().html(d+' '+dias[nDay]);	
		$('.paginacion-especial:not(.celdanovisible)').last().attr('valor',prettyDate);
	}
	cambiaDeMes();
});
function cambiaDeMes(){

	var fecha = $('.paginacion-especial:not(.celdanovisible)').first().attr('valor');
	fecha = fecha.split("-");
	var mes = parseInt(fecha[1]) - 1;
	var meses = new Array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
	
	if($('#lang-es-selector').hasClass('lang-sel')==false){
			meses =new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "Novemeber", "December");
		}
	if($('.mes-paginacion-especial').html()!= meses[mes]){
		$('.mes-paginacion-especial').html(meses[mes]);
	}
	return false;
}