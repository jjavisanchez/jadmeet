<?php

namespace Tfg\JadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;

use Tfg\JadBundle\Entity\Supuesto;
use Tfg\JadBundle\Entity\TemaAbierto;
use Tfg\JadBundle\Entity\Restriccion;
use Tfg\SesionJadBundle\Form\Type\SupuestoType;
use Tfg\JadBundle\Form\GestionClaves\TemaAbiertoType;
use Tfg\SesionJadBundle\Form\Type\RestriccionType;

use Tfg\UsuarioBundle\Entity\Rol;
use Tfg\JadBundle\Entity\Jad;
use Tfg\JadBundle\Entity\JadUsuarioRol;
use Tfg\SesionJadBundle\Entity\SesionJad;



class DefaultController extends Controller
{

    //muestra el menu del JAD
    public function menuAction()
    {
    		$usuario = $this->get('security.context')->getToken()->getUser();
    		$em = $this->get('doctrine')->getEntityManager();

    		$jad = $this->getRequest()->getSession()->get('jad');
    		$rols = $this->getRequest()->getSession()->get('rol');
        $rol = new Rol();
        $rol->unserialize($rols);
        $proposito = $jad->getPropositos();
        $alcance = $jad->getAlcance();
        $objetivosDireccion = $jad->getObjetivosDireccion();

    		return $this->render('JadBundle:Default:jad_menu.html.twig',array('rol'=>$rol,
                                                                          'jad'=>$jad,
                                                                          'proposito'=>$proposito,
                                                                          'alcance'=>$alcance,
                                                                          'objetivosDireccion'=>$objetivosDireccion));

    }

    //muestra todas las sesiones del jad
    public function sesionesAction()
    {

            //Controlar si venimos de dentro de una sesion para eliminar sus ficheros
            $request = $this->getRequest();
            $uri_request = $request->headers->get('referer');

            //para cuando salimos del workshop eliminamos el fichero de turnos
            if ( preg_match("/workshop$/", $uri_request) )
              unlink("/Users/josejavi14/Sites/TfgJadMeet/src/Tfg/SesionJadBundle/turnos.json");


            $jad = $this->getRequest()->getSession()->get('jad');
            $rols = $this->getRequest()->getSession()->get('rol');
            $rol = new Rol();
            $rol->unserialize($rols);

            $em = $this->getDoctrine()->getEntityManager();
            $sesiones = $em->getRepository('SesionJadBundle:SesionJad')->findAll();

            return $this->render('JadBundle:Default:jad_sesiones.html.twig',array('rol'=>$rol,
                                                                                  'jad'=>$jad,
                                                                                  'sesiones'=>$sesiones));
    }

    //Entra en el menu de la sesión jad
    public function sesionAction($nombreSesion)
    {

        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');
        $rol = new Rol();
        $rol->unserialize($rols);
        $em = $this->getDoctrine()->getEntityManager();
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);

        $sesionUsuariosConfirmados = $em->getRepository('SesionJadBundle:SesionesJadUsuarios')->findBy(array('sesionJad' => $sesion, 'asistencia' => true));

        //Controlar si venimos de dentro de una sesion para eliminar sus ficheros
            $request = $this->getRequest();
            $uri_request = $request->headers->get('referer');

            //para cuando salimos del workshop eliminamos el fichero de turnos
            if ( preg_match("/workshop$/", $uri_request) ){
              $pathSesionBundle = $this->get('kernel')->locateResource('@SesionJadBundle');
              $file_turns = $this->container->getParameter('file_turns');
              unlink("$pathSesionBundle/$file_turns");
              $file_name_current_point = $this->container->getParameter('file_currentpoint');
              unlink("$pathSesionBundle/$file_name_current_point");
              $file_active_screens = $this->container->getParameter('file_activescreens');
              unlink("$pathSesionBundle/$file_active_screens");

            }

        //crear en session una variable para almacenar la sesion en la que estamos. Esta se deberia machacar cada vez que nos metemos en una sesion diferente.
        //En el caso del rol y el jad estas variables las guardo mediante el evento onclick. En este caso
        //consulto la variable que le llega al controlador desde el routing (url).
        //No termina de funcionar pq al serializar sesionJad la variable jad da problemas.
        //$this->getRequest()->getSession()->set('sesion_jad', $sesion->serialize());

//        $prueba_sesion = new SesionJad();
//        $sesionjadinsession = $this->getRequest()->getSession()->get('sesion_jad');
//        $prueba_sesion->unserialize($sesionjadinsession);


