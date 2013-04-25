<?php
namespace Tfg\SesionJadBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
* Si no necesitamos pasar info desde el lanzamiento del evento no es necesario extender la clase de Event
*
*  Section Creating an Event Object in {@link{http://symfony.com/doc/current/components/event_dispatcher/introduction.html}
*/

class AgendaPointEvent extends Event {
	//Mensaje a enviar en este caso un array asociativo con el nombre del evento y la lista de turnos
	protected $object;

	public function __construct($object){
		$this->object = $object;
	}

	public function getObject()
	{
	    return $this->object;
	}

}