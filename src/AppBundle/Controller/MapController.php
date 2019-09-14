<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GameTable;
use AppBundle\Entity\Map;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Map controller.
 * @Route("map")
 */
class MapController extends Controller
{
    /**
     * @Route("/{id}/new", name="map_new", methods={"POST"})
     */
    public function newAction(GameTable $gameTable)
    {
        if ($gameTable->getMap()) {
            return $this->redirectToRoute('router');
        }

        $map = new Map();
        $map->setGameTable($gameTable);

        $em = $this->getDoctrine()->getManager();
        $em->persist($map);
        $em->flush();

        return $this->render('map/new.html.twig', array(
            // ...
        ));
    }
}
