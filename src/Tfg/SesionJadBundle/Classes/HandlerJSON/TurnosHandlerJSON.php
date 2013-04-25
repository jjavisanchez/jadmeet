<?php

namespace Tfg\SesionJadBundle\Classes\HandlerJSON;

use JMS\Serializer\Serializer;

use Tfg\UsuarioBundle\Entity\Usuario;
use Tfg\SesionJadBundle\Classes\Turns;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tfg\SesionJadBundle\Event\SocketEvents;
use Tfg\SesionJadBundle\Event\RequestTurnEvent;
use Tfg\SesionJadBundle\Event\RemoveTurnEvent;

/**
     *
     * Append dates to file .json which is indicate for path and filename attributes of this class, from a object source
     * pass as parameter
     *
     * @param       object  $object Object source which its dates are added to te file
     * @return      array   $aux represents turns list
*/

class TurnosHandlerJSON extends HandlerJSON{

	public function appendDatesFile($object)
    {
        $source = $this->readFile();
        $aux = $source->getTurnsList();
        //Here we check the $object is not in aux array
        foreach($aux as $obj){
            if($obj->getId() == $object->getId()){
                throw new \Exception("Error: Objeto a insertar en el fichero ya esta contenido", 1);
            }
        }
        $aux[] = $object;
        $source->setTurnsList($aux);

        $fh = fopen("$this->path/$this->filename", 'w')
                or die("Error al abrir fichero de salida");
        $objectJSON = $this->serializer->serialize($source, 'json');
        fwrite($fh, $objectJSON);
        fclose($fh);

        //Creamos el mensaje para el evento del socket a partir de un array asociativo la variable posicion la utilizo para controlar si es el primero en la lista y ponerlo en rojo
        $posicion = count($aux);
        $json_object = $this->serializer->serialize($object, 'json');
        $mensajeZMQ = array('evento'=>SocketEvents::REQUEST_TURN,'posicion'=>$posicion,'objeto'=>$json_object);
        $json_mensajeZMQ = json_encode($mensajeZMQ);
        $event = new RequestTurnEvent($json_mensajeZMQ);
        $this->dispatcher->dispatch(SocketEvents::REQUEST_TURN, $event);

        return $aux;
    }




    /**
     *
     * Remove dates to files .json which is indicate for path and filename attributes of this class, from a object source
     * pass as parameter
     *
     * @param       object  $object Object source which its dates are removed to te file
     * @return      array   $aux represents turns list
     */
    public function removeDatesFile($object)
    {
       		$turns = $this->readFile();
            //Con la variable $turns (objeto de la clase Turns) a traves del metodo getTurnsList() recuperamos el array con todos los turnos pedidos hasta el momento, la cual pasaremos a la plantilla.
            $turnsList = $turns->getTurnsList();
            foreach($turnsList as $key=>$user){
                if($user->getId() == $object){
                    unset($turnsList[$key]);
                    $turnsList = array_values($turnsList);
                }
            }
            $newTurns = new Turns();
            $newTurns->setTurnsList($turnsList);

        $fh = fopen("$this->path/$this->filename", 'w')
                or die("Error al abrir fichero de salida");
        $turnosJSON = $this->serializer->serialize($newTurns, 'json');
        fwrite($fh, $turnosJSON);
        fclose($fh);

        //Creamos el mensaje para el evento del socket a partir de un array asociativo
        $json_object = $this->serializer->serialize($turnsList, 'json');
        $mensajeZMQ = array('evento'=>SocketEvents::REMOVE_TURN, 'turnos'=>$json_object);
        $json_mensajeZMQ = json_encode($mensajeZMQ);
        $event = new RemoveTurnEvent($json_mensajeZMQ);
        $this->dispatcher->dispatch(SocketEvents::REMOVE_TURN, $event);

        return $turnsList;
    }
}