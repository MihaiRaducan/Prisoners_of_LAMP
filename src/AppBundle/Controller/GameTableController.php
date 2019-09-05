<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GameTable;
use AppBundle\Entity\Player;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * type is restricted to two choices: '2-4'' and '5-6'
     * users can't create more than one table at a time
     * @Route("/new/{type}", name="gametable_new", methods={"POST"})
     */
    public function newAction($type, UserInterface $user=null)
    {
        if (!in_array($type, ["2-4", "5-6"]) || $this->alreadyPlaying($user)) {
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
     * Finds and displays a gameTable entity.
     * if the gameTable has more than the allowed number of players, the last additions are dropped
     * @Route("/{id}", name="gametable_show", methods={"GET"})
     */
    public function showAction($id, UserInterface $user=null)
    {
        $em = $this->getDoctrine()->getManager();
        $gameTable = $em->getRepository('AppBundle:GameTable')->find($id);

        dump($gameTable);

        if ($gameTable == null) {
            return $this->redirectToRoute('router');
        }

        $maxPlayers = substr($gameTable->getMapType(), -1);
        $removed = 0;
        $em = $this->getDoctrine()->getManager();
        while (count($gameTable->getPlayers()) > intval($maxPlayers)) {
            $players = $gameTable->getPlayers();
            $em->remove($players[count($players) - 1]);
            $em->flush();
            $removed++;
        }

        if ($removed !== 0) {
            return $this->redirectToRoute('router');
        }

        $colors = [null, 'red', 'blue', 'white', 'orange', 'green', 'brown'];

        foreach ($colors as $color){
            $colorList[$color] = $color;
        }

        $colorForm = $this->createFormBuilder($this->getPlayer($gameTable, $user))
            ->add('color', ChoiceType::class, [
                'choices' => $colorList
                ])
            ->getForm()
        ;





        $leaveForm = $this->createLeaveForm($gameTable);

        return $this->render('gametable/show.html.twig', array(
            'gameTable' => $gameTable,
            'color_form' => $colorForm->createView(),
            'leave_form' => $leaveForm->createView(),
        ));
    }

    /**
     * Finds and joins a gameTable entity only if the user_player is not partipating in other gameTables
     *
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

        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->persist($gameTable);
        $em->flush();

        return $this->redirectToRoute('gametable_show', array(
            'id' => $gameTable->getId(),
        ));
    }

    /**
     * Removes the current player belonging to the user from a gameTable entity.
     *
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
            //TO DO: add other conditions type getGame()->getStatus()
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
