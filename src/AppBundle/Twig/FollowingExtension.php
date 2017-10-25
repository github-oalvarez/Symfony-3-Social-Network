<?php
/* IMPORTANTE !!!!!!
 * No olvidar incluir la extensión de TWIG dentro de 'app\config\services.yml'
 */
namespace AppBundle\Twig;
// Activo 'RegistryInterface' para poder usar Doctrine
use Symfony\Bridge\Doctrine\RegistryInterface;

class FollowingExtension extends \Twig_Extension{
/* CARGAMOS DOCTRINE **************************************************************/
  // Para usar 'Doctrine' necesiatmos de 'RegistryInterface'
  protected $doctrine;

  public function __construct(RegistryInterface $doctrine){
    $this->doctrine = $doctrine;
  }
/**********************************************************************************/
/* DEFINIMOS NOMBRE DEL FILTRO + FUNCIÓN FILTRO ***********************************/
  public function getFilters(){
    return array(
      /*
       * indicamos como llamaremos al filtro `following'
       * y que función ejecutará el filtro `followingFilter`
       */
      new \Twig_SimpleFilter('following', array($this, 'followingFilter'))
    );
  }
/**********************************************************************************/
/* FUNCIÓN FILTRO *****************************************************************/
  public function followingFilter($user, $followed){
    $following_repo = $this->doctrine->getRepository('BackendBundle:Following');
    $user_following = $following_repo->findOneBy(array(
      "user"=>$user,
      "followed"=>$followed
    ));
    if(!empty($user_following) && is_object($user_following)){
      $result=true;
    }else{
      $result=false;
    }
    return $result;
  }
/**********************************************************************************/
/* DEFINIMOS LA FUNCIÓN ***********************************************************/
  public function getName(){
    return 'following_extension';
  }
/**********************************************************************************/
}
