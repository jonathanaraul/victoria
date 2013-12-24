/**
 * @author Jonathan Araul
 *
 */
$('.pagina-normal').live("click", function() {
	var valor = $(this).attr('valor');
	var tipo = 'noticias';
	var data = 'tipo='+tipo+'&valor='+valor;
	
	$('.borrar').remove();
	$('#loader').css('display','');

	$.post(paginacionResultados, data, function(data) {
		
		var obj = JSON.parse(data);
		$('#contenido-pagina').append( obj.variable);
		$('#fila-loader').remove();
		
	});
});
/*
jQuery('.pagina-normal').click(function() {  
	
	var actual = jQuery(this);
	var valor = actual.attr('valor');
	var tipo = 'noticias';
	var data = 'tipo='+tipo+'&valor='+valor;
	console.log('paso por aqui');
	jQuery('.borrar').remove();	
	jQuery('#loader').css('display','');

	jQuery.ajax({
		type : "POST",
		url : paginacionResultados,
		data : data,
		dataType : "json",
		success : function(data) {
			jQuery('.contenido-pagina').append( data.html);
			jQuery('#fila-loader').remove();
								 
								 }
	      });

    return false;
  });
  
*/