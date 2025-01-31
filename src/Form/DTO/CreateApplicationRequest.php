<?php

namespace App\Form\DTO;

use App\Enum\ActionEnum;

class CreateApplicationRequest
{
    private int $userId;
    private int $quantity;
    private float $price;
    private ActionEnum $action;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getAction(): ActionEnum
    {
        return $this->action;
    }

    public function setAction(ActionEnum $action): void
    {
        $this->action = $action;
    }
}
