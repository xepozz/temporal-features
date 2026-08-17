<?php

declare(strict_types=1);

namespace Harness\Feature\SerializationContext;

use Temporal\Api\Common\V1\Payload;
use Temporal\DataConverter\ActivitySerializationContext;
use Temporal\DataConverter\HasWorkflowSerializationContext;
use Temporal\DataConverter\SerializationContext;

final class Signature
{
    public static function workflow(string $namespace, ?string $workflowId): string
    {
        return \sprintf('wf|%s|%s', $namespace, $workflowId ?? '');
    }

    public static function activity(
        string $namespace,
        ?string $workflowId,
        ?string $workflowType,
        string $activityType,
        string $taskQueue,
        bool $isLocal,
    ): string {
        return \sprintf(
            'act|%s|%s|%s|%s|%s|%s',
            $namespace,
            $workflowId ?? '',
            $workflowType ?? '',
            $activityType,
            $taskQueue,
            $isLocal ? 'true' : 'false',
        );
    }

    public static function of(?SerializationContext $context): string
    {
        if ($context instanceof ActivitySerializationContext) {
            return self::activity(
                $context->namespace,
                $context->workflowId,
                $context->workflowType,
                $context->activityType,
                $context->taskQueue,
                $context->isLocal,
            );
        }

        if ($context instanceof HasWorkflowSerializationContext) {
            return self::workflow($context->getNamespace(), $context->getWorkflowId());
        }

        return SigningConverter::NO_CONTEXT;
    }

    public static function ofPayload(?Payload $payload): string
    {
        return $payload === null ? '' : ($payload->getMetadata()[SigningConverter::METADATA_KEY] ?? '');
    }

    /**
     * @param iterable<Payload>|null $payloads
     */
    public static function first(?iterable $payloads): string
    {
        foreach ($payloads ?? [] as $payload) {
            return self::ofPayload($payload);
        }

        return '';
    }
}
