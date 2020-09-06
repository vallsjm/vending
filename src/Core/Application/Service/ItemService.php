<?php

declare(strict_types=1);

namespace Core\Application\Service;

use Common\Application\Service\BaseService;
use Core\Domain\Model\Item\ItemId;
use Core\Domain\Model\Item\View\Item;
use Common\Domain\ValueObject\Money\BaseMoney;
use Core\Domain\Model\Item\Type\ItemPriceType;

final class ItemService extends BaseService
{

    public function load(): void
    {
        foreach (ItemPriceType::PRICES as $name => $price) {
            $payload = [
                'item_id' => (string) ItemId::generate(),
                'name'    => (string) $name,
                'price'   => (float) $price,
                'amount'  => (int) 10
            ];

            $command = $this->messageFactory->createMessageFromArray(
                'Core\Domain\Model\Item\Command\CreateItem', ['payload' => $payload]
            );

            $this->commandBus->dispatch($command);
        }
    }

    public function findItemByName(string $name): ?Item
    {
        $payload = [
            'name' => $name
        ];

        $query = $this->messageFactory->createMessageFromArray(
            'Core\Domain\Model\Item\Query\GetItemByName', ['payload' => $payload]
        );

        $item = null;
        $this->queryBus->dispatch($query)->then(function ($result) use (&$item) {
            $item = $result;
        });

        if (!$item) {
            throw new \InvalidArgumentException('Item not found: '. $name);
        }

        return $item;
    }

    public function buyItem(Item $item): string
    {
        $payload = [
            'item_id' => $item->itemId()
        ];

        $command = $this->messageFactory->createMessageFromArray(
            'Core\Domain\Model\Item\Command\BuyItem', ['payload' => $payload]
        );

        $this->commandBus->dispatch($command);

        return $item->name();
    }

    public function updateItemAmount(Item $item, int $amount): void
    {
        $payload = [
            'item_id' => $item->itemId(),
            'amount' => $amount
        ];

        $command = $this->messageFactory->createMessageFromArray(
            'Core\Domain\Model\Item\Command\UpdateItemAmount', ['payload' => $payload]
        );

        $this->commandBus->dispatch($command);
    }

    public function updateItemPrice(Item $item, float $price): void
    {
        $payload = [
            'item_id' => $item->itemId(),
            'price' => $price
        ];

        $command = $this->messageFactory->createMessageFromArray(
            'Core\Domain\Model\Item\Command\UpdateItemPrice', ['payload' => $payload]
        );

        $this->commandBus->dispatch($command);
    }

    public function items(): array
    {
        $payload = [];
        $query = $this->messageFactory->createMessageFromArray(
            'Core\Domain\Model\Item\Query\GetAllItems', ['payload' => $payload]
        );

        $items = [];
        $this->queryBus->dispatch($query)->then(function ($result) use (&$items) {
            $items = $result;
        });

        return $items;
    }

    public function status(): array
    {
        $items = $this->items();
        $status = [];
        foreach ($items as $item) {
            $status[] = [
                'item' => $item->name(),
                'price' => $item->price()->value(),
                'amount' => $item->amount()
            ];
        }
        return $status;
    }

}
