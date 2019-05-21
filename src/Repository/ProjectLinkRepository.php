<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\ProjectLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProjectLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectLink[]    findAll()
 * @method ProjectLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectLinkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProjectLink::class);
    }

    public function findByIdentifier(string $identifier): ?ProjectLink
    {
        return $this->findOneBy(['identifier' => $identifier]);
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
            } catch (\Exception $e) {
            }
        } while ($this->findByIdentifier($identifier));

        return $identifier;
    }

    public function deleteByIdentifier(string $linkIdentifier)
    {
        $links = $this->findBy([
            'identifier' => $linkIdentifier,
            'accessLevel' => ProjectLink::EDITORIAL_ACCESS_LEVELS
        ]);
        foreach ($links as $link) {
            $this->_em->remove($link);
        }
        $this->_em->flush();

        return true;
    }
}
