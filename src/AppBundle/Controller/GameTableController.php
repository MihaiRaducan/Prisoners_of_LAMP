<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GameTable;
use AppBundle\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Gametable controller.
 * @Route("gametable")
 */
class GameTableController extends Controller
{
    /**
     * Lists all gameTable entities that are still open
     * gameTables with max number of players will not show the join button
     * @Route("/", name="gametable_index", methods={"GET"})
     */
    public function indexAction(UserInterface $user=null)
    {
        $em = $this->getDoctrine()->getManager();
        $gameTables = $em->getRepository('AppBundle:GameTable')->findByStatus(true);

        $fullStatus = [];
        foreach ($gameTables as $gameTable) {
            $fullStatus [$gameTable->getId()] = $this->isFull($gameTable);
        }

        return $this->render('gametable/index.html.twig', array(
            'gameTables' => $gameTables,
            'fullStatus' => $fullStatus,
            'alreadyPlaying' => $this->alreadyPlaying($user),
        ));
    }

    /**
     * Creates a new gameTable entity.
     * type is restricted to two choices: '3-4'' and '5-6'
     * users can't create more than one table at a time
     * @Route("/new/{type}", name="gametable_new", methods={"POST"})
     */
    public function newAction($type, UserInterface $user=null)
    {
        if (!in_array($type, ["3-4", "5-6"]) || $this->alreadyPlaying($user)) {
            return $this->redirectToRoute('router');
        }
        $player = new Player();
        $player->setUser($user);

        $gameTable = new GameTable();
        $gameTable->setStatus(true)->setMapType($type)->addPlayer($player);

        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->persist($gameTable);
        $em->flush();

        return $this->redirectToRoute('gametable_show', array(
            'id' => $gameTable->getId(),
        ));
    }

    /**
     * Finds and displays a gameTable entity; only the ones with status = true will be displayed
     * if the gameTable has more than the allowed number of players, the last additions are dropped
     * if the gameTable has at least the minimum number of players and all have set their colors and initialDice, call the setPlayerOrder() function
     * if two players have the same color the second player color will be set to null
     * if two players have the same initialDice number the second player initialDice number will be set to null
     * @Route("/{id}", name="gametable_show", methods={"GET", "PATCH"})
     */
    public function showAction(Request $request, $id, UserInterface $user=null)
    {
        $em = $this->getDoctrine()->getManager();
        $gameTable = $em->getRepository('AppBundle:GameTable')->find($id);

        if ($gameTable === null || $gameTable->getStatus() === false) {
            return $this->redirectToRoute('router');
        }

        $maxPlayers = substr($gameTable->getMapType(), -1);
        $removed = 0;
        while (count($gameTable->getPlayers()) > intval($maxPlayers)) {
            $players = $gameTable->getPlayers();
            $em->remove($players[count($players) - 1]);
            $em->flush();
            $removed++;
        }
        if ($removed !== 0) {
            return $this->redirectToRoute('router');
        }

        for ($i = 0; $i<count($gameTable->getPlayers()); $i++) {
            $currentColor = $gameTable->getPlayers()[$i]->getColor();
            if ($currentColor !== null) {
                for ($j = $i + 1; $j<count($gameTable->getPlayers()); $j++) {
                    $unluckyPlayer = $gameTable->getPlayers()[$j];
                    if ($unluckyPlayer->getColor() == $currentColor) {
                        $unluckyPlayer->setColor(null);
                        $em->persist($unluckyPlayer);
                        $em->flush();
                    }
                }
            }
            $currentInitialDice = $gameTable->getPlayers()[$i]->getInitialDice();
            if ($currentInitialDice !== null) {
                for ($k = $i + 1; $k<count($gameTable->getPlayers()); $k++) {
                    $unluckyPlayer = $gameTable->getPlayers()[$k];
                    if ($unluckyPlayer->getInitialDice() == $currentInitialDice) {
                        $unluckyPlayer->setInitialDice(null);
                        $em->persist($unluckyPlayer);
                        $em->flush();
                    }
                }
            }
        }

        if ($this->enoughPlayersReady($gameTable)) {
            $this->setPlayerOrder($gameTable);
        }

        $userPlayer = $this->getPlayer($gameTable, $user);
        if ($userPlayer !== null) {
            $colorForm = $this->createSetColorForm($userPlayer);
            $colorForm->handleRequest($request);

            if ($colorForm->isSubmitted() && $colorForm->isValid()){
                $em->flush();
                return $this->redirectToRoute('gametable_show', array('id' => $id));
            }
            $diceRollForm = $this->createDiceRollForm($userPlayer);
            $leaveForm = $this->createLeaveForm($gameTable);

            return $this->render('gametable/show.html.twig', array(
                'gameTable' => $gameTable,
                'color_form' => $colorForm->createView(),
                'dice_roll_form' => $diceRollForm->createView(),
                'leave_form' => $leaveForm->createView(),
                'isFull' => $this->isFull($gameTable),
            ));
        }

        return $this->render('gametable/show.html.twig', array(
            'gameTable' => $gameTable,
            'color_form' => null,
            'dice_roll_form' => null,
            'leave_form' => null,
            'isFull' => $this->isFull($gameTable),
            'alreadyPlaying' => $this->alreadyPlaying($user),
        ));
    }

