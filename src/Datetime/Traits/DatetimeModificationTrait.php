<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Datetime\Traits;

use Throwable;

trait DatetimeModificationTrait
{

    public function modify(string $modifier): static
    {
        try {
            return static::createFromInterface(parent::modify($modifier));
        } catch (Throwable $exception) {
            static::reportException($exception);

            return $this;
        }
    }

    public function subHours(int $hours = 1): static
    {
        return $this->modify("-{$hours} hours");
    }

    public function addHours(int $hours = 1): static
    {
        return $this->modify("+{$hours} hours");
    }

    public function subDays(int $days = 1): static
    {
        return $this->modify("-{$days} days");
    }

    public function addDays(int $days = 1): static
    {
        return $this->modify("+{$days} days");
    }

    public function subMonths(int $months = 1): static
    {
        return $this->modify("-{$months} months");
    }

    public function addMonths(int $months = 1): static
    {
        return $this->modify("+{$months} months");
    }

    public function startOfDay(): static
    {
        return $this->modify('00:00:00');
    }

    public function endOfDay(): static
    {
        return $this->modify('23:59:59.999999');
    }

    public function startOfMonth(): static
    {
        return $this->modify('first day of this month 00:00:00');
    }

    public function endOfMonth(): static
    {
        return $this->modify('last day of this month 23:59:59.999999');
    }

    public function startOfYear(): static
    {
        return $this->modify('first day of January 00:00:00');
    }

    public function endOfYear(): static
    {
        return $this->modify('last day of December 23:59:59.999999');
    }
}
