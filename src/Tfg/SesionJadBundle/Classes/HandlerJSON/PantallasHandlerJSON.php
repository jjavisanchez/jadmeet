<?php

namespace Tfg\SesionJadBundle\Classes\HandlerJSON;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tfg\SesionJadBundle\Event\SocketEvents;
use Tfg\SesionJadBundle\Event\RequestTurnEvent;
use Tfg\SesionJadBundle\Event\RemoveTurnEvent;
use Tfg\SesionJadBundle\Event\HideScreenEvent;
use Tfg\SesionJadBundle\Event\ShowScreenEvent;
use JMS\Serializer\Serializer;

class PantallasHandlerJSON extends HandlerJSON{

	/**
	 * Implementación del método appendDatesFiles de la clase Abstracta HandlerJSON
	 * aunque no se añaden nuevos datos, la funcion que damos aquí es similar y es
	 * modificar el fichero json que representa el objeto, para cambiar la pantalla
	 * activa en la pantalla compartida
	 *
	 * @param $object contiene la zona de pantalla compartida que hay que mostrar A o B, en ralación al fichero será modificado para indicar que pantalla debe visualizarse tras el cambio
	 *
	*/
	public function appendDatesFile($object)
    {
    	$pantalla = $object['pantalla'];
    	$info = $object['info'];
        $sesion = $object['sesion'];

    	$pantallas_activas = $this->readFile();
    	$pantallas_activas[$pantalla] = $info;

    	$fh = fopen("$this->path/$this->filename", 'w')
                or die("Error al abrir fichero de salida");
        $pantallas_activas_JSON = $this->serializer->serialize($pantallas_activas, 'json');
        fwrite($fh, $pantallas_activas_JSON);
        fclose($fh);

        //Creamos el mensaje para el evento del socket a partir de un array asociativo
        $mensajeZMQ = array('evento'=>SocketEvents::SHOW_SCREEN, 'pantalla'=>$pantalla, 'info'=>$info, 'sesion'=>$sesion );
        //$json_mensajeZMQ = json_encode($mensajeZMQ);
        $event = new ShowScreenEvent($mensajeZMQ);
        $this->dispatcher->dispatch(SocketEvents::SHOW_SCREEN, $event);

        return $pantallas_activas;

    }

    /**
	 * Implementación del método removeDatesFiles de la clase Abstracta HandlerJSON
	 * aunque no se eliminen nuevos datos, la funcion que damos aquí es similar y es
	 * ocultar la pantalla indicada por el parametro.
	 *
	 * @param $object contiene la zona de pantalla compartida que hay que ocultar A o B
	*/
     public function removeDatesFile($object)
    {
    	$pantallas_activas = $this->readFile();
    	$pantallas_activas[$object] = '0';

    	$fh = fopen("$this->path/$this->filename", 'w')
                or die("Error al abrir fichero de salida");
        $pantallas_activas_JSON = $this->serializer->serialize($pantallas_activas, 'json');
        fwrite($fh, $pantallas_activas_JSON);
        fclose($fh);

        //Creamos el mensaje para el evento del socket a partir de un array asociativo
        $mensajeZMQ = array('evento'=>SocketEvents::HIDE_SCREEN, 'pantallas'=>$pantallas_activas_JSON);
        $json_mensajeZMQ = json_encode($mensajeZMQ);
        $event = new HideScreenEvent($json_mensajeZMQ);
        $this->dispatcher->dispatch(SocketEvents::HIDE_SCREEN, $event);

        return $pantallas_activas;
    }

}