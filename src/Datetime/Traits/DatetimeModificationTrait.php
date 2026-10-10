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

    public function subHours(int $hours): static
    {
        return $this->modify("-{$hours} hours");
    }

    public function addHours(int $hours): static
    {
        return $this->modify("+{$hours} hours");
    }

    public function subDays(int $days): static
    {
        return $this->modify("-{$days} days");
    }

    public function addDays(int $days): static
    {
        return $this->modify("+{$days} days");
    }
}
