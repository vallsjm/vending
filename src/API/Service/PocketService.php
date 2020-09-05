<?php

declare(strict_types=1);

namespace API\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Common\Domain\ValueObject\Money\BaseMoney;
use Core\Domain\Model\Coin\ChangeValue;
use Core\Domain\Model\Coin\CoinValue;

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

    public function total(): BaseMoney
    {
        return ChangeValue::fromArray($this->coins());
    }

    public function change(BaseMoney $price): BaseMoney
    {
        return $this->total()->sub($price);
    }

    public function status(): array
    {
        return [
            'money' => $this->total()->value(),
            'coins' => $this->coins()
        ];
    }

    public function insertCoin(float $coin): void
    {
        $coin = CoinValue::fromFloat($coin);
        $pocket = $this->coins();
        array_push($pocket, $coin->value());
        $this->session->set('pocket', $pocket);
    }

    public function returnCoin(float $coin): void
    {
        $pocket = $this->coins();
        if (($key = array_search($coin, $pocket)) !== false) {
            unset($pocket[$key]);
            $this->session->set('pocket', $pocket);
        } else {
            throw new \InvalidArgumentException('The coin is not found inside the pocket.');
        }
    }

    public function returnCoins(array $coins): void
    {
        foreach ($coins as $coin) {
            $this->returnCoin($coin);
        }
    }

    public function availableCoinsForChange(BaseMoney $change): array
    {
        $availableCoins = [];
        if ($change->isGreaterThanZero()) {
            $pocket = $this->coins();
            rsort($pocket, SORT_NUMERIC);
            foreach ($pocket as $money) {
                $coin = CoinValue::fromFloat($money);
                if ($change->isGreaterThanOrEqualTo($coin)) {
                    $availableCoins[] = $coin->value();
                    $change = $change->sub($coin);
                }
            }
        }

        return $availableCoins;
    }


}
