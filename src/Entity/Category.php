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

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Post::class)]
    private Collection $category;

    public function __construct()
    {
        $this->category = new ArrayCollection();
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

    /**
     * @return Collection<int, Post>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addOption(Post $option): static
    {
        if (!$this->category->contains($option)) {
            $this->category->add($option);
            $option->setCategory($this);
        }

        return $this;
    }

    public function removeOption(Post $option): static
    {
        if ($this->category->removeElement($option)) {
            // set the owning side to null (unless already changed)
            if ($option->getCategory() === $this) {
                $option->setCategory(null);
            }
        }

        return $this;
    }
}
