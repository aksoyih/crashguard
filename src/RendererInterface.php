<?php

declare(strict_types=1);

namespace Aksoyih\Crashguard;

/**
 * Interface for error renderers
 */
interface RendererInterface
{
    /**
     * Render error data
     */
    public function render(array $errorData): string;
}