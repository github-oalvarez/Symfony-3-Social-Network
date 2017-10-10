<?php
// namespace BackendBundle\Form;

/* Cambiamos el namespace al cambiar el Bundle                     ************************/
  namespace AppBundle\Controller;
/******************************************************************************************/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/* Añadimos los componentes que permitirán el uso de nuevas clases ************************/
  use Symfony\Component\HttpFoundation\Session\Session; // Permite usar sesiones
  use Symfony\Component\HttpFoundation\Response;        // Permite usar el método Response
  use BackendBundle\Entity\Following;                   // Da acceso a la Entidad Following
  use BackendBundle\Entity\User;                        // Da acceso a la Entidad User
/******************************************************************************************/
class FollowingController extends Controller{
/* OBJETO SESSIÓN
 * Para activar las sesiones inicializamos la variable e incluimos
 * en ella el objeto Session()
 * No olvidar dar acceso al componenete de Symfony
 * Session() permitirá usar los mensajes FLASHBAG
 */
  private $session;
  public function __construct(){
     $this->session = new Session();
  }
/********************************************************************/
/* MÉTODO EJECUCIÓN AJAX PARA HACER FOLLOW ***************************/
  public function followAction(Request $request){
      // Capturamos los datos de nuestro usuario con el que estamos logueados
     $user = $this->getUser();
     // Almaceno en la variable $followed_id al usuario que voy a seguir
     $followed_id = $request->get('followed');
     $em = $this->getDoctrine()->getManager();// Cargo el Entity Manager
     // Cargo el repositorio de la entidad Usuario
     $user_repo = $em->getRepository('BackendBundle:User');
     // Busco al usuario al que queremos seguir
     $followed = $user_repo->find($followed_id);
     $following = new Following();// Creo un objeto a partir de la entidad
     $following->setUser($user); // Usuario que va a seguir
     $following->setFollowed($followed);// Usuario Seguido
     $em->persist($following);// Persistimos la query
     $flush = $em->flush();// Incluimos los datos en la BD
     if($flush == null){
        $status = "Ahora estás siguiendo a este usuario!!";
     }else{
        $status = "No se ha podido seguir a este usuario";
     }
     // para usar Response es necesario incluir el componente
     return new Response($status);
  }
/********************************************************************/

/* MÉTODO EJECUCIÓN AJAX PARA HACER UNFOLLOW *************************/
  public function unfollowAction(Request $request){
      // Capturamos los datos de nuestro usuario con el que estamos logueados
     $user = $this->getUser();
     // Almaceno en la variable $followed_id al usuario que voy a seguir
     $followed_id = $request->get('followed');
     $em = $this->getDoctrine()->getManager();// Cargo el Entity Manager
     // Sacamos el objeto que tiene el seguidor y seguido
     $following_repo = $em->getRepository('BackendBundle:Following');
     // Sacamos el registro de la tabla con las siguientes condiciones
     $followed = $following_repo->findOneBy(array(
       'user'=>$user,
       'followed'=>$followed_id
     ));
     $em->remove($followed);// Elimina el registro del EM
     $flush = $em->flush();// Incluimos los datos en la BD
     if($flush == null){
        $status = "Has dejado de Seguir a este usuario!!";
     }else{
        $status = "No has dejado de Seguir a este usuario";
     }
     // para usar Response es necesario incluir el componente
     return new Response($status);
  }
/********************************************************************/

}
