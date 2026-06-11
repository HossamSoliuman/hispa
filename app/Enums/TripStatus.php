<?php

namespace App\Enums;

enum TripStatus: int
{
    case New = 1;
    case InProgress = 2;
    case Cancelled = 3;
    case Finished = 4;
    case Counting = 5;
    case Counted = 6;
    case ReadyToSell = 7;
    case Sold = 8;

    public function label(): string
    {
        return __('trips.statuses.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'primary',
            self::InProgress => 'info',
            self::Finished => 'secondary',
            self::Counting => 'warning',
            self::Counted => 'warning',
            self::ReadyToSell => 'success',
            self::Sold => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Sold, self::Cancelled], true);
    }

    /** @return array<int, self> */
    public function allowedNext(): array
    {
        return match ($this) {
            self::New => [self::InProgress, self::Cancelled],
            self::InProgress => [self::Finished, self::Cancelled],
            self::Finished => [self::ReadyToSell],
            self::Counting => [self::Counted],
            self::Counted => [self::ReadyToSell],
            self::ReadyToSell => [self::Sold],
            self::Sold, self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return in_array($to, $this->allowedNext(), true);
    }

    public function transitionLabelTo(self $to): string
    {
        $key = match ([$this, $to]) {
            [self::New, self::InProgress] => 'start_trip',
            [self::InProgress, self::Finished] => 'finish_trip',
            [self::Finished, self::ReadyToSell] => 'mark_ready',
            [self::Counting, self::Counted] => 'finish_counting',
            [self::Counted, self::ReadyToSell] => 'mark_ready',
            [self::ReadyToSell, self::Sold] => 'mark_sold',
            default => 'cancel_trip',
        };

        return __('trips.actions.'.$key);
    }
}
