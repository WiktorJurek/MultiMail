<?php

namespace App\Entity;

use App\Repository\UserCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCategoryRepository::class)]
class UserCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCategory(): ?int
    {
        return $this->id_category;
    }

    public function setIdCategory(int $id_category): static
    {
        $this->id_category = $id_category;

        return $this;
    }
}
