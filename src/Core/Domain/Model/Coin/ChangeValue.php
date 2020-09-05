<?php

declare(strict_types=1);

namespace Core\Domain\Model\Coin;

use Common\Domain\ValueObject\Money\BaseMoney;

final class ChangeValue extends BaseMoney
{
    public function validate(float $value): void
    {
    }
}
