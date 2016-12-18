<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Form\TaskType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Class TaskController
 * @package AppBundle\Controller
 * @Route("/task")
 * @Security("has_role('ROLE_USER')")
 */
class TaskController extends Controller
{
    /**
     * @param $name
     * @Route("/add")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $task = new Task();
        $user = $this->getUser();
        $form = $this->createForm(new TaskType($user), $task);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user->addTask($task);
            $task->setUser($user);
            $task->setDone(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute("app_task_showall");

        }

        return ["form" => $form->createView(), "user" => $user];
    }

    /**
     * @return array
     * @Route("/showall")
     *
     * @Template()
     */
    public function showallAction()
    {

        $id = $this->getUser()->getId();
        $user = $this->getDoctrine()->getRepository("AppBundle:User")->find($id);
//        $tasks = $user->getTasks();
        $tasks = $this->getDoctrine()->getRepository("AppBundle:Task")->getAllTasks($id);
        $today = new \DateTime();

        return ["user" => $user, "tasks" => $tasks, "today" => $today];
    }


    /**
     * @param $id
     * @return array
     * @Route("/show/{id}")
     * @Template()
     */
    public function showAction($id)
    {
        $user = $this->getUser();
        $task = $this->getDoctrine()->getRepository("AppBundle:Task")->find($id);

        $comments = $task->getComments();
        return ["task" => $task, "comments" => $comments, "user" => $user];
    }


    /**
     * @Route("/edit/{id}")
     * @Template()
     *
     */
    public function editAction(Request $request, $id)
    {
        $user = $this->getUser();
        $task = $this->getDoctrine()->getRepository("AppBundle:Task")->find($id);

        $form = $this->createForm(new TaskType($user), $task);
        $form->handleRequest($request);
        if ($task == false) {
            throw $this->createNotFoundException("nie znaleziono zadania");
        }



        if ($form->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("app_task_showall");
        }

        return ["form" => $form->createView(), "user" => $user];

    }


    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($id)
    {
        $task = $this->getDoctrine()->getRepository("AppBundle:Task")->find($id);

        if ($task == false) {
            throw $this->createNotFoundException("Nie znaleziono takiego zadania");

        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();

        return $this->redirectToRoute("app_task_showall");
    }

    /**
     * @param $id
     * @Route("/setdone/{id}")
     *
     */
    public function setDoneAction($id)
    {
        $task = $this->getDoctrine()->getRepository("AppBundle:Task")->find($id);

        if ($task == false) {
            throw $this->createNotFoundException("Nie znaleziono takiego zadania");

        }

        $em = $this->getDoctrine()->getManager();
        $task->setDone(1);
        $em->flush();

        return $this->redirectToRoute("app_task_show", ["id" => $id]);
    }

    /**
     * @
     * @Route("/done")
     * @Template("AppBundle:Task:showall.html.twig")
     */
    public function showDone()
    {
        $id = $this->getUser()->getId();

        $user = $this->getUser();
        $tasks = $this->getDoctrine()->getRepository("AppBundle:Task")->getDone($id);
        $today = new \DateTime();

        return ["user" => $user, "tasks" => $tasks, "today" => $today];

    }

    /**
     * @
     * @Route("/undone")
     * @Template("AppBundle:Task:showall.html.twig")
     */
    public function showUnDone()
    {
        $id = $this->getUser()->getId();

        $user = $this->getUser();
        $tasks = $this->getDoctrine()->getRepository("AppBundle:Task")->getUndone($id);
        $today = new \DateTime();

        return ["user" => $user, "tasks" => $tasks, "today" => $today];

    }

    /**
     * @Route("/deletes")
     * @Template()
     */
    public function multiDeleteAction()
    {
           $tasks = $_POST;

           foreach ($tasks as $id)
           {
               $task = $this->getDoctrine()->getRepository("AppBundle:Task")->find($id);

               if ($task == false) {
                   throw $this->createNotFoundException("Nie znaleziono takiego zadania");

               }

               $em = $this->getDoctrine()->getManager();
               $em->remove($task);
               $em->flush();

           }


        return $this->redirectToRoute("app_task_showall");
    }


}
