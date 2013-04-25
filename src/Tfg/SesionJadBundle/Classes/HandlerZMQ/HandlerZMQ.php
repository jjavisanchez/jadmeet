<?php

namespace Tfg\SesionJadBundle\Classes\HandlerZMQ;

class HandlerZMQ{

	private $host;
	private $socket;

	public function __construct($host){
		$this->host = $host;
	}

	public function connectSocket()
    {
        if ($this->socket) {
            return;
        }

        $context = new \ZMQContext();
        $this->socket = $context->getSocket(\ZMQ::SOCKET_PUB);
        $this->socket->connect($this->host);
    }

    public function getHost(){
        return $this->host;
    }

    public function write($message)
    {
        $this->connectSocket();
        $this->socket->send((string)$message);
    }
}
?>