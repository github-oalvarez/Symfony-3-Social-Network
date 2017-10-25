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