    /**
     * Finds and joins a gameTable entity only if the user_player is not partipating in other gameTables
     * @Route("/{id}", name="gametable_join", methods={"POST"})
     */
    public function joinAction($id, UserInterface $user=null)
    {
        $em = $this->getDoctrine()->getManager();
        $gameTable = $em->getRepository('AppBundle:GameTable')->find($id);

        if ($this->alreadyPlaying($user) || $gameTable == null || $gameTable->getStatus() == false || $this->isFull($gameTable)) {
            return $this->redirectToRoute('router');
        }

        $player = new Player();
        $player->setUser($user);
        $gameTable->addPlayer($player);

        $em->persist($player);
        $em->persist($gameTable);
        $em->flush();

        return $this->redirectToRoute('gametable_show', array(
            'id' => $gameTable->getId(),
        ));
    }

    /**
     * Removes the current player belonging to the user from a gameTable entity.
     * @Route("/{id}", name="gametable_leave", methods={"DELETE"})
     */
    public function leaveAction(Request $request, GameTable $gameTable, UserInterface $user=null)
    {
        $form = $this->createLeaveForm($gameTable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($gameTable->getPlayers() as $player){
                if ($player->getUser() === $user) {
                    $gameTable->removePlayer($player);
                    $user->removePlayer($player);
                    $em->remove($player);
                }
            }
            if (count($gameTable->getPlayers()) == 0) {
                $em->remove($gameTable);
            }
            $em->flush();
        }
        return $this->redirectToRoute('gametable_index');
    }

    /**
     * finds whether the user has a player already involved in a game(Table)
     * @param UserInterface|null $user
     * @return bool
     */
    private function alreadyPlaying (UserInterface $user) {
        foreach ($user->getPlayers() as $player) {
            if ($player->getGameTable()->getStatus() == true) {
                return true;
            }
            //TODO: add other conditions type getGame()->getStatus()
        }
        return false;
    }

    /**
     * determines whether a GameTable is full
     * @return bool
     */
    private function isFull(GameTable $gameTable) {
        $maxPlayers = substr($gameTable->getMapType(), -1);
        if (count($gameTable->getPlayers()) < intval($maxPlayers)) {
            return false;
        }
        return true;
    }

    /**
     * finds if the user has a player at this gameTable and returns that player; if no player is found it returns null
     * @param GameTable $gameTable
     * @param UserInterface $user
     * @return |null
     */
    private function getPlayer(GameTable $gameTable, UserInterface $user){
        foreach ($gameTable->getPlayers() as $player) {
            if ($player->getUser() == $user) {
                return $player;
            }
        }
        return null;
    }

    /**
     * checks if more than the minimum required number of players for given mapType have set their color and initial dice roll
     * @param GameTable $gameTable
     * @return bool
     */
    private function enoughPlayersReady(GameTable $gameTable) {
        $minPlayers = substr($gameTable->getMapType(), 0, 1);
        if (count($gameTable->getPlayers()) < intval($minPlayers)) {
            return false;
        }
        foreach ($gameTable->getPlayers() as $player) {
            if ($player->getColor() === null || $player->getInitialDice() === null) {
                return false;
            }
        }
        return true;
    }

    /**
     * generates the turn order for each player based on all the initial dice rolls
     * rsort->sorts the array in reverse numerical order (old keys are not kept)
     * array_search-> returns the first key for the searched value (keys start at 0)
     * @param GameTable $gameTable
     */
    private function setPlayerOrder(GameTable $gameTable) {
        foreach ($gameTable->getPlayers() as $player) {
            $diceRolls[] = $player->getInitialDice();
        }
        rsort($diceRolls);

        $em = $this->getDoctrine()->getManager();
        foreach ($gameTable->getPlayers() as $player) {
            $turnOrder = 1 + array_search($player->getInitialDice(), $diceRolls);
            $player->setTurnOrder($turnOrder);
            $em->persist($player);
            $em->flush();
        }
        return;
    }

    /**
     * creates a form for choosing a color; colors already chosen by other players are removed from the option list
     * @param Player $player
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createSetColorForm(Player $player){
        $colors = [null, 'red', 'blue', 'white', 'orange', 'green', 'brown'];
        $alreadyPicked = [];

        foreach ($player->getGameTable()->getPLayers() as $otherPlayer) {
            if ($player != $otherPlayer && $otherPlayer->getColor() !== null) {
                $alreadyPicked[] = $otherPlayer->getColor();
            }
        }

        $colors = array_diff($colors, $alreadyPicked);
        foreach ($colors as $color){
            $colorList[$color] = $color;
        }

        return $this->createFormBuilder($player)
            ->setMethod('PATCH')
            ->add('color', ChoiceType::class, ['choices' => $colorList])
            ->getForm()
            ;
    }

    /**
     * create form for generating a random double dice number
     * @param Player $player
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDiceRollForm(Player $player)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('set_initial_dice', array('id' => $player->getId())))
            ->setMethod('PATCH')
            ->getForm()
            ;
    }

    /**
     * create a leave form for when a player wishes to leave a game_table
     * @param GameTable $gameTable
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createLeaveForm(GameTable $gameTable)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('gametable_leave', array('id' => $gameTable->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
