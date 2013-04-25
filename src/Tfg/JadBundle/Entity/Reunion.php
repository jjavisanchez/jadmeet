<?php

namespace Tfg\JadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tfg\JadBundle\Entity\Reunion
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Reunion
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
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;
    
    /**
     * @ORM\ManyToOne(targetEntity="Jad")
     * @ORM\JoinColumn(name="jad_id", referencedColumnName="id")
     */
    private $jad;

    /**
     * @var string $nota
     *
     * @ORM\Column(name="nota", type="text")
     */
    private $nota;
    
    /**
     * @var \DateTime $fecha
     *
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;


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
     * Set nombre
     *
     * @param string $nombre
     *
     */
    public function setNombre($nombre)
    {
    	$this->nombre = $nombre;
    
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
     * Set jad
     *
     * @param Tfg\JadBundle\Entity\Jad $jad
     * @return Reunion
     */
    public function setJad($jad=null)
    {
        $this->jad = $jad;
    
        return $this;
    }

    /**
     * Get jad
     *
     * @return Tfg\JadBundle\Entity\Jad 
     */
    public function getJad()
    {
        return $this->jad;
    }

    /**
     * Set nota
     *
     * @param string $nota
     * @return Reunion
     */
    public function setNota($nota)
    {
        $this->nota = $nota;
    
        return $this;
    }

    /**
     * Get nota
     *
     * @return string 
     */
    public function getNota()
    {
        return $this->nota;
    }
    
    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     */
    public function setFecha($fecha)
    {
    	$this->fecha = $fecha;
    
    }
    
    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
    	return $this->fecha;
    }
}