<?php

namespace Tfg\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tfg\UsuarioBundle\Entity\Usuario;
use Tfg\UsuarioBundle\Form\GestionCuentaUsuario\UsuarioType;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class ApiRestController extends Controller{

	/**
     * @Route("/usuario/login")
     * @Template()
     */

	public function loginAction(){

		$request = $this->getRequest();
		$username = $request->query->get('usuario');

		$em = $this->get('doctrine.orm.entity_manager');
		$user = $em->getRepository('UsuarioBundle:Usuario')->findOneByEmail($username);
		$password = $user->getPassword();

		if($password == $request->query->get('password')){
			 $providerKey = 'frontend';
			 $roles = array('ROLE_USUARIO');
			 $token = new UsernamePasswordToken($user, null, $providerKey, $roles);
        	 $this->container->get('security.context')->setToken($token);

        	 $usuario = $this->get('security.context')->getToken()->getUser();

        	 $serializer = $this->container->get('serializer');
             $usuarioJSON = $serializer->serialize($usuario, 'json');

        	 $response = new Response($usuarioJSON);
        	 $response->setStatusCode(200);
        	 $response->headers->set('Content-type', 'application/json');
        	 //$response->setContent($username);
             return $response;
		}

		$response = new Response();
		$response->setStatusCode(404);
		return $response;
	}
}

?>