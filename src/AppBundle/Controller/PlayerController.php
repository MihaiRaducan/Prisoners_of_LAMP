<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
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
     * generates a random double dice number and assigns it to the player
     * if one of the other players already has the same number in the database, the function calls on itself again
     * @param Player $player
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/setDice", name="set_initial_dice", methods={"PATCH"})
     */
    public function setInitialDiceAction(Player $player) {
        $initialDiceRoll = array_sum($this->generateDoubleDice());

        $otherDiceRolls = [];
        foreach ($player->getGameTable()->getPLayers() as $otherPlayer) {
            if ($player != $otherPlayer && $otherPlayer->getInitialDice() !== null) {
                $otherDiceRolls[] = $otherPlayer->getInitialDice();
            }
        }

        while (in_array($initialDiceRoll, $otherDiceRolls)) {
            $initialDiceRoll = array_sum($this->generateDoubleDice());
        }

        $player->setInitialDice($initialDiceRoll);
        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->flush();

        return $this->redirectToRoute('gametable_show', array(
            'id' => $player->getGameTable()->getId(),
        ));
    }

    private function generateDoubleDice() {
        return [mt_rand(1, 6), mt_rand(1, 6)];
    }


}
