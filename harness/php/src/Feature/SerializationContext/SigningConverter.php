<?php

declare(strict_types=1);

namespace Harness\Feature\SerializationContext;

use Temporal\Api\Common\V1\Payload;
use Temporal\DataConverter\JsonConverter;
use Temporal\DataConverter\PayloadConverterInterface;
use Temporal\DataConverter\SerializationContext;
use Temporal\DataConverter\SerializationContextAwareInterface;
use Temporal\DataConverter\Type;

class SigningConverter implements PayloadConverterInterface, SerializationContextAwareInterface
{
    public const ENCODING = 'ser-context/signed';
    public const METADATA_KEY = 'ctx-signature';
    public const NO_CONTEXT = 'none';

    private JsonConverter $json;
    private string $signature = self::NO_CONTEXT;

    public function __construct()
    {
        $this->json = new JsonConverter();
    }

    public function getEncodingType(): string
    {
        return self::ENCODING;
    }

    public function getSerializationContext(): ?SerializationContext
    {
        return null;
    }

    public function withSerializationContext(?SerializationContext $context): static
    {
        $clone = clone $this;
        $clone->signature = Signature::of($context);
        return $clone;
    }

    public function toPayload($value): ?Payload
    {
        if (!$value instanceof SignedValue) {
            return null;
        }

        return (new Payload())
            ->setData((string) \json_encode($value->value))
            ->setMetadata([
                'encoding' => self::ENCODING,
                self::METADATA_KEY => $this->signature,
            ]);
    }

    public function fromPayload(Payload $payload, Type $type): mixed
    {
        $encoded = Signature::ofPayload($payload);
        if ($encoded !== $this->signature) {
            throw new \RuntimeException(\sprintf(
                'serialization context mismatch: payload encoded as "%s", decoded as "%s"',
                $encoded,
                $this->signature,
            ));
        }

        return new SignedValue(\json_decode($payload->getData(), false, 512, \JSON_THROW_ON_ERROR));
    }
}
