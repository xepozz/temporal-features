<?php

declare(strict_types=1);

namespace Harness\Feature\SerializationContext;

use Temporal\Api\History\V1\HistoryEvent;
use Temporal\Client\Workflow\WorkflowExecutionHistory;

final class History
{
    /**
     * @return list<HistoryEvent>
     */
    public static function events(WorkflowExecutionHistory $history): array
    {
        return \iterator_to_array($history->getEvents(), false);
    }

    /**
     * @param list<HistoryEvent> $events
     * @param callable(HistoryEvent): bool $predicate
     */
    public static function find(array $events, string $name, callable $predicate): HistoryEvent
    {
        foreach ($events as $event) {
            if ($predicate($event)) {
                return $event;
            }
        }

        throw new \RuntimeException("no {$name} event in history");
    }
}
