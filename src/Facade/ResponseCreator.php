<?php

declare(strict_types=1);

namespace Chiron\ResponseCreator\Facade;

use Chiron\Core\Facade\AbstractFacade;

final class ResponseCreator extends AbstractFacade
{
    protected static function getFacadeAccessor(): string
    {
        // phpcs:ignore SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName
        return \Chiron\ResponseCreator\ResponseCreator::class;
    }
}
