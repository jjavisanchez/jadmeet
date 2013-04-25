<?php

namespace Tfg\JadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tfg\JadBundle\Entity\Jad
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Jad
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
     * @var string $nombreProyecto
     *
     * @ORM\Column(name="nombreProyecto", type="string", length=100)
     */
    private $nombreProyecto;

    /**
     * @var string $propositos
     *
     * @ORM\Column(name="propositos", type="text")
     */
    private $propositos;

    /**
     * @var string $alcance
     *
     * @ORM\Column(name="alcance", type="text")
     */
    private $alcance;

    /**
     * @var string $objetivosDireccion
     *
     * @ORM\Column(name="objetivosDireccion", type="text")
     */
    private $objetivosDireccion;

     /**
      * @var string $slug
      *
      *  @ORM\Column(type="string", length=100)
      */
     protected $slug;


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
     * @return Jad
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        $this->slug = Util::getSlug($nombre);


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
     * Set nombreProyecto
     *
     * @param string $nombreProyecto
     * @return Jad
     */
    public function setNombreProyecto($nombreProyecto)
    {
        $this->nombreProyecto = $nombreProyecto;

        return $this;
    }

    /**
     * Get nombreProyecto
     *
     * @return string
     */
    public function getNombreProyecto()
    {
        return $this->nombreProyecto;
    }

    /**
     * Set propositos
     *
     * @param string $propositos
     * @return Jad
     */
    public function setPropositos($propositos)
    {
        $this->propositos = $propositos;

        return $this;
    }

    /**
     * Get propositos
     *
     * @return string
     */
    public function getPropositos()
    {
        return $this->propositos;
    }

    /**
     * Set alcance
     *
     * @param string $alcance
     * @return Jad
     */
    public function setAlcance($alcance)
    {
        $this->alcance = $alcance;

        return $this;
    }

    /**
     * Get alcance
     *
     * @return string
     */
    public function getAlcance()
    {
        return $this->alcance;
    }



    /**
     * Set objetivosDireccion
     *
     * @param string $objetivosDireccion
     * @return Jad
     */
    public function setObjetivosDireccion($objetivosDireccion)
    {
        $this->objetivosDireccion = $objetivosDireccion;

        return $this;
    }

    /**
     * Get objetivosDireccion
     *
     * @return string
     */
    public function getObjetivosDireccion()
    {
        return $this->objetivosDireccion;
    }



    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }


    public function Jad()
    {
    	return this;
    }

    public function __toString(){

    	return $this->getNombre();
    }
}