<?php

namespace Tfg\SesionJadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tfg\UsuarioBundle\Entity\Usuario;
use Tfg\UsuarioBundle\Form\GestionCuentaUsuario\UsuarioType;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

use Tfg\SesionJadBundle\Classes\HandlerJSON;
use Tfg\SesionJadBundle\Classes\TurnosHandlerJSON;
use  Tfg\SesionJadBundle\Classes\ZmqHandler;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Tfg\SesionJadBundle\Event\SocketEvents;
use Tfg\SesionJadBundle\Event\RequestTurnEvent;
use Tfg\SesionJadBundle\Event\SocketEventListener;


class ApiRestController extends Controller{


    /**
     * @Route("sesionjad/turns", name="turns")
     * es utilizado por el móvil a través de la API REST
     */
    public function requestTurnAction(){

        //Obtener datos de usuario
        $request = $this->getRequest();
        $username = $request->request->get('usuario');
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('UsuarioBundle:Usuario')->findOneByEmail($username);

        $handlerTurnos = $this->get('handlerJSONTurnos');
        $path = $this->get('kernel')->locateResource('@SesionJadBundle');
        $handlerTurnos->setPath($path);
        $handlerTurnos->setDispatcher($this->get('event_dispatcher'));
        $handlerTurnos->setFilename($this->container->getParameter('file_turns'));
        $handlerTurnos->setEntityName("Tfg\SesionJadBundle\Classes\Turns");


        //Configuramos el listener pasandole el handlerZMQ
        //$handlerZMQ = $this->get('handlerZMQ');
        //$listener = new SocketEventListener($handlerZMQ);
        $listener = $this->get('socketEventListener');
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->addListener(SocketEvents::REQUEST_TURN, array($listener, 'onRequestTurn'));

        try{
        $handlerTurnos->appendDatesFile($user);
        }catch(\Exception $e){
            $response = new Response($e->getMessage());
            $response->setStatusCode(501);
            $response->headers->set('Content-type', 'text/plain');
            return $response;
        }



        $serializer = $this->container->get('serializer');
        $userJSON = $serializer->serialize($user, 'json');
        $response = new Response($userJSON);
        $response->setStatusCode(201);
        $response->headers->set('Content-type', 'application/json');
        $currentUrl = $this->getRequest()->getUri();
        $userId = $user->getId();
        $locationResource= "$currentUrl/$userId";
        $response->headers->set('Location',$locationResource);


        return $response;

        //Recuperar desde el fichero turnos un array con los turnos hasta el momento

    }

}

?>