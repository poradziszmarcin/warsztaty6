<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MainController
 * @package AppBundle\Controller
 *
 *
 */
class MainController extends Controller
{
    /**
     * @Route("/")
     * @Template("@App/Main/homepage.html.twig")
     */
    public function mainAction()
    {


        $user = $this->getUser();
        if ($user == true) {
            $id = $this->getUser()->getId();
            $doneTasks = count($this->getDoctrine()->getRepository("AppBundle:Task")->getDone($id));
            $undoneTasks = count($this->getDoctrine()->getRepository("AppBundle:Task")->getUndone($id));
            $tasks = $doneTasks + $undoneTasks;

            return ["user" => $user, "tasks" => $tasks, "undone" => $undoneTasks, "done" => $doneTasks];
        } else {
            return ["user"=>$user];
        }
    }

    /**
     * @return array
     * @Route("/account")
     * @Template()
     */
    public function profileAction()
    {
        $user = $this->getUser();

        return ["user" => $user];
    }


}
