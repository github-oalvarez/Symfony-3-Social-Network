<?php
// namespace BackendBundle\Form;

/* Cambiamos el namespace al cambiar el Bundle                     *********************************/
namespace AppBundle\Controller;
/***************************************************************************************************/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/***************************************************************************************************/
class NotificationController extends Controller
{
/* MÉTODO PARA MOSTRAR LAS NOTIFICACIONES DE UN PERFIL  ********************************************/
  public function indexAction(Request $request)
  {
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
    /***********************************************************************************************/
    // indicamos la vista
    return $this->render('AppBundle:Notification:notification_page.html.twig',
      array(
        'user'=>$user,
        'pagination'=>$notifications
      ));
  }
/***************************************************************************************************/
}
