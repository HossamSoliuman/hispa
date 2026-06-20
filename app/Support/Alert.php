<?php

namespace App\Support;

use App\Enums\AlertSeverity;
use App\Enums\AlertType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;

/**
 * An immutable, already-translated owner warning produced by
 * {@see \App\Service\Owner\OwnerAlertService}. Keeps the view and the JSON
 * polling endpoint trivial and the service easy to assert against in tests.
 *
 * @implements Arrayable<string, mixed>
 */
final class Alert implements Arrayable
{
    public function __construct(
        public AlertType $type,
        public AlertSeverity $severity,
        public string $title,
        public string $message,
        public ?string $url = null,
        public ?Carbon $dueAt = null,
    ) {}

    /**
     * @return array{
     *     type: string, severity: int, severity_color: string,
     *     icon: string, title: string, message: string,
     *     url: string|null, due_at: string|null, due_for_humans: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'severity' => $this->severity->value,
            'severity_color' => $this->severity->color(),
            'icon' => $this->type->icon(),
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'due_at' => $this->dueAt?->toIso8601String(),
            'due_for_humans' => $this->dueAt?->diffForHumans(),
        ];
    }
}
