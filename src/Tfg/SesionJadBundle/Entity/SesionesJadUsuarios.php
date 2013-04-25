<?php

namespace Tfg\SesionJadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tfg\SesionJadBundle\Entity\SesionesJadUsuarios
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SesionesJadUsuarios
{

     /**
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tfg\UsuarioBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id", nullable=false)
     *
     */
    private $usuario;

    /**
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tfg\SesionJadBundle\Entity\SesionJad")
     * @ORM\JoinColumn(name="SesionJad_id", referencedColumnName="id", nullable=false)
     */
    private $sesionJad;

    /**
     * @var boolean $asistencia
     *
     * @ORM\Column(name="asistencia", type="boolean")
     */
    private $asistencia;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->usuario." ".$this->jad;
    }

    /**
     * Set usuario
     *
     * @param Tfg\UsuarioBundle\Entity\Usuario $usuario
     * @return SesionesJadUsuarios
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
     * Set jad
     *
     * @param Tfg\SesionJadBundle\Entity\SesionJad $sesionJad
     * @return SesionesJadUsuarios
     */
    public function setSesionJad($sesionJad=null)
    {
        $this->sesionJad = $sesionJad;

        return $this;
    }

    /**
     * Get jad
     *
     * @return Tfg\SesionJadBundle\Entity\SesionJad
     */
    public function getSesionJad()
    {
        return $this->sesionJad;
    }

    /**
     * Set asistencia
     *
     * @param Tfg\JadBundle\Entity\Jad $jad
     * @return SesionesJadUsuarios
     */
    public function setAsistencia($asistencia)
    {
        $this->asistencia = $asistencia;

        return $this;
    }

    /**
     * Get asistencia
     *
     * @return boolean
     */
    public function getAsistencia()
    {
        return $this->asistencia;
    }

}