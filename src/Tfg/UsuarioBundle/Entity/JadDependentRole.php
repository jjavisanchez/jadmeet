<?php

namespace Tfg\UsuarioBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JadDependentRole implements RoleInterface{

    private $user;

    public function __construct(UserInterface $user){
        $this->user = $user;
    }

    public function getRole(){

        //Recuperar la url para coger el jad o consultar de alguna manera el JAD, cuando el usuario ya ha elegido un jad
        //y consultar que rol tiene en ese JAD.

        return ROLE_USUARIO . strtoupper($roleEnSesion);
    }

}