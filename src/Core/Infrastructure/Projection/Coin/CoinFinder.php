<?php

declare(strict_types=1);

namespace Core\Infrastructure\Projection\Coin;

use Common\Infrastructure\Projection\BaseFinder;
use Core\Infrastructure\Projection\Table;
use Core\Domain\Model\Coin\View\Coin;

final class CoinFinder extends BaseFinder
{
    public function findAll(): ?array
    {
        $stmt = $this->connection->prepare(\sprintf('SELECT * FROM %s ORDER BY value DESC', Table::COIN));
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'Core\\Domain\\Model\\Coin\\View\\Coin');
        return $stmt->fetchAll();
    }

    public function findById(string $coinId): ?Coin
    {
        $stmt = $this->connection->prepare(\sprintf('SELECT * FROM %s where id = :coin_id', Table::COIN));
        $stmt->bindValue('coin_id', $coinId);
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'Core\\Domain\\Model\\Coin\\View\\Coin');
        $result = $stmt->fetch();

        if (false === $result) {
            return null;
        }

        return $result;
    }

    public function findByValue(float $value): ?Coin
    {
        $stmt = $this->connection->prepare(\sprintf('SELECT * FROM %s where value = :value LIMIT 1', Table::COIN));
        $stmt->bindValue('value', $value);
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'Core\\Domain\\Model\\Coin\\View\\Coin');
        $result = $stmt->fetch();

        if (false === $result) {
            return null;
        }

        return $result;
    }

}
