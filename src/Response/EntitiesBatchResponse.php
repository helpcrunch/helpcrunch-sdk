<?php

namespace Helpcrunch\Response;

use Helpcrunch\Entity\HelpcrunchEntity;

class EntitiesBatchResponse extends SuccessResponse
{
    /**
     * @param HelpcrunchEntity[] $entities
     * @param string|null $message
     * @param int|null $status
     */
    public function __construct(array $entities, $message = null, int $status = self::HTTP_OK)
    {
        $serializedEntities = [];
        foreach ($entities as $entity) {
            $serializedEntities[] = $entity->jsonSerialize();
        }

        parent::__construct(['data' => $serializedEntities], $message, $status);
    }
}
