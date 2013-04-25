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
	 * @ORM\ManyToMany(targetEntity="Tfg\UsuarioBundle\Entity\Usuario")
	 * @ORM\JoinTable(name="usuarios_TemaAbierto",
	 *      joinColumns={@ORM\JoinColumn(name="temaAberto_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="usuario_id", referencedColumnName="id")}
	 *      )
	 */
	private $usuarios;

	/**
	 * @ORM\ManyToOne(targetEntity="Tfg\SesionJadBundle\Entity\PuntoAgenda")
	 * @ORM\JoinColumn(name="puntoagenda_id", referencedColumnName="id", nullable=true)
	 */
	private $puntoAgenda;

	/**
	 * @var string $solucion
	 *
	 * @ORM\Column(name="solucion", type="text")
	 */
	private $solucion;

	/**
	 * @var \DateTime $fechaLimite
	 *
	 * @ORM\Column(name="fechaLimite", type="datetime")
	 */
	private $fechaLimite;

	/**
	 * @var boolean $finalizado
	 *
	 * @ORM\Column(name="finalizado", type="boolean")
	 */
	private $finalizado;

	public function __construct() {
		$this->usuarios = new \Doctrine\Common\Collections\ArrayCollection();
	}


	/**
	 * Set usuarios
	 *
	 * @param \Doctrine\Common\Collections\ArrayCollection $usuarios
	 * @return TemaAbierto
	 */
	public function setUsuarios($usuarios=null)
	{
		$this->usuarios = $usuarios;

		return $this;
	}

	/**
	 * Get usuarios
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getUsuarios()
	{
		return $this->usuarios;
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