<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GameTable;
use AppBundle\Entity\Map;
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
            $fullStatus [$gameTable->getId()] = $gameTable->isFull();
        }

        return $this->render('gametable/index.html.twig', array(
            'gameTables' => $gameTables,
            'fullStatus' => $fullStatus,
            'alreadyPlaying' => $user->alreadyPlaying(),
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
        if (!in_array($type, ["3-4", "5-6"]) || $user->alreadyPlaying()) {
            return $this->redirectToRoute('router');
        }
        $player = new Player();
        $player->setUser($user);

        $gameTable = new GameTable();
        $gameTable->setStatus(true)->setMapType($type)->addPlayer($player);

        $map = new Map($type);
        $map->setGameTable($gameTable);
        $gameTable->setMap($map);

        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->persist($gameTable);
        $em->persist($map);
        foreach ($map->getTiles() as $tile) {
            $em->persist($tile);
        }
        foreach ($map->getEdges() as $edge) {
            $em->persist($edge);
        }
        $em->flush();

        return $this->redirectToRoute('gametable_show', array(
            'id' => $gameTable->getId(),
        ));
    }

    /**
     * Finds and displays a gameTable entity; only the ones with status = true will be displayed
     * if the gameTable has more than the allowed number of players, the last additions are dropped with function trimmedPlayers()
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

        foreach ($gameTable->trimmedPlayers() as $player) {
            $em->remove($player);
            $em->flush();
        }

        foreach ($gameTable->resetSameColorPlayers() as $player) {
            $em->persist($player);
            $em->flush();
        }

        foreach ($gameTable->resetSameDicePlayers() as $player) {
            $em->persist($player);
            $em->flush();
        }

        if ($gameTable->enoughPlayersReady()) {
            $gameTable->setStatus(false);
            $em->persist($gameTable);
            $em->flush();
            $gameTable->setPlayerOrder();
            foreach ($gameTable->getPlayers() as $player) {
                $em->persist($player);
            }
            $em->flush();
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
            ));
        }

        return $this->render('gametable/show.html.twig', array(
            'gameTable' => $gameTable,
            'color_form' => null,
            'dice_roll_form' => null,
            'leave_form' => null,
            'alreadyPlaying' => $user->alreadyPlaying(),
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

        if ($user->alreadyPlaying() || $gameTable == null || $gameTable->getStatus() == false || $gameTable->isFull()) {
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
                foreach ($gameTable->getMap()->getTiles() as $tile) {
                    $em->remove($tile);
                }
                foreach ($gameTable->getMap()->getEdges() as $edge) {
                    $em->remove($edge);
                }
                $em->remove($gameTable->getMap());
                $em->remove($gameTable);
            }
            $em->flush();
        }
        return $this->redirectToRoute('gametable_index');
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
