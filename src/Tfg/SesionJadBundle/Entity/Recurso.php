<?php

namespace Tfg\SesionJadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tfg\SesionJadBundle\Entity\Recurso
 *
 * @ORM\MappedSuperclass
 */
abstract class Recurso
{
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 *
	 * @ORM\ManyToOne(targetEntity="Tfg\UsuarioBundle\Entity\Usuario")
	 * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
	 */
	private $usuario;

	/**
	 *
	 * @ORM\ManyToOne(targetEntity="SesionJad")
	 * @ORM\JoinColumn(name="sesionjad_id", referencedColumnName="id")
	 */
	private $sesionJad;


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Set usuario
	 *
	 * @param Tfg\UsuarioBundle\Entity\Usuario $usuario
	 * @return Recurso
	 */
	public function setUsuario( $usuario = null)
	{
		$this->usuario = $usuario;

		return $this;
	}

	/**
	 * Get usuario
	 *
	 * @return Tfg\UsuarioBundle\Entity\Usuario
	 */
	public function getUsuario()
	{
		return $this->usuario;
	}

	/**
	 * Set sesionJad
	 *
	 * @param Tfg\SesionJadBundle\Entity\SesionJad $sesionJad
	 * @return Recurso
	 */
	public function setSesionJad($sesionJad = null)
	{
		$this->sesionJad = $sesionJad;

		return $this;
	}

	/**
	 * Get sesionJad
	 *
	 * @return Tfg\SesionJadBundle\Entity\SesionJad
	 */
	public function getSesionJad()
	{
		return $this->sesionJad;
	}
}


/**
 * @ORM\Entity
 * @ORM\Table(name="Foto")
 *
 */
class Foto extends Recurso {

	/**
	 * @var string $url
	 *
	 * @ORM\Column(name="url", type="string", length=255)
	 */
	private $url;

	/**
	 * @var string $nombreFichero
	 *
	 * @ORM\Column(name="nombreFichero", type="string", length=255)
	 */
	private $nombreFichero;
	
	/**
	 * Set url
	 *
	 * @param string $url
	 * @return Foto
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	
		return $this;
	}
	
	/**
	 * Get url
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}
	
	/**
	 * Set nombreFichero
	 *
	 * @param string $nombreFichero
	 * @return Foto
	 */
	public function setNombreFichero($nombreFichero)
	{
		$this->nombreFichero = $nombreFichero;
	
		return $this;
	}
	
	/**
	 * Get nombreFichero
	 *
	 * @return string
	 */
	public function getNombreFichero()
	{
		return $this->nombreFichero;
	}
}

/**
 * @ORM\Entity
 * @ORM\Table(name="Duda")
 */
class Duda extends Recurso {
	/**
	 * @var string $duda
	 *
	 * @ORM\Column(name="duda", type="text")
	 */
	private $duda;
	
	/**
	 * Set duda
	 *
	 * @param string $duda
	 * @return Foto
	 */
	public function setDuda($duda)
	{
		$this->duda = $duda;
	
		return $this;
	}
	
	/**
	 * Get duda
	 *
	 * @return string
	 */
	public function getDuda()
	{
		return $this->duda;
	}
}

/**
 * @ORM\Entity
 * @ORM\Table(name="Figura")
 */
class Figura extends Recurso {
	

	/**
	 *
	 * @ORM\ManyToOne(targetEntity="Modelo")
	 * @ORM\JoinColumn(name="modelo_id", referencedColumnName="id", nullable=false)
	 */
	private $modelo;

	/**
	 * @var integer $numVotos
	 *
	 * @ORM\Column(name="numVotos", type="integer")
	 */
	private $numVotos;
	
	/**
	 * Set modelo
	 *
	 * @param Tfg\SesionJadBundle\Entity\Modelo $modelo
	 * @return Figura
	 */
	public function setModelo(\Tfg\SesionJadBundle\Entity\Modelo $modelo = null)
	{
		$this->modelo = $modelo;
	
		return $this;
	}
	
	/**
	 * Get modelo
	 *
	 * @return Tfg\SesionJadBundle\Entity\Modelo
	 */
	public function getModelo()
	{
		return $this->modelo;
	}
	
	

}