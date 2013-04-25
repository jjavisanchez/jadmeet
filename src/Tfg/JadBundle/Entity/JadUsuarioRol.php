<?php

namespace Tfg\JadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;



/**
 * Tfg\JadBundle\Entity\JadUsuarioRol
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Tfg\JadBundle\Entity\JadUsuarioRolRepository")
 */
class JadUsuarioRol
{

    /**
     *
     * @ORM\Id @ORM\ManyToOne(targetEntity="Jad")
     * @ORM\JoinColumn(name="jad_id", referencedColumnName="id", nullable=false)
     */
    private $jad;

     /**
     *
     * @ORM\Id @ORM\ManyToOne(targetEntity="Tfg\UsuarioBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id", nullable=false)
     */
    private $usuario;

     /**
     *
     * @ORM\ManyToOne(targetEntity="Tfg\UsuarioBundle\Entity\Rol")
     * @ORM\JoinColumn(name="rol_nombre", referencedColumnName="nombre", nullable=false)
     */
    private $rol;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->jad." ".$this->usuario;
    }

    /**
     * Set jad
     *
     * @param Tfg\JadBundle\Entity\Jad $jad
     * @return JadUsuarioRol
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
     * Set usuario
     *
     * @param Tfg\UsuarioBundle\Entity\Usuario $usuario
     * @return JadUsuarioRol
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
     * Set rol
     *
     * @param Tfg\UsuarioBundle\Entity\Rol $rol
     * @return JadUsuarioRol
     */
    public function setRol($rol=null)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return Tfg\UsuarioBundle\Entity\Rol
     */
    public function getRol()
    {
        return $this->rol;
    }

    public function getJadUsuarioRol()
    {
    	return this;
    }

    public function __toString(){

    	return $this->getJad()->getNombre()." ".$this->getUsuario()->getNombre()." ".$this->getRol()->getNombre();
    }
}