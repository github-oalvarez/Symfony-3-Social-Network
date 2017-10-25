// Escuchamos el documento y activamos la función
$(document).ready(function(){
/* Identificamos el elemento con esa clase  ".nick-input"
y cuando salgamos de él (.blur) actuamos */
  $(".nick-input").blur(function(){
// Capturamos el valor
    var nick = this.value;
// Cargamos ajax
  $.ajax({
    url: URL+'/nick-test',
    data: {
      nick: nick
    },
    type: 'POST',
		success: function(response){
// Si 'response'=used
        if(response == "used"){
          $(".nick-input").css("border","1px solid red");
// Si 'response'!=used
        }else{
          $(".nick-input").css("border","1px solid green");
        }
      }
    });
  });
});
