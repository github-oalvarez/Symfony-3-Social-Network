<?php
/* IMPORTANTE !!!!!!
 * No olvidar incluir la extensión de TWIG dentro de 'app\config\services.yml'
 */
namespace AppBundle\Twig;
// Activo 'RegistryInterface' para poder usar Doctrine
use Symfony\Bridge\Doctrine\RegistryInterface;

class GetUserExtension extends \Twig_Extension{
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
       * indicamos como llamaremos al filtro 'get_user'
       * y que función ejecutará el filtro `getUserFilter`
       */
       new \Twig_SimpleFilter('get_user', array($this, 'getUserFilter'))
 		);
 	}
/**********************************************************************************/
/* FUNCIÓN FILTRO *****************************************************************/
  public function getUserFilter($user_id){
    // cargamos el repositorio de Like
    $user_repo = $this->doctrine->getRepository('BackendBundle:User');
    /*
     * buscamos un registro que comparta el $user_id introducida en el método
     */
    $user = $user_repo->findOneBy(array(
 			"id" => $user_id,
 		));
    // Si no está vacio
    if(!empty($user) && is_object($user)){
			$result = $user;
		}else{
			$result = false;
		}
		return $result;
	}
/**********************************************************************************/
/* DEFINIMOS LA FUNCIÓN ***********************************************************/
	public function getName() {
		return 'get_user_extension';
	}
/**********************************************************************************/
}
