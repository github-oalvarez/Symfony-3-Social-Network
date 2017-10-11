<?php
/*
 * No olvidar incluir la extensión de TWIG dentro de 'app\config\services.yml'
 */
namespace AppBundle\Twig;
// Activo 'RegistryInterface' para poder usar Doctrine
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserStatsExtension extends \Twig_Extension{
  /*
   * Para usar 'Doctrine' necesiatmos de 'RegistryInterface'
   */
  protected $doctrine;

  public function __construct(RegistryInterface $doctrine){
    $this->doctrine = $doctrine;
  }
  /*********************************************************/
  /* Función que hará de filtro */
  public function getFilters(){
    return array(
      /*
       * indicamos como llamaremos al filtro 'users_stats'
       * y que función ejecutará el filtro `userStatsFilter`
       */
      new \Twig_SimpleFilter('user_stats', array($this, 'userStatsFilter'))
    );
  }
  public function userStatsFilter($user){
    // cargamos el repositorio de Like
    $like_repo = $this->doctrine->getRepository('BackendBundle:Like');
    $following_repo = $this->doctrine->getRepository('BackendBundle:Following');
    $publication_repo = $this->doctrine->getRepository('BackendBundle:Publication');
    /*
     * buscamos un registro que comparta el $user y la $publication
     * introducida en el método
     */
     // extraemos todos los seguidos por el $user logueado
     $user_following = $following_repo->findBy(array('user'=>$user));
     // extraemos todos los seguidores del $user logueado
     $user_followers = $following_repo->findBy(array('followed'=>$user));
     // extraemos todas las publicaciones del $user logueado
     $user_publications = $publication_repo->findBy(array('user'=>$user));
     // extraemos los likes
     $user_likes = $like_repo->findBy(array('user'=>$user));
     $result = array (
       'following'=>count($user_following),
       'followers'=>count($user_followers),
       'publications'=>count($user_publications),
       'likes'=>count($user_likes)
     );
     return $result;
   }
  /*********************************************************/
  public function getName(){
    return 'user_stats_extension';
  }
}
