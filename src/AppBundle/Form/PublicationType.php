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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/******************************************************************************************/
class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        // 'text' será el nombre de la columna de la base de datos
        ->add('text', TextareaType::class, array(
				'label' => 'Mensaje',
				'required' => 'required',
				'attr' => array(
					'class' => 'form-control'
				)
			))
			->add('image', FileType::class, array(
				'label' => 'Foto',
				'required' => false,
				'data_class' => null,
				'attr' => array(
					'class' => 'form-control'
				)
			))
			->add('document', FileType::class, array(
				'label' => 'Documento',
				'required' => false,
				'data_class' => null,
				'attr' => array(
					'class' => 'form-control'
				)
			))
            ->add('Enviar', SubmitType::class, array(
				"attr" => array(
					"class" => "btn btn-success"
				)
			))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BackendBundle\Entity\Publication'
        ));
    }
}
