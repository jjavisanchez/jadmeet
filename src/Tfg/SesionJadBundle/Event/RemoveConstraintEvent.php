<?php
namespace Tfg\SesionJadBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
* Si no necesitamos pasar info desde el lanzamiento del evento no es necesario extender la clase de Event
*
*  Section Creating an Event Object in {@link{http://symfony.com/doc/current/components/event_dispatcher/introduction.html}
*/

class RemoveConstraintEvent extends Event {

	//En los eventos la variable $object siempre contiene el mensaje a pasar a través de ZMQ
	protected $object;

	public function __construct($object){
		$this->object = $object;
	}

	public function getObject()
	{
	    return $this->object;
	}


}