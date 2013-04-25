<?php

namespace Tfg\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


use JMS\Serializer\Annotation\Type;


/**
 * Tfg\UsuarioBundle\Entity\Usuario
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tfg\UsuarioBundle\Entity\UsuarioRepository")
 * @UniqueEntity("email")
 */
class Usuario implements UserInterface
{
    /**
     * @var integer $id
     *
     * @Type("integer")
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $nombre
     *
     * @Type("string")
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var string $apellidos
     *
     * @type("string")
     *
     * @ORM\Column(name="apellidos", type="string", length=150)
     */
    private $apellidos;

    /**
     * @var string $password
     *
     * @type("string")
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string $email
     *
     * @type("string")
     *
     * @ORM\Column(name="email", unique=true, type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var \DateTime $fecha_nacimiento
     *
     * @type("DateTime")
     *
     * @ORM\Column(name="fecha_nacimiento", type="date")
     */
    private $fecha_nacimiento;

    /**
     * @var string $ciudad
     *
     * @type("string")
     *
     * @ORM\Column(name="ciudad", type="string", length=100)
     */
    private $ciudad;

    /**
     * @var string $titulacion
     *
     * @type("string")
     *
     * @ORM\Column(name="titulacion", type="string", length=100)
     */
    private $titulacion;

    /**
     * @var string $perfil
     *
     * @type("string")
     *
     * @ORM\Column(name="perfil", type="text")
     */
    private $perfil;

    /**
     * @var string $slug
     *
     * @type("string")
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string $salt
     *
     * @type("string")
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     * @Assert\Null()
     */
    private $salt;




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
     * Set apellidos
     *
     * @param string $apellidos
     *
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;

    }

    /**
     * Get apellidos
     *
     * @return string
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     */
    public function setPassword($password)
    {
        $this->password = $password;

    }

    /**
     * Get password
     *
     * @return string
     *
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     */
    public function setEmail($email)
    {
        $this->email = $email;

    }

    /**
     * Get email
     *
     * @return string
     *
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set fecha_nacimiento
     *
     * @param \DateTime $fechaNacimiento
     *
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fecha_nacimiento = $fechaNacimiento;

    }

    /**
     * Get fecha_nacimiento
     *
     * @return \DateTime
     */
    public function getFechaNacimiento()
    {
        return $this->fecha_nacimiento;
    }

    /**
     * Set ciudad
     *
     * @param string $ciudad
     *
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;


    }

    /**
     * Get ciudad
     *
     * @return string
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set titulacion
     *
     * @param string $titulacion
     *
     */
    public function setTitulacion($titulacion)
    {
        $this->titulacion = $titulacion;

    }

    /**
     * Get titulacion
     *
     * @return string
     */
    public function getTitulacion()
    {
        return $this->titulacion;
    }

    /**
     * Set perfil
     *
     * @param string $perfil
     *
     */
    public function setPerfil($perfil)
    {
        $this->perfil = $perfil;


    }

    /**
     * Get perfil
     *
     * @return string
     */
    public function getPerfil()
    {
        return $this->perfil;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;


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

    /**
     * Set salt
     *
     * @param string $salt
     *
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;


    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }


    public function eraseCredentials() {

    }

    /**
     * Get roles
     *
     * @return string
     */
    public function getRoles() {

        return array(
            'ROLE_USUARIO'
            //new JadDependentRole($this)   //rol dinámico;
            );
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername() {
        return $this->getEmail();
    }

    /**
     * Compare emails
     *
     * @return string
     */

    public function equals(UserInterface $usuario)
     {
         return $this->getEmail() == $usuario->getEmail();
     }


     /**
     * Build complete usermane to be showed.
     *
     * @return string
     */
    public function __toString()
   {
       return $this->getNombre().' '.$this->getApellidos();
   }
}
?>

