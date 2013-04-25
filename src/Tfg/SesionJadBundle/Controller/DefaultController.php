<?php
namespace Tfg\SesionJadBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;

use Tfg\SesionJadBundle\SesionJadBundle;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tfg\SesionJadBundle\Classes\HandlerJSON;
use Tfg\SesionJadBundle\Classes\Turns;
use Tfg\UsuarioBundle\Entity\Usuario;
use Tfg\SesionJadBundle\Entity\SesionJad;

use Tfg\JadBundle\Entity\Supuesto;
use Tfg\JadBundle\Entity\TemaAbierto;
use Tfg\JadBundle\Entity\Restriccion;
use Tfg\SesionJadBundle\Form\Type\SupuestoType;
use Tfg\SesionJadBundle\Form\Type\TemaAbiertoType;
use Tfg\SesionJadBundle\Form\Type\RestriccionType;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Tfg\SesionJadBundle\Event\SocketEvents;
use Tfg\SesionJadBundle\Event\RemoveTurnEvent;
use Tfg\SesionJadBundle\Event\NewAgreementEvent;
use Tfg\SesionJadBundle\Event\EditAgreementEvent;
use Tfg\SesionJadBundle\Event\RemoveAgreementEvent;
use Tfg\SesionJadBundle\Event\SocketEventListener;



class DefaultController extends Controller
{

    //inicia la sesión o workshop
    public function inWorkshopAction($nombreSesion)
    {

        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');

        //La varialble rol dentro de la sesión esta serializada cuadno se guardo en le default controller dentro
        //de UsuarioBundle al guardar las variables del usuario en sesión con el método actualizarSesionVar()
        //por ello es necesario deserializar cada vez que se recupera
        $rol = unserialize($rols);

        $em = $this->getDoctrine()->getEntityManager();
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);

