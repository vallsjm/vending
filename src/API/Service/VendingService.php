<?php

declare(strict_types=1);

namespace API\Service;

use API\Service\PocketService;
use Core\Application\Service\CoinService;
use Core\Application\Service\ItemService;
use Core\Domain\Model\Coin\ChangeValue;
use Core\Domain\Model\Coin\CoinValue;
use Core\Application\Calculate\Change;

final class VendingService
{
    private $pocketService;
    private $coinService;
    private $itemService;

    public function __construct(
        PocketService $pocketService,
        CoinService $coinService,
        ItemService $itemService
    ) {
        $this->pocketService = $pocketService;
        $this->coinService = $coinService;
        $this->itemService = $itemService;
    }

    public function insertCoin(string $coin)
    {
        $coin = CoinValue::fromString($coin);
        $this->pocketService->insertCoin($coin->value());
    }

    public function returnAllCoins(): array
    {
        $coinsToReturn = $this->pocketService->coins();
        $coinsReturned = $this->pocketService->returnCoins($coinsToReturn);
        $this->pocketService->reset();

        return $coinsReturned;
    }

    public function coinStatus(): array
    {
        return $this->pocketService->status();
    }

    public function buyItem(string $name): array
    {
        $item  = $this->itemService->findItemByName($name);
        if ($item->amount() < 1) {
            throw new \InvalidArgumentException('We don\'t have enough ' . $name);
        }

        $change = new Change(
            $item->price(),
            $this->pocketService->coins(),
            $this->coinService->coins()
        );

        # return all change coins
        $changeCoinsFromPocket = $this->pocketService->returnCoins(
                $change->changeCoinsFromPocket()
        );
        $this->coinService->returnCoins(
            $this->coinService->findCoinsByValues(
                $changeCoinsFromMachine = $change->changeCoinsFromMachine()
            )
        );

        # the coins on the pocket are the price of the item
        $this->coinService->insertCoins(
            $this->coinService->findCoinsByValues(
                $this->pocketService->coins()
            )
        );

        $itemName = $this->itemService->buyItem($item);
        $this->pocketService->reset();

        return array_merge([$itemName], $changeCoinsFromPocket, $changeCoinsFromMachine);
    }

    public function itemStatus(): array
    {
        return $this->itemService->status();
    }

    public function serviceItemUpdate(string $name, array $payload): array
    {
        $item  = $this->itemService->findItemByName($name);
        if (isset($payload['price'])) {
            $this->itemService->updateItemPrice($item, (float) $payload['price']);
        }
        if (isset($payload['amount'])) {
            $this->itemService->updateItemAmount($item, (int) $payload['amount']);
        }

        return $payload;
    }

    public function serviceCoinUpdate(string $coin, array $payload): array
    {
        $coin = CoinValue::fromString($coin);
        $coin = $this->coinService->findCoinByValue($coin);
        if (isset($payload['amount'])) {
            $this->coinService->updateCoinAmount($coin, (int) $payload['amount']);
        }

        return $payload;
    }

    public function serviceStatus(): array
    {
        $status = [
            'pocket'  => $this->pocketService->status(),
            'machine' => $this->coinService->status(),
            'items'   => $this->itemService->status()
        ];

        return $status;
    }
}
