<?php

namespace Tfg\SesionJadBundle\Classes\HandlerJSON;
use Tfg\SesionJadBundle\Event\SocketEvents;
use Tfg\SesionJadBundle\Event\AgendaPointEvent;

class PuntosHandlerJSON extends HandlerJSON{

	public function appendDatesFile($object)
    {

    }
     public function removeDatesFile($object)
    {

    }


    /**
     *
     * @param integer $id represeta el punto siguiente
     *
     **/

    public function increment($id_punto_actual, $orden_punto_actual){

    	$source = $this->readFile();

    	$source['punto']= $id_punto_actual;

    	$fh = fopen("$this->path/$this->filename", 'w')
                or die("Error al abrir fichero de salida");
        $objectJSON = $this->serializer->serialize($source, 'json');
        fwrite($fh, $objectJSON);
        fclose($fh);

        $mensajeZMQ = array('evento'=>SocketEvents::NEXT_AGENDA,'punto'=> $source['punto'], 'orden_actual'=>$orden_punto_actual);
        $json_mensajeZMQ = json_encode($mensajeZMQ);
        $event = new AgendaPointEvent($json_mensajeZMQ);
        $this->dispatcher->dispatch(SocketEvents::NEXT_AGENDA, $event);
    }

	/**
     *
     * @param integer $id represeta el punto anterior
     *
     **/
    public function decrement($id_punto_actual, $orden_punto_actual){
    	$source = $this->readFile();

    	$source['punto']= $id_punto_actual;

    	$fh = fopen("$this->path/$this->filename", 'w')
                or die("Error al abrir fichero de salida");
        $objectJSON = $this->serializer->serialize($source, 'json');
        fwrite($fh, $objectJSON);
        fclose($fh);

        $mensajeZMQ = array('evento'=>SocketEvents::BACK_AGENDA, 'punto'=> $source['punto'], 'orden_actual'=>$orden_punto_actual);
        $json_mensajeZMQ = json_encode($mensajeZMQ);
        $event = new AgendaPointEvent($json_mensajeZMQ);
        $this->dispatcher->dispatch(SocketEvents::BACK_AGENDA, $event);

    }

}