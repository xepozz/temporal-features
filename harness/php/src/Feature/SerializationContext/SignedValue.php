<?php

declare(strict_types=1);

namespace Harness\Feature\SerializationContext;

final class SignedValue
{
    public function __construct(
        public mixed $value,
    ) {}
}
