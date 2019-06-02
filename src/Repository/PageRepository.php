<?php

namespace App\Repository;

use App\Entity\EmptyPage;
use App\Entity\Page;
use App\Entity\Project;
use App\Entity\WorkProgram\TablePage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function deleteByProjectVersion(int $projectId, int $version)
    {
        $qb = $this->createQueryBuilder('page');
        $qb->delete()
            ->where(
                $qb->expr()->eq('page.project', $projectId),
                $qb->expr()->eq('page.version', $version)
            );
        $qb->getQuery()->getResult();
    }

    public function getNotMainPagesForProjectByVersion(Project $project, ?int $version = null)
    {
        if (empty($version)) {
            $version = $project->getCurrentVersion();
        }

        $qb = $this->createQueryBuilder('p');
        return $qb
            ->where(
                'p.project=:projectId',
                'p.version=:version',
                $qb->expr()->orX(
                    $qb->expr()->isInstanceOf('p', TablePage::class),
                    $qb->expr()->isInstanceOf('p', EmptyPage::class)
                )
            )
            ->setParameter('projectId', $project->getId())
            ->setParameter('version', $version)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Page[] Returns an array of Page objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Page
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
