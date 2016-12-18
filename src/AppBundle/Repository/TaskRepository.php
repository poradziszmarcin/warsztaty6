<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Task;
use Doctrine\ORM\EntityRepository;

/**
 * TaskRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TaskRepository extends EntityRepository
{

    public function getUndone($id)
    {
        $tasks = $this->getEntityManager()->createQuery(
            "SELECT t FROM AppBundle:Task t WHERE t.user = :id AND t.done = 0 ORDER BY t.date DESC")->setParameter("id",$id)->getResult();

        return $tasks;
    }

    public function getDone($id)
    {
        $tasks = $this->getEntityManager()->createQuery(
            "SELECT t FROM AppBundle:Task t WHERE t.user = :id AND t.done = 1 ORDER BY t.date DESC")->setParameter("id",$id)->getResult();

        return $tasks;
    }

    public function getTaskbyCategory($id,$cat)
    {
        $tasks = $this->getEntityManager()->createQuery(
            "SELECT t FROM AppBundle:Task t WHERE t.user = :id AND t.category = :cat ORDER BY t.date DESC")
            ->setParameters(array("id"=>$id,"cat"=>$cat))->getResult();

        return $tasks;
    }


    public function getAllTasks($id)
    {
        $tasks = $this->getEntityManager()->createQuery(
            "SELECT t FROM AppBundle:Task t WHERE t.user = :id  ORDER BY t.date DESC ")
            ->setParameters(array("id"=>$id))->getResult();

        return $tasks;
    }


    public function getCategoryUndone($id,$cat)
    {
        $tasks = $this->getEntityManager()->createQuery(
            "SELECT t FROM AppBundle:Task t WHERE t.user = :id AND t.done = 0 AND t.category = :cat ORDER BY t.date DESC")
            ->setParameters(array("id"=>$id,"cat"=>$cat))->getResult();

        return $tasks;
    }

    public function getCategoryDone($id,$cat)
    {
        $tasks = $this->getEntityManager()->createQuery(
            "SELECT t FROM AppBundle:Task t WHERE t.user = :id AND t.done = 1 AND t.category = :cat ORDER BY t.date DESC")
            ->setParameters(array("id"=>$id,"cat"=>$cat))->getResult();

        return $tasks;
    }


}