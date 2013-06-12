<?php
namespace Tfg\SesionJadBundle\Event;

final class SocketEvents
{
	/**
     * The socket.requestturn event is thrown each time an turn is requested
     * in the system form nfc device
     *
     * The event listener receives an Event instance.
     *
     * @var string
     */

	const REQUEST_TURN = 'request.turn';
     const REMOVE_TURN = 'remove.turn';
     const HIDE_SCREEN ='hide.screen';
     const SHOW_SCREEN = 'show.screen';
     const NEXT_AGENDA = 'next.agenda';
     const BACK_AGENDA = 'back.agenda';
     const NEW_AGREEMENT = 'new.agreement';
     const REMOVE_AGREEMENT = 'remove.agreement';
     const EDIT_AGREEMENT = 'edit.agreement';
     const NEW_OPENISSUE = 'new.openissue';
     const REMOVE_OPENISSUE = 'remove.openissue';
     const EDIT_OPENISSUE = 'edit.openissue';
     const NEW_CONSTRAINT = 'new.constraint';
     const REMOVE_CONSTRAINT = 'remove.constraint';
     const EDIT_CONSTRAINT = 'edit.constraint';
}