<?php
/* IMPORTANTE !!!!!!
 * No olvidar incluir la extensión del SERVICIO  dentro de 'app\config\services.yml'
 */
namespace AppBundle\Services;

use BackendBundle\Entity\Notification;    // Da acceso a la Entidad Usuario

class NotificationService{
/* CARGAMOS EL ENTITY MANAGER DE DOCTRINE *****************************************/
  /*
   * Para el correcto funcionamiento es necesario incluir la
   * variable $manager de doctrine
   */
  public $manager;
  public function __construct($manager) {
		$this->manager = $manager;
	}
/**********************************************************************************/
/* MÉTODO PARA GUARDAR LAS NOTIFICACIONES EN LA BD ********************************/
  public function set($user, $type, $typeId, $extra = null){
    // $user el usuario para el que va la notificación
    // $type tipo de notificación
    // $typeId guardar el registro concreto del usuario que generó la notificación
    // Cargamos el Entity Manager
    $em = $this->manager;
    // Creamos el objeto Notification
    $notification = new Notification();
    // Guardamos los datos recogidos dentro del objeto Notificacion
    $notification->setUser($user);
    $notification->setType($type);
    $notification->setTypeId($typeId);
    $notification->setReaded(0);
    $notification->setCreatedAt(new \DateTime("now"));
    $notification->setExtra($extra);
    // Persisitimos los datos
    $em->persist($notification);
    // Incluimos los datos dentro de la base de datos
    $flush = $em->flush();
    // Comprobamos la subida de datos a la BD
    if($flush == null){$status = true;}else{$status = false;}
    return $status;
  }
/**********************************************************************************/
/* MÉTODO PARA LEER LAS NOTIFICACIONES DE LA BD ***********************************/
  public function read($user){
    $em = $this->manager;
    $notification_repo = $em->getRepository('BackendBundle:Notification');
    $notifications = $notification_repo->findBy(array('user' => $user));
    foreach($notifications as $notification){
      $notification->setReaded(1);
      $em->persist($notification);
    }
    $flush = $em->flush();

    if($flush == null){
      return true;
    }else{
      return false;
    }
    return true;
  }
}
