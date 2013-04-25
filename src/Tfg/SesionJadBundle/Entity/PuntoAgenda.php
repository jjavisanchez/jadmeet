<?php

namespace Tfg\SesionJadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tfg\SesionJadBundle\Entity\PuntoAgenda
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PuntoAgenda
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
     * @ORM\ManyToOne(targetEntity="Tfg\SesionJadBundle\Entity\SesionJad")
     * @ORM\JoinColumn(name="sesionjad_id", referencedColumnName="id", nullable=false)
     */
    private $sesionJad;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var integer $orden
     *
     * @ORM\Column(name="orden", type="integer")
     */
    private $orden;

    /**
     * @var integer $duracion
     *
     * @ORM\Column(name="duracion", type="integer")
     */
    private $duracion;


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
     * Set sesionJad
     *
     * @param Tfg\SesionJadBundle\Entity\SesionJad $sesionJad
     * @return PuntoAgenda
     */
    public function setSesionJad($sesionJad=null)
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

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return PuntoAgenda
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set orden
     *
     * @param integer $orden
     * @return PuntoAgenda
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;
    
        return $this;
    }

    /**
     * Get orden
     *
     * @return integer 
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set duracion
     *
     * @param integer $duracion
     * @return PuntoAgenda
     */
    public function setDuracion($duracion)
    {
        $this->duracion = $duracion;
    
        return $this;
    }

    /**
     * Get duracion
     *
     * @return integer 
     */
    public function getDuracion()
    {
        return $this->duracion;
    }
}