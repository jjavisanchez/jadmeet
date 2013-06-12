<?php

namespace Tfg\UsuarioBundle\Controller;

use Tfg\JadBundle\Entity\Jad;
use Tfg\UsuarioBundle\Form\GestionHomeUsuario\jadsDeUsuarioType;
use Tfg\JadBundle\Form\GestionJad\jadType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tfg\UsuarioBundle\Entity\Usuario;
use Tfg\UsuarioBundle\Entity\Rol;
use Tfg\JadBundle\Entity\JadUsuarioRol;
use Tfg\JadBundle\Entity\Clave;
use Tfg\JadBundle\Entity\JadUsuarioRolRepository;
use Tfg\UsuarioBundle\Form\GestionCuentaUsuario\UsuarioType;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    public function loginAction()
    {

        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();

        $error = $peticion->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR,
                $sesion->get(SecurityContext::AUTHENTICATION_ERROR));


        return $this->render('UsuarioBundle:Default:login.html.twig', array('ultimo_usuario' => $sesion->get(SecurityContext::LAST_USERNAME),'error' => $error));

    }

    public function registroAction()
       {

        $peticion = $this->getRequest();

        $usuario = new Usuario();

        $formulario = $this->createForm(new UsuarioType(), $usuario);

        if ($peticion->getMethod() == 'POST') {
            $formulario->bindRequest($peticion);

                if ($formulario->isValid()) {
//                    $encoder = $this->get('security.encoder_factory')
//                                 ->getEncoder($usuario);
                    $usuario->setSalt(null);
//                    $passwordCodificado = $encoder->encodePassword(
//                         $usuario->getPassword(),
//                         $usuario->getSalt()
//                         );
//                    $usuario->setPassword($passwordCodificado);

                    $usuario->setSlug($usuario->getEmail());
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($usuario);
                    $em->flush();

                    $this->get('session')->setFlash('info', '¡Enhorabuena! Te has registrado correctamente en JadMeetUs');

                    $token = new UsernamePasswordToken(
                        $usuario,
                        $usuario->getPassword(),
                        'usuarios',
                        $usuario->getRoles()
                        );
                $this->container->get('security.context')->setToken($token);

                    return $this->redirect($this->generateUrl('home_usuario'));
             }
        }

        return $this->render(
                             'UsuarioBundle:Default:registro.html.twig',
                             array('formulario' => $formulario->createView())
                );
       }

       public function nuevojadAction(){

            $peticion = $this->getRequest();
            $jad = new Jad();

            $formulario = $this->createForm(new JadType(), $jad);


            return $this->render(
                                    'JadBundle:Default:nuevo_jad.html.twig',
                                    array('formulario' => $formulario->createView())
                );
       }

    public function homeAction(){

         //Controlar si venimos de dentro de una sesion para eliminar sus ficheros
            $request = $this->getRequest();
            $uri_request = $request->headers->get('referer');

            //para cuando salimos del workshop eliminamos el fichero de turnos
            if ( preg_match("/workshop$/", $uri_request) )
              unlink("/Users/josejavi14/Sites/TfgJadMeet/src/Tfg/SesionJadBundle/turnos.json");

        $usuario = $this->get('security.context')->getToken()->getUser();
        $nombre = $usuario->getNombre();

        $em = $this->get('doctrine')->getEntityManager();
        $formulario = $this->createForm(new jadsDeUsuarioType($em), null , array('usuario'=>$usuario));


        $jurs = $em->getRepository('JadBundle:JadUsuarioRol')->findByUsuario($usuario);


        return $this->render('UsuarioBundle:Default:home.html.twig', array(
                                                    'formulario' => $formulario->createView(),
                                                    'jurs' => $jurs));
    }

    /**
     * @Route("/ReunionesByJad", name="ReunionesByJad")
     * es utilizado por el la llamada ajax, javascript
     */
    /*public function getReunionesByJad()
    {
    	$this->em = $this->get('doctrine')->getEntityManager();
    	$this->repository = $this->em->getRepository('JadBundle:Reunion');

    	$jadId = $this->get('request')->query->get('data');

    	$serializer = $this->container->get('serializer');
    	//controlamos el valor por defecto del dropdownlist o select
    	if(empty($jadId))
    	{
    		$noJadSeleccionadoJSON = $serializer->serialize('No has seleccionado un Jad','json');
    		$temasAbiertosJSON = $serializer->serialize('No hay temas abiertos para este jad','json');
    		$resumen = array($noJadSeleccionadoJSON,$temasAbiertosJSON);
    		$resumenJSON = $serializer->serialize($resumen,'json');
    		return new Response($resumenJSON);
    	}
    	else{
    		//Controlar que para el jad seleccionado el usuario actual es el coordinador
    		$usuario = $this->get('security.context')->getToken()->getUser();
    		$em = $this->get('doctrine')->getEntityManager();
    		$jad = $em->getRepository('JadBundle:Jad')->find($jadId);
    		$jur = $em->getRepository('JadBundle:JadUsuarioRol')->findOneBy(array('jad'=>$jad, 'usuario' =>$usuario));
    		$rol = $jur->getRol()->getNombre();

    		//Obtener los temas abiertos o tareas que el usuario tiene para el jad seleccionado
		    $temasAbiertos = $em->getRepository('JadBundle:TemaAbierto')->findByJad($jadId);
		    if(!$temasAbiertos){
		    	$temasAbiertosJSON = $serializer->serialize('No hay temas abiertos para este jad','json');
		    }else
			    {
				    $temasAbiertosResumen = array();
				    foreach($temasAbiertos as $tAbierto){
				    	$aux = array('nombre'=>$tAbierto->getNombre(), 'fecha'=>$tAbierto->getFechaLimite()->format('d-m H:i'));
				    	$temasAbiertosResumen[]= $aux;
				    }
				    $temasAbiertosJSON = $serializer->serialize($temasAbiertosResumen,'json');
			    }

    		if ($rol == 'Coordinador'){
	    		$reuniones = $this->repository->findBy(array('jad'=>$jadId));
	    		$reunionesResumen = array();

	    		if (empty($reuniones))
	    		{
	    			$noHayReunionesJSON = $serializer->serialize('No hay reuniones','json');
	    			$resumen = array($noHayReunionesJSON,$temasAbiertosJSON);
	    			$resumenJSON = $serializer->serialize($resumen,'json');
	    			return new Response($resumenJSON);
	    		}
	    		foreach($reuniones as $reunion)
	    		{
	    			$aux = array('nombre'=>$reunion->getNombre(),'fecha'=>$reunion->getFecha());
	    			$reunionesResumen[] = $aux;
	    		}
	    		$reunionesJSON = $serializer->serialize($reunionesResumen, 'json');
	    		$resumen = array($reunionesJSON,$temasAbiertosJSON);
	    		$resumenJSON = $serializer->serialize($resumen,'json');
	    		return new Response($resumenJSON);
    		}

    		$noCoordinadorJSON = $serializer->serialize('No eres coordinador de este jad','json');
    		$resumen = array($noCoordinadorJSON,$temasAbiertosJSON);
    		$resumenJSON = $serializer->serialize($resumen, 'json');
    		return new Response($resumenJSON);
    	}
    }*/


    /**
     * Funcion creada para empotrar en la vista home.html.twig otra vista a traves de este controller
     */
    public function itemListJadAction($jur){
        //$usuario = $this->get('security.context')->getToken()->getUser();
        //$em = $this->get('doctrine')->getEntityManager();
        //$jad = $em->getRepository('JadBundle:Jad')->find($jadId);
    	//$jur = $em->getRepository('JadBundle:JadUsuarioRol')->findOneBy(array('jad'=>$jad, 'usuario' =>$usuario));
    	//$rol = $jur->getRol()->getSlug();

        return $this->render('UsuarioBundle:Default:itemListJad.html.twig', array('jur'=>$jur));

    }

    /**
     *  @Route("/actualizarSesionVar", name="actualizarSesionVar")
     * Funcion creada para guardar en variables de sesión el rol del usuario en el jad que ha elegido en la pantalla
     * home.
     */
    public function actualizarSesionVar()
    {
        $usuario = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('doctrine')->getEntityManager();

        //Cogo la variable id pasada desde la peticion ajax
        $id = $this->get('request')->get('jadId');

        $jad = $em->getRepository('JadBundle:Jad')->find($id);
        $jur = $em->getRepository('JadBundle:JadUsuarioRol')->findOneBy(array('jad'=>$jad,'usuario' =>$usuario));
        $rol = $jur->getRol();


        //Ahora vamos a guardar los datos en variables de sesión para tenerlos accesibles
        $this->getRequest()->getSession()->set('rol', $rol->serialize());
        $this->getRequest()->getSession()->set('jad', $jad);

        return new Response('ok');
    }
    /**
     *  @Route("/getinfojad", name="getInfoJAD")
     *  Funcion creada para obtener la información de un JAD y mostrarla en el resumen de la pantalla Home
     *  a partir del JAD que el usuario selecciona, es llamada mediante AJAX en una petición GET
     */
    public function getInfoJAD(){

        $usuario = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('doctrine')->getEntityManager();
        $serializer = $this->container->get('serializer');
        $resumen = array();

        //Cogo la variable id pasada desde la peticion ajax
        $jadId = $this->get('request')->get('jadId');

        //Obtenemos el rol del usuario
        $jur = $em->getRepository('JadBundle:JadUsuarioRol')->findOneBy(array('jad'=>$jadId, 'usuario' =>$usuario));
        $rol = array( 'rol' => $jur->getRol()->getNombre());

        $resumen[] = $serializer->serialize($rol,'json');

        //Si el rol es coordinador obtenemos las reuniones
        if ($rol['rol'] == 'Coordinador'){
        //Consulta Reuniones
        $emReunion = $em->getRepository('JadBundle:Reunion');
        $reuniones = $emReunion->findBy(array('jad'=>$jadId));
        $reunionesResumen = array();
            if (empty($reuniones))
            {
                $noHayReunionesJSON = $serializer->serialize('No hay reuniones','json');
                $resumen[] = $noHayReunionesJSON;
            }else{
                    foreach($reuniones as $reunion)
                    {
                        $aux = array('nombre'=>$reunion->getNombre(),'fecha'=>$reunion->getFecha());
                        $reunionesResumen[] = $aux;
                    }
                    $reunionesJSON = $serializer->serialize($reunionesResumen,'json');
                    $resumen[] = $reunionesJSON;
                }
        } //end if

        //Obtenemos los temas abiertos para todos los usuarios
        $temasAbiertos = $em->getRepository('JadBundle:TemaAbierto')->findByJad($jadId);
        $temasAbiertosResumen = array();
        if(empty($temasAbiertos)){
            $noHayTemasAbiertosJSON = $serializer->serialize('No hay temas abiertos','json');
            $resumen[] = $noHayTemasAbiertosJSON;
        }else
            {
                foreach($temasAbiertos as $tAbierto){
                    $aux = array('nombre'=>$tAbierto->getNombre(), 'fecha'=>$tAbierto->getFechaLimite()->format('d-m H:i'));
                    $temasAbiertosResumen[]= $aux;
                }
                $temasAbiertosJSON = $serializer->serialize($temasAbiertosResumen,'json');
                $resumen[] = $temasAbiertosJSON;
            }
            $resumenJSON = $serializer->serialize($resumen,'json');
            return new Response($resumenJSON);
    }
}
