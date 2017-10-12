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
  use BackendBundle\Entity\User;                        // Da acceso a la Entidad Usuario
  use AppBundle\Form\RegisterType;                      // Da acceso al Formulario RegisterType
  use AppBundle\Form\UserType;                          // Da acceso al Formulario UserType
/******************************************************************************************/
class UserController extends Controller{
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
/* MÉTODO PARA EL LOGIN *********************************************/
  public function loginAction(Request $request){
    /* si existe el objeto User nos rediriges a home            */
    if( is_object($this->getUser()) ){
      return $this->redirect('home');
    }
    /************************************************************/
    $authenticationUtils = $this->get('security.authentication_utils');
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();
    return $this->render('AppBundle:User:login.html.twig', array(
        'lastUsername'=>$lastUsername,
        'error'=>$error ));
  }
/********************************************************************/

/* MÉTODO PARA EL REGISTRO DE USUARIO *******************************/
  public function registerAction(Request $request){
    /* si existe el objeto User nos rediriges a home            */
    if(is_object($this->getUser())){
      return $this->redirect('home');
    }
    /************************************************************/
    // Creamos un nuevo objeto User
    $user = new User();
    /*
     * Creamos el formulario a partir de la clase RegisterType,
     * le pasaremos la variable User
     * hay que declarar la clase RegisterType arriba
     */
    $form = $this->createForm(RegisterType::class, $user);
    $form->handleRequest($request);
    // Si se envía y es válido el formulario
    if($form->isSubmitted() && $form->isValid()){
      $em = $this->getDoctrine()->getManager();
      // $user_repo = $em->getTepository("BackendBundle:User");
      // hacemos la consulta
      $query = $em->createQuery('SELECT u FROM BackendBundle:User u WHERE u.email = :email OR u.nick = :nick')
                  ->setParameter('email', $form->get("email")->getData())
                  ->setParameter('nick', $form->get("nick")->getData());
      //extraemos el resultado de la $query
      $user_isset = $query->getResult();
      // Si no hay ningun usuario con ese email y nick
      if(count($user_isset==0)){
        // si el usuario no existe
        $factory = $this->get("security.encoder_factory");
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword(
            $form->get("password")->getData(),
            $user->getSalt()
        );
        // subimos los datos usando los setters
        $user->setPassword($password);
        $user->setRole("ROLE_USER");
        $user->setImage(null);
        // persistimos los datos dentro de Doctirne
        $em->persist($user);
        // guardamos los datos persistidos dentro de la BD
        $flush = $em->flush();
        // Si se guardan correctamente los datos en la BD
        if($flush == null){
            $status = "Te has registrado correctamente!";
        }else{
            $status = "No te has registrado correctamente";
        }
      }else{
        // si el usuario existe
        $status = "El usuario ya existe!!";
      }
    }else{
      $status = "No te has registrado correctamente !!";
    }
    if($form->isSubmitted()){
      // generamos los mensajes FLASH (necesario activar las sesiones)
      $this->session->getFlashBag()->add("status", $status);
      return $this->redirect("login");
    }
    // enviamos la vista con el html del formulario ($form)
    return $this->render('AppBundle:User:register.html.twig', array(
      'form'=>$form->createView()
    ));
  }
/********************************************************************/

/* MÉTODO PARA LA CONSULTA AJAX (¿EXISTE EL NICK DE REGISTRO?) */
  public function nickTestAction(Request $request){
    // Guardamos dentro de la variable $nick el dato que nos llega por POST
    $nick = $request->get('nick');
    // Busco dentro de la BD el dato
    $em = $this->getDoctrine()->getManager();
  	$user_repo = $em->getRepository('BackendBundle:User');
  	$user_isset = $user_repo->findOneBy(array('nick' => $nick));
    $result = 'used';
  	if (count($user_isset) >= 1 && is_object($user_isset)) {
  		$result = 'used';
  	} else {
  		$result = 'unused';
  	}
    // Para usar el método response es necesario cargar el componente
    return new Response($result);
  }
/********************************************************************/

/* MÉTODO PARA CONFIGURAR EL PERFIL DE USUARIO */
  public function editUserAction(Request $request){
    //  al cargar la variable user se cargarán los datos existentes dentro del formulario.
    $user = $this->getUser();
    // Declaramos la imagen que subimos al formulario
    $user_image=$user->getImage();
    // Creamos el formulario
    $form = $this->createForm(UserType::class, $user);
    /* Enlazamos la información de la request cuando nosotros enviamos el formulario
    sobreescribiendo el objeto $user*/
    $form->handleRequest($request);
    // Si el formulairo se ha enviado y es válido
    if($form->isSubmitted()){
      if($form->isValid()){
        $em = $this->getDoctrine()->getManager();
        // Hacemos la consulta
        $query = $em->createQuery('SELECT u FROM BackendBundle:User u WHERE u.email = :email OR u.nick = :nick')
                    ->setParameter('email', $form->get("email")->getData())
                    ->setParameter('nick', $form->get("nick")->getData());
        //extraemos el resultado de la $query
        $user_isset = $query->getResult();
        /*
         * Si el email Y el nick del usuario seteados son iguales a los existentes
         * O
         * No hay ningun usuario con ese email y nick entonces...
         */
        if(  ( count($user_isset) == 0)
          ||
          ($user->getEmail() == $user_isset[0]->getEmail() && $user->getNick() == $user_isset[0]->getNick() )
        ){
          // Upload file
          $file = $form["image"]->getData();
          if(!empty($file) && $file !=null){
            // extraemos la extensión del fichero
            $ext = $file->guessExtension();
            if($ext=='jpg' || $ext=='jpeg' || $ext=='png' || $ext=='gif'){
              // renombramos el archivo con el idUser+fecha+extensión
              $file_name = $user->getId().time().'.'.$ext;
              // movemos el fichero
              $file->move("uploads/users",$file_name);
              $user->setImage($file_name);
            }
          }else{
            $user->setImage($user_image);
          }
          // persistimos los datos dentro de Doctirne
          $em->persist($user);
          // guardamos los datos persistidos dentro de la BD
          $flush = $em->flush();
          // Si se guardan correctamente los datos en la BD
          if($flush == null){
            $status = "Has modificado tus datos correctamente!";
          }else{
            $status = "No has modificado tus datos correctamente";
          }
        }else{
          // si el usuario existe
          $status = "El usuario ya existe!!";
        }
      }else{
        $status = "No se han actualizado tus datos correctamente";
      }
      // generamos los mensajes FLASH (necesario activar las sesiones)
      $this->session->getFlashBag()->add("status", $status);
      return $this->redirect('my-data');
    }
    // Enviamos el formulario y su vista a la plantilla TWIG
    return $this->render('AppBundle:User:editUser.html.twig', array(
      "form"=>$form->createView()
    ));
  }
/********************************************************************/

/* MÉTODO PARA LISTAR USUARIOS */
  public function usersAction(Request $request){
    /* si existe el objeto User nos rediriges a home            */
    if( !is_object($this->getUser()) ){
      return $this->redirect('home');
    }
    /************************************************************/
    $em = $this->getDoctrine()->getManager();
    $dql = "SELECT u FROM BackendBundle:User u ORDER BY u.id ASC";
    $query = $em->createQuery($dql);
    $paginator = $this->get('knp_paginator');
    $pagination = $paginator->paginate(
      $query,
      $request->query->getInt('page',1),
      5 );
    return $this->render('AppBundle:User:users.html.twig', array('pagination'=>$pagination));
  }
/********************************************************************/

/* MÉTODO PARA BUSCAR USUARIOS */
  public function searchAction(Request $request){
    /* si existe el objeto User nos rediriges a home            */
    if( !is_object($this->getUser()) ){
      return $this->redirect('home');
    }
    /************************************************************/
    $em = $this->getDoctrine()->getManager();
    // usamos 'trim' para limpiar los espacios por delante ypor detrás
    $search = trim($request->query->get("search", null));
    if($search==null){
      return $this->redirect($this->generateURL('home_publications'));
    }
    $dql = "SELECT u FROM BackendBundle:User u
      WHERE u.name LIKE :search
      OR u.surname LIKE :search
      OR u.nick LIKE :search
      ORDER BY u.id ASC";
    // Buscamos una coincidencia dentro de la base de datos
    $query = $em->createQuery($dql)->setParameter('search', "%$search%");
    // Iniciamos el paginador
    $paginator = $this->get('knp_paginator');
    $pagination = $paginator->paginate(
      $query,
      $request->query->getInt('page',1),
      5 );
    // Devolvemos la vista con la información generado por el paginador
    return $this->render('AppBundle:User:users.html.twig', array('pagination'=>$pagination));
  }
  /********************************************************************/

  /* MÉTODO PARA MOSTRAR EL PERFIL DE USUARIO */
  public function profileAction(Request $request, $nickname = null){
    // Cargo Entity Manager de Doctrine dentro de lavariable $em
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
			$user = $this->getUser();
		}
    /*
     * Si el usuario que llega por la url está vacio o no es objeto
     * si existe el objeto User nos rediriges a home
     */
    if (empty($user) || !is_object($user)) {
			return $this->redirect($this->generateUrl('home_publications'));
		}
    /************************************************************/
    // Busco el $id del usuario señalado
    $user_id = $user->getId();
    // Realizo la consulta
		$dql = "SELECT p FROM BackendBundle:Publication p WHERE p.user = $user_id ORDER BY p.id DESC";
    // Cargo la Query de la consulta $dql
		$query = $em->createQuery($dql);
    /*
     * Iniciamos el paginador
     */
    $paginator = $this->get('knp_paginator');
		$publications = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 5
		);
    /************************************************************/
    // Devolvemos la vista con la información generado por el paginador
    return $this->render('AppBundle:User:profile.html.twig', array(
			'user' => $user,
			'pagination' => $publications
		));
  }
  /********************************************************************/

}
