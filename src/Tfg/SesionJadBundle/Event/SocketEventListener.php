<?php
namespace Tfg\SesionJadBundle\Event;

use Tfg\SesionJadBundle\Event\RequestTurnEvent;
use Tfg\SesionJadBundle\Event\RemoveTurnEvent;
use Tfg\SesionJadBundle\Event\HideScreenEvent;
use Tfg\SesionJadBundle\Event\ShowScreenEvent;
use Tfg\SesionJadBundle\Event\NewAgreementEvent;
use Tfg\SesionJadBundle\Event\EditAgreementEvent;
use Tfg\SesionJadBundle\Event\RemoveAgreementEvent;
use Tfg\sesionJadBundle\Classes\HandlerZMQ\HandlerZMQ;
use Tfg\SesionJadBundle\Classes\Turns;
use Tfg\SesionJadBundle\Classes\TurnosHandlerJSON;
use Tfg\SesionJadBundle\Entity\SesionJad;
use Tfg\SesionJadBundle\Classes\PantallasHandlerJSON;
use Tfg\JadBundle\Entity\Clave;
use Tfg\JadBundle\Entity\Supuesto;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SocketEventListener{

	protected $handlerZMQ;
	protected $container;

	public function __construct(HandlerZMQ $handlerZMQ, ContainerInterface $container){
		$this->handlerZMQ = $handlerZMQ;
		$this->container = $container;
	}

	public function onRequestTurn(RequestTurnEvent $event){

		$object = $event->getObject();

		//inicializar zmq, puedo depurar con die(mostrar lo que sea) y verlo al hacer la peticion rest desde un cliente rest en el navegador

		$this->handlerZMQ->write($object);

	}

	public function onRemoveTurn(RemoveTurnEvent $event){

		$object = $event->getObject();

		$this->handlerZMQ->write($object);

	}

	public function onHideScreen(HideScreenEvent $event){

		$object = $event->getObject();

		$this->handlerZMQ->write($object);

	}

	public function onShowScreen(ShowScreenEvent $event){

		//$object representa el Array que sera el mensaje a pasar a traves de ZMQ una vez se le haya añadido
		//el elemento content. ¡¡¡¡NOTA SERIALIZAR A JSON ESTE ARRAY ($object) ANTES DE PASARLO!!!!
		$object = $event->getObject();
		$info = $object['info'];

		switch ($info) {
			case 'turnos':
				//Recuperar los turnos en un array desde el fichero y añadirlos al mensaje zmq ($object) para serializarlo.
				$bundlePath = $this->container->get('kernel')->locateResource('@SesionJadBundle');
		        $filename = $this->container->getParameter('file_turns');
		        $fullpath = "$bundlePath/$filename";
		        $turnsList = null;
		        if(file_exists($fullpath)){
		            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
		            $handlerTurnos = $this->container->get('handlerJSONTurnos');
		            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
		            $handlerTurnos->setPath($bundlePath);
		            $handlerTurnos->setFilename($filename);
		            $handlerTurnos->setEntityName("Tfg\SesionJadBundle\Classes\Turns");
		            $turns = $handlerTurnos->readFile();
		            //Con la variable $turns (objeto de la clase Turns) a traves del metodo getTurnsList() recuperamos el array con todos los turnos pedidos hasta el momento, la cual pasaremos a la plantilla.
		            $turnsList = $turns->getTurnsList();
		            $serializer = $this->container->get('serializer');
		            $turnsList_JSON = $serializer->serialize($turnsList, 'json');
		            $object['contenido'] = $turnsList_JSON;
		            $object = json_encode($object);
        		}
				break;
			case 'agenda':
				//Recuperar el punto actual del fichero y la agenda desde la BBDD
				$bundlePath = $this->container->get('kernel')->locateResource('@SesionJadBundle');
				$filename = $this->container->getParameter('file_currentpoint');
		        $fullpath = "$bundlePath/$filename";
		        $punto = null;
		        if(file_exists($fullpath)){
		            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
		            $handlerPoint = $this->container->get('handlerJSONPuntos');
		            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
		            $handlerPoint->setPath($bundlePath);
		            $handlerPoint->setFilename($filename);
		            $handlerPoint->setEntityName('array<string,string>');
		            $array_punto = $handlerPoint->readFile();
		            $punto = $array_punto['punto'];
		        }
		        $em = $this->container->get('doctrine')->getEntityManager();
		        $puntosAgenda = $em->getRepository('SesionJadBundle:PuntoAgenda')->findBySesionJad($object['sesion']);
		        $puntos = array();
		        foreach($puntosAgenda as $puntoAgenda){
		        	$aux = array('nombre'=>$puntoAgenda->getNombre(), 'id'=>$puntoAgenda->getId(), 'orden'=>$puntoAgenda->getOrden());
		        	$puntos[] = $aux;
		        }
		        $serializer = $this->container->get('serializer');
		        $puntosAgenda_JSON = $serializer->serialize($puntos,'json');
		        $contenido = array('punto'=>$punto, 'puntosAgenda'=>$puntosAgenda_JSON);
		        $contenido_JSON = $serializer->serialize($contenido,'json');
		        $object['contenido'] = $contenido_JSON;
		        $object = json_encode($object);
				break;
			case 'objetivos':
				$em = $this->container->get('doctrine')->getEntityManager();
				$sesion = $em->getRepository('SesionJadBundle:SesionJad')->find($object['sesion']);
				$jad = $sesion->getJad();
				$contenido = array();
				$contenido['propositos_jad'] = $jad->getPropositos();
				$contenido['objetivos_direccion_jad'] = $jad->getObjetivosDireccion();
				$contenido['descripcion_sesion'] = $sesion->getDescripcion();
				$serializer = $this->container->get('serializer');
				$contenido_JSON = $serializer->serialize($contenido,'json');
				$object['contenido'] = $contenido_JSON;
				$object = json_encode($object);
				break;
			case 'acuerdos':
				$em = $this->container->get('doctrine')->getEntityManager();
				$acuerdos = $em->getRepository('JadBundle:Supuesto')->findByJad($object['sesion']);
				$contenido = array();
				foreach($acuerdos as $acuerdo){
					$aux = array();
					$aux['id'] = $acuerdo->getId();
					$aux['nombre'] = $acuerdo->getNombre();
					$aux['descripcion'] = $acuerdo->getDescripcion();
					$contenido[] = $aux;
				}
				$serializer = $this->container->get('serializer');
				$contenido_JSON = $serializer->serialize($contenido,'json');
				$object['contenido'] = $contenido_JSON;
				$object = json_encode($object);
				break;
			case 'temasAbiertos':
				$em = $this->container->get('doctrine')->getEntityManager();
				$temas_abiertos = $em->getRepository('JadBundle:TemaAbierto')->findByJad($object['sesion']);
				$contenido = array();
				foreach($temas_abiertos as $tema_abierto){
					$aux = array();
					$aux['id'] = $tema_abierto->getId();
					$aux['nombre'] = $tema_abierto->getNombre();
					$aux['descripcion'] = $tema_abierto->getDescripcion();
					$contenido[] = $aux;
				}
				$serializer = $this->container->get('serializer');
				$contenido_JSON = $serializer->serialize($contenido,'json');
				$object['contenido'] = $contenido_JSON;
				$object = json_encode($object);
				break;
			case 'restricciones':
				$em = $this->container->get('doctrine')->getEntityManager();
				$restricciones = $em->getRepository('JadBundle:Restriccion')->findByJad($object['sesion']);
				$contenido = array();
				foreach($restricciones as $restriccion){
					$aux = array();
					$aux['id'] = $restriccion->getId();
					$aux['nombre'] = $restriccion->getNombre();
					$aux['descripcion'] = $restriccion->getDescripcion();
					$contenido[] = $aux;
				}
				$serializer = $this->container->get('serializer');
				$contenido_JSON = $serializer->serialize($contenido,'json');
				$object['contenido'] = $contenido_JSON;
				$object = json_encode($object);
				break;
			default:
				$object['contenido'] = "La información solicitada no es correcta";
				break;
		}

		$this->handlerZMQ->write($object);
	}

	public function onNextPoint(AgendaPointEvent $event){

		$object = $event->getObject();
		$this->handlerZMQ->write($object);
	}

	public function onBackPoint(AgendaPointEvent $event){

		$object = $event->getObject();
		$this->handlerZMQ->write($object);
	}

	public function onNewAgreement(NewAgreementEvent $event){
			$object = $event->getObject();
			$this->handlerZMQ->write($object);
	}

	public function onEditAgreement(EditAgreementEvent $event){
			$object = $event->getObject();
			$this->handlerZMQ->write($object);
	}

	public function onRemoveAgreement(RemoveAgreementEvent $event){
			$object = $event->getObject();
			$this->handlerZMQ->write($object);
	}

	public function onNewOpenIsuue(NewOpenIssueEvent $event){
			$object = $event->getObject();
			$this->handlerZMQ->write($object);
	}

	public function onRemoveOpenIssue(RemoveOpenIssueEvent $event){
		$object = $event->getObject();
		$this->handlerZMQ->write($object);
	}

	public function onEditOpenIssue(EditOpenIssueEvent $event){
			$object = $event->getObject();
			$this->handlerZMQ->write($object);
	}

	public function onNewConstraint(NewConstraintEvent $event){
		$object = $event->getObject();
		$this->handlerZMQ->write($object);
	}

	public function onRemoveConstraint(RemoveConstraintEvent $event){
		$object = $event->getObject();
		$this->handlerZMQ->write($object);
	}

	public function onEditConstraint(EditConstraintEvent $event){
		$object = $event->getObject();
		$this->handlerZMQ->write($object);
	}

}