<?php

namespace Tfg\SesionJadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class PuntoAgendaType extends AbstractType

{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre');
        $builder->add('orden');
        $builder->add('duracion');

    }



    public function getName()
    {

        return "puntoAgenda";
    }



    public function getDefaultOptions(array $options)
	{
    return array(
    	'data_class' => 'Tfg\SesionJadBundle\Entity\PuntoAgenda',
    	);
	}
}