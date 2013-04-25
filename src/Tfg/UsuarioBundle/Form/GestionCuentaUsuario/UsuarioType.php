<?php

namespace Tfg\UsuarioBundle\Form\GestionCuentaUsuario;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UsuarioType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
       {
               $builder
               ->add('nombre')
               ->add('apellidos')
               ->add('email','email')
               ->add('password', 'repeated', array(
                     'type' => 'password',
                     'invalid_message' => 'Las dos contraseñas deben coincidir',
                     'options' => array('label' => 'Contraseña'),
                ))
               ->add('fecha_nacimiento','date',array(
                 'widget' => 'single_text',
                 'format' => 'dd-MM-yyyy',
                 'attr' => array('class' => 'date'),
        ))
               /*->add('fecha_nacimiento', 'birthday',array(
                      'widget'=>'choice',
                      'empty_value' => array('year' => 'Año', 'month' => 'Mes', 'day' => 'Día'),
                    )
                )*/
               ->add('ciudad')
               ->add('titulacion')
               ->add('perfil')
               ;
        }

    public function getName()
       {
           return 'usuariotype';
       }
}

?>
