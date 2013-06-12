<?php

namespace Tfg\JadBundle\Form\GestionClaves;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Tfg\UsuarioBundle\Entity\Usuario;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\From;


class TemaAbiertoType extends AbstractType
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
        $builder->add('usuario', 'entity', array(
            'class' => 'UsuarioBundle:Usuario',
            'required' => false,
            'query_builder' => function(EntityRepository $er) use($options){
                                       return $er->createQueryBuilder('usuario')
                                       ->innerJoin('JadBundle:JadUsuarioRol', 'jur','WITH', 'usuario = jur.usuario')
                                       ->where('jur.jad = ?1')
                                       ->setParameter('1', $options['jad']);

                                },
            'empty_value' => 'Elige un usuario'
        ));
        $builder->add('solucion', 'textarea' ,array(
                 'required' => false));
        $builder->add('fechaLimite','date',array(
                 'widget' => 'single_text',
                 'format' => 'dd-MM-yyyy',
                 'attr' => array('class' => 'date'),
                 'required' => false));
        $builder->add('finalizado', 'checkbox' ,array(
                 'required' => false));
    }

    public function getName()
    {
        $a = $this->id;
        return "temaabierto".$a;
    }

    public function getId(){
        return $this->id;
    }

    public function getDefaultOptions(array $options)
	{
    return array(
    	'data_class' => 'Tfg\JadBundle\Entity\TemaAbierto',
        'jad' => null
    	);
	}
}