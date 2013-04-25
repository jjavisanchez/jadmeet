<?php

namespace Tfg\SesionJadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tfg\SesionJadBundle\Entity\SesionJad
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SesionJad implements \Serializable
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
     * @ORM\ManyToOne(targetEntity="Tfg\JadBundle\Entity\Jad")
     * @ORM\JoinColumn(name="jad_id", referencedColumnName="id")
     */
    private $jad;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;


    /**
     * @var \DateTime $fecha
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string $lugar
     *
     * @ORM\Column(name="lugar", type="string", length=255)
     */
    private $lugar;

    /**
     * @var boolean $documentacionAprobada
     *
     * @ORM\Column(name="documentacionAprobada", type="boolean")
     */
    private $documentacionAprobada;

       /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;

     /**
      * @var string $slug
      *
      *  @ORM\Column(type="string", length=100)
      */
     protected $slug;


    public function __construct() {
        $this->id = null;
        $this->jad= null;
        $this->nombre = null;
        $this->fecha = null;
        $this->documentacionAprobada = null;
        $this->slug = null;
    }


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
     * @return SesionJad
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
     * @return SesionJad
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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return SesionJad
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
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

    /**
     * Set lugar
     *
     * @param string $lugar
     * @return SesionJad
     */
    public function setLugar($lugar)
    {
        $this->lugar = $lugar;

        return $this;
    }

    /**
     * Get lugar
     *
     * @return string
     */
    public function getLugar()
    {
        return $this->lugar;
    }

    /**
     * Set documentacionAprobada
     *
     * @param boolean $documentacionAprobada
     * @return SesionJad
     */
    public function setDocumentacionAprobada($documentacionAprobada)
    {
        $this->documentacionAprobada = $documentacionAprobada;

        return $this;
    }

    /**
     * Get documentacionAprobada
     *
     * @return boolean
     */
    public function getDocumentacionAprobada()
    {
        return $this->documentacionAprobada;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return SesionJad
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

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }


    public function serialize() {
        return serialize(array(
                'id'=>$this->getId(),
                'jad'=>$this->getJad(),
                'nombre'=>$this->getNombre(),
                'fecha'=>$this->getFecha(),
                'documentacionAprobada'=>$this->getDocumentacionAprobada(),
                'slug'=>$this->getSlug()
                ));
    }

    public function unserialize($serialized) {

        $serialized = unserialize($serialized);

        $this->id = $serialized['id'];
        $this->jad = $serialized['jad'];
        $this->nombre = $serialized['nombre'];
        $this->fecha = $serialized['fecha'];
        $this->documentacionAprobada = $serialized['documentacionAprobada'];
        $this->slug = $serialized['slug'];


    }

}