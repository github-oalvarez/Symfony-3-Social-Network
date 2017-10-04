<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/* Añadimos los componentes que permitirán el uso de nuevas clases ************************/
use BackendBundle\Entity\User;                        // Da acceso a la Entidad Usuario
use Symfony\Component\HttpFoundation\Session\Session; // Permite usar sesiones
use Symfony\Component\HttpFoundation\Response; // Permite usar el método Response
use AppBundle\Form\RegisterType;                      // Da acceso al Formulario RegisterType
use AppBundle\Form\UserType;                      // Da acceso al Formulario UserType
/******************************************************************************************/
class UserController extends Controller{
/*
 * OBJETO SESSIÓN
 * Para activar las sesiones inicializamos la variable e incluimos
 * en ella el objeto Session()
 * No olvidar dar acceso al componenete de Symfony
 * Session() permitirá usar los mensajes FLASHBAG
 */
    private $session;
    public function __construct(){
      $this->session = new Session();
    }
/*******************************************************************/
/* MÉTODO PARA EL LOGIN */
    public function loginAction(Request $request)
    {
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
/* MÉTODO PARA EL REGISTRO DE USUARIO */
    public function registerAction(Request $request)
    {
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
        if($form->isSubmitted()){
            if($form->isValid()){
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
                        // generamos los mensajes FLASH (necesario activar las sesiones)
                        $this->session->getFlashBag()->add("status", $status);
                        return $this->redirect("login");
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
            // generamos los mensajes FLASH (necesario activar las sesiones)
            $this->session->getFlashBag()->add("status", $status);
        }
        // generamos los mensajes FLASH

        // enviamos la vista con el html del formulario ($form)
        return $this->render('AppBundle:User:register.html.twig', array(
            "form"=>$form->createView()
        ));
    }

/* MÉTODO PARA LA CONSULTA AJAX (¿EXISTE EL NICK DE REGISTRO?) */
    public function nickTestAction(Request $request)
    {
        // Guardamos dentro de la variable $nick el dato que nos llega por POST
        $nick = $request->get("nick");
        // Busco dentro de la BD el dato
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository("BackendBundle:User");
        $user_isset = $user_repo->findOneBy(array("nick"=>$nick));
        $result = "used";
        if(count($user_isset) >= 1 && is_object($user_isset)){
            $result = "used";
        }else{
            $result = "unused";
        }
        // Para usar el método response es necesario cargar el componente
        return new Response ($result);
    }

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

}
