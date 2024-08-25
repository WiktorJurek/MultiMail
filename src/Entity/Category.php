<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, MailCampaign>
     */
    #[ORM\ManyToMany(targetEntity: MailCampaign::class, mappedBy: 'categories')]
    private Collection $mailCampaigns;

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->mailCampaigns = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, MailCampaign>
     */
    public function getMailCampaigns(): Collection
    {
        return $this->mailCampaigns;
    }

    public function addMailCampaign(MailCampaign $mailCampaign): static
    {
        if (!$this->mailCampaigns->contains($mailCampaign)) {
            $this->mailCampaigns->add($mailCampaign);
            $mailCampaign->addCategory($this);
        }

        return $this;
    }

    public function removeMailCampaign(MailCampaign $mailCampaign): static
    {
        if ($this->mailCampaigns->removeElement($mailCampaign)) {
            $mailCampaign->removeCategory($this);
        }

        return $this;
    }
}
