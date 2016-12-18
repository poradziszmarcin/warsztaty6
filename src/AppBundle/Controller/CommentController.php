<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentController
 * @package AppBundle\Controller
 * @Route("/comment")
 * @Security("has_role('ROLE_USER')")
 */
class CommentController extends Controller
{
    /**
     * @Route("/addcomment/{id}")
     * @Template()
     */
    public function AddAction(Request $request, $id)
    {
        $user = $this->getUser();
        $task = $this->getDoctrine()->getRepository("AppBundle:Task")->find($id);
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $comment->setTask($task);
            $task->addComment($comment);
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute("app_task_show", ["id" => $id]);
        }
        return ["form" => $form->createView(),"user"=>$user];
    }

    /**
     * @Route("edit/{id}")
     */
    public function editAction(Request $request, $id)
    {
        $user = $this->getUser();
        $comment = $this->getDoctrine()->getRepository("AppBundle:Comment")->find($id);


        if ($comment == false) {
            throw $this->createNotFoundException("nie ma takiego komentarza");
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("app_task_showall");
        }

        return ["form" => $form->createView(),"user"=>$user];

    }


    /**
     *
     *
     * @Route("delete/{tid}comm{id}")
     */
    public function deleteAction($id, $tid)
    {
        $comment = $this->getDoctrine()->getRepository("AppBundle:Comment")->find($id);


        if ($comment == false) {
            throw $this->createNotFoundException("nie ma takiego komentarza");
        }

        $em=$this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute("app_task_show",["id"=>$tid]);
    }

}
