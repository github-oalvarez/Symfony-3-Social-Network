<?php
// namespace BackendBundle\Form;

/* Cambiamos el namespace al cambiar el Bundle *****************************************************/
  namespace AppBundle\Controller;
/***************************************************************************************************/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/* Añadimos los componentes que permitirán el uso de nuevas clases *********************************/
  use Symfony\Component\HttpFoundation\Response;           // Permite usar el método Response
  use Symfony\Component\HttpFoundation\Session\Session;    // Permite usar sesiones
  use AppBundle\Form\PrivateMessageType;                   // Da acceso al Formulario PrivateMessageType
  use BackendBundle\Entity\User;                           // Da acceso a la Entidad User
  use BackendBundle\Entity\PrivateMessage;                 // Da acceso a la Entidad PrivateMessage
/***************************************************************************************************/
class PrivateMessageController extends Controller {
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
/***************************************************************************************************/
/* MÉTODO PARA LA HOME MENSAJES PRIVADOS ***********************************************************/
	public function indexAction(Request $request) {
    // Cargamos el Entity Manager de Doctrine
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();
    // Creamos un objeto PrivateMessage()
		$private_message = new PrivateMessage();
    // Creamos el formulario
		$form = $this->createForm(PrivateMessageType::class,
      $private_message,
      array(
          /*
           * Usamos la propiedad 'empty_data' para pasar la variable
           * ( viene de 'src\AppBundle\Controller\PrivateMessageController.php',
           * 'src\AppBundle\form\PrivateMessageType.php',
           * 'src\BackendBundle\Repository\UserRepository.php' y
           * 'src\BackendBundle\Resources\config\doctrine\user.orm.yml')
           */
			     'empty_data' => $user
		));
    // Pasamos los datos de la petición del formulario y los pase a la entidad
		$form->handleRequest($request);
    // Comprobamos si se envió el Mensaje y si fué válido
		if($form->isSubmitted() && $form->isValid() ){
      /*********************************************************************************************/
      /* SUBIMOS LA IMAGEN *************************************************************************/
			$file = $form['image']->getData();
      if( !empty($file) && $file != null ){
        //capturamos la extensión del fichero
        $ext = $file->guessExtension();
      }
			if(
        !empty($file)
        && $file != null
        && ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif')){
          // comprobamos la extensión del fichero
          // Damos nombre al archivo
          $file_name = $user->getId().time().".".$ext;
          // Subimos el archivo al hosting
          $file->move("uploads/messages/images", $file_name);
          // Guardamos el nombre del fichero en la BD
				  $private_message->setImage($file_name);
			}else{
          // Guardamos el nombre del fichero en la BD
				  $private_message->setImage(null);
		  }
      /*********************************************************************************************/
      /* SUBIMOS EL DOCUMENTO **********************************************************************/
			$doc = $form['file']->getData();
      if( !empty($doc) && $doc != null ){
        //capturamos la extensión del fichero
        $ext = $doc->guessExtension();
      }
      // comprobamos la extensión del fichero
      if(
        !empty($doc)
        && $doc != null
        && ($ext == 'pdf')){
					// Damos nombre al archivo
          $file_name = $user->getId().time().".".$ext;
          // Subimos el archivo al hosting
					$doc->move("uploads/messages/documents", $file_name);
          // Guardamos el nombre del fichero en la BD
					$private_message->setFile($file_name);
			}else{
        // Guardamos el nombre del fichero en la BD como null (vacío)
				$private_message->setFile(null);
			}
      // subimos los datos usando los setters
			$private_message->setEmitter($user); // usuario logueado
		  $private_message->setCreatedAt(new \DateTime("now"));
			$private_message->setReaded(0);
      // persistimos los datos dentro de Doctirne
		  $em->persist($private_message);
      // guardamos los datos persistidos dentro de la BD
			$flush = $em->flush();
      // Si se guardan correctamente los datos en la BD
			if($flush == null){
					$status = "El mensaje privado se ha enviado correctamente !!";
				}else{
			    $status = "El mensaje privado no se ha enviado";
			}
    }
    /* MOSTRAR FLASHBAG ****************************************************************************/
    // Si seenvió el formulario mostrar las FlashBag
    if($form->isSubmitted()){
      // generamos los mensajes FLASH (necesario activar las sesiones)
			$this->session->getFlashBag()->add("status", $status);
			return $this->redirectToRoute("private_message_index");
		}
    /***********************************************************************************************/
		$private_messages = $this->getPrivateMessages($request);
		$this->setReaded($em, $user);
		return $this->render('AppBundle:PrivateMessage:index.html.twig', array(
        // Pasamos el formulario a la vista
        'form' => $form->createView(),
			  'pagination' => $private_messages
		));
	}
/***************************************************************************************************/
/* MÉTODO PARA LA HOME MENSAJES PRIVADOS ENVIADOS***************************************************/
// Necesita de la función 'public function getPrivateMessages($request, $type = null)'
	public function sendedAction(Request $request){
		$private_messages = $this->getPrivateMessages($request, "sended");

		return $this->render('AppBundle:PrivateMessage:sended.html.twig',array(
			'pagination' => $private_messages
		));
	}
/***************************************************************************************************/
/* MÉTODO PARA CAPTURAR LOS MENSAJES PRIVADOS ENVIADOS**********************************************/
	public function getPrivateMessages($request, $type = null){
    // Cargamos el Entity Manager
		$em = $this->getDoctrine()->getManager();
    // cargamos el objeto user (logueado)
		$user =	$this->getUser();
    // extraemos el id del usuario logueado
		$user_id = $user->getId();
    /*
     * Si el tipo es "sended" enviados mostrará los mensajes enviados
     * Si el tipo es "recived" enviados mostrará los mensajes recibidos
     */
		if($type == "sended"){
			$dql = "SELECT p FROM BackendBundle:PrivateMessage p WHERE"
					. " p.emitter = $user_id ORDER BY p.id DESC";
		}else{
			$dql = "SELECT p FROM BackendBundle:PrivateMessage p WHERE"
					. " p.receiver = $user_id ORDER BY p.id DESC";
		}
    // Cargamos la Query dentro del Entity Manager
		$query = $em->createQuery($dql);
    // Iniciamos el paginador
    $paginator = $this->get('knp_paginator');
    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), //inicio
        5 // número de publicaciones por página
      );
		return $pagination;
	}
/***************************************************************************************************/
/* MÉTODO AUXILIAR CONTAR MENSAJES NO LEIDOS *******************************************************/
	public function notReadedAction(){
    // Cargamos el Entity Manager
		$em = $this->getDoctrine()->getManager();
    // cargamos el objeto user (logueado)
		$user = $this->getUser();
		$private_message_repo = $em->getRepository('BackendBundle:PrivateMessage');
		$count_not_readed_msg = count($private_message_repo->findBy(array(
			'receiver' => $user,
			'readed'   => 0
		)));
		return new Response($count_not_readed_msg);
	}
/***************************************************************************************************/
/* MÉTODO AUXILIAR CONVERTIR MENSAJES NO LEIDOS EN LEIDOS*******************************************/
	private function setReaded($em, $user){
		$private_message_repo = $em->getRepository('BackendBundle:PrivateMessage');
		$messages = $private_message_repo->findBy(array(
			'receiver' => $user,
			'readed'   => 0
		));
		foreach($messages as $msg){
			$msg->setReaded(1);
			$em->persist($msg);
		}
		$flush = $em->flush();
		if($flush == null){ $result = true; }else{ $result = false; }
		return $result;
	}
/***************************************************************************************************/
}
