<?php

declare(strict_types=1);

namespace Core\Domain\Model\Coin\Handler;

use Core\Domain\Model\Coin\Query\GetAllCoins;
use Core\Infrastructure\Projection\Coin\CoinFinder;
use React\Promise\Deferred;

class GetAllCoinsHandler
{
    private $coinFinder;

    public function __construct(CoinFinder $coinFinder)
    {
        $this->coinFinder = $coinFinder;
    }

    public function __invoke(GetAllCoins $query, Deferred $deferred = null)
    {
        $coins = $this->coinFinder->findAll();
        if (null === $deferred) {
            return $coins;
        }

        $deferred->resolve($coins);
    }
}
