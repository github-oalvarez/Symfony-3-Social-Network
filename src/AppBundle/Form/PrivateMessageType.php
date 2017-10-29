<?php

// namespace BackendBundle\Form;
/* Cambiamos el namespace al cambiar el Bundle                     ************************/
namespace AppBundle\Form;
/******************************************************************************************/
use Symfony\Component\Form\AbstractType;                // Clase necesario para AbstractType
use Symfony\Component\Form\FormBuilderInterface;        // Clase necesario para AbstractType
use Symfony\Component\OptionsResolver\OptionsResolver;  // Clase necesario para AbstractType
/* Añadimos los componentes que permitirán el uso de nuevas clases ************************/
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\TextareaType;
  use Symfony\Component\Form\Extension\Core\Type\FileType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/*
 * EntityType permite mostrar en el formulario un listado de opciones procedente
 * de otro formulario
 */
 use Symfony\Bridge\Doctrine\Form\Type\EntityType;     // Campo Tipo EntityType
/******************************************************************************************/
class PrivateMessageType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options){
      /*
       * Usamos la propiedad 'empty_data' para pasar la variable
       * ( viene de 'src\AppBundle\Controller\PrivateMessageController.php',
       * 'src\AppBundle\form\PrivateMessageType.php',
       * 'src\BackendBundle\Repository\UserRepository.php' y
       * 'src\BackendBundle\Resources\config\doctrine\user.orm.yml')
       */
		  $user = $options['empty_data'];
      $builder
      // 'receiver' será el nombre de la columna de la base de datos
      /*
       * Tendrá un listado con los distintos posibles usuarios a los
       * que enviarle los mensajes.
       */
      /* SELECTOR LISTA */
			->add('receiver', EntityType::class,array(
				'class' => 'BackendBundle:User',
        /* Indicamos la query de dónde saldrá la información del selector
         * usamos el $er (entity repository) con la entrada del usuario logueado
         * y la función de 'src\BackendBundle\Repository\UserRepository.php'
         * getFollowingUsers($user)
         */
				'query_builder' => function($er) use($user){
				//	return $er->screateQueryBuilder('u'); // Sacar todos los usuarios
          return $er->getFollowingUsers($user);
				},
				'choice_label' => function($user){
          // mostrará dentro de las opciones la siguiente información....
					return $user->getName()." ".$user->getSurname()." - ".$user->getNick();
				},
				'label' => 'Para:',
				'attr' => array('class' =>'form-control')
			))
      // 'message' será el nombre de la columna de la base de datos
			->add('message', TextareaType::class, array(
				'label' => 'Mensaje',
				'required' => 'required',
				'attr' => array('class' =>'form-control')
			))
      // 'image' será el nombre de la columna de la base de datos
			->add('image', FileType::class, array(
				'label' => 'Imagen',
				'required' => false,
				'data_class' => null,
				'attr' => array('class' => 'form-control')
			))
      // 'file' será el nombre de la columna de la base de datos
			->add('file', FileType::class, array(
				'label' => 'Archivo',
				'required' => false,
				'data_class' => null,
				'attr' => array('class' => 'form-control')
			))
      // Enviamos el formulario
      ->add('Enviar', SubmitType::class, array(
				"attr" => array("class" => "btn btn-success")
			));
    }
    /**************************************************************************************/
    /* DEFINIMOS LA ENTIDAD DONDE SE INCLUIRAN LOS DATOS EN LA BD *************************/
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array('data_class' => 'BackendBundle\Entity\PrivateMessage'));
    }
    /*************************************************************************************/
}
