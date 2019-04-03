<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomPageRepository")
 */
class CVMainPage extends Page
{
    const DEFAULT_FIELDS = [
        'style' => '',
        'color' => '',
        'firstName' => '',
        'lastName' => '',
        'email' => '',
        'title' => '',
        'address' => [],
        'phones' => [],
        'social' => [],
        'sections' => [],
        'other' => [],
        'homepage' => '',
        'extraInfo' => '',
    ];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $fields = self::DEFAULT_FIELDS;

    public function __construct()
    {
        $this->setNumber(0);
        $this->fields = array_merge($this->fields ?? [], self::DEFAULT_FIELDS);
    }

    public function getStyle(): string
    {
        return $this->fields['style'] ?? '';
    }

    public function setStyle(string $style): self
    {
        $this->fields['style'] = $style;

        return $this;
    }

    public function getColor(): string
    {
        return $this->fields['color'] ?? '';
    }

    public function setColor(string $color): self
    {
        $this->fields['color'] = $color;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->fields['firstName'] ?? '';
    }

    public function setFirstName(string $firstName): self
    {
        $this->fields['firstName'] = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->fields['lastName'] ?? '';
    }

    public function setLastName(string $lastName): self
    {
        $this->fields['lastName'] = $lastName;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->fields['title'] ?? '';
    }

    public function setTitle(string $title): self
    {
        $this->fields['title'] = $title;

        return $this;
    }

    public function getAddress(): array
    {
        return $this->fields['address'] ?? [];
    }

    public function setAddress(array $address): self
    {
        $this->fields['address'] = $address;

        return $this;
    }

    public function getPhones(): array
    {
        return $this->fields['phones'] ?? [];
    }

    public function setPhones(array $phones): self
    {
        $this->fields['phones'] = $phones;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->fields['email'] ?? '';
    }

    public function setEmail(string $email): self
    {
        $this->fields['email'] = $email;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getSocial(): array
    {
        return $this->fields['social'] ?? [];
    }

    public function setSocial(array $social): self
    {
        $this->fields['social'] = $social;

        return $this;
    }

    public function getSections(): array
    {
        return $this->fields['sections'] ?? [];
    }

    public function setSections(array $sections): self
    {
        $this->fields['sections'] = $sections;

        return $this;
    }

    public function getHomepage(): string
    {
        return $this->fields['homepage'] ?? '';
    }

    public function setHomepage(string $homepage): self
    {
        $this->fields['homepage'] = $homepage;

        return $this;
    }

    public function getExtraInfo(): string
    {
        return $this->fields['extraInfo'] ?? '';
    }

    public function setExtraInfo(string $extraInfo): self
    {
        $this->fields['extraInfo'] = $extraInfo;

        return $this;
    }

    public function getQuote(): string
    {
        return $this->fields['quote'] ?? '';
    }

    public function setQuote(string $quote): self
    {
        $this->fields['quote'] = $quote;

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): self
    {
        $this->fields = array_merge($fields, self::DEFAULT_FIELDS);

        return $this;
    }
}
