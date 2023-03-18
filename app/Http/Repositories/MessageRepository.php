<?php

namespace App\Http\Repositories;

use App\Models\Message;

class MessageRepository extends AbstractRepository
{
    public function __construct(Message $message)
    {
        parent::__construct($message);
    }

    public function model()
    {
        return app(Message::class);
    }
}
