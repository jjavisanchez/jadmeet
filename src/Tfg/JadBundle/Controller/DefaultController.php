<?php

namespace Tfg\JadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;

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
