<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;


/**
 * Player controller.
 * @Route("player")
 */

class PlayerController extends Controller
{
    /**
     * for testing only
     * @Route("/", name="default_player", methods={"GET"})
     */
    public function defaultAction() {
        return new Response('player route working');
    }

    /**
     * receives a completed form from route "gametable_show"
     * sets the player color then redirects back to "gametable_show";
     * @Route("/", name="set_color", methods={"POST"})
     */
    public function setColorAction() {

    }


}
