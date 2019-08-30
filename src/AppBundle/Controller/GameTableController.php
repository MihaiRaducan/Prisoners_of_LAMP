<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GameTable;
use AppBundle\Entity\Player;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Gametable controller.
 * @Security("has_role('ROLE_USER')")
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

        dump($fullStatus);
        dump($this->alreadyPlaying($user));
        return $this->render('gametable/index.html.twig', array(
            'gameTables' => $gameTables,
            'fullStatus' => $fullStatus,
            'alreadyPlaying' => $this->alreadyPlaying($user),
        ));
    }

    /**
     * Creates a new gameTable entity.
     * type is restricted to two choices: '2-4'' and '5-6'
     * users can't create more than one active table at a time
     * @Route("/new/{type}", name="gametable_new", methods={"GET"})
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

        $leaveForm = $this->createLeaveForm($gameTable);

        return $this->redirectToRoute('gametable_show', array(
            'id' => $gameTable->getId(),
            'leave_form' => $leaveForm->createView(),
        ));
    }

    /**
     * Finds and displays a gameTable entity.
     *
     * @Route("/{id}", name="gametable_show", methods={"GET"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $gameTable = $em->getRepository('AppBundle:GameTable')->find($id);

        if ($gameTable == null) {
            return $this->redirectToRoute('router');
        }

        $leaveForm = $this->createLeaveForm($gameTable);

        return $this->render('gametable/show.html.twig', array(
            'gameTable' => $gameTable,
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
        $leaveForm = $this->createLeaveForm($gameTable);

        $player = new Player();
        $player->setUser($user);
        $gameTable->addPlayer($player);
        if ($this->isFull($gameTable)) {
            $gameTable->setStatus(false);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->persist($gameTable);
        $em->flush();

        return $this->render('gametable/show.html.twig', array(
            'gameTable' => $gameTable,
            'leave_form' => $leaveForm->createView(),
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

    private function alreadyPlaying (UserInterface $user=null) {
        $response = false;
        foreach ($user->getPlayers() as $player) {
            if ($player->getGameTable()->getStatus() == true) {
                $response = true;
            }
            //add other conditions type getGame()->getStatus()
        }
        return $response;
    }

    /**
     * finds the GameTable instances that are not yet full and still open
     * @return bool
     *
     */
    private function isFull(GameTable $gameTable) {
        $maxPlayers = substr($gameTable->getMapType(), -1);
        if (count($gameTable->getPlayers()) < intval($maxPlayers)) {
            return false;
        }
        return true;
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
