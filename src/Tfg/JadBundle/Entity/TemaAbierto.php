<?php
namespace Tfg\JadBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Tfg\JadBundle\Entity\TemaAbierto
 * @ORM\Entity
 * @ORM\Table(name="TemaAbierto")
 *
 */
class TemaAbierto extends Clave {

	/**
	 * @ORM\ManyToOne(targetEntity="Tfg\UsuarioBundle\Entity\Usuario")
	 * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id", nullable=true)
	 *
	 */
	private $usuario;

	/**
	 * @ORM\ManyToOne(targetEntity="Tfg\SesionJadBundle\Entity\PuntoAgenda")
	 * @ORM\JoinColumn(name="puntoagenda_id", referencedColumnName="id", nullable=true)
	 */
	private $puntoAgenda;

	/**
	 * @var string $solucion
	 *
	 * @ORM\Column(name="solucion", type="text",  nullable=true)
	 */
	private $solucion;

	/**
	 * @var \DateTime $fechaLimite
	 *
	 * @ORM\Column(name="fechaLimite", type="datetime", nullable=true)
	 */
	private $fechaLimite;

	/**
	 * @var boolean $finalizado
	 *
	 * @ORM\Column(name="finalizado", type="boolean")
	 */
	private $finalizado;

	public function __construct() {

	}


	/**
	 * Set usuario
	 *
	 * @param Tfg\UsuarioBundle\Entity\Usuario $usuario
	 * @return TemaAbierto
	 */
	public function setUsuario($usuario=null)
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
	 * Set puntoAgenda
	 *
	 * @param Tfg\SesionJadBundle\Entity\PuntoAgenda $puntoAgenda
	 * @return TemaAbierto
	 */
	public function setPuntoAgenda($puntoAgenda)
	{
		$this->puntoAgenda = $puntoAgenda;

		return $this;
	}

	/**
	 * Get puntoAgenda
	 *
	 * @return Tfg\SesionJadBundle\Entity\PuntoAgenda
	 */
	public function getPuntoAgenda()
	{
		return $this->puntoAgenda;
	}

	/**
	 * Set solucion
	 *
	 * @param string $solucion
	 * @return TemaAbierto
	 */
	public function setSolucion($solucion)
	{
		$this->solucion = $solucion;

		return $this;
	}

	/**
	 * Get solucion
	 *
	 * @return string
	 */
	public function getSolucion()
	{
		return $this->solucion;
	}

	/**
	 * Set fechaLimite
	 *
	 * @param \DateTime $fechaLimite
	 * @return TemaAbierto
	 */
	public function setFechaLimite($fechaLimite)
	{
		$this->fechaLimite = $fechaLimite;

		return $this;
	}

	/**
	 * Get fechaLimite
	 *
	 * @return \DateTime
	 */
	public function getFechaLimite()
	{
		return $this->fechaLimite;
	}

	/**
	 * Set finalizado
	 *
	 * @param boolean $finalizado
	 * @return TemaAbierto
	 */
	public function setFinalizado($finalizado)
	{
		$this->finalizado = $finalizado;

		return $this;
	}

	/**
	 * Get finalizado
	 *
	 * @return boolean
	 */
	public function getFinalizado()
	{
		return $this->finalizado;
	}

	public function __toString(){
		return $this->getNombre();
	}

}