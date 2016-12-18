<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;

use AppBundle\Entity\User;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 * @package AppBundle\Controller
 * @Route("/category")
 * @Security("has_role('ROLE_USER')")
 */
class CategoryController extends Controller
{
    /**
     *
     * @Route("/add")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $category = new Category();
        $user = $this->getUser();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user->addCategory($category);
            $category->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute("app_category_show");

        }

        return ["form" => $form->createView(), "user"=>$user];
    }

    /**
     * @return array
     * @Route("/show")
     * @Template()
     */
    public function showAction()
    {

        $id = $this->getUser()->getId();
        $user = $this->getDoctrine()->getRepository("AppBundle:User")->find($id);
        $categories = $user->getCategories();

        return ["user"=>$user, "categories"=>$categories];
    }


    /**
     * @Route("edit/{id}")
     * @Template()
     */
    public function editAction(Request $request,$id)
    {
        $user=$this->getUser();
        $category = $this->getDoctrine()->getRepository("AppBundle:Category")->find($id);


        if ($category ==false)
        {
            throw $this->createNotFoundException("nie znaleziono kategorii");
        }

        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("app_category_show");
        }

        return ["form"=>$form->createView(),"user"=>$user];

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/delete/{id}")
     */
    public function deleteAction($id)
    {
        $category = $this->getDoctrine()->getRepository("AppBundle:Category")->find($id);

        if ($category == false)
        {
            throw $this->createNotFoundException("Nie znaleziono kategorii");

        }

//        if (count($category->getTasks()) > 0)
//        {
//            throw new Exception("nie można usunąć kategori która ma zadania");
//        }





        $em = $this->getDoctrine()->getManager();
        $tasks = $category->getTasks();
        if ($tasks!=null)
        {
            foreach ($tasks as $task)
            {
                $category->removeTask($task);
                $task->setCategory(null);
            }
        }
        $em->remove($category);

        $em->flush();
        return $this->redirectToRoute("app_category_show");

    }


    /**
     * @param $id
     * @Route("/tasks/{cat}")
     * @Template("@App/Category/showall.html.twig")
     */
    public function showTaskAction($cat)
    {
        $category = $this->getDoctrine()->getRepository("AppBundle:Category")->find($cat);
        $user = $this->getUser();
        $id = $this->getUser()->getId();
        $today = new \DateTime();
        $tasks= $this->getDoctrine()->getRepository("AppBundle:Task")->getTaskbyCategory($id,$cat);
        return ["tasks"=>$tasks, "user"=>$user, "today"=>$today,"cat"=>$category];
    }


    /**
     * @
     * @Route("/tasks/done/user{id}cat{cat}")
     * @Template("AppBundle:Category:showall.html.twig")
     */
    public function showDone($cat)
    {
        $category = $this->getDoctrine()->getRepository("AppBundle:Category")->find($cat);
        $id = $this->getUser()->getId();
        $user = $this->getUser();
        $tasks = $this->getDoctrine()->getRepository("AppBundle:Task")->getCategoryDone($id,$cat);
        $today = new \DateTime();

        return ["user" => $user, "tasks" => $tasks, "today" => $today,"cat"=>$category];

    }

    /**
     * @
     * @Route("/tasks/undone/user{id}cat{cat}")
     * @Template("AppBundle:Category:showall.html.twig")
     */
    public function showUnDone($cat)
    {
        $category = $this->getDoctrine()->getRepository("AppBundle:Category")->find($cat);
        $id = $this->getUser()->getId();
        $user = $this->getUser();
        $tasks = $this->getDoctrine()->getRepository("AppBundle:Task")->getCategoryUndone($id,$cat);
        $today = new \DateTime();

        return ["user" => $user, "tasks" => $tasks, "today" => $today,"cat"=>$category];

    }


}
