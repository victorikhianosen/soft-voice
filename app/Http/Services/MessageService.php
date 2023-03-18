<?php

namespace App\Http\Services;

use App\Models\Message;
use Illuminate\Support\Arr;
use App\Http\Traits\Paginatable;
use App\Http\Resources\MessageResource;
use App\Http\Repositories\MessageRepository;

class MessageService
{
    use Paginatable;

    public function __construct(
        protected MessageRepository $messageRepository
    ) {
    }

    public function getAll(array $data)
    {
        $conditions = [];
        $result = $this->messageRepository->findAll(
            $data['current_page'] ?? 1,
            $data['per_page'] ?? 50,
            $data['query'] ?? '',
            $conditions,
            $data['sort']['column'] ?? 'created_at',
            $data['sort']['dir'] ?? 'desc',
        );
        return [
            'data' => MessageResource::collection($result['data']),
            'meta' => $this->getMeta($data, $result['total'])
        ];
    }

    public function store(array $data): MessageResource
    {
        $data['type'] = gettype($data['recipients']) === 'array' ? 'single' : 'bulk';
        if (gettype($data['recipients']) === 'array') {
            $data['type'] = 'single';
            $data['to'] = json_encode($data['recipients']);
        } else {
            $data['type'] = 'bulk';
            $data['to'] = $data['recipients'];
        }
        $data['notification'] = $data['inapp_notification'];
        $message = $this->messageRepository->create($data);
        return new MessageResource($message->fresh());
    }

    public function resend(Message $message)
    {
        $data = $message->toArray();
        Arr::forget($data, ['id', 'status']);
        $message = $this->messageRepository->create($data);
        return new MessageResource($message->fresh());
    }
}
