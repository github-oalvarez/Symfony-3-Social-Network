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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/******************************************************************************************/
class RegisterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          // 'name' será el nombre de la columna de la base de datos
          ->add('name', TextType::class, array(
            'label'=>'Nombre',
            'required'=>'required',
            'attr'=>array(
              'class'=>'form-name form-control'
            )
          ))
          ->add('surname', TextType::class, array(
            'label'=>'Apellido',
            'required'=>'required',
            'attr'=>array(
              'class'=>'form-surname form-control'
            )
          ))
          ->add('nick', TextType::class, array(
            'label'=>'Nick',
            'required'=>'required',
            'attr'=>array(
              'class'=>'form-nick form-control nick-input'
            )
          ))
          ->add('email', EmailType::class, array(
            'label'=>'Correo electrónico',
            'required'=>'required',
            'attr'=>array(
              'class'=>'form-email form-control'
            )
          ))
          ->add('password', PasswordType::class, array(
            'label'=>'Contraseña',
            'required'=>'required',
            'attr'=>array(
              'class'=>'form-password form-control'
            )
          ))
          ->add('Registrarse',SubmitType::class, array(
            "attr"=>array(
              "class"=>"form-submit btn btn-success"
            )
          ));
//          ->add('role')->add('bio')->add('active')->add('image');
    }

    /**************************************************************************************/
    /* DEFINIMOS LA ENTIDAD DONDE SE INCLUIRAN LOS DATOS EN LA BD *************************/
    public function configureOptions(OptionsResolver $resolver)    {
        $resolver->setDefaults(array('data_class' => 'BackendBundle\Entity\User'));
    }
}
