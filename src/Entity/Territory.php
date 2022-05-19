<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TerritoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Territory
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: TerritoryRepository::class)]
class Territory
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 6)]
    private $nuts;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 60)]
    private $title;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getNuts(): ?string
    {
        return $this->nuts;
    }

    /**
     * @param string $nuts
     * @return $this
     */
    public function setNuts(string $nuts): self
    {
        $this->nuts = $nuts;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
