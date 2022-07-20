<?php

declare(strict_types=1);

namespace Chiron\ResponseCreator\Traits;

use Chiron\ResponseCreator\ResponseCreator;
use UnexpectedValueException;

// TODO : créer directement une méthode ->html($content, $headers = [], $charset = 'utf-8') dans le ResponseCapableTrait ??? eventuellement permettre aussi de spécifier le statuscode !!!!
// TODO : eventuellement permettre de chainer les appels du responseCreator pour ajouter des headers ou ajouter un charset. ex : $this->responder->withHeaders($headers)->withCharset('utf-8')->withBody($content)->asHtml()  // ou respond() ou create() ou send() ou generate() à la place de ->asHtml()
// TODO : ou alors ajouter les headers/statuscode et autre charset une fois la réponse générée car ces méthodes existent sur la response : ex : $this->responder->html()->withStatusCode(200)->withHeaders() // et on fera une méthode custom pour injecter le charset !!!

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

    protected function responder(): ResponseCreator
    {
        if ($this->hasResponder()) {
            return $this->responder;
        }

        // TODO : faire un throw new MissingContainerException('Container is missing, use setContainer() method to set it.');
        // TODO : lever plutot une improperlyconfiguredexception !!!
        throw new UnexpectedValueException(sprintf('Response Creator implementation not set in "%s".', static::class));
    }
}
