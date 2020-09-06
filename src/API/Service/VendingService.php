<?php

declare(strict_types=1);

namespace API\Service;

use API\Service\PocketService;
use Core\Application\Service\CoinService;
use Core\Application\Service\ItemService;
use Core\Domain\Model\Coin\ChangeValue;

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

    public function insertCoin(string $value)
    {
        $this->pocketService->insertCoin((float) $value);
    }

    public function returnAllCoins(): array
    {
        $coins = $this->pocketService->coins();
        $this->pocketService->reset();

        return $coins;
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

        $change = $this->pocketService->change($item->price());
        if ($change->isLessThanZero()) {
            throw new \InvalidArgumentException('Not enough money for ' . $name . ', that cost ' . $item->price());
        }

        $changeCoinsFromPocket = $this->pocketService->availableCoinsForChange($change);
        $change = $change->sub(ChangeValue::fromArray($changeCoinsFromPocket));
        $changeCoinsFromMachine = $this->coinService->availableCoinsForChange($change);
        $change = $change->sub(ChangeValue::fromArray($changeCoinsFromMachine));

        if ($change->isGreaterThanZero()) {
            throw new \InvalidArgumentException('We don\'t have enough coins to return the change.');
        }

        # return all change coins
        $this->pocketService->returnCoins($changeCoinsFromPocket);
        $this->coinService->returnCoins($changeCoinsFromMachine);

        # the coins on the pocket are the price of the item
        $coins = $this->pocketService->coins();
        $this->coinService->insertCoins($coins);
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

        return [];
    }

    public function serviceCoinUpdate(string $value, array $payload): array
    {
        $coin  = $this->coinService->findItemByValue($value);
        if (isset($payload['amount'])) {
            $this->coinService->updateCoinAmount($coin, (int) $payload['amount']);
        }

        return [];
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
