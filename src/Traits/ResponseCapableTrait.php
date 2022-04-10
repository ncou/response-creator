<?php

declare(strict_types=1);

namespace Chiron\ResponseCreator\Traits;

use Chiron\ResponseCreator\ResponseCreator;
use UnexpectedValueException;

trait ResponseCapableTrait
{
    /** @var ?ResponseCreator */
    protected $responder;

    public function setResponder(ResponseCreator $responder): ResponseCapableInterface
    {
        $this->responder = $responder;
        // TODO : lever une exception si on n'a pas implémenté l'interface ResponseCapableInterface car le return $this sera en conflit avec le return typehint !!!
        //https://github.com/thephpleague/container/blob/4.x/src/ContainerAwareTrait.php

        return $this;
    }

    public function hasResponder(): bool
    {
        return $this->responder instanceof ResponseCreator;
    }

    protected function getResponder(): ResponseCreator
    {
        if ($this->hasResponder()) {
            return $this->responder;
        }

        // TODO : faire un throw new MissingContainerException('Container is missing, use setContainer() method to set it.');
        throw new UnexpectedValueException(sprintf('Response Creator implementation not set in "%s".', static::class));
    }
}
