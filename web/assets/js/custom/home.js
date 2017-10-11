/* FUNCIÓN PARA LISTAR PUBLICACIONES SCROLL INFINITO... */
// Escuchamos el documento y activamos la función
$(document).ready(function(){
  /*
   * Usando alert("USERS"); sabremos si hemos
   * cargado el jquery de la librería más el propio
   */
  //alert("USERS");
  var ias = jQuery.ias({
    //el contenedor que contiene el listado es clase .box-content dentro de un id .time-line
    container: '#timeline .box-content',
    item: '.publication-item', //item a paginar
    pagination: '#timeline .pagination', // esta clase contiene los controles de navegación
    next: '#timeline .pagination .next_link',
    triggerPageThreshold: 5 // cada cuantos elementos mostrará la siguiente página
  });
  ias.extension(new IASTriggerExtension({
    text:'Ver más publicaciones',
    offset: 3
  }));
  ias.extension(new IASSpinnerExtension({
    src: URL+'/../assets/images/ajax-loader.gif'
  }));
  ias.extension(new IASNoneLeftExtension({
    text: 'No hay más publicaciones'
  }));
  ias.on('ready', function(event){
    buttons();
  });
  ias.on('rendered', function(event){
    buttons();
  });
});
/*************************************************************/

/* ...FUNCIÓN PARA LISTAR PUBLICACIONES SCROLL INFINITO */
function buttons(){
  /* Incluimos el código javascript que nos permita mostrar los mensajes en globos */
	$('[data-toggle="tooltip"]').tooltip();

  $(".btn-img").unbind("click").click(function(){
		$(this).parent().find('.pub-image').fadeToggle();
	});
  /*************************************************************/
  /* Damos funcionalidad al botón eliminar publicación */
  $(".btn-delete-pub").unbind('click').click(function(){
    // Ocultamos la publicación
    $(this).parent().parent().addClass('hidden');
    // Eliminamos dentro de la base de datos la publicación
    $.ajax({
			url: URL+'/publication/remove/'+$(this).attr("data-id"),
			type: 'GET',
			success: function(response){console.log(response);}
		});
	});
  /*************************************************************/
  /* Damos funcionalidad al botón LIKE publicación */
  $(".btn-like").unbind('click').click(function(){
    // Ocultamos una opción y mostramos la otra...
		$(this).addClass("hidden");
		$(this).parent().find('.btn-unlike').removeClass("hidden");
    // ...Ocultamos una opción y mostramos la otra
		$.ajax({
			url: URL+'/like/'+$(this).attr("data-id"),
			type: 'GET',
			success: function(response){
				console.log(response);
			}
		});
	});
  /*************************************************************/
  /* Damos funcionalidad al botón UNLIKE publicación */
  $(".btn-unlike").unbind('click').click(function(){
    // Ocultamos una opción y mostramos la otra...
    $(this).addClass("hidden");
    $(this).parent().find('.btn-like').removeClass("hidden");
    // ...Ocultamos una opción y mostramos la otra
    $.ajax({
      url: URL+'/unlike/'+$(this).attr("data-id"),
      type: 'GET',
      success: function(response){
        console.log(response);
      }
    });
  });
  /*************************************************************/
}
/*************************************************************/
