<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project|null findOneById(int $id)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * Generate new random unique identifier
     *
     * @return string|null
     */
    public function generateNewUniqueIdentifier(): string
    {
        $identifier = null;
        do {
            try {
                $identifier = bin2hex(random_bytes(7));
            }catch (\Exception $e) {}
        } while ($this->findOneBy(['identifier' => $identifier]));

        return $identifier;
    }

    public function findByIdentifier(string $identifier)
    {
        return $this->findOneBy(['identifier' => $identifier]);
    }

    public function getVersionForProject(int $projectId, ?int $limit = 10): array
    {
        return array_map(
            function ($value) { return intval($value); },
            array_column(
                $this->getVersionsQueryBuilder($projectId, $limit)->getQuery()->getScalarResult(),
                'version'
            )
        );
    }

    public function getVersions(): array
    {
        return $this->getVersionsQueryBuilder()->getQuery()->getArrayResult();
    }

    private function getVersionsQueryBuilder(?int $projectId = null, ?int $limit = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('project')
            ->select(['project.id', 'pages.version'])
            ->leftJoin('project.pages', 'pages')
            ->groupBy('project.id', 'pages.id')
            ->orderBy('pages.version', 'DESC')
        ;

        if ($projectId) {
            $qb
                ->where($qb->expr()->eq('project.id', $projectId))
                ->select('pages.version');
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }

    public function getCountVersionsForAll(): array
    {
        $result = $this
            ->createQueryBuilder('project')
            ->select(['project.id', 'pages.version'])
            ->leftJoin('project.pages', 'pages')
            ->groupBy('project.id', 'pages.version')
            ->getQuery()->getArrayResult();

        $counter = [];
        foreach ($result as $item) {
            $key = $item['id'];
            array_key_exists($key, $counter) ? ++$counter[$key] : $counter[$key] = 1;
        }

        return $counter;
    }

    /**
     */

    private function countVersionsQuery(?int $projectId = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('project');
        $qb->select(['project.id', 'version'])
            ->leftJoin('project.pages', 'pages')
            ->groupBy('project.id', 'pages.version');

        if ($projectId) {
            $qb
                ->select($qb->expr()->count('pages.version'))
                ->where($qb->expr()->eq('project.id', $projectId));

        }

        return $qb;
    }
}
