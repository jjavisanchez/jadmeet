<?php

namespace Tfg\JadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tfg\JadBundle\Entity\Clave
 *
 * @ORM\MappedSuperclass
 */
abstract class Clave
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
     * @ORM\ManyToOne(targetEntity="Jad", cascade={"persist"})
     * @ORM\JoinColumn(name="jad_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $jad;


    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;


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
     * Set jad
     *
     * @param Tfg\JadBundle\Entity\Jad $jad
     * @return Clave
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
     * Set nombre
     *
     * @param string $nombre
     * @return Clave
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Clave
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }
}


