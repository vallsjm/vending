<?php

declare(strict_types=1);

namespace Core\Domain\Model\Coin\View;

use \JsonSerializable;
use Core\Domain\Model\Coin\CoinValue;

final class Coin implements JsonSerializable
{
    /**
     * @var CoinId
     */
    private $id;

    private $value;

    private $amount;

    public function __construct()
    {
        $this->id     = (string) $this->id;
        $this->value  = CoinValue::fromString($this->value);
        $this->amount = (int) $this->amount;
    }

    public function coinId(): string
    {
        return $this->id;
    }

    public function value(): CoinValue
    {
        return $this->value;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function toArray(): array
    {
        return [
            'id'     => $this->id,
            'value'  => $this->value->value(),
            'amount' => $this->amount
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
