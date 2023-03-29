<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use JsonException;
use Mangano22\CommentsClient\CommentClient;
use Mangano22\CommentsClient\Model\Comment;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * @covers \Mangano22\CommentsClient\CommentClient
 * @covers \Mangano22\CommentsClient\Factory\CommentFactory
 */
class ClientTest extends TestCase
{
    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function testGetComments(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(__DIR__.'/data/listCommentsData.json')),
        ]);
        $client = new CommentClient(new Client(['handler' => $mock]));
        $result = $client->getComments();

        self::assertCount(3, $result);
        self::assertInstanceOf(Comment::class, $result[0]);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function testAddComment(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(__DIR__.'/data/commentData.json')),
        ]);
        $client = new CommentClient(new Client(['handler' => $mock]));

        $comment = new Comment('John', 'test text');
        $result  = $client->addComment($comment);
        $this->assertResultCheck($result);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function testUpdateComment(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(__DIR__.'/data/commentData.json')),
        ]);
        $client = new CommentClient(new Client(['handler' => $mock]));

        $comment = (new Comment('John', 'test text'))
            ->setId(1);
        $result  = $client->updateComment($comment);
        $this->assertResultCheck($result);
    }

    private function assertResultCheck($result): void
    {
        self::assertInstanceOf(Comment::class, $result);
        self::assertIsInt($result->getId());
        self::assertIsString($result->getName());
        self::assertIsString($result->getText());
    }
}
