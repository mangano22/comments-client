<?php

namespace Mangano22\CommentsClient\Factory;

use JsonException;
use Mangano22\CommentsClient\Model\Comment;
use RuntimeException;

class CommentFactory
{
    private const REQUIRED_FIELDS = ['id', 'name', 'text'];

    /**
     * @throws JsonException
     */
    public static function extractComment(string $jsonData): Comment
    {
        $data = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);

        return self::createComment($data['result']);
    }

    /**
     * @return Comment[]
     *
     * @throws JsonException
     */
    public static function extractCommentsArray(string $jsonData): array
    {
        $result = [];
        $data   = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);
        foreach ($data['result'] as $item) {
            $result[] = self::createComment($item);
        }

        return $result;
    }

    private static function createComment(array $data): Comment
    {
        foreach (self::REQUIRED_FIELDS as $field) {
            if (!array_key_exists($field, $data)) {
                throw new RuntimeException('Comment has no field: ' . $field);
            }
        }

        return (new Comment($data['name'], $data['text']))
            ->setId($data['id']);
    }
}
