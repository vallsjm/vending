<?php

declare(strict_types=1);

namespace Core\Application\Service;

use Common\Application\Service\BaseService;
use Core\Domain\Model\Coin\CoinId;
use Core\Domain\Model\Coin\View\Coin;
use Common\Domain\ValueObject\Money\BaseMoney;
use Core\Domain\Model\Coin\Type\CoinValueType;
use Core\Domain\Model\Coin\ChangeValue;

final class CoinService extends BaseService
{
    public function load(): void
    {
        foreach (CoinValueType::INSERT as $value) {
            $payload = [
                'coin_id' => (string) CoinId::generate(),
                'value'   => (float) $value,
                'amount'  => 10
            ];

            $command = $this->messageFactory->createMessageFromArray(
                'Core\Domain\Model\Coin\Command\CreateCoin', ['payload' => $payload]
            );

            $this->commandBus->dispatch($command);
        }
    }

    public function findCoinByValue(float $value): ?Coin
    {
        $payload = [
            'value' => $value
        ];

        $query = $this->messageFactory->createMessageFromArray(
            'Core\Domain\Model\Coin\Query\GetCoinByValue', ['payload' => $payload]
        );

        $coin = null;
        $this->queryBus->dispatch($query)->then(function ($result) use (&$coin) {
            $coin = $result;
        });

        if (!$coin) {
            throw new \InvalidArgumentException('Coin value not found: ' . $value);
        }

        return $coin;
    }

    public function insertCoin(float $value): void
    {
        $coin = $this->findCoinByValue($value);

        $payload = [
            'coin_id' => $coin->coinId()
        ];

        $command = $this->messageFactory->createMessageFromArray(
            'Core\Domain\Model\Coin\Command\InsertCoin', ['payload' => $payload]
        );

        $this->commandBus->dispatch($command);
    }

    public function insertCoins(array $coins): void
    {
        foreach ($coins as $coin) {
            $this->insertCoin($coin);
        }
    }

    public function returnCoin(float $value): void
    {
        $coin = $this->findCoinByValue($value);

        $payload = [
            'coin_id' => $coin->coinId()
        ];

        $command = $this->messageFactory->createMessageFromArray(
            'Core\Domain\Model\Coin\Command\ReturnCoin', ['payload' => $payload]
        );

        $this->commandBus->dispatch($command);
    }

    public function updateCoinAmount(Coin $coin, int $amount): void
    {
        $payload = [
            'coin_id' => $coin->coinId(),
            'amount' => $amount
        ];

        $command = $this->messageFactory->createMessageFromArray(
            'Core\Domain\Model\Coin\Command\UpdateCoinAmount', ['payload' => $payload]
        );

        $this->commandBus->dispatch($command);
    }

    public function returnCoins(array $coins): void
    {
        foreach ($coins as $coin) {
            $this->returnCoin($coin);
        }
    }

    public function coins(): array
    {
        $payload = [];
        $query = $this->messageFactory->createMessageFromArray(
            'Core\Domain\Model\Coin\Query\GetAllCoins', ['payload' => $payload]
        );

        $coins = [];
        $this->queryBus->dispatch($query)->then(function ($result) use (&$coins) {
            $coins = $result;
        });

        return $coins;
    }

    public function availableCoinsForChange(BaseMoney $change): array
    {
        $availableCoins = [];
        if ($change->isGreaterThanZero()) {
            $coins = $this->coins();
            foreach ($coins as $coin) {
                $value  = $coin->value();
                if (in_value($value->value(), CoinValueType::RETURN)) {
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

    public function status(): array
    {
        $coins = $this->coins();
        $status = [];
        $total = ChangeValue::fromFloat(0);
        foreach ($coins as $coin) {
            $status[] = [
                'value' => $coin->value()->value(),
                'amount' => $coin->amount()
            ];

            $totalThisCoin = ChangeValue::fromFloat($coin->value()->value());
            $totalThisCoin = $totalThisCoin->mul(
                ChangeValue::fromFloat($coin->amount())
            );

            $total = $total->add($totalThisCoin);
        }

        return [
            'total' => $total->value(),
            'coins' => $status
        ];
    }


}
