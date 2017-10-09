/* FUNCIÓN PARA LISTAR USUARIOS SCROLL INFINITO... */
// Escuchamos el documento y activamos la función
$(document).ready(function(){
  /*
   * Usando alert("USERS"); sabremos si hemos
   * cargado el jquery de la librería más el propio
   */
  //alert("USERS");
  var ias = jQuery.ias({
    //el contenedor que contiene el listado es clase .box-content dentro de un id .time-line
    container: '#time-line .box-content',
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
  /* ACTIVAR FUNCIÓN PARA SEGUIR USUARIOS... */
  ias.on('ready', function(event){
    buttons(); // Función que registra el follow
  });
  ias.on('rendered', function(event){
    buttons(); // Función que registra el follow
  });
  /* ...ACTIVAR FUNCIÓN PARA SEGUIR USUARIOS */
});
/* ...FUNCIÓN PARA LISTAR USUARIOS SCROLL INFINITO */
/* FUNCIÓN PARA SEGUIR USUARIOS... */
function buttons(){

  }
/* ...FUNCIÓN PARA SEGUIR USUARIOS */
