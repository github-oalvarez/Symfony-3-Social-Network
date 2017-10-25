/* FUNCIÓN PARA LISTAR USUARIOS SCROLL INFINITO... */
// Escuchamos el documento y activamos la función
$(document).ready(function(){
  /*
   * Usando alert("USERS"); sabremos si hemos
   * cargado el jquery de la librería más el propio
   */
  //alert("USERS");
  var ias = jQuery.ias({
    container: '.box-users', //el contenedor que contiene el listado es clase .box-users
    item: '.user-item', //item a paginar
    pagination: '.pagination', // esta clase contiene los controles de navegación
    next: '.pagination .next_link',
    //loader: '<div class="publication"><img src="/web/assets/images/ajax-loader.gif"/></div>',
    triggerPageThreshold: 5 // cada cuantos elementos mostrará la siguiente página
  });
  ias.extension(new IASTriggerExtension({
		text: 'Ver más personas',
		offset: 3
	}));
	ias.extension(new IASSpinnerExtension({
		src: URL+'/../assets/images/ajax-loader.gif'
	}));
	ias.extension(new IASNoneLeftExtension({
		text: 'No hay más personas'
	}));
  /* ACTIVAR FUNCIÓN PARA SEGUIR USUARIOS... */
  ias.on('ready', function(event){
    followButtons(); // Función que registra el follow
  });
  ias.on('rendered', function(event){
    followButtons(); // Función que registra el follow
  });
  /* ...ACTIVAR FUNCIÓN PARA SEGUIR USUARIOS */
});
/* ...FUNCIÓN PARA LISTAR USUARIOS SCROLL INFINITO */
