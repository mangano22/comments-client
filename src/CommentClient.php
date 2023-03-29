<?php

namespace Mangano22\CommentsClient;

use GuzzleHttp\Psr7\Request;
use JsonException;
use Mangano22\CommentsClient\Factory\CommentFactory;
use Mangano22\CommentsClient\Model\Comment;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use RuntimeException;

class CommentClient
{
    private const BASE_URI = 'https://example.com';

    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client  = $client;
    }

    /**
     * @return Comment[]
     *
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function getComments(): array
    {
        $url      = self::BASE_URI . '/comments';
        $request  = new Request('GET', $url, ['Accept' => 'application/json']);
        $response = $this->client->sendRequest($request);
        $contents = $response->getBody()->getContents();

        return CommentFactory::extractCommentsArray($contents);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function addComment(Comment $comment): Comment
    {
        $url      = self::BASE_URI . '/comment';
        $request  = new Request('POST', $url, ['Accept' => 'application/json'], json_encode($comment, JSON_THROW_ON_ERROR));
        $response = $this->client->sendRequest($request);
        $contents = $response->getBody()->getContents();

        return CommentFactory::extractComment($contents);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function updateComment(Comment $comment): Comment
    {
        if (!$id = $comment->getId()) {
            throw new RuntimeException('Comment has no field: id');
        }

        $url      = self::BASE_URI . '/comments/' . $id;
        $request  = new Request('PUT', $url, ['Accept' => 'application/json'], json_encode($comment, JSON_THROW_ON_ERROR));
        $response = $this->client->sendRequest($request);
        $contents = $response->getBody()->getContents();

        return CommentFactory::extractComment($contents);
    }
}
