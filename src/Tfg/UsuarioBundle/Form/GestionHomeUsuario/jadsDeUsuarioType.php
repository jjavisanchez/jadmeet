<?php
namespace Tfg\UsuarioBundle\Form\GestionHomeUsuario;

use Doctrine\ORM\Query\Expr\From;

use Tfg\JadBundle\Entity\JadUsuarioRolRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;

use Tfg\JadBundle\Entity\Jad;
use Tfg\JadBundle\Entity\JadUsuarioRol;
use Tfg\UsuarioBundle\Entity\Usuario;



class jadsDeUsuarioType extends AbstractType {


	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;

	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$factory = $builder->getFormFactory();

		$builder->add('jad', 'entity', array(
    		'class' => 'JadBundle:Jad',
			'property' => 'nombre',
			'query_builder' => function(EntityRepository $er) use($options){
									   return $er->createQueryBuilder('jad')
									   ->innerJoin('JadBundle:JadUsuarioRol', 'jur','WITH', 'jad = jur.jad')
									   ->where('jur.usuario = ?1')
                           			   ->setParameter('1', $options['usuario']);

								},
			'empty_value' => 'Elige un JAD',
			'label' => ' ',
			'attr' => array('class' => 'selector_jad')
		));


		$emReunion = $this->em->getRepository('JadBundle:Reunion');
		$emJadTest = $this->em->getRepository('JadBundle:Jad');

		$refreshReunion = function ($form, $jad) use ($factory, $emReunion, $emJadTest){

				$r = $emReunion->findOneBy(array('jad'=>$jad));

				/*if ($jad instanceof Jad ) {
					$r = $emReunion->findOneBy(array('jad'=>$jad));
				}
				else if(is_numeric($jad)){este si se debe de ejecutar ya que el select nos esta devolviendo un entero con el id del JAD seleccinoado ¡¡¡¡¡¡¡MIRAR!!!!!!
					$j = $emJadTest->find($jad);
					$r = $emReunion->findOneBy(array('jad'=>$j));
				}else {
					$r = $emReunion->findOneBy(array('jad'=>$emJadTest->find(1)));
				}*/
				//$r = $emReunion->findOneBy(array('jad'=>$emJadTest->find(1)));
				//foreach($reunion as $r){
					//$resumen = $r->getNombre() . " Fecha " . $r->getFecha()->format('d-m-Y') . " Hora " . $r->getFecha()->format('H:i');
					$reuniones = 'No has seleccionado un JAD';
					$form->add($factory->createNamed('reuniones', 'text',$reuniones,array('disabled'=>'true','read_only'=>'true', 'attr'=>array("id"=>"jadsDeUsuarioType_reuniones"))));
					$temasAbiertos = 'No has seleccionado un JAD';
					$form->add($factory->createNamed('temasAbiertos', 'text',$temasAbiertos,array('disabled'=>'true','read_only'=>'true', 'attr'=>array("id"=>"jadsDeUsuarioType_temasAbiertos"))));
					//}

				//$resumen = 'No hay reuniones';
				//$form->add($factory->createNamed('reuniones', 'text',$resumen,array('disabled'=>'true','read_only'=>'true')));

		};

		$builder->addEventListener(FormEvents::PRE_SET_DATA, function (DataEvent $event) use ($refreshReunion) {
			$form = $event->getForm();
			$data = $event->getData();


			if ($data == null){
				$refreshReunion($form, null);
			}

			if ($data instanceof Jad) {
				$refreshReunion($form, $data);
			}
		});


		/*$builder->addEventListener(FormEvents::PRE_BIND, function (DataEvent $event) use ($refreshReunion) {
				$form = $event->getForm();
				$data = $event->getData();

				if (array_key_exists('jad', $data)) {
					$refreshReunion($form, $data['jad']);
				}

		});*/

	}

	public function getDefaultOptions(array $options)
	{
		return array(
				'data_class' => 'Tfg\JadBundle\Entity\Jad',
				'usuario'    => null,
		);
	}

	public function getName()
	{
		return 'jadsDeUsuarioType';
	}
}
?>