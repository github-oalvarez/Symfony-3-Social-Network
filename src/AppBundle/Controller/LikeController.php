<?php
// namespace BackendBundle\Form;

/* Cambiamos el namespace al cambiar el Bundle                     *********************************/
  namespace AppBundle\Controller;
/***************************************************************************************************/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/* Añadimos los componentes que permitirán el uso de nuevas clases *********************************/
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Session\Session; // Permite usar sesiones
  use AppBundle\Form\PublicationType;                   // Da acceso al Formulario PublicationType
  use BackendBundle\Entity\Publication;                 // Da acceso a la Entidad Publication
  use BackendBundle\Entity\User;                        // Da acceso a la Entidad User
  use BackendBundle\Entity\Like;                        // Da acceso a la Entidad Like
/***************************************************************************************************/
class LikeController extends Controller
{
/* MÉTODO PARA LA CONSULTA AJAX (LIKE PUBLICACIÓN) *************************************************/
  public function likeAction($id=null){
    // Obtengo los datos del usuario logueado
    $user = $this->getUser();
    // Cargo Entity Manager de Doctrine dentro de lavariable $em
    $em = $this->getDoctrine()->getManager();
    // Cargo la entidad Publication dentro de $publication_repo
    $publication_repo = $em->getRepository('BackendBundle:Publication');
    // Busco el registro por su $id
		$publication = $publication_repo->find($id);
    // Creamos el nuevo objeto like
    $like = new Like();
    //Pasamnos el usuario que está logueado a la publicación
    $like->setUser($user);
		$like->setPublication($publication);
		$em->persist($like);
		$flush = $em->flush();

    if ($flush == null) {
      /* Sistema de notificaciones......*************************************************************/
      /*
       * Cargamos el SERVICIO NOTIFICACIONES
       * ( Ver 'app\config\services.yml', 'src\AppBundle\Services\NotificationService.php' y
       * 'src\AppBundle\Controller\NotificationController.php' )
       */
      $notification = $this->get('app.notification_service');
      // Llamamos al método SET del SERVICIO NOTIFICACIONES
      $notification->set(
        $publication->getUser(),
        'like',
        $user->getId(),
        $publication->getId()
      );
      /*********************************************************************************************/
			$status = 'Te gusta esta publicación !!';
		} else {
			$status = 'No se ha podido guardar el me gusta !!';
		}
    // Para usar el método response es necesario cargar el componente
    return new Response($status);
	}
/***************************************************************************************************/
/* MÉTODO PARA LA CONSULTA AJAX (UNLIKE PUBLICACIÓN) ***********************************************/
  public function unlikeAction($id = null) {
    // Obtengo los datos del usuario logueado
    $user = $this->getUser();
    // Busco dentro de la BD el dato según nuestra $id
    $em = $this->getDoctrine()->getManager();
    // Extraemos el repositorio 'Like'
    $like_repo = $em->getRepository('BackendBundle:Like');
    // Buscamos la coincidencia mediante un array ($user y $id)
    $like = $like_repo->findOneBy(array(
      'user' => $user,
      'publication' => $id
    ));
    // Eliminamos el registro de la coincidencia de $em
    $em->remove($like);
    // Eliminamos el registro de la coincidencia de la BD
    $flush = $em->flush();

    if ($flush == null) {
      $status = 'Ya no te gusta esta publicación !!';
    } else {
      $status = 'No se ha podido desmarcar el me gusta !!';
    }
    // Para usar el método response es necesario cargar el componente
    return new Response($status);
  }
/***************************************************************************************************/
/* MÉTODO PARA MOSTRAR LOS PERFILES QUE SIGUE UN PERFIL  *******************************************/
  public function likesAction(Request $request, $nickname = null){
    // Cargo Entity Manager de Doctrine dentro de la variable $em
    $em = $this->getDoctrine()->getManager();
    /*
     * Si $nickname es distinto de 'null' lo busco en la BD,
     * si es null coloco el del usuario logueado
     */
    if ($nickname != null) {
      // Cargo la entidad User dentro de $user_repo
      $user_repo = $em->getRepository("BackendBundle:User");
      // Busco el registro por su nick que será igual a $nickname
      $user = $user_repo->findOneBy(array("nick" => $nickname));
    } else {
      // Cargo el usuario logueado
      $user = $this->getUser();
    }
    /*
     * Si el usuario que llega por la url está vacio o no es objeto
     * si existe el objeto User nos rediriges a home
     */
    if (empty($user) || !is_object($user)) {
      return $this->redirect($this->generateUrl('home_publications'));
    }
    /***************************************************************************************/
    // Busco el $id del usuario señalado
    $user_id = $user->getId();
    // Realizo la consulta
    $dql = "SELECT l FROM BackendBundle:Like l WHERE l.user = $user_id ORDER BY l.id DESC";
    // Cargo la Query de la consulta $dql
    $query = $em->createQuery($dql);
    /* Iniciamos el paginador **********************************************************************/
    $paginator = $this->get('knp_paginator');
    $likes = $paginator->paginate(
        $query, $request->query->getInt('page', 1), 5
    );
    /***********************************************************************************************/
    // Devolvemos la vista con la información generado por el paginador
    return $this->render('AppBundle:Like:likes.html.twig', array(
      'user' => $user,
      'pagination' => $likes
    ));
	}
/***************************************************************************************************/
}
