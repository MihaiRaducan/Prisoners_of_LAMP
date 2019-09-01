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
}
