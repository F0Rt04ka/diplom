<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Project;
use App\Entity\ProjectLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function customSave(Comment $comment): void
    {
        $con = $this->getEntityManager()->getConnection();
        $con->setAutoCommit(false);
        $con->insert('comment', [
            'text' => $comment->getText(),
            'is_new' => $comment->getIsNew(),
            'page_num' => $comment->getPageNum(),
            'project_link_id' => $comment->getProjectLink()->getId(),
        ]);
    }

    public function clearComments(ProjectLink $projectLink): void
    {
        $this->createQueryBuilder('c')
            ->delete()
            ->where('c.projectLink=:projectLink')
            ->setParameter('projectLink', $projectLink->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @param Project $project
     * @return Comment[]
     */
    public function findAllNewComments(Project $project)
    {
        $qb = $this->createQueryBuilder('c');
        return $qb
            ->join('c.projectLink', 'l')
            ->where($qb->expr()->eq('l.project', $project->getId()))
            ->getQuery()
            ->getArrayResult();
    }
}
