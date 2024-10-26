<?php

namespace Tests\Unit;

use App\Models\Message;
use App\Services\MessageService;
use App\Validations\MessageValidation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_message(): void
    {
        $messageData = [
            'content' => 'Test message content'
        ];

        $message = MessageService::storeMessage($messageData);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('Test message content', $message->content);
        $this->assertDatabaseHas('messages', ['content' => 'Test message content']);
    }
}