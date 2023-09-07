<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Enums\PaymeState;
use App\Exceptions\PaymeException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymeTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymeController extends Controller
{
    protected int $minAmount = 1_000;
    protected int $maxAmount = 100_000_000_00;

    protected int $timeout = 6000 * 1000;

    protected string $identity = 'order_id';

    public function payme(Request $request)
    {
        $method = $request->method;

        switch ($method) {
            case 'CheckPerformTransaction':
                return $this->handleCheckPerformTransaction($request);

            case 'CreateTransaction':
                return $this->handleCreateTransaction($request);

            case 'CheckTransaction':
                return $this->handleCheckTransaction($request);

            default:
                return response()->json([
                    'error' => [
                        'code' => -32601,
                        'message' => [
                            "uz" => "Metod topilmadi.",
                            "ru" => "Метод не найден.",
                            "en" => "Method not found.",
                        ]
                    ]
                ]);
        }
    }

    private function handleCheckPerformTransaction(Request $request)
    {
        if (!$request->params['account'] || !$request->params['amount']) {
            return response()->json([
                'error' => [
                    'code' => -32600,
                    'message' => [
                        "uz" => "Notog`ri JSON-RPC obyekt yuborilgan.",
                        "ru" => "Передан неправильный JSON-RPC объект.",
                        "en" => "Handed the wrong JSON-RPC object."
                    ]
                ]
            ]);
        }

        $amount = $request->params['amount'];
        if (!$this->isValidAmount($amount)) {
            return response()->json([
                'error' => [
                    'code' => -31001,
                    'message' => [
                        "uz" => "Notug'ri summa.",
                        "ru" => "Неверная сумма.",
                        "en" => "Wrong amount.",
                    ]
                ]
            ]);
        }

        $order = Order::find($request->params['account']['order_id']);
        if (!$order) {
            return response()->json([
                'error' => [
                    'code' => -31050,
                    'message' => [
                        "uz" => "Order Topilmadi.",
                        "ru" => "Передан неправильный JSON-RPC объект.",
                        "en" => "Handed the wrong JSON-RPC object."
                    ]
                ]
            ]);
        }

        return $this->successCheckPerformTransaction();
    }

    private function handleCreateTransaction(Request $request)
    {
        if (!$request->params['id'] || !$request->params['time'] || !$request->params['account'] || !$request->params['amount']) {
            throw new PaymeException(PaymeException::USER_NOT_FOUND);
        }

        $id = $request->params['id'];
        $time = $request->params['time'];
        $amount = $request->params['amount'];
        $account = $request->params['account'];
        $order_account = $request->params['account']['order_id'];

        if (!array_key_exists($this->identity, $account)) {
            throw new PaymeException(PaymeException::USER_NOT_FOUND);
        }

        $order = Order::where('id', $order_account)->first();

        if (!$order) {
            return response()->json([
                'error' => [
                    'code' => -31050,
                    'message' => [
                        "uz" => "Order Topilmadi.",
                        "ru" => "Передан неправильный JSON-RPC объект.",
                        "en" => "Handed the wrong JSON-RPC object."
                    ]
                ]
            ]);
        }

        if (!$this->isValidAmount($amount)) {
            return response()->json([
                'error' => [
                    'code' => -31001,
                    'message' => [
                        "uz" => "Notug'ri summa.",
                        "ru" => "Неверная сумма.",
                        "en" => "Wrong amount.",
                    ]
                ]
            ]);
        }

        $transaction = PaymeTransaction::where('transaction', $id)->first();

        if ($transaction) {
            if ($transaction->state != PaymeState::Pending) {
                throw new PaymeException(PaymeException::CANT_PERFORM_TRANS);
            }
            if (!$this->checkTimeout($transaction->create_time)) {
                $transaction->update([
                    'state' => PaymeState::Cancelled,
                    'reason' => 4
                ]);

                throw new PaymeException(error: PaymeException::CANT_PERFORM_TRANS, customMessage: [
                    "uz" => "Vaqt tugashi o'tdi",
                    "ru" => "Тайм-аут прошел",
                    "en" => "Timeout passed"
                ]);
            }

            // if($transaction->transaction == $request->params['id']){
            //     return response()->json([
            //         'error' => [
            //             'code'
            //         ]
            //     ]);
            // }

            return $this->successCreateTransaction(
                $transaction->create_time,
                $transaction->id,
                $transaction->state
            );
        }

        $transaction = PaymeTransaction::create([
            'transaction' => $id,
            'payme_time' => $time,
            'amount' => $amount,
            'state' => PaymeState::Pending,
            'create_time' => $this->microtime(),
            'owner_id' => $order_account,
            'order_id' => $order_account,
        ]);

        return $this->successCreateTransaction(
            $transaction->create_time,
            $transaction->id,
            $transaction->state
        );
    }

    private function handleCheckTransaction(Request $request)
    {
        if (!$request->params['id']) {
            throw new PaymeException(PaymeException::JSON_RPC_ERROR);
        }

        $id = $request->params['id'];

        $transaction = PaymeTransaction::where('transaction', $id)->first();

        if ($transaction) {
            return $this->successCheckTransaction(
                $transaction->create_time,
                $transaction->perform_time,
                $transaction->cancel_time,
                $transaction->id,
                $transaction->state,
                $transaction->reason
            );
        } else {
            throw new PaymeException(PaymeException::TRANS_NOT_FOUND);
        }
    }


    protected function microtime(): int
    {
        return (time() * 1000);
    }

    private function checkTimeout($created_time): bool
    {
        return $this->microtime() <= ($created_time + $this->timeout);
    }

    public function isValidAmount($amount): bool
    {
        if ($amount < $this->minAmount || $amount > $this->maxAmount) {
            return false;
        }

        return true;
    }

    public function successCreateTransaction($createTime, $transaction, $state)
    {
        return $this->success([
            'create_time' => $createTime,
            'perform_time' => 0,
            'cancel_time' => 0,
            'transaction' => strval($transaction),
            'state' => $state,
            'reason' => null
        ]);
    }

    public function successCheckPerformTransaction()
    {
        return $this->success([
            "allow" => true
        ]);
    }

    public function successCheckTransaction($createTime, $performTime, $cancelTime, $transaction, $state, $reason)
    {
        return $this->success([
            "create_time" => $createTime ?? 0,
            "perform_time" => $performTime ?? 0,
            "cancel_time" => $cancelTime ?? 0,
            "transaction" => strval($transaction),
            "state" => $state,
            "reason" => $reason
        ]);
    }

    public function success($result): JsonResponse
    {
        return response()->json([
            'jsonrpc' => '2.0',
            'result' => $result,
        ]);
    }

    public function error($error): JsonResponse
    {
        return response()->json([
            'jsonrpc' => '2.0',
            'error' => $error,
        ]);
    }
}
