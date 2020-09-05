<?php

declare(strict_types=1);

namespace Core\Infrastructure\Projection\Item;

use Common\Infrastructure\Projection\BaseFinder;
use Core\Infrastructure\Projection\Table;
use Core\Domain\Model\Item\View\Item;

final class ItemFinder extends BaseFinder
{
    public function findAll(): ?array
    {
        $stmt = $this->connection->prepare(\sprintf('SELECT * FROM %s ORDER BY name DESC', Table::ITEM));
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'Core\\Domain\\Model\\Item\\View\\Item');
        return $stmt->fetchAll();
    }

    public function findById(string $itemId): ?Item
    {
        $stmt = $this->connection->prepare(\sprintf('SELECT * FROM %s WHERE id = :item_id', Table::ITEM));
        $stmt->bindValue('item_id', $itemId);
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'Core\\Domain\\Model\\Item\\View\\Item');
        $result = $stmt->fetch();

        if (false === $result) {
            return null;
        }

        return $result;
    }

    public function findByName(string $name): ?Item
    {
        $stmt = $this->connection->prepare(\sprintf('SELECT * FROM %s WHERE name = :name LIMIT 1', Table::ITEM));
        $stmt->bindValue('name', $name);
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'Core\\Domain\\Model\\Item\\View\\Item');
        $result = $stmt->fetch();

        if (false === $result) {
            return null;
        }

        return $result;
    }

}
