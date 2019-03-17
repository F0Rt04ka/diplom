<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class MainPage extends Page
{
    public function __construct()
    {
        $this->setNumber(0);
    }

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $header1 = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $header2 = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $universityName1 = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $universityName2 = '';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $universityName3 = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $author = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $workName = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $workTitle = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $courseName = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $footer = '';

    public function getHeader1(): ?string
    {
        return $this->header1;
    }

    public function setHeader1(string $header1): self
    {
        $this->header1 = $header1;
        
        return $this;
    }

    public function getHeader2(): ?string
    {
        return $this->header2;
    }
    
    public function setHeader2(string $header2): self
    {
        $this->header2 = $header2;

        return $this;
    }

    public function getUniversityName1(): ?string
    {
        return $this->universityName1;
    }

    public function setUniversityName1(string $universityName1): self
    {
        $this->universityName1 = $universityName1;

        return $this;
    }

    public function getUniversityName2(): ?string
    {
        return $this->universityName2;
    }

    public function setUniversityName2(string $universityName2): self
    {
        $this->universityName2 = $universityName2;

        return $this;
    }

    public function getUniversityName3(): ?string
    {
        return $this->universityName3;
    }
    
    public function setUniversityName3(string $universityName3): self 
    {
        $this->universityName3 = $universityName3;
        
        return $this;
    }
    
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        
        return $this;
    }

    public function getWorkName(): ?string
    {
        return $this->workName;
    }

    public function setWorkName(string $workName): self
    {
        $this->workName = $workName;
        
        return $this;
    }

    public function getWorkTitle(): ?string
    {
        return $this->workTitle;
    }

    public function setWorkTitle(string $workTitle): self
    {
        $this->workTitle = $workTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getCourseName(): ?string
    {
        return $this->courseName;
    }

    public function setCourseName(string $courseName): self
    {
        $this->courseName = $courseName;

        return $this;
    }

    public function getFooter(): ?string
    {
        return $this->footer;
    }

    public function setFooter(string $footer): self
    {
        $this->footer = $footer;

        return $this;
    }
}
