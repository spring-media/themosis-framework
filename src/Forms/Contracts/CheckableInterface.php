<?php

namespace Themosis\Forms\Contracts;

interface CheckableInterface
{
    /**
     * Verify a value against a choice and return
     * a "checked" HTML attribute.
     */
    public function checked(callable $callback, array $args): string;
}
