<?php

namespace App\Http\Services;

use App\Enums\Status;
use App\Exports\TransactionExport;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\Paginatable;
use App\Traits\UploadsFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Repositories\WalletRepository;
use App\Http\Resources\TransactionResource;
use App\Http\Repositories\TransactionRepository;
use App\Http\Services\NotificationService\UserNotificationService;
use App\Traits\HasRestrictions;

class TransactionService
{
    use Paginatable, UploadsFile, HasRestrictions;

    public function __construct(
        protected WalletRepository $walletRepository,
        protected TransactionRepository $transactionRepository,
        protected UserNotificationService $userNotificationService
    )
    {

    }

    public function getAll(array $data, int $userId = null)
    {
        $conditions = [];
        if ($userId) $conditions['user_id'] = $userId;
        if (isset($data['type']) && $data['type']) $conditions['type'] = $data['type'];
        $result = $this->transactionRepository->findAll(
            $data['current_page'] ?? 1,
            $data['per_page'] ?? 50,
            $data['query'] ?? '',
            $conditions,
            $data['sort']['column'] ?? 'created_at',
            $data['sort']['dir'] ?? 'desc',
        );
        return [
            'data' => TransactionResource::collection($result['data']),
            'meta' => $this->getMeta($data, $result['total'])
        ];
    }

    public function approveTransaction(Transaction $transaction)
    {
        if (Status::tryFrom($transaction->status) !== Status::PENDING) {
            abort(404, "Transaction already processed");
        }

        if ($transaction->type === 'deposit') {
            $this->walletRepository->creditUserWallet(
                $transaction->user->id,
                $transaction->amount,
                $transaction->currency
            );
            $this->userNotificationService->sendWalletCreditNotification($transaction);
        }

        $this->transactionRepository->update($transaction, ['status' => Status::APPROVED]);
    }

    public function declineTransaction(Transaction $transaction)
    {
        if (Status::tryFrom($transaction->status) !== Status::PENDING) {
            abort(400, "Transaction already processed");
        }
        if ($transaction->type === 'withdrawal') {
            $this->walletRepository->creditUserWallet(
                $transaction->user->id,
                $transaction->amount,
                $transaction->currency
            );
        }
        $this->transactionRepository->update($transaction, ['status' => Status::DECLINED]);
    }

    public function export(string $format): string
    {
        if ($this->isRestricted('data-export')) {
			abort(400, "You current plan doesn't support this feature");
		}

        $directory = 'exports/admin';
        $dir = $this->getExportDirectory($directory);
        $transactions = $this->transactionRepository->getAll('*');
        if ($format === 'pdf') {
            $file = time().'.pdf';
            Pdf::loadView('exports.transaction', ['transactions' => $transactions])->save("{$dir}/{$file}");
        } else if ($format === 'excel') {
            $file = time().'.xlsx';
            Excel::store(new TransactionExport($transactions), "public/{$directory}/{$file}");
        } else {
            $file = time().'.xml';
            $xml = $this->prepareXML("{$dir}/{$file}", "Transactions");
            foreach ($transactions as $transaction) {
                $xml->startElement('Transaction');
                $xml->writeElement('transaction_id', $transaction->id);
                $xml->writeElement('reference', $transaction->reference);
                $xml->writeElement('user', $transaction->user->name);
                $xml->writeElement('type', $transaction->type);
                $xml->writeElement('amount', $transaction->amount);
                $xml->writeElement('description', $transaction->description);
                $xml->writeElement('method', $transaction->method);
                $xml->writeElement('status', $transaction->status);
                $xml->writeElement('date_created', $transaction->created_at);
                $xml->endElement();
            }
            $this->closeXML($xml);
        }
        return asset("/storage/{$directory}/{$file}");
    }

    public function generateReference()
    {
        $ref = $this->getRefCode();
        while ($this->transactionRepository->getAll(
            '*',
            conditions: ['reference' => $ref]
            )->count() > 0
        ) {
            $ref = $this->getRefCode();
        }
        return $ref;
    }

    private function getRefCode()
    {
        $ref = "";
        for ($i = 0; $i<10; $i++) $ref .= mt_rand(0,9);
        return $ref;
    }
}
