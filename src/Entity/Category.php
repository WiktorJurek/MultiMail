<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: UserCategory::class, mappedBy: 'category')]
    private $userCategories;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUserCategories()
    {
        return $this->userCategories;
    }

    public function addUserCategory(UserCategory $userCategory): self
    {
        if (!$this->userCategories->contains($userCategory)) {
            $this->userCategories[] = $userCategory;
            $userCategory->setCategory($this);
        }

        return $this;
    }

    public function removeUserCategory(UserCategory $userCategory): self
    {
        if ($this->userCategories->removeElement($userCategory)) {
            // Set the owning side to null (unless already changed)
            if ($userCategory->getCategory() === $this) {
                $userCategory->setCategory(null);
            }
        }

        return $this;
    }
}
