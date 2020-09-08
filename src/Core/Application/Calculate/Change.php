<?php

declare(strict_types=1);

namespace Core\Application\Calculate;

use Core\Domain\Model\Coin\CoinValue;
use Core\Domain\Model\Coin\ChangeValue;
use Core\Domain\Model\Item\ItemPrice;
use Core\Domain\Model\Coin\Type\CoinValueType;

final class Change
{
    private $change;
    private $price;
    private $coinsPocket;
    private $coinsResumeMachine;
    private $changeCoinsFromPocket;
    private $changeCoinsFromMachine;

    public function __construct(
        ItemPrice $price,
        array $coinsPocket,
        array $coinsResumeMachine
    )
    {
        $this->price              = $price;
        $this->coinsPocket        = $coinsPocket;
        $this->coinsResumeMachine = $coinsResumeMachine;

        $totalPocket = ChangeValue::fromArray($this->coinsPocket);
        $change = $totalPocket->sub($price);

        if ($change->isLessThanZero()) {
            throw new \InvalidArgumentException('Not enough money for it, that cost ' . $price);
        }

        $this->changeCoinsFromPocket = $this->availableCoinsFromPocket($change);
        $change = $change->sub(ChangeValue::fromArray($this->changeCoinsFromPocket));
        $this->changeCoinsFromMachine = $this->availableCoinsFromMachine($change);
        $change = $change->sub(ChangeValue::fromArray($this->changeCoinsFromMachine));

        if ($change->isGreaterThanZero()) {
            throw new \InvalidArgumentException('We don\'t have enough coins to return the change.');
        }

        $this->change = $change;
    }


    private function availableCoinsFromPocket(ChangeValue $change): array
    {
        $availableCoins = [];
        if ($change->isGreaterThanZero()) {
            $pocket = $this->coinsPocket;
            rsort($pocket, SORT_NUMERIC);
            foreach ($pocket as $coin) {
                if (in_array($coin, CoinValueType::RETURN)) {
                    $coin = CoinValue::fromFloat($coin);
                    if ($change->isGreaterThanOrEqualTo($coin)) {
                        $availableCoins[] = $coin->value();
                        $change = $change->sub($coin);
                    }
                }
            }
        }

        return $availableCoins;
    }

    private function availableCoinsFromMachine(ChangeValue $change): array
    {
        $availableCoins = [];
        if ($change->isGreaterThanZero()) {
            $coins = $this->coinsResumeMachine;
            foreach ($coins as $coin) {
                $value  = $coin->value();
                if (in_array($value->value(), CoinValueType::RETURN)) {
                    $amount = $coin->amount();
                    while ($change->isGreaterThanOrEqualTo($value) && ($amount > 0)) {
                        $availableCoins[] = $value->value();
                        $change = $change->sub($value);
                        $amount--;
                    }
                }
            }
        }

        return $availableCoins;
    }

    public function price(): ItemPrice
    {
        return $this->price;
    }

    public function change(): ChangeValue
    {
        return $this->change;
    }

    public function changeCoinsFromPocket(): array
    {
        return $this->changeCoinsFromPocket;
    }

    public function changeCoinsFromMachine(): array
    {
        return $this->changeCoinsFromMachine;
    }

}
