<?php

declare(strict_types=1);

namespace Chiron\ResponseCreator\Facade;

use Chiron\Core\Facade\AbstractFacade;

//Attention si on conserve la facade il faut modifier le fichier composer.json pour inclure le package chiron/core sinon on n'aura pas accés à la classe générique AbstractFacade qui est dans le package core !!!!

final class ResponseCreator extends AbstractFacade
{
    protected static function getFacadeAccessor(): string
    {
        // phpcs:ignore SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName
        return \Chiron\ResponseCreator\ResponseCreator::class;
    }
}
