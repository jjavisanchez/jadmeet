<?php

namespace Tfg\SesionJadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class RestriccionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre');
        $builder->add('descripcion');

    }

    public function getName()
    {
        return 'restriccion';
    }

    public function getDefaultOptions(array $options)
	{
    return array(
    	'data_class' => 'Tfg\JadBundle\Entity\Restriccion',
    	);
	}
}