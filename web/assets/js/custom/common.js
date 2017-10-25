/* FUNCIÓN PARA GESTIONAR NOTIFICACIONES... *******************************************************/
// Cuando la  página haya cargado...
$(document).ready(function () {
  /* VER / OCULTAR NOTIFICACIONES GENERALES
   * Observamos el contenido (texto) de la etiqueta class="label-notifications",
   * el cual si es igual a 0 ocultaremos (añadimos o eliminamos la clase hidden).
   */
	if ($(".label-notifications").text() == 0) {
		$(".label-notifications").addClass("hidden");
	} else {
		$(".label-notifications").removeClass("hidden");
	}
  /***********************************************************************************************/
  /* VER / OCULTAR NOTIFICACIONES MENSAJES
   * Observamos el contenido (texto) de la etiqueta class=".label-notifications-msg",
   * el cual si es igual a 0 ocultaremos (añadimos o eliminamos la clase hidden).
   */
	if ($(".label-notifications-msg").text() == 0) {
		$(".label-notifications-msg").addClass("hidden");
	} else {
		$(".label-notifications-msg").removeClass("hidden");
	}
  /***********************************************************************************************/
	/* CARGAMOS LA FUNCIÓN NOTIFICATIONS...*********************************************************/
  notifications();
  /***********************************************************************************************/
  /* REPETIMOS LA FUNCIÓN notifications(); CADA x milisegundos ***********************************/
  setInterval(function () {
		notifications();
	}, 60000);
  /***********************************************************************************************/
});
/**************************************************************************************************/
function notifications() {
  /* FUNCIÓN AJAX NOTIFICACIONES GENERALES...******************************************************/
	$.ajax({
		url: URL + '/notifications/get',
		type: 'GET',
		success: function (response) {
      // incluimos dentro de la etiqueta html con clase ".label-notifications" el contenido de response
			$(".label-notifications").html(response);
      // Colocamos / Quitamosla clase 'hidden' según response
			if (response == 0) {
				$(".label-notifications").addClass("hidden");
			} else {
				$(".label-notifications").removeClass("hidden");
			}
		}
	});
  /***********************************************************************************************/
  /* FUNCIÓN AJAX NOTIFICACIONES MENSAJES...******************************************************/
	$.ajax({
		url: URL + '/private-message/notification/get',
		type: 'GET',
		success: function (response) {
			// incluimos dentro de la etiqueta html con clase ".label-notifications-msg" el contenido de response
      $(".label-notifications-msg").html(response);
      // Colocamos / Quitamosla clase 'hidden' según response
			if (response == 0) {
				$(".label-notifications-msg").addClass("hidden");
			} else {
				$(".label-notifications-msg").removeClass("hidden");
			}
		}
	});
  /***********************************************************************************************/
}
