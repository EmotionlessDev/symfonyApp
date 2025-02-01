<?php

namespace App\Entity;

use App\Repository\PortfolioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PortfolioRepository::class)]
class Portfolio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'portfolios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?float $balance = null;

    /**
     * @var Collection<int, Depositary>
     */
    #[ORM\OneToMany(targetEntity: Depositary::class, mappedBy: 'portfolio')]
    private Collection $depositaries;

    /**
     * @var Collection<int, Application>
     */
    #[ORM\OneToMany(targetEntity: Application::class, mappedBy: 'portfolioId')]
    private Collection $applications;

    #[ORM\Column]
    private ?int $frozenBalance = null;

    public function __construct()
    {
        $this->depositaries = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection<int, Depositary>
     */
    public function getDepositaries(): Collection
    {
        return $this->depositaries;
    }

    public function subBalance(float $amount): static
    {
        $this->balance -= $amount;

        return $this;
    }

    public function addBalance(float $amount): static
    {
        $this->balance += $amount;

        return $this;
    }

    public function addDepositary(Depositary $depositary): static
    {
        if (!$this->depositaries->contains($depositary)) {
            $this->depositaries->add($depositary);
            $depositary->setPortfolio($this);
        }

        return $this;
    }

    public function removeDepositary(Depositary $depositary): static
    {
        if ($this->depositaries->removeElement($depositary)) {
            // set the owning side to null (unless already changed)
            if ($depositary->getPortfolio() === $this) {
                $depositary->setPortfolio(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): static
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setPortfolioId($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): static
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getPortfolioId() === $this) {
                $application->setPortfolioId(null);
            }
        }

        return $this;
    }

    public function getStockQuantity(Stock $stock): int
    {
        $quantity = 0;
        foreach ($this->depositaries as $depositary) {
            if ($depositary->getStock() === $stock) {
                $quantity += $depositary->getQuantity();
            }
        }

        return $quantity;
    }

    public function getFrozenBalance(): ?int
    {
        return $this->frozenBalance;
    }

    public function setFrozenBalance(int $frozenBalance): static
    {
        $this->frozenBalance = $frozenBalance;

        return $this;
    }

    public function subFrozenBalance(int $amount): static
    {
        $this->frozenBalance -= $amount;

        return $this;
    }

    public function addFrozenBalance(int $amount): static
    {
        $this->frozenBalance += $amount;

        return $this;
    }

    public function subStockQuantity(Stock $stock, int $quantity): static
    {
        foreach ($this->depositaries as $depositary) {
            if ($depositary->getStock() === $stock) {
                $depositary->subQuantity($quantity);
                break;
            }
        }

        return $this;
    }

    public function addStockQuantity(Stock $stock, int $quantity): static
    {
        foreach ($this->depositaries as $depositary) {
            if ($depositary->getStock() === $stock) {
                $depositary->addQuantity($quantity);
                break;
            }
        }

        return $this;
    }

    public function subStockFrozenQuantity(Stock $stock, int $quantity): static
    {
        foreach ($this->depositaries as $depositary) {
            if ($depositary->getStock() === $stock) {
                $depositary->subFrozenQuantity($quantity);
                break;
            }
        }

        return $this;
    }

    public function addStockFrozenQuantity(Stock $stock, int $quantity): static
    {
        foreach ($this->depositaries as $depositary) {
            if ($depositary->getStock() === $stock) {
                $depositary->addFrozenQuantity($quantity);
                break;
            }
        }

        return $this;
    }
}
