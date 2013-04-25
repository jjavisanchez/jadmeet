<?php

namespace Tfg\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tfg\UsuarioBundle\Entity\Rol
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Rol implements \Serializable
{

    /**
     * @var string $nombre
     * @ORM\Id
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;
    
    /**
      * @var string $slug
      * 
      *  @ORM\Column(type="string", length=100) 
      */
     protected $slug;
     

     function __construct() {
         $this->nombre = null;
         $this->slug = null;
     }

              /**
     * Set nombre
     *
     * @param string $nombre
     * @return Rol
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
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
    
    
   
    public function __toString(){
        return $this->nombre;
    }

    public function serialize() {
        return serialize(array(
                'nombre'=>$this->getNombre(),
                'slug'=>$this->getSlug()
                ));
    }

    public function unserialize($serialized) {
        
        $serialized = unserialize($serialized);
        
        $this->nombre = $serialized['nombre'];
        $this->slug = $serialized['slug'];
        
        
    }
}
