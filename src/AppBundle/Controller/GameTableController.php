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
     * Lists all gameTable entities.
     *
     * @Route("/", name="gametable_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $gameTables = $em->getRepository('AppBundle:GameTable')->findAll();

        return $this->render('gametable/index.html.twig', array(
            'gameTables' => $gameTables,
        ));
    }

    /**
     * Creates a new gameTable entity.
     * type is restricted to two choices: '2-4'' and '5-6'
     * users can't create more than one active table at a time
     * @Route("/new/{type}", name="gametable_new", methods={"GET"})
     */
    public function newAction(UserInterface $user=null, $type)
    {
        if (!in_array($type, ["2-4", "5-6"]) || $this->alreadyPlaying($user)) {
            return $this->redirectToRoute('router');
        }
        $player = new Player();
        $player->setUser($user);

        $gameTable = new GameTable();
        $gameTable->setStatus(true)->setMapType($type)->addUser($user);
        $gameTable->addPlayer($player);

        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->persist($gameTable);
        $em->flush();

        return $this->redirectToRoute('gametable_show', array('id' => $gameTable->getId()));
    }

    /**
     * Finds and displays a gameTable entity.
     *
     * @Route("/{id}", name="gametable_show", methods={"GET"})
     */
    public function showAction(GameTable $gameTable)
    {
        $deleteForm = $this->createDeleteForm($gameTable);

        return $this->render('gametable/show.html.twig', array(
            'gameTable' => $gameTable,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing gameTable entity.
     *
     * @Route("/{id}/edit", name="gametable_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, GameTable $gameTable)
    {
        $deleteForm = $this->createDeleteForm($gameTable);
        $editForm = $this->createForm('AppBundle\Form\GameTableType', $gameTable);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gametable_edit', array('id' => $gameTable->getId()));
        }

        return $this->render('gametable/edit.html.twig', array(
            'gameTable' => $gameTable,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a gameTable entity.
     *
     * @Route("/{id}", name="gametable_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, GameTable $gameTable)
    {
        $form = $this->createDeleteForm($gameTable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($gameTable);
            $em->flush();
        }

        return $this->redirectToRoute('gametable_index');
    }

    /**
     * Creates a form to delete a gameTable entity.
     *
     * @param GameTable $gameTable The gameTable entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(GameTable $gameTable)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('gametable_delete', array('id' => $gameTable->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function alreadyPlaying (UserInterface $user=null) {
        $response = false;
        foreach ($user->getPlayers() as $player) {
            if ($player->getGameTable()->getStatus() == true) {
                $response = true;
            }
        }
        return $response;
    }
}