        $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
        $filename = $this->container->getParameter('file_turns');
        $fullpath = "$bundlePath/$filename";
        $turnsList = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerTurnos = $this->get('handlerJSONTurnos');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerTurnos->setPath($bundlePath);
            $handlerTurnos->setFilename($filename);
            $handlerTurnos->setEntityName("Tfg\SesionJadBundle\Classes\Turns");
            $turns = $handlerTurnos->readFile();
            //Con la variable $turns (objeto de la clase Turns) a traves del metodo getTurnsList() recuperamos el array con todos los turnos pedidos hasta el momento, la cual pasaremos a la plantilla.
            $turnsList = $turns->getTurnsList();
        }else{
                //Array a partir del cual inicializo el fichero .json sin ningun usuario
                $turns = new Turns();
                //Obtenemos el handler desde un un service container dentro de la clase handlerjson
                $handlerTurnos = $this->get('handlerJSONTurnos');
                //Configuramos el handler mediante la inyeccion de dependencias a traves de los setters
                $handlerTurnos->setPath($bundlePath);
                $handlerTurnos->setFilename($filename);
                //Finalmente creamos el fichero
                $handlerTurnos->createFile($turns);
        }

        $filename = $this->container->getParameter('file_currentpoint');
        $fullpath = "$bundlePath/$filename";
        $punto = null;
        $puntosAgenda = $em->getRepository('SesionJadBundle:PuntoAgenda')->findBySesionJad($sesion->getId());
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerPoint = $this->get('handlerJSONPuntos');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerPoint->setPath($bundlePath);
            $handlerPoint->setFilename($filename);
            $handlerPoint->setEntityName('array<string,string>');
            $array_punto = $handlerPoint->readFile();
            $punto = $array_punto['punto'];
            $punto = $em->getRepository('SesionJadBundle:PuntoAgenda')->find($punto);
        }else{
            $id_primer_punto_agenda = $puntosAgenda[0]->getId();
            $array_punto = array('punto'=>$id_primer_punto_agenda);
            $punto = $array_punto['punto'];
            $punto = $em->getRepository('SesionJadBundle:PuntoAgenda')->find($punto);
            $handlerPoint = $this->get('handlerJSONPuntos');
            $handlerPoint->setPath($bundlePath);
            $handlerPoint->setFilename($filename);
            $handlerPoint->createFile($array_punto);
        }

        $filename = $this->container->getParameter('file_activescreens');
        $fullpath = "$bundlePath/$filename";
        $screens = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerScreen = $this->get('handlerJSONPantallas');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerScreen->setPath($bundlePath);
            $handlerScreen->setFilename($filename);
            $handlerScreen->setEntityName('array<string,string>');
            $screens = $handlerScreen->readFile();
        }else{
            $screens = array('A'=>"agenda", 'B'=>"turnos");
            $handlerScreen = $this->get('handlerJSONPantallas');
            $handlerScreen->setPath($bundlePath);
            $handlerScreen->setFilename($filename);
            $handlerScreen->createFile($screens);
        }

        $acuerdos = $em->getRepository('JadBundle:Supuesto')->findByJad($sesion);

        return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop.html.twig', array('sesion'=>$sesion,
                                                                                              'rol'=>$rol,
                                                                                              'jad'=>$jad,
                                                                                              'puntosAgenda'=>$puntosAgenda,
                                                                                              'turnos'=>$turnsList,
                                                                                              'puntoAgendaActual'=>$punto,
                                                                                              'screens'=>$screens,
                                                                                              'acuerdos'=>$acuerdos));
    }

     //inicia la pantalla de administración mostrando la pestaña de turnos
    public function inWorkshopAdminAction($nombreSesion)
    {
        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');

        //La varialble rol dentro de la sesión esta serializada cuadno se guardo en le default controller dentro
        //de UsuarioBundle al guardar las variables del usuario en sesión con el método actualizarSesionVar()
        //por ello es necesario deserializar cada vez que se recupera
        $rol = unserialize($rols);

        $em = $this->getDoctrine()->getEntityManager();
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);

        $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
        $filename = $this->container->getParameter('file_turns');
        $fullpath = "$bundlePath/$filename";
        $turnsList = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerTurnos = $this->get('handlerJSONTurnos');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerTurnos->setPath($bundlePath);
            $handlerTurnos->setFilename($filename);
            $handlerTurnos->setEntityName("Tfg\SesionJadBundle\Classes\Turns");
            $turns = $handlerTurnos->readFile();
            //Con la variable $turns (objeto de la clase Turns) a traves del metodo getTurnsList() recuperamos el array con todos los turnos pedidos hasta el momento, la cual pasaremos a la plantilla.
            $turnsList = $turns->getTurnsList();
        }
        $filename = $this->container->getParameter('file_activescreens');
        $fullpath = "$bundlePath/$filename";
        $screens = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerScreen = $this->get('handlerJSONPantallas');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerScreen->setPath($bundlePath);
            $handlerScreen->setFilename($filename);
            $handlerScreen->setEntityName('array<string,string>');
            $screens = $handlerScreen->readFile();
        }

        return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin.html.twig', array('sesion'=>$sesion,
                                                                                              'rol'=>$rol,
                                                                                              'jad'=>$jad,
                                                                                              'turnos'=>$turnsList,
                                                                                              'screens'=>$screens));
    }

        /*=======================================================================================================================
    ======================================== Funciones del controlador llamadas desde AJAX-jquery con load() ====================
    =========================================================================================================================*/

     //Pestaña de administración con la Agenda
    public function inWorkshopAdminAgendaAction($nombreSesion)
    {
        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');

        //La varialble rol dentro de la sesión esta serializada cuadno se guardo en el default controller dentro
        //de UsuarioBundle al guardar las variables del usuario en sesión con el método actualizarSesionVar()
        //por ello es necesario deserializar cada vez que se recupera
        $rol = unserialize($rols);

        $em = $this->getDoctrine()->getEntityManager();
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);

        $puntosAgenda = $em->getRepository('SesionJadBundle:PuntoAgenda')->findBySesionJad($sesion->getId());

        $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
        $filename = $this->container->getParameter('file_currentpoint');
        $fullpath = "$bundlePath/$filename";
        $punto = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerPoint = $this->get('handlerJSONPuntos');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerPoint->setPath($bundlePath);
            $handlerPoint->setFilename($filename);
            $handlerPoint->setEntityName('array<string,string>');
            $array_punto = $handlerPoint->readFile();
            $punto = $array_punto['punto'];
            $punto = $em->getRepository('SesionJadBundle:PuntoAgenda')->find($punto);
        }
        $filename = $this->container->getParameter('file_activescreens');
        $fullpath = "$bundlePath/$filename";
        $screens = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerScreen = $this->get('handlerJSONPantallas');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerScreen->setPath($bundlePath);
            $handlerScreen->setFilename($filename);
            $handlerScreen->setEntityName('array<string,string>');
            $screens = $handlerScreen->readFile();
        }

        return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin_agenda.html.twig', array('sesion'=>$sesion,
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad,
                                                                                                           'puntosAgenda'=>$puntosAgenda,
                                                                                                           'puntoAgendaActual'=>$punto,
                                                                                                           'screens'=>$screens));
    }

    //Pestaña de administración con los turnos
    public function inWorkshopAdminTurnosAction($nombreSesion)
    {
        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');

        //La varialble rol dentro de la sesión esta serializada cuadno se guardo en el default controller dentro
        //de UsuarioBundle al guardar las variables del usuario en sesión con el método actualizarSesionVar()
        //por ello es necesario deserializar cada vez que se recupera
        $rol = unserialize($rols);

        $em = $this->getDoctrine()->getEntityManager();
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);

        $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
        $filename = $this->container->getParameter('file_turns');
        $fullpath = "$bundlePath/$filename";
        $turnsList = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerTurnos = $this->get('handlerJSONTurnos');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerTurnos->setPath($bundlePath);
            $handlerTurnos->setFilename($filename);
            $handlerTurnos->setEntityName("Tfg\SesionJadBundle\Classes\Turns");
            $turns = $handlerTurnos->readFile();
            //Con la variable $turns (objeto de la clase Turns) a traves del metodo getTurnsList() recuperamos el array con todos los turnos pedidos hasta el momento, la cual pasaremos a la plantilla.
            $turnsList = $turns->getTurnsList();
        }
        $filename = $this->container->getParameter('file_activescreens');
        $fullpath = "$bundlePath/$filename";
        $screens = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerScreen = $this->get('handlerJSONPantallas');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerScreen->setPath($bundlePath);
            $handlerScreen->setFilename($filename);
            $handlerScreen->setEntityName('array<string,string>');
            $screens = $handlerScreen->readFile();
        }

        return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin_turnos.html.twig', array('sesion'=>$sesion,
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad,
                                                                                                           'turnos'=>$turnsList,
                                                                                                           'screens'=>$screens));
    }

    public function inWorkshopAdminObjetivosAction($nombreSesion)
    {
        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');

        //La varialble rol dentro de la sesión esta serializada cuadno se guardo en el default controller dentro
        //de UsuarioBundle al guardar las variables del usuario en sesión con el método actualizarSesionVar()
        //por ello es necesario deserializar cada vez que se recupera
        $rol = unserialize($rols);

        $em = $this->getDoctrine()->getEntityManager();
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);

        return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin_objetivos.html.twig', array('sesion'=>$sesion,
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad));
    }

    public function inWorkshopAdminAcuerdosAction($nombreSesion)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $peticion = $this->getRequest();


        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');

        $supuesto = new Supuesto();
        $form = $this->createForm(new SupuestoType(), $supuesto);

        //La varialble rol dentro de la sesión esta serializada cuadno se guardo en el default controller dentro
        //de UsuarioBundle al guardar las variables del usuario en sesión con el método actualizarSesionVar()
        //por ello es necesario deserializar cada vez que se recupera
        $rol = unserialize($rols);
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);
        $acuerdos = $em->getRepository('JadBundle:Supuesto')->findByJad($sesion);
        $real_jad = $em->getRepository('JadBundle:Jad')->findOneBySlug($jad->getSlug());

        $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
        $filename = $this->container->getParameter('file_activescreens');
        $fullpath = "$bundlePath/$filename";
        $screens = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerScreen = $this->get('handlerJSONPantallas');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerScreen->setPath($bundlePath);
            $handlerScreen->setFilename($filename);
            $handlerScreen->setEntityName('array<string,string>');
            $screens = $handlerScreen->readFile();
        }

        $listener = $this->get('socketEventListener');
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->addListener(SocketEvents::NEW_AGREEMENT, array($listener, 'onNewAgreement'));

        if ($peticion->getMethod() == 'POST') {
            $form->bindRequest($peticion);

                if ($form->isValid()) {
                    $supuesto->setJad($real_jad);
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($supuesto);
                    $em->flush();
                    //lanzamos el evento new agreement.
                    $mensajeZMQ = array('evento'=>SocketEvents::NEW_AGREEMENT, 'id'=>$supuesto->getId(), 'nombre'=>$supuesto->getNombre(), 'descripcion'=>$supuesto->getDescripcion());
                    $mensajeZMQ = json_encode($mensajeZMQ);
                    $event = new NewAgreementEvent($mensajeZMQ);
                    $dispatcher->dispatch(SocketEvents::NEW_AGREEMENT, $event);
                }

                $acuerdos = $em->getRepository('JadBundle:Supuesto')->findByJad($sesion);
                return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin_acuerdos.html.twig', array('sesion'=>$sesion,
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad,
                                                                                                           'acuerdos'=>$acuerdos,
                                                                                                           'form'=>$form->createView(),
                                                                                                           'screens'=>$screens));
        }

        return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin_acuerdos.html.twig', array('sesion'=>$sesion,
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad,
                                                                                                           'acuerdos'=>$acuerdos,
                                                                                                           'form'=>$form->createView(),
                                                                                                           'screens'=>$screens));
    }

    public function inWorkshopAdminEliminarAcuerdoAction($nombreSesion, $id){
        $em = $this->getDoctrine()->getEntityManager();

        $jad = $this->getRequest()->getSession()->get('jad');
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);
        $rols = $this->getRequest()->getSession()->get('rol');
        $rol = unserialize($rols);

        $listener = $this->get('socketEventListener');
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->addListener(SocketEvents::REMOVE_AGREEMENT, array($listener, 'onRemoveAgreement'));

        $supuesto =  $em->getRepository('JadBundle:Supuesto')->find($id);

         if (!$supuesto) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        $em->remove($supuesto);
        $em->flush();
        //lanzamos el evento remove agreement.
        $mensajeZMQ = array('evento'=>SocketEvents::REMOVE_AGREEMENT, 'id'=>$id);
        $mensajeZMQ = json_encode($mensajeZMQ);
        $event = new RemoveAgreementEvent($mensajeZMQ);
        $dispatcher->dispatch(SocketEvents::REMOVE_AGREEMENT, $event);

        $acuerdos = $em->getRepository('JadBundle:Supuesto')->findByJad($sesion);

        $supuesto = new Supuesto();
        $form = $this->createForm(new SupuestoType(), $supuesto);

        $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
        $filename = $this->container->getParameter('file_activescreens');
        $fullpath = "$bundlePath/$filename";
        $screens = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerScreen = $this->get('handlerJSONPantallas');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerScreen->setPath($bundlePath);
            $handlerScreen->setFilename($filename);
            $handlerScreen->setEntityName('array<string,string>');
            $screens = $handlerScreen->readFile();
        }


        return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin_acuerdos.html.twig', array('sesion'=>$sesion,
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad,
                                                                                                           'acuerdos'=>$acuerdos,
                                                                                                           'form'=>$form->createView(),
                                                                                                           'screens'=>$screens));
    }

    public function inWorkshopAdminEditarAcuerdoAction($nombreSesion, $id){

        $nombre = $this->get('request')->get('nombre');
        $descripcion = $this->get('request')->get('descripcion');

        $em = $this->getDoctrine()->getEntityManager();

        $jad = $this->getRequest()->getSession()->get('jad');
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);
        $rols = $this->getRequest()->getSession()->get('rol');
        $rol = unserialize($rols);

        $listener = $this->get('socketEventListener');
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->addListener(SocketEvents::EDIT_AGREEMENT, array($listener, 'onEditAgreement'));

        $supuesto =  $em->getRepository('JadBundle:Supuesto')->find($id);

         if (!$supuesto) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $supuesto->setNombre($nombre);
        $supuesto->setDescripcion($descripcion);
        $em->flush();

        //lanzamos el evento remove agreement.
        $mensajeZMQ = array('evento'=>SocketEvents::EDIT_AGREEMENT, 'id'=>$supuesto->getId(), 'nombre'=>$supuesto->getNombre(), 'descripcion'=>$supuesto->getDescripcion());
        $mensajeZMQ = json_encode($mensajeZMQ);
        $event = new EditAgreementEvent($mensajeZMQ);
        $dispatcher->dispatch(SocketEvents::EDIT_AGREEMENT, $event);

        $acuerdos = $em->getRepository('JadBundle:Supuesto')->findByJad($sesion);

        $supuesto = new Supuesto();
        $form = $this->createForm(new SupuestoType(), $supuesto);

        $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
        $filename = $this->container->getParameter('file_activescreens');
        $fullpath = "$bundlePath/$filename";
        $screens = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerScreen = $this->get('handlerJSONPantallas');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerScreen->setPath($bundlePath);
            $handlerScreen->setFilename($filename);
            $handlerScreen->setEntityName('array<string,string>');
            $screens = $handlerScreen->readFile();
        }

        return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin_acuerdos.html.twig', array('sesion'=>$sesion,
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad,
                                                                                                           'acuerdos'=>$acuerdos,
                                                                                                           'form'=>$form->createView(),
                                                                                                           'screens'=>$screens));
    }

     public function inWorkshopAdminTemasAbiertosAction($nombreSesion)
    {

        $peticion = $this->getRequest();
        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');

        //La varialble rol dentro de la sesión esta serializada cuadno se guardo en el default controller dentro
        //de UsuarioBundle al guardar las variables del usuario en sesión con el método actualizarSesionVar()
        //por ello es necesario deserializar cada vez que se recupera
        $rol = unserialize($rols);

        $em = $this->getDoctrine()->getEntityManager();
        $real_jad = $em->getRepository('JadBundle:Jad')->findOneBySlug($jad->getSlug());
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);
        $temas_abiertos = $em->getRepository('JadBundle:TemaAbierto')->findByJad($sesion);

        $tema_abierto = new TemaAbierto();

        $form = $this->createForm(new TemaAbiertoType('nuevo'), $tema_abierto, array('sesion'=>$sesion));

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
                    $tema_abierto->setPuntoAgenda($punto_actual);
                    $em->persist($tema_abierto);
                    $em->flush();
                    //lanzamos el evento new agreement.
                    /*$mensajeZMQ = array('evento'=>SocketEvents::NEW_AGREEMENT, 'id'=>$supuesto->getId(), 'nombre'=>$supuesto->getNombre(), 'descripcion'=>$supuesto->getDescripcion());
                    $mensajeZMQ = json_encode($mensajeZMQ);
                    $event = new NewAgreementEvent($mensajeZMQ);
                    $dispatcher->dispatch(SocketEvents::NEW_AGREEMENT, $event);*/
                }

                $acuerdos = $em->getRepository('JadBundle:Supuesto')->findByJad($sesion);
                return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin_temasabiertos.html.twig', array('sesion'=>$sesion,
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad,
                                                                                                           'acuerdos'=>$acuerdos,
                                                                                                           'form'=>$form->createView()));
        }


        return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin_temasabiertos.html.twig', array('sesion'=>$sesion,
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad,
                                                                                                           'temas_abiertos'=>$temas_abiertos,
                                                                                                           'form'=>$form->createView()));
    }

    public function inWorkshopAdminEditarTemaAbiertoAction($nombreSesion, $id){
        $em = $this->getDoctrine()->getEntityManager();
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);
        $tema_abierto = $em->getRepository('JadBundle:TemaAbierto')->find($id);
        $form = $this->createForm(new TemaAbiertoType('editar'), $tema_abierto, array('sesion'=>$sesion));
        return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin_temasabiertos_form.html.twig', array('form'=>$form->createView()));
    }

    public function inWorkshopAdminRestriccionesAction($nombreSesion)
    {
        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');

        //La varialble rol dentro de la sesión esta serializada cuadno se guardo en el default controller dentro
        //de UsuarioBundle al guardar las variables del usuario en sesión con el método actualizarSesionVar()
        //por ello es necesario deserializar cada vez que se recupera
        $rol = unserialize($rols);

        $em = $this->getDoctrine()->getEntityManager();
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);

        return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin_restricciones.html.twig', array('sesion'=>$sesion,
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad));
    }

    public function inWorkshopAdminPasarTurnoAction($nombreSesion, $id)
    {
        $jad = $this->getRequest()->getSession()->get('jad');
        $rols = $this->getRequest()->getSession()->get('rol');

        //La varialble rol dentro de la sesión esta serializada cuadno se guardo en el default controller dentro
        //de UsuarioBundle al guardar las variables del usuario en sesión con el método actualizarSesionVar()
        //por ello es necesario deserializar cada vez que se recupera
        $rol = unserialize($rols);

        $em = $this->getDoctrine()->getEntityManager();
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);

        //El código de arriba solo hace falta para pasarlo a la plantilla twigh el rol y la sesion

        $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
        $filename = $this->container->getParameter('file_turns');
        $fullpath = "$bundlePath/$filename";
        $turnsList = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerTurnos = $this->get('handlerJSONTurnos');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerTurnos->setPath($bundlePath);
            $handlerTurnos->setFilename($filename);
            $handlerTurnos->setEntityName("Tfg\SesionJadBundle\Classes\Turns");
            $handlerTurnos->setDispatcher($this->get('event_dispatcher'));

            //configurar listener
            //$handlerZMQ = $this->get('handlerZMQ');
            //$listener = new SocketEventListener($handlerZMQ);
            $listener = $this->get('socketEventListener');
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->addListener(SocketEvents::REMOVE_TURN, array($listener, 'onRemoveTurn'));
            $turnsList = $handlerTurnos->removeDatesFile($id);
        }

        //Codigo para recuperar screens y pasarla a la plantilla twig y mostrar los botones de forma correcta
        //hace falta porque al hacer click en pasar turno, en ese cliente, se actualiza con la funcion load de jquery
        //que carga el resultado twig, en vez de cargarse mediante el evento del scocket.
        //De esta forma hay que mantener esa información al cargar la nueva plantilla
        $filename = $this->container->getParameter('file_activescreens');
        $fullpath = "$bundlePath/$filename";
        $screens = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerScreen = $this->get('handlerJSONPantallas');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerScreen->setPath($bundlePath);
            $handlerScreen->setFilename($filename);
            $handlerScreen->setEntityName('array<string,string>');
            $screens = $handlerScreen->readFile();
        }

        return $this->render('SesionJadBundle:Default:jad_sesion_inworkshop_admin_turnos.html.twig', array('sesion'=>$sesion,
                                                                                                           'rol'=>$rol,
                                                                                                           'jad'=>$jad,
                                                                                                           'turnos'=>$turnsList,
                                                                                                           'screens'=>$screens));
    }

    public function inWorkshopAdminOcultarPantallaAction(){

        $pantalla_a_ocultar =$this->get('request')->get('pantalla');

        $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
        $filename = $this->container->getParameter('file_activescreens');
        $fullpath = "$bundlePath/$filename";
        $screens = null;
        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerScreen = $this->get('handlerJSONPantallas');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerScreen->setPath($bundlePath);
            $handlerScreen->setFilename($filename);
            $handlerScreen->setEntityName('array<string,string>');
            $handlerScreen->setDispatcher($this->get('event_dispatcher'));

            //configurar listener
            //$handlerZMQ = $this->get('handlerZMQ');
            //$listener = new SocketEventListener($handlerZMQ);
            $listener = $this->get('socketEventListener');
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->addListener(SocketEvents::HIDE_SCREEN, array($listener, 'onHideScreen'));

            $screens = $handlerScreen->removeDatesFile($pantalla_a_ocultar);
        }

        return new Response();
    }

    public function inWorkshopAdminMostrarPantallaAction($nombreSesion){
        $pantalla_a_mostrar =$this->get('request')->get('pantalla');
        $info_a_mostrar = $this->get('request')->get('info');
        $em = $this->getDoctrine()->getEntityManager();
        $sesion = $em->getRepository('SesionJadBundle:SesionJad')->findOneBySlug($nombreSesion);

        $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
        $filename = $this->container->getParameter('file_activescreens');
        $fullpath = "$bundlePath/$filename";
        $screens = null;

        if(file_exists($fullpath)){
            //Crearmos y configuramos el handlerJSON para leer el fichero y cargar su contenido en una variable que pasaremos a la plantilla twig
            $handlerScreen = $this->get('handlerJSONPantallas');
            //Configuramos el handler mediante la inyeccion de dependencias al service (que es nuestro handlerJSON) a traves de setters
            $handlerScreen->setPath($bundlePath);
            $handlerScreen->setFilename($filename);
            $handlerScreen->setEntityName('array<string,string>');
            $handlerScreen->setDispatcher($this->get('event_dispatcher'));

            //configurar listener
            //$handlerZMQ = $this->get('handlerZMQ');
            //$listener = new SocketEventListener($handlerZMQ);
            $listener = $this->get('socketEventListener');
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->addListener(SocketEvents::SHOW_SCREEN, array($listener, 'onShowScreen'));

            $info_pantalla = array('pantalla'=>$pantalla_a_mostrar,'info'=>$info_a_mostrar,'sesion'=>$sesion);
            $screens = $handlerScreen->appendDatesFile($info_pantalla);
        }

        return new Response();
    }

    public function inWorkshopAdminMostrarPuntoSiguienteAction(){


        $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
        $filename = $this->container->getParameter('file_currentpoint');
        $fullpath = "$bundlePath/$filename";
        if(file_exists($fullpath)){
            $handlerPunto = $this->get('handlerJSONPuntos');
            $handlerPunto->setPath($bundlePath);
            $handlerPunto->setFilename($filename);
            $handlerPunto->setEntityName('array<string,string>');
            $handlerPunto->setDispatcher($this->get('event_dispatcher'));

            $listener = $this->get('socketEventListener');
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->addListener(SocketEvents::NEXT_AGENDA, array($listener,'onNextPoint'));

            $punto_actual = $handlerPunto->readFile();
            $id_punto_actual = $punto_actual['punto'];

            $em = $this->getDoctrine()->getEntityManager();
            $punto_actual = $em->getRepository('SesionJadBundle:PuntoAgenda')->findOneById($id_punto_actual);
            $orden_punto_actual = $punto_actual->getOrden();
            $puntosAgenda = $em->getRepository('SesionJadBundle:PuntoAgenda')->findBySesionJad($punto_actual->getSesionJad());

            $nuevo_id_punto_actual = "";
            $nuevo_orden_punto_actual = $orden_punto_actual +1;
            foreach($puntosAgenda as $punto){
                $orden_punto = $punto->getOrden();
                if($orden_punto == $nuevo_orden_punto_actual){
                    $nuevo_id_punto_actual = $punto->getId();
                }
             }

            $punto_actual = $handlerPunto->increment($nuevo_id_punto_actual, $nuevo_orden_punto_actual);
        }

        return new Response();
    }

    public function inWorkshopAdminMostrarPuntoAnteriorAction(){

        $bundlePath = $this->get('kernel')->locateResource('@SesionJadBundle');
        $filename = $this->container->getParameter('file_currentpoint');
        $fullpath = "$bundlePath/$filename";
        if(file_exists($fullpath)){
            $handlerPunto = $this->get('handlerJSONPuntos');
            $handlerPunto->setPath($bundlePath);
            $handlerPunto->setFilename($filename);
            $handlerPunto->setEntityName('array<string,string>');
            $handlerPunto->setDispatcher($this->get('event_dispatcher'));

            $listener = $this->get('socketEventListener');
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->addListener(SocketEvents::BACK_AGENDA, array($listener,'onBackPoint'));

            $punto_actual = $handlerPunto->readFile();
            $id_punto_actual = $punto_actual['punto'];

            $em = $this->getDoctrine()->getEntityManager();
            $punto_actual = $em->getRepository('SesionJadBundle:PuntoAgenda')->findOneById($id_punto_actual);
            $orden_punto_actual = $punto_actual->getOrden();
            $puntosAgenda = $em->getRepository('SesionJadBundle:PuntoAgenda')->findBySesionJad($punto_actual->getSesionJad());

             $nuevo_id_punto_actual = "";
             $nuevo_orden_punto_actual = $orden_punto_actual -1;
             foreach($puntosAgenda as $punto){
                $orden_punto = $punto->getOrden();
                if($orden_punto == $nuevo_orden_punto_actual){
                    $nuevo_id_punto_actual = $punto->getId();
                }
             }

            $handlerPunto->decrement($nuevo_id_punto_actual, $nuevo_orden_punto_actual);
        }
        return new Response();
    }


    /*=======================================================================================================================
    ======================================== llamadas AJAX ==================================================================
    =========================================================================================================================*/


     /*
     * @Route("/checkChanges", name="checkChanges")
     *es utilizado por el Navegador a través de la interfaz EventSource de los Server Sent Events
     */
     /*
    public function checkChanges()
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        //Configuring handler for turnos
        $handlerTurnos = $this->get('handlerJSON');
        $pathSesionJadBundle = $this->get('kernel')->locateResource('@SesionJadBundle');
        $handlerTurnos->setPath($pathSesionJadBundle);
        $handlerTurnos->setFilename($this->container->getParameter('file_turns'));

        $listaUsuarios = $handlerTurnos->checkChanges();

        $serializer = $this->container->get('serializer');
        $listaUsuariosJSON = $serializer->serialize($listaUsuarios, 'json');

            echo "event: isThereNewUserTurn\n";

            //foreach ($listaUsuarios as $usuario){
            //echo "data: {$usuario->getNombre()} <br>\n";
            //}
            echo "data:".json_encode($listaUsuariosJSON)."\n\n";
            //echo "data: \n\n";
            ob_flush();
            flush();

            //Devolvemos un objeto response ya que todos los métodos de un controlador tienen que devolver un response.
            $response = new Response();
            $response->setStatusCode(200);
            return $response;
    }*/




}
?>