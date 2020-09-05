<?php

declare(strict_types=1);

namespace Core\Domain\Model\Item;

use Common\Domain\Aggregate\BaseAggregateRoot;
use Common\Domain\Entity\EntityInterface;
use Core\Domain\Model\Item\Event\ItemWasCreated;
use Core\Domain\Model\Item\Event\ItemWasBought;
use Core\Domain\Model\Item\Event\AmountItemWasUpdated;

final class Item extends BaseAggregateRoot
{
    /**
     * @var ItemId
     */
    private $itemId;

    /**
     * @var ItemName
     */
    private $name;

    /**
     * @var ItemPrice
     */
    private $price;

    /**
     * @var ItemAmount
     */
    private $amount;

    public static function createWithData(
        ItemId $itemId,
        ItemName $name,
        ItemPrice $price,
        ItemAmount $amount
    ): Item {
        $self = new self();

        $self->recordThat(ItemWasCreated::withData(
            $itemId,
            $name,
            $price,
            $amount
        ));

        return $self;
    }

    public function buyOneItem(): void
    {
        $this->recordThat(ItemWasBought::withData(
            $this->itemId,
            $this->name,
            $this->price,
            $this->amount->dec()
        ));
    }

    public function updateAmountItem(ItemAmount $amount): void
    {
        $this->recordThat(AmountItemWasUpdated::withData(
            $this->itemId,
            $this->name,
            $this->price,
            $this->amount,
            $amount
        ));
    }

    public function itemId(): ItemId
    {
        return $this->itemId;
    }

    public function name(): ItemName
    {
        return $this->name;
    }

    public function price(): ItemPrice
    {
        return $this->price;
    }

    public function amount(): ItemAmount
    {
        return $this->amount;
    }

    protected function aggregateId(): string
    {
        return (string) $this->itemId;
    }

    protected function whenItemWasCreated(ItemWasCreated $event): void
    {
        $this->itemId = $event->itemId();
        $this->name = $event->name();
        $this->price = $event->price();
        $this->amount = $event->amount();
    }

    protected function whenItemWasBought(ItemWasBought $event): void
    {
        $this->amount = $event->amount();
    }

    protected function whenAmountItemWasUpdated(AmountItemWasUpdated $event): void
    {
        $this->amount = $event->newAmount();
    }

    public function equals(EntityInterface $other): bool
    {
        return \get_class($this) === \get_class($other) && $this->itemId->equals($other->itemId);
    }
}
