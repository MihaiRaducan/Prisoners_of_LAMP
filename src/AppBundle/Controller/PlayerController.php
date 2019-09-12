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

    /**
     * checks the normallized distribution of values resulting from the mt_rand(1,6) function
     * @param $n
     * @return Response
     * @Route("/testDice/{n}", name="test_dice", methods={"GET"})
     */
    public function testDiceAction($n) {
        $dice1 = [];
        $dice2 = [];
        for ($face = 1; $face <=6; $face++) {
            $dice1[$face] = 0;
            $dice2[$face] = 0;
        }

        $diceSum = [];
        for ($sum = 2; $sum <= 12; $sum++) {
            $diceSum[$sum] = 0;
        }

        for ($i = 1; $i <= $n; $i++) {
            $doubleDice = $this->generateDoubleDice();
            for ($case = 1; $case <= 6; $case++) {
                if ($doubleDice['d1'] === $case) {
                    $dice1[$case]++;
                }
                if ($doubleDice['d2']=== $case) {
                    $dice2[$case]++;
                }
            }
            for ($ddSum = 2; $ddSum <= 12; $ddSum++) {
                if (array_sum($doubleDice) === $ddSum) {
                    $diceSum[$ddSum]++;
                }
            }
        }

        $sumDice1 = array_sum($dice1);
        foreach ($dice1 as $key => $value) {
            $dice1['prob' . $key] = $value/$sumDice1*100;
        }
        $sumDice2 = array_sum($dice2);
        foreach ($dice2 as $key => $value) {
            $dice2['prob' . $key] = $value/$sumDice2*100;
        }
        $sumDiceSum = array_sum($diceSum);
        foreach ($diceSum as $key => $value) {
            $diceSum['prob' . $key] = $value/$sumDiceSum*100;
        }

        return $this->render('gametable/test.html.twig', array(
            'n' => $n,
            'dice1' => $dice1,
            'dice2' => $dice2,
            'diceSum' => $diceSum,
        ));
    }

    /**
     * @return array
     */
    private function generateDoubleDice() {
        return [
            'd1' => mt_rand(1, 6),
            'd2' => mt_rand(1, 6)
        ];
    }
}
