<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/* Añadimos los componentes que permitirán el uso de nuevas clases */
use BackendBundle\Entity\User;                        // Da acceso a la Entidad Usuario
use AppBundle\Form\RegisterType;                      // Da acceso al Formulario Register
use Symfony\Component\HttpFoundation\Session\Session; // Permite usar sesiones
use Symfony\Component\HttpFoundation\Response; // Permite usar el método Response
/*******************************************************************/
class UserController extends Controller
{
/*
 * Para activar las sesiones inicializamos la variable e incluimos
 * en ella el objeto Session()
 * No olvidar dar acceso al componenete de Symfony
 * Session() permitirá usar los mensajes FLASH
 */
    private $session;
    public function __construct(){
      $this->session = new Session();
    }
/*******************************************************************/
    public function loginAction(Request $request)
    {
        return $this->render('AppBundle:User:login.html.twig', array(
            "titulo"=>"Login"));
    }
    public function registerAction(Request $request){
        // Creamos un nuevo objeto User
        $user = new User();
        // Creamos el formulario a partir de la clase RegisterType,
        // le pasaremos la variable User
        // hay que declarar la clase arriba
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
                  // generamos los mensajes FLASH
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
        // generamos los mensajes FLASH
        $this->session->getFlashBag()->add("status", $status);
        // enviamos la vista con el html del formulario ($form)
        return $this->render('AppBundle:User:register.html.twig', array(
            "form"=>$form->createView()
        ));
    }
    public function nickTestAction(Request $request){
        $nick = $request->get("nick");
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getTepository("BackendBundle:User");
        $user_isset = $user_repo->findOneBy(array("nick"=>$nick));
        $result = "used";
        if(count($user_isset >= 1 && is_object($user_isset))){
            $result = "used";
        }else{
            $result = "unused";
        }
        // Para usar el método response es necesario cargar el componente
        return new Response ($result);
    }
}
