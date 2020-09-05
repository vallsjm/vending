<?php

declare(strict_types=1);

namespace Core\Domain\Model\Coin\Handler;

use Core\Domain\Model\Coin\Command\UpdateAmountCoin;
use Core\Domain\Model\Coin\Coin;
use Core\Domain\Model\Coin\CoinCollectionInterface;

class UpdateAmountCoinHandler
{
    /**
     * @var CoinCollectionInterface
     */
    private $coinCollection;

    public function __construct(CoinCollectionInterface $coinCollection)
    {
        $this->coinCollection = $coinCollection;
    }

    /**
     * @throws ItemNotFound
     */
    public function __invoke(UpdateAmountCoin $command): void
    {
        $coin = $this->coinCollection->get($command->coinId());
        if (!$coin) {
            throw new \InvalidArgumentException(\sprintf('Coin with id %s cannot be found.', (string) $command->coinId()));
        }

        $coin->updateAmountCoin($command->amount());
        $this->coinCollection->save($coin);
    }
}
