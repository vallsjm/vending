<?php

declare(strict_types=1);

namespace API\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Core\Domain\Model\Coin\MoneyValue;

final class PocketService
{
    private $session;

    public function __construct(
        SessionInterface $session
    ) {
        $this->session = $session;
    }

    public function coins(): array
    {
        return $this->session->get('pocket', []);
    }

    public function reset(): void
    {
        $this->session->set('pocket', []);
    }

    public function insertCoin(float $coin): void
    {
        $pocket = $this->coins();
        array_push($pocket, $coin);
        $this->session->set('pocket', $pocket);
    }

    public function returnCoin(float $coin): float
    {
        $pocket = $this->coins();
        if (($key = array_search($coin, $pocket)) !== false) {
            unset($pocket[$key]);
            $this->session->set('pocket', $pocket);
            return $coin;
        } else {
            throw new \InvalidArgumentException('The coin is not found inside the pocket.');
        }
    }

    public function returnCoins(array $coins): array
    {
        foreach ($coins as $coin) {
            $this->returnCoin($coin);
        }

        return $coins;
    }

    public function total(): float
    {
        $money = MoneyValue::fromArray($this->coins());

        return $money->value();
    }

    public function status(): array
    {
        return [
            'money' => $this->total(),
            'coins' => $this->coins()
        ];
    }

}
