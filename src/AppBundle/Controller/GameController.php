<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Entity\GameTable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Game controller.
 * @Route("game")
 */
class GameController extends Controller
{
    /**
     * @Route("/{id}/new", name="game_new", methods={"POST"})
     */
    public function newAction(GameTable $gameTable)
    {
        if ($gameTable->getGame()) {
            return $this->redirectToRoute('router');
        }

        $game = new Game();
        $game->setGameTable($gameTable);

        $em = $this->getDoctrine()->getManager();
        $em->persist($game);
        $em->flush();

        return $this->render('game/new.html.twig', array(
            // ...
        ));
    }

}
