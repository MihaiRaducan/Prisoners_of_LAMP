<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DefaultController extends Controller
{
    /**
     * all illegal actions and routes are redirected here
     * the router finds if the user has players involved in active games and redirects there
     * @Route("/", name="router")
     */
    public function routerAction(Request $request, UserInterface $user=null)
    {
        foreach ($user->getPlayers() as $player) {
            if ($player->getGameTable()->getStatus() == true) {
                return $this->redirectToRoute('gametable_show', array(
                    'id' => $player->getGameTable()->getId(),
                ));
            }
            //add other conditions type getGame()->getStatus()
        }
        return $this->render('default/workingonit.html.twig', [
            'text' => 'This functionality hasn\'t been added yet. I\'m working on it. Or maybe you were trying to sneak around?'
        ]);
    }

    /**
    * @Route("/symfony", name="symfony_homepage")
    */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

}
