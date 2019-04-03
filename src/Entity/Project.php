<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    const TYPE_DEFAULT = 'default';
    const TYPE_CV      = 'cv';
    const TYPES = [
        self::TYPE_DEFAULT,
        self::TYPE_CV,
        ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(choices=Project::TYPES)
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @Assert\GreaterThanOrEqual(0)
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $currentVersion = 0;

    /**
     * @var int
     */
    private $selectedVersion;

    /**
     * @ORM\Column(type="string", unique=true, length=10)
     */
    private $identifier;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Page", mappedBy="project", orphanRemoval=true, cascade={"persist"})
     */
    private $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCurrentVersion(): ?int
    {
        return $this->currentVersion;
    }

    public function setCurrentVersion(int $currentVersion): self
    {
        $this->currentVersion = $currentVersion;

        return $this;
    }

    public function incCurrentVersion(): self
    {
        $this->currentVersion++;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return ArrayCollection|Page[]
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setProject($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->contains($page)) {
            $this->pages->removeElement($page);
            // set the owning side to null (unless already changed)
            if ($page->getProject() === $this) {
                $page->setProject(null);
            }
        }

        return $this;
    }

    public function getMainPage(?int $version = null): ?Page
    {
        $mainPageClass = $this->getType() === self::TYPE_CV ? CVMainPage::class : MainPage::class;
        $mainPage = $this->getPagesByType($mainPageClass, $version)->first();

        return  $mainPage ? $mainPage : null;
    }

    /**
     * @param string $pageType
     * @param int|null $version
     * @return ArrayCollection|Page[]
     */
    public function getPagesByType(string $pageType, ?int $version = null): Collection
    {
        $pages = $this->getPagesByVersion($version)->filter(
            function (Page $page) use ($pageType) {
                return $page instanceof $pageType;
            });

        return  $pages;
    }

    /**
     * @param int|null $version
     * @return ArrayCollection|Page[]
     */
    public function getPagesByVersion(?int $version = null): Collection
    {
        if ($this->currentVersion == 0) {
            return $this->getPages();
        }

        $version = $version ?? $this->currentVersion;
        $criteria = Criteria::create()->where(Criteria::expr()->eq('version', $version));

        return $this->getPages()->matching($criteria);

    }

    public function getSelectedVersion(): ?int
    {
        return $this->selectedVersion ?? $this->getCurrentVersion();
    }

    public function setSelectedVersion(int $selectedVersion): Project
    {
        $this->selectedVersion = $selectedVersion;

        return $this;
    }
}
