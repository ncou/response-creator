<?php

declare(strict_types=1);

namespace Chiron\ResponseCreator\Traits;

use Chiron\ResponseCreator\ResponseCreator;

interface ResponseCapableInterface
{
    public function setResponder(ResponseCreator $responder): self;

    /**
     * Indicates if the responder is defined.
     *
     * @return bool
     */
    public function hasResponder(): bool;
}
