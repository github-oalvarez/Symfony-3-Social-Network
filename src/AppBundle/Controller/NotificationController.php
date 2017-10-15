<?php
// namespace BackendBundle\Form;

/* Cambiamos el namespace al cambiar el Bundle                     *********************************/
namespace AppBundle\Controller;
/***************************************************************************************************/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/***************************************************************************************************/
/* Añadimos los componentes que permitirán el uso de nuevas clases *********************************/
  use Symfony\Component\HttpFoundation\Response;        // Permite usar el método Response
/***************************************************************************************************/
class NotificationController extends Controller
{
/* MÉTODO PARA MOSTRAR LAS NOTIFICACIONES DE UN PERFIL  ********************************************/
  public function indexAction(Request $request){
    // Cargo el Entity Manager
    $em = $this->getDoctrine()->getManager();
    // Sacamos el usuario del cual gestionar las NOTIFICACIONES
    $user = $this->getUser();
    $user_id = $user->getId();
    // Buscamos el registro de notificaciones del usuario logueado
    $dql = "SELECT n FROM BackendBundle:Notification n WHERE n.user = $user_id ORDER BY n.id DESC";
    // Cargamos la query
    $query = $em->createQuery($dql);
    /* Iniciamos el paginador **********************************************************************/
    $paginator = $this->get('knp_paginator');
    $notifications = $paginator->paginate(
        $query, $request->query->getInt('page', 1), 5
    );
    /* NOTIFICACIONES ******************************************************************************/
    /*
     * Iniciamos el servicio 'app.notification_service' declarado dentro de 'app\config\services.yml'
     * ver el método 'read' dentro de 'src\AppBundle\Services\NotificationService.php'
     * ( Ver 'app\config\services.yml', 'src\AppBundle\Services\NotificationService.php' y
     * 'src\AppBundle\Controller\NotificationController.php' )
     */
    $notification = $this->get('app.notification_service');
    $notification->read($user);
    // Esto marcará como leidas nuestras publicaciones
    /***********************************************************************************************/
    // indicamos la vista
    return $this->render('AppBundle:Notification:notification_page.html.twig',
      array(
        'user'=>$user,
        'pagination'=>$notifications
    ));
  }
/***************************************************************************************************/
/* MÉTODO AJAX PARA CONTAR EL NÚMERO DE NOTIFICACIONES SIN LEER ************************************/
  public function countNotificationsAction(){
    // Cargo el Entity Manager
    $em = $this->getDoctrine()->getManager();
    // Extraemos el repositorio de las Notification de su entidad
    $notification_repo = $em->getRepository("BackendBundle:Notification");
    // Buscamos la coincidencia según usuario y readed igual a '0' (sin leer)
    $notifications = $notification_repo->findBy(array(
      'user' => $this->getUser(),
      'readed' => 0
    ));
    // Enviamos el contador de notaficaciones no leidas
    return new Response(count($notifications));
    /* Para ver la respuesta colocar la url indicada dentro del ROUTING */
  }
/***************************************************************************************************/
}