        return $this->render('SesionJadBundle:Default:jad_sesion.html.twig',array('sesion'=>$sesion,
                                                                                  'rol'=>$rol,
                                                                                  'jad'=>$jad,
                                                                                  'sesionUsuariosConfirmados'=>$sesionUsuariosConfirmados
                                                                                  ));
    }

    public function participantesAction(){
        $usuario = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('doctrine')->getEntityManager();

        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');
        $rol = new Rol();
        $rol->unserialize($rols);

        $usuariosSistema = $em->getRepository('UsuarioBundle:Usuario')->findAll();

        return $this->render('JadBundle:Default:jad_participantes.html.twig',array('rol'=>$rol,
                                                                          'usuarios'=>$usuariosSistema,
                                                                          'jad'=>$jad
                                                                          ));
    }

    public function calendarioAction(){
        $usuario = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('doctrine')->getEntityManager();

        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');
        $rol = new Rol();
        $rol->unserialize($rols);


        return $this->render('JadBundle:Default:jad_calendario.html.twig',array('rol'=>$rol,
                                                                          'jad'=>$jad
                                                                          ));
    }

    public function clavesAction(){
        $usuario = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('doctrine')->getEntityManager();

        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');
        $rol = new Rol();
        $rol->unserialize($rols);


        $supuesto = new Supuesto();
        $form = $this->createForm(new SupuestoType(), $supuesto);

         $acuerdos = $em->getRepository('JadBundle:Supuesto')->findByJad($jad);

        return $this->render('JadBundle:Default:jad_claves.html.twig',array('rol'=>$rol,
                                                                          'jad'=>$jad,
                                                                          'acuerdos'=>$acuerdos,
                                                                          'form'=>$form->createView()
                                                                          ));
    }

    public function objetivosAction(){
        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');

        //La varialble rol dentro de la sesión esta serializada cuadno se guardo en el default controller dentro
        //de UsuarioBundle al guardar las variables del usuario en sesión con el método actualizarSesionVar()
        //por ello es necesario deserializar cada vez que se recupera
        $rol = unserialize($rols);

        $em = $this->getDoctrine()->getEntityManager();


        return $this->render('JadBundle:Default:jad_objetivos.html.twig', array('rol'=>$rol,
                                                                                'jad'=>$jad));
    }

    public function acuerdosAction(){

      $usuario = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('doctrine')->getEntityManager();

        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');
        $rol = new Rol();
        $rol->unserialize($rols);


        $supuesto = new Supuesto();
        $form = $this->createForm(new SupuestoType(), $supuesto);

         $acuerdos = $em->getRepository('JadBundle:Supuesto')->findByJad($jad);

        return $this->render('JadBundle:Default:jad_acuerdos.html.twig',array('rol'=>$rol,
                                                                          'jad'=>$jad,
                                                                          'acuerdos'=>$acuerdos,
                                                                          'form'=>$form->createView()
                                                                          ));
    }

    public function restriccionesAction(){

        $peticion = $this->getRequest();
        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');

        //La varialble rol dentro de la sesión esta serializada cuadno se guardo en el default controller dentro
        //de UsuarioBundle al guardar las variables del usuario en sesión con el método actualizarSesionVar()
        //por ello es necesario deserializar cada vez que se recupera
        $rol = unserialize($rols);

        $em = $this->getDoctrine()->getEntityManager();
        $real_jad = $em->getRepository('JadBundle:Jad')->findOneBySlug($jad->getSlug());

        $restricciones = $em->getRepository('JadBundle:Restriccion')->findByJad($jad);

        $restriccion = new Restriccion();
        $form = $this->createForm(new RestriccionType('nuevo'), $restriccion);

         if ($peticion->getMethod() == 'POST') {
            $form->bindRequest($peticion);

                if ($form->isValid()) {
                    $restriccion->setJad($real_jad);
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($restriccion);
                    $em->flush();
                }

                $restricciones = $em->getRepository('JadBundle:Restriccion')->findByJad($jad);
                return $this->render('JadBundle:Default:jad_restricciones.html.twig', array('sesion'=>$sesion,
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad,
                                                                                                           'restricciones'=>$restricciones,
                                                                                                           'form'=>$form->createView()));
        }

        return $this->render('JadBundle:Default:jad_restricciones.html.twig', array('rol'=>$rol,
                                                                                    'jad'=>$jad,
                                                                                    'form'=>$form->createView(),
                                                                                    'restricciones'=>$restricciones));
    }



    public function temasAbiertosAction(){

        $peticion = $this->getRequest();
        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');

        //La varialble rol dentro de la sesión esta serializada cuadno se guardo en el default controller dentro
        //de UsuarioBundle al guardar las variables del usuario en sesión con el método actualizarSesionVar()
        //por ello es necesario deserializar cada vez que se recupera
        $rol = unserialize($rols);

        $em = $this->getDoctrine()->getEntityManager();
        $real_jad = $em->getRepository('JadBundle:Jad')->findOneBySlug($jad->getSlug());
        $temas_abiertos = $em->getRepository('JadBundle:TemaAbierto')->findByJad($jad);

        $tema_abierto = new TemaAbierto();

        $form = $this->createForm(new TemaAbiertoType('nuevo'), $tema_abierto, array('jad'=>$jad));




         if ($peticion->getMethod() == 'POST') {
            $form->bindRequest($peticion);

                if ($form->isValid()) {
                    $tema_abierto->setJad($real_jad);
                    $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
                    $filename = $this->container->getParameter('file_currentpoint');
                    $fullpath = "$bundlePath/$filename";
                    $id_punto_actual = "";
                    if(file_exists($fullpath)){
                        $handlerPunto = $this->get('handlerJSONPuntos');
                        $handlerPunto->setPath($bundlePath);
                        $handlerPunto->setFilename($filename);
                        $handlerPunto->setEntityName('array<string,string>');
                        $handlerPunto->setDispatcher($this->get('event_dispatcher'));
                        $punto_actual = $handlerPunto->readFile();
                        $id_punto_actual = $punto_actual['punto'];
                    }
                    $punto_actual = $em->getRepository('SesionJadBundle:PuntoAgenda')->find($id_punto_actual);
                    $temas_abiertos = $em->getRepository('JadBundle:TemaAbierto')->findByJad($sesion);
                    $tema_abierto->setPuntoAgenda($punto_actual);
                    $em->persist($tema_abierto);
                    $em->flush();
                    //lanzamos el evento new agreement.
                }

                $temas_abiertos = $em->getRepository('JadBundle:TemaAbierto')->findByJad($sesion);
                return $this->render('JadBundle:Default:jad_temas_abiertos.html.twig', array('rol'=>$rol,
                                                                                                           'jad'=>$jad,
                                                                                                           'temas_abiertos'=>$temas_abiertos,
                                                                                                           'form'=>$form->createView()));
        }


        return $this->render('JadBundle:Default:jad_temas_abiertos.html.twig', array(
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad,
                                                                                                           'temas_abiertos'=>$temas_abiertos,
                                                                                                           'form'=>$form->createView()));
    }



    //Funcion auxiliar para obtener colores aleatorios definidos en el css al abrir la pantalla que muestra las sesiones de un jad
    public function getColorAction($colors)
   {
       $colores = array('red','blue','yellow','purple','orange','blueocean','black');
       $aleatorio = rand(0,6);
       foreach ($colors as $c){
           if($c == $colores[$aleatorio])
           {
               return new Response($this->getColorAction($colors));
           }
       }

       return new Response($colores[$aleatorio]);
   }

   /**
     *  @Route("/getinfosesionjad", name="getInfoSesionJAD")
     *  Funcion creada para obtener la información de una sesion JAD o Workshop y mostrarla en el resumen de la pantalla Home
     *  cuando el usuario pasa el ratón por encima de un icono que representa un Workshop, es llamada mediante AJAX en una petición GET
     */

   public function getInfoSesionJAD(){

     $em = $this->get('doctrine')->getEntityManager();
     $serializer = $this->container->get('serializer');
     //$resumen = array();

     //Cogo la variable id pasada desde la peticion ajax
     $sesionId = $this->get('request')->get('sesionId');

     $sesionUsuariosConfirmados = $em->getRepository('SesionJadBundle:SesionesJadUsuarios')->findBy(array('jad' => $sesionId, 'asistencia' => true));
     //$resumen[] = $serializer->serialize($sesionUsuariosConfirmados,'json');
     $resumenJSON = $serializer->serialize($sesionUsuariosConfirmados,'json');

    return new Response($resumenJSON);
   }
}
