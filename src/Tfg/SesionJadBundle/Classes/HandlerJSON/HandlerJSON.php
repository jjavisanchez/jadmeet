<?php


namespace Tfg\SesionJadBundle\Classes\HandlerJSON;

use JMS\Serializer\Serializer;

use Tfg\UsuarioBundle\Entity\Usuario;
use Tfg\SesionJadBundle\Classes\Turns;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tfg\SesionJadBundle\Event\SocketEvents;
use Tfg\SesionJadBundle\Event\RequestTurnEvent;

abstract class HandlerJSON
{

    protected $path;
    protected $filename;
    protected $serializer;
    protected $dispatcher;
    protected $entityName;

    //Constructor en el que le pasamos utilizando inyeccion de depnecencias un service container en este caso un un service container a traves del cual accedemos a los servicios concretamente al jms_serializer
    public function __construct(Serializer $serializer)
    {
        //$root = realpath($_SERVER["DOCUMENT_ROOT"]);
        $this->serializer = $serializer;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function getEntityName()
    {
        return $this->entityName;
    }

    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;
    }

    public function createFile($object)
    {
        //Convertimos cualquier objeto en un objeto con formato json, lo ideal sería conservar la clase para así poder recuperarla mas tarde de una forma sencilla ¡¡¡Solucionado con jmserializer!!!!
        if (!file_exists("$this->path/$this->filename")){
            $objectJSON = $this->serializer->serialize($object, 'json');
            $fh = fopen("$this->path/$this->filename", 'w')
                or die("Error al abrir fichero de salida");

            fwrite($fh, $objectJSON);
            fclose($fh);
        }
    }

    /**
     *
     * Read file .json which is indicate for path and filename attributes of the class indicate in entityName
     *
     * @return      object
     */
    public function readFile()
    {

        $object_json_from_file = file_get_contents("$this->path/$this->filename");
        if ($object_json_from_file == false) {
            return "error al leer fichero";
        }

        //with deserialize I get an object of the original type specified by $entityName
        $content = $this->serializer->deserialize($object_json_from_file, $this->entityName, 'json');

        return $content;
    }

    /**
     *
     * Append dates to file .json which is indicate for path and filename attributes of this class, from a object source
     * pass as parameter
     *
     * @param       object  $object Object source which its dates are added to te file
     * @return      array   $aux represents turns list
     */
    abstract public function appendDatesFile($object);
    /*
    {
        $source = $this->readFile();
        //¡¡¡¡¡ NO GENERICO SOLO PARA TURNOS!!! habra problemas
        $aux = $source->getTurnsList();
        //Here we check the $object is not in aux array
        foreach($aux as $obj){
            if($obj->getId() == $object->getId()){
                throw new \Exception("Error: Objeto a insertar en el fichero ya esta contenido", 1);
            }
        }
        $aux[] = $object;
        $source->setTurnsList($aux);

        $fh = fopen("$this->path/$this->filename", 'w')
                or die("Error al abrir fichero de salida");
        $objectJSON = $this->serializer->serialize($source, 'json');
        fwrite($fh, $objectJSON);
        fclose($fh);

        //Creamosel mensaje a devolver por el socket un array con la posicion y el objeto
        $posicion = count($aux);
        $json_object = $this->serializer->serialize($object, 'json');
        $mensajeZMQ = array('posicion'=>$posicion,'objeto'=>$json_object);
        $json_mensajeZMQ = json_encode($mensajeZMQ);
        $event = new RequestTurnEvent($json_mensajeZMQ);
        $this->dispatcher->dispatch(SocketEvents::REQUEST_TURN, $event);

        return $aux;
    }*/




    /**
     *
     * Remove dates to files .json which is indicate for path and filename attributes of this class, from a object source
     * pass as parameter
     *
     * @param       object  $object Object source which its dates are removed to te file
     * @return      array   $aux represents turns list
     */
    abstract public function removeDatesFile($object);
    /*
    {

        $source = $this->readFile();
        $aux = $source->getTurnsList();
        //Here we check the $object is not in aux array
        foreach($aux as $obj){
            if($obj->getId() == $object->getId()){
                throw new \Exception("Error: Objeto a insertar en el fichero ya esta contenido", 1);
            }
        }
        $aux[] = $object;
        $source->setTurnsList($aux);

        $fh = fopen("$this->path/$this->filename", 'w')
                or die("Error al abrir fichero de salida");
        $objectJSON = $this->serializer->serialize($source, 'json');
        fwrite($fh, $objectJSON);
        fclose($fh);

        return $aux;
    }*/

    //Solo vale para turnos y recuperamos el ultimo usuario ¡¡¡NO!!! GENERICO PARA COMPROBAR CAMBISO EN ARCHIVOS JSONs
    //Vieja no se usa solo la usaba a travaes de la interfaz EventSouce de los Server Sent Events.
    /*
    public function checkChanges()
    {
        $turns = $this->readFile("Tfg\SesionJadBundle\Classes\Turns");

        $turnsList = $turns->getTurnsList();

        $nuevoUsuario = end($turnsList);


        return  $turnsList;

    }*/


    //elimina el fichero .json
    public function deleteFile()
    {
        unlink("$this->path/$this->filename");
    }

}

?>
