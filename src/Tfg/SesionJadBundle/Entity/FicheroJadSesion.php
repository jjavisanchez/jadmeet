<?php

namespace Tfg\SesionJadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tfg\SesionJadBundle\Entity\FicheroJadSesion
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FicheroJadSesion
{

    /**
     *
     * @ORM\Id @ORM\ManyToOne(targetEntity="Tfg\SesionJadBundle\Entity\SesionJad")
     * @ORM\JoinColumn(name="sesionjad_id", referencedColumnName="id", nullable=false)
     */
    private $sesionJad;

    /**
     *
     * @ORM\Id @ORM\ManyToOne(targetEntity="Tfg\JadBundle\Entity\Fichero")
     * @ORM\JoinColumn(name="fichero_id", referencedColumnName="id", nullable=false)
     */
    private $fichero;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Tfg\JadBundle\Entity\Jad")
     * @ORM\JoinColumn(name="jad_id", referencedColumnName="id", nullable=false)
     */
    private $jad;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->jad." ".$this->fichero;
    }

    /**
     * Set sesionJad
     *
     * @param Tfg\SesionJadBundle\Entity\SesionJad $sesionJad
     * @return FicheroJadSesion
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
     * Set fichero
     *
     * @param Tfg\JadBundle\Entity\Fichero $fichero
     * @return FicheroJadSesion
     */
    public function setFichero($fichero=null)
    {
        $this->fichero = $fichero;
    
        return $this;
    }

    /**
     * Get fichero
     *
     * @return Tfg\JadBundle\Entity\Fichero 
     */
    public function getFichero()
    {
        return $this->fichero;
    }

    /**
     * Set jad
     *
     * @param Tfg\JadBundle\Entity\Jad $jad
     * @return FicheroJadSesion
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
}
