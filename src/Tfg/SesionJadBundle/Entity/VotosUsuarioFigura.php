<?php

namespace Tfg\SesionJadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tfg\SesionJadBundle\Entity\VotosUsuarioFigura
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class VotosUsuarioFigura
{

    /**
     *
     * @ORM\Id @ORM\ManyToOne(targetEntity="Tfg\UsuarioBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id", nullable=false)
     */
    private $usuario;

    /**
     *
     * @ORM\Id @ORM\ManyToOne(targetEntity="Tfg\SesionJadBundle\Entity\Figura")
     * @ORM\JoinColumn(name="figura_id", referencedColumnName="id", nullable=false)
     */
    private $figura;

    /**
     * @var integer $numVotos
     *
     * @ORM\Column(name="numVotos", type="integer")
     */
    private $numVotos;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->usuario." ".$this->figura;
    }

    /**
     * Set usuario
     *
     * @param Tfg\UsuarioBundle\Entity\Usuario $usuario
     * @return VotosUsuarioFigura
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
     * Set figura
     *
     * @param Tfg\SesionJadBundle\Entity\Figura $figura
     * @return VotosUsuarioFigura
     */
    public function setFigura($figura=null)
    {
        $this->figura = $figura;
    
        return $this;
    }

    /**
     * Get figura
     *
     * @return Tfg\SesionJadBundle\Entity\Figura 
     */
    public function getFigura()
    {
        return $this->figura;
    }

    /**
     * Set numVotos
     *
     * @param integer $numVotos
     * @return VotosUsuarioFigura
     */
    public function setNumVotos($numVotos)
    {
        $this->numVotos = $numVotos;
    
        return $this;
    }

    /**
     * Get numVotos
     *
     * @return integer 
     */
    public function getNumVotos()
    {
        return $this->numVotos;
    }
}