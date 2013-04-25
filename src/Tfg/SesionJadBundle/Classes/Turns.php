<?php

/**
 * Description of Turnos
 * Clase para representar los turnos en el fichero turnos.json
 *
 * @author josejavi14
 */

namespace Tfg\SesionJadBundle\Classes;


use Symfony\Component\DependencyInjection\Container;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Router;
use JMS\SerializerBundle\Serializer\Serializer;

use Tfg\UsuarioBundle\Entity\Usuario;
use JMS\Serializer\Annotation\Type;

class Turns {


	/**
     * @var array $turnlist
     *
     * @type("array<Tfg\UsuarioBundle\Entity\Usuario>")
     *
     */
	private $turnsList;

	public function __construct(){

		$this->turnsList = array();

	}


    public function getTurnsList()
    {
        return $this->turnsList;
    }

    public function setTurnsList($turnsList)
    {
        $this->turnsList = $turnsList;
    }

}