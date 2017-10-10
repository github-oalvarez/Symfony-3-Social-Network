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
/******************************************************************************************/
class PublicationController extends Controller
{

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

/* MÉTODO PARA LA HOME  *********************************************/
  public function indexAction(Request $request){
		$em = $this->getDoctrine()->getManager();
    $user = $this->getUser(); //capturamos el usuario
		// Creamos un nuevo objeto Publication
		$publication = new Publication();
		$form = $this->createForm(PublicationType::class, $publication);

    $form->handleRequest($request);
    // Plantemos los distinos escenarios
    if($form->isSubmitted() && $form->isValid()){
      // Subimos la imagen
      $file = $form['image']->getData();
      if(!empty($file) && $file != null){
        $ext = $file->guessExtension();//capturamos la extensión del fichero
        // comprobamos la extensión del fichero
        if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif'){
          // Damos nombre al archivo
          $file_name = $user->getId().time().".".$ext;
          // Subimos el archivo al hosting
          $file->move("uploads/publications/images", $file_name);
          // Guardamos el nombre del fichero en la BD
          $publication->setImage($file_name);
        }else{
          $publication->setImage(null);
        }
      }else{
        $publication->setImage(null);
      }
      // Subimos los documentos
      $doc = $form['document']->getData();
      if(!empty($doc) && $doc!=null){
        $ext = $doc->guessExtension(); //capturamos la extensión del fichero
        // comprobamos la extensión del fichero
        if($ext=='pdf'){
          // Damos nombre al archivo
          $file_name=$user->getId().time().'.'.$ext;
          // Subimos el archivo al hosting
          $doc->move('uploads/publications/documents',$file_name);
          // Guardamos el nombre del fichero en la BD
          $publication->setDocument($file_name);
        }else{
          $publication->setDocument(null);
        }
      }else{
        $publication->setDocument(null);
      }
      // subimos los datos usando los setters
      $publication->setUser($user);
      $publication->setCreatedAt(new \DateTime("now"));
      // persistimos los datos dentro de Doctirne
      $em->persist($publication);
      // guardamos los datos persistidos dentro de la BD
      $flush = $em->flush();
      // Si se guardan correctamente los datos en la BD
      if($flush == null){
          $status = 'Has subido la publicación correctamente!';
      }else{
          $status = 'No has subido la publicación correctamente!';
      }
    }else{
      $status = 'La publicación no se ha creado, porque el formulario no es válido';
    }
    // Si seenvió el formulario mostrar las FlashBag
    if($form->isSubmitted()){
      // generamos los mensajes FLASH (necesario activar las sesiones)
      $this->session->getFlashBag()->add('status', $status);
      return $this->redirectToRoute('home_publication');
    }
    /*
     * Cargamos el método auxiliar que lista las publicaciones
     * 'getPublications'
     */
    $publications = $this->getPublications($request);
 		return $this->render('AppBundle:Publication:home.html.twig',array(
 			'form' => $form->createView(),
 			'pagination' => $publications
 		));
	}
/********************************************************************/

/* MÉTODO AUXILIAR PARA EXTRAER LAS PUBLICACIONES PUBLICADAS ********/
  /*
  USE curso_social_network;
  SELECT text FROM publications WHERE user_id = 6
    OR user_id IN (SELECT followed FORM following WHERE user = 6)
  * (SELECT followed FORM following WHERE user = 6) extrae el listado
  * de usuarios a los que sigo
  */
  public function getPublications($request){
    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();
    // Extraemos el repositorio
    $publications_repo = $em->getRepository('BackendBundle:Publication');
    $following_repo = $em->getRepository('BackendBundle:Following');
    // Preparamos la query
    /*
    SELECT text FROM publications WHERE user_id = 6
      OR user_id IN (SELECT followed FORM following WHERE user = 6)
     */
    // extraigo del repositorio todos los usuarios a los que sigo
    $following = $following_repo->findBy(array('user'=>$user));
    $following_array = array();
    foreach($following as $follow){
        // genero el array con los usuarios a los que sigo
        $following_array[]=$follow->getFollowed();
    }
    // Buscamos una coincidencia dentro de la base de datos
    $query = $publications_repo->createQueryBuilder('p')
        ->where('p.user = (:user_id) OR p.user IN(:following)')
        ->setParameter('user_id', $user->getId())
        ->setParameter('following', $following_array)
        ->orderBy('p.id','DESC')
        ->getQuery();
    // Iniciamos el paginador
    $paginator = $this->get('knp_paginator');
    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), //inicio
        5 // número de publicaciones por página
      );
    // devolvemos la variable '$paginator'
    return $pagination;
  }
/********************************************************************/

/* MÉTODO AJAX PARA ELIMINAR LAS PUBLICACIONES PUBLICADAS ***********/
  public function removePublicationAction(Request $request, $id = null){
    // Extraemos el entity manager
		$em = $this->getDoctrine()->getManager();
    // Extraemos el repositorio de las publicaciones de su entidad
		$publication_repo = $em->getRepository('BackendBundle:Publication');
    // Buscamos la publicación que entra por la url 'id'
		$publication = $publication_repo->find($id);
    // Obtenemos el id de nuestro usuario
		$user = $this->getUser();
    /*
     * Creamos la condición que permita borrar solo las publicaciones que
     * hemos creado con nuestro usuario
     */
		if($user->getId() == $publication->getUser()->getId()){
      // Eliminamos el objeto dentro de doctrine
			$em->remove($publication);
      // persistimos la eliminación dentro de la bD
			$flush = $em->flush();
      // preparamos los mensajes informativos según cada casuistica
			if($flush == null){
				$status = 'La publicación se ha borrado correctamente';
			}else{
				$status = 'La publicación no se ha borrado';
			}

		}else{
			$status = 'La publicación no se ha borrado';
		}
    // para usar el objeto response es necesario cargarlo al ppio del controlador
		return new Response($status);
	}
/********************************************************************/
}
