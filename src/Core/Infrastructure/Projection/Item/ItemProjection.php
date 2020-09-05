<?php

declare(strict_types=1);

namespace Core\Infrastructure\Projection\Item;

use Common\Infrastructure\Projection\BaseReadProjection;
use Core\Domain\Model\Item\Event\ItemWasCreated;
use Core\Domain\Model\Item\Event\ItemWasBought;
use Core\Domain\Model\Item\Event\AmountItemWasUpdated;

final class ItemProjection extends BaseReadProjection
{
    public function listenerList(): array
    {
        return [
            ItemWasCreated::class => function ($state, ItemWasCreated $event) {
                /** @var ItemReadModel $readModel */
                // @phpstan-ignore-next-line
                $readModel = $this->readModel();
                $readModel->stack('insert', [
                    'id' => $event->itemId()->value(),
                    'name' => $event->name()->value(),
                    'price' => $event->price()->value(),
                    'amount' => $event->amount()->value()
                ]);
            },
            ItemWasBought::class => function ($state, ItemWasBought $event) {
                /** @var ItemReadModel $readModel */
                // @phpstan-ignore-next-line
                $readModel = $this->readModel();
                $readModel->stack('update', [
                    'amount' => $event->amount()->value()
                ],[
                    'id' => $event->itemId()->value()
                ]);
            },
            AmountItemWasUpdated::class => function ($state, AmountItemWasUpdated $event) {
                /** @var ItemReadModel $readModel */
                // @phpstan-ignore-next-line
                $readModel = $this->readModel();
                $readModel->stack('update', [
                    'amount' => $event->newAmount()->value()
                ],[
                    'id' => $event->itemId()->value()
                ]);
            },
        ];
    }
}
