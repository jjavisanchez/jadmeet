<?php

namespace Tfg\SesionJadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class RestriccionType extends AbstractType

{

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;

    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre');
        $builder->add('descripcion');

    }



    public function getName()
    {
        $a = $this->id;
        return "restriccion".$a;
    }

    public function getId(){
        return $this->id;
    }

    public function getDefaultOptions(array $options)
	{
    return array(
    	'data_class' => 'Tfg\JadBundle\Entity\Restriccion',
    	);
	}
}