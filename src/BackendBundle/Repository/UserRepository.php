<?php

namespace BackendBundle\Repository;
/*
 * Usamos la propiedad 'empty_data' para pasar la variable
 * ( viene de 'src\AppBundle\Controller\PrivateMessageController.php',
 * 'src\AppBundle\form\PrivateMessageType.php',
 * 'src\BackendBundle\Repository\UserRepository.php' y
 * 'src\BackendBundle\Resources\config\doctrine\user.orm.yml')
 */
class UserRepository extends \Doctrine\ORM\EntityRepository {

	public function getFollowingUsers($user){
    // Cargo el Entity Manager dentro de la varibale $em
		$em = $this->getEntityManager();
    // Cargo el repositorio de seguidores a partir de la entidad
		$following_repo = $em->getRepository('BackendBundle:Following');
    // Filtro el repositorio según los follows que hacemos
		$following = $following_repo->findBy(array('user' => $user));
    // Creo el array con la búsqueda
		$following_array = array();
		foreach($following as $follow){
			$following_array[] = $follow->getFollowed();
		}
		$user_repo = $em->getRepository('BackendBundle:User');
    // 'u' represntará a la tabla de usuarios
		$users = $user_repo->createQueryBuilder('u')
      /*
       * extraer cuando : el usuario sea distinto al logeado
       * y coincida con el listado de following
       */
			->where("u.id != :user AND u.id IN (:following)")
      // indicamos los valores de la búsqueda 'user' y 'following'
			->setParameter('user', $user->getId())
			->setParameter('following', $following_array)
      // indicamos el orden
      /* ORDENAMOS POR APELLIDO
      ->orderBy('u.surname', 'DESC');
      */
      ->orderBy('u.id', 'DESC');
    // Regresamos el listado con la búsqueda.
		return $users;
	}

}
