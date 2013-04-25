<?php

namespace Tfg\SesionJadBundle\Form\Type;

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
        $builder->add('usuarios', 'entity', array(
            'class' => 'UsuarioBundle:Usuario',

            'query_builder' => function(EntityRepository $er) use($options){
                                       return $er->createQueryBuilder('usuario')
                                       ->innerJoin('SesionJadBundle:SesionesJadUsuarios', 'sjur','WITH', 'usuario = sjur.usuario')
                                       ->where('sjur.sesionJad = ?1')
                                       ->setParameter('1', $options['sesion']);

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
        $builder->add('finalizado');
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
        'sesion' => null
    	);
	}
}