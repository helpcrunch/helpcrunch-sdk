<?php

namespace Helpcrunch\Exception;

use Helpcrunch\Response\InnerErrorCodes;
use Symfony\Component\HttpFoundation\JsonResponse;

class EntityNotFoundException extends HelpcrunchException
{
    const MESSAGE = 'Entity not found ';

    /**
     * @var string
     */
    private $entityName = '';

    public function __construct(string $entityName = '')
    {
        parent::__construct(
            self::MESSAGE . $entityName,
            JsonResponse::HTTP_NOT_FOUND,
            InnerErrorCodes::ENTITY_NOT_FOUND
        );

        $this->entityName = $entityName;
    }

    public function getData(): string
    {
        return self::MESSAGE . $this->entityName;
    }
}
