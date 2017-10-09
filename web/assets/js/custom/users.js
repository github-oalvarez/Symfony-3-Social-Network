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
    triggerPageThreshold: 5 // cada cuantos elementos mostrará la siguiente página
  });
  ias.extension(new IASTriggerExtension({
    text:'Ver más personas',
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
/* FUNCIÓN PARA SEGUIR USUARIOS... */
function followButtons(){
  $(".btn-follow").unbind("click").click(
    function(){
      $(this).addClass("hidden");
      $(this).parent().find(".btn-unfollow").removeClass("hidden");
      $.ajax({
        url: URL+'/follow',
        type: 'POST',
        data: {
          followed: $(this).attr("data-followed")
        },
        succes: function(response){
          console.log(response);
        }
      });
    });
    $(".btn-unfollow").unbind("click").click(
      function(){
        $(this).addClass("hidden");
        $(this).parent().find(".btn-follow").removeClass("hidden");
        $.ajax({
          url: URL+'/unfollow',
          type: 'POST',
          data: {
            followed: $(this).attr("data-followed")
          },
          succes: function(response){
            console.log(response);
          }
        });
      });
  }
/* ...FUNCIÓN PARA SEGUIR USUARIOS */
