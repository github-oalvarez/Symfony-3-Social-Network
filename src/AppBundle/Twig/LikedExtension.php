<?php
/* IMPORTANTE !!!!!!
 * No olvidar incluir la extensión de TWIG dentro de 'app\config\services.yml'
 */
namespace AppBundle\Twig;
// Activo 'RegistryInterface' para poder usar Doctrine
use Symfony\Bridge\Doctrine\RegistryInterface;

class LikedExtension extends \Twig_Extension{
/* CARGAMOS DOCTRINE **************************************************************/
  // Para usar 'Doctrine' necesiatmos de 'RegistryInterface'
  protected $doctrine;

  public function __construct(RegistryInterface $doctrine){
    $this->doctrine = $doctrine;
  }
/**********************************************************************************/
/* DEFINIMOS NOMBRE DEL FILTRO + FUNCIÓN FILTRO ***********************************/
  public function getFilters() {
		return array(
      /*
       * indicamos como llamaremos al filtro 'Liked'
       * y que función ejecutará el filtro `likedFilter`
       */
       new \Twig_SimpleFilter('liked', array($this, 'likeFilter'))
 		);
 	}
/**********************************************************************************/
/* FUNCIÓN FILTRO *****************************************************************/
  public function likeFilter($user, $publication){
    // cargamos el repositorio de Like
    $like_repo = $this->doctrine->getRepository('BackendBundle:Like');
    /*
     * buscamos un registro que comparta el $user y la $publication
     * introducida en el método
     */
    $publication_liked = $like_repo->findOneBy(array(
 			"user" => $user,
 			"publication" => $publication
 		));
    // Si no está vacio
    if(!empty($publication_liked) && is_object($publication_liked)){
			$result = true;
		}else{
			$result = false;
		}
		return $result;
	}
/**********************************************************************************/
/* DEFINIMOS LA FUNCIÓN ***********************************************************/
	public function getName() {
		return 'liked_extension';
	}
/**********************************************************************************/
}
