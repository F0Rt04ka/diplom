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

    /**
     * @param Project $project
     * @return array
     */
    public function findProjectLinkWithNewComments(Project $project)
    {
//        return $this->_em->createQueryBuilder()
//            ->select('pl.id')
//            ->from(ProjectLink::class, 'pl')
//            ->join(Comment::class, 'c', 'WITH', 'c.projectLink = pl.id')
//            ->where(
//                'pl.project = :project_id',
//                'c.isNew = :is_new',
//                'pl.accessLevel = :access_level'
//            )
//            ->groupBy('pl.id')
//            ->setParameter('project_id', $project->getId())
//            ->setParameter('is_new', true)
//            ->setParameter('access_level', ProjectLink::ACCESS_LVL_COMMENTS)
//            ->getQuery()
//            ->getResult();
        $con = $this->getEntityManager()->getConnection();
        return $con->executeQuery(
            'SELECT pl.identifier, pl.project_version
            FROM project_link pl
                     JOIN comment c ON pl.id = c.project_link_id
            WHERE pl.project_id = :project_id
              AND c.is_new = :is_new
              AND pl.access_level = :access_level
            GROUP BY pl.id',
            [
                'project_id' => $project->getId(),
                'is_new' => true,
                'access_level' => ProjectLink::ACCESS_LVL_COMMENTS,
            ]
        )->fetchAll();
    }

    public function readCommentsOnProjectLink($projectLinkIdentifier)
    {
        //TODO: не обновляется статус.
        $link = $this->getEntityManager()->getRepository(ProjectLink::class)
            ->findByIdentifier($projectLinkIdentifier);
        $this->createQueryBuilder('c')
            ->update()
            ->set('c.isNew', ':is_new')
            ->where('c.projectLink=:project_link')
            ->setParameter('is_new', false)
            ->setParameter('project_link', $link->getId())
            ->getQuery()
            ->execute();
    }
}
