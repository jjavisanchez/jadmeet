<?php

namespace Tfg\JadBundle\Form\GestionJad;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class JadType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
       {
               $builder
               ->add('nombre')
               ->add('nombreProyecto')
               ->add('propositos')
               ->add('alcance')
               ->add('objetivosDireccion')
               ;
        }

    public function getName()
       {
           return 'jadtype';
       }
}

?>