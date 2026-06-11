<?php

namespace Tests\Unit;

use App\Enums\TripStatus;
use PHPUnit\Framework\TestCase;

class TripStatusTest extends TestCase
{
    public function test_allowed_next_matches_state_machine(): void
    {
        $this->assertSame([TripStatus::InProgress, TripStatus::Cancelled], TripStatus::New->allowedNext());
        $this->assertSame([TripStatus::Finished, TripStatus::Cancelled], TripStatus::InProgress->allowedNext());
        $this->assertSame([TripStatus::ReadyToSell], TripStatus::Finished->allowedNext());
        $this->assertSame([TripStatus::Counted], TripStatus::Counting->allowedNext());
        $this->assertSame([TripStatus::ReadyToSell], TripStatus::Counted->allowedNext());
        $this->assertSame([TripStatus::Sold], TripStatus::ReadyToSell->allowedNext());
        $this->assertSame([], TripStatus::Sold->allowedNext());
        $this->assertSame([], TripStatus::Cancelled->allowedNext());
    }

    public function test_new_cannot_transition_directly_to_sold_or_counting(): void
    {
        $this->assertFalse(TripStatus::New->canTransitionTo(TripStatus::Sold));
        $this->assertFalse(TripStatus::New->canTransitionTo(TripStatus::Counting));
        $this->assertFalse(TripStatus::New->canTransitionTo(TripStatus::Finished));
    }

    public function test_sold_and_cancelled_are_terminal(): void
    {
        $this->assertTrue(TripStatus::Sold->isTerminal());
        $this->assertTrue(TripStatus::Cancelled->isTerminal());
        $this->assertSame([], TripStatus::Sold->allowedNext());
        $this->assertSame([], TripStatus::Cancelled->allowedNext());
    }

    public function test_non_terminal_statuses_are_not_terminal(): void
    {
        foreach ([TripStatus::New, TripStatus::InProgress, TripStatus::Finished, TripStatus::Counting, TripStatus::Counted, TripStatus::ReadyToSell] as $status) {
            $this->assertFalse($status->isTerminal(), "{$status->name} should not be terminal");
        }
    }

    public function test_color_and_label_return_non_empty_for_all_statuses(): void
    {
        foreach (TripStatus::cases() as $status) {
            $this->assertNotEmpty($status->color(), "{$status->name} color should not be empty");
        }
    }
}
