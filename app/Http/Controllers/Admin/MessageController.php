<?php

namespace App\Http\Controllers\Admin;

use App\Http\Repositories\MessageRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListMessageRequest;
use App\Http\Requests\MessageRequest;
use App\Http\Services\MessageService;
use App\Traits\RespondWithHttpStatus;
use App\Models\Message;

class MessageController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected MessageService $messageService,
        protected MessageRepository $messageRepository
    ) {
    }

    public function index(ListMessageRequest $request)
    {
        $result = $this->messageService->getAll($request->all());
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function store(MessageRequest $request)
    {
        $message = $this->messageService->store($request->all());
        return $this->success("Notification queued", $message);
    }

    public function resend(Message $message)
    {
        $message = $this->messageService->resend($message);
        return $this->success("Notification resent", $message);
    }

    public function delete(Message $message)
    {
        $this->messageRepository->destroy($message);
        return $this->success("Notification deleted");
    }
}
