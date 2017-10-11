<?php
// namespace BackendBundle\Form;

/* Cambiamos el namespace al cambiar el Bundle                     ************************/
  namespace AppBundle\Controller;
/******************************************************************************************/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/* Añadimos los componentes que permitirán el uso de nuevas clases ************************/
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Session\Session;    // Permite usar sesiones
  use AppBundle\Form\PublicationType;                      // Da acceso al Formulario PublicationType
  use BackendBundle\Entity\Publication;                    // Da acceso a la Entidad Publication
  use BackendBundle\Entity\User;                          // Da acceso a la Entidad User
  use BackendBundle\Entity\Like;                          // Da acceso a la Entidad Like
/******************************************************************************************/
class LikeController extends Controller
{
/* MÉTODO PARA LA CONSULTA AJAX (LIKE PUBLICACIÓN) */
  public function likeAction($id=null){
    // Obtengo los datos del usuario logueado
    $user = $this->getUser();
    // Busco dentro de la BD el dato según nuestra $id
    $em = $this->getDoctrine()->getManager();
    $publication_repo = $em->getRepository('BackendBundle:Publication');
		$publication = $publication_repo->find($id);
    // Creamos el nuevo objeto like
    $like = new Like();
    //Pasamnos el usuario que está logueado a la publicación
    $like->setUser($user);
		$like->setPublication($publication);
		$em->persist($like);
		$flush = $em->flush();

    if ($flush == null) {
			$status = 'Te gusta esta publicación !!';
		} else {
			$status = 'No se ha podido guardar el me gusta !!';
		}
    // Para usar el método response es necesario cargar el componente
    return new Response($status);
	}
/********************************************************************/
/* MÉTODO PARA LA CONSULTA AJAX (UNLIKE PUBLICACIÓN) */
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
/********************************************************************/
}
