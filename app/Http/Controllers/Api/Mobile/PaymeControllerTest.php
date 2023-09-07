<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Enums\PaymeState;
use App\Exceptions\PaymeException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymeTransaction;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymeControllerTest extends Controller
{
    protected int $minAmount = 1_000;
    protected int $maxAmount = 100_000_000_00;

    protected int $timeout = 6000 * 1000;

    protected string $identity = 'order_id';

    public function payme(Request $req)
    {
        if ($req->method == "CheckPerformTransaction") {
            if (empty($req->params['account'])) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -32504,
                        'message' => "Недостаточно привилегий для выполнения метода"
                    ]
                ];
                return json_encode($response);
            } else {
                $a = $req->params['account'];
                $t = Order::where('id', $a['order_id'])->first();
                \Log::info($t->total);
                if (empty($t)) {
                    $response = [
                        'id' => $req->id,
                        'error' => [
                            'code' => -31050,
                            'message' => [
                                "uz" => "Buyurtma topilmadi",
                                "ru" => "Заказ не найден",
                                "en" => "Order not found"
                            ]
                        ]
                    ];
                    return json_encode($response);
                } elseif ("{$t->total}" != "{$req->params['amount']}") {

                    $response = [
                        'id' => $req->id,
                        'error' => [
                            'code' => -31001,
                            'message' => [
                                "uz" => "Notogri summa",
                                "ru" => "Неверная сумма",
                                "en" => "Incorrect amount"
                            ]
                        ]
                    ];
                    return json_encode($response);
                }
            }
            $t = Order::where('id', $a['order_id'])->where('total', $req->params['amount'])->first();
            $response = [

                'result' => [
                    'allow' => true,
                    'detail' => [
                        "receipt_type" => 0
                    ],
                    'items' => [
                        [
                            'title' => 'Test',
                            'price' => $t->total,
                            'count' => 1,
                            'code' => "10899001001000000",
                            "vat_percent" => 0,
                            "package_code" => "190309"
                        ]
                    ]
                ]


            ];
            return json_encode($response);
        } else if ($req->method == "CreateTransaction") {
            // $new = date('Y-m-d H:i:s', $req->params['time']);
            $new = now();

            if (empty($req->params['account'])) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -32504,
                        'message' => "Недостаточно привилегий для выполнения метода"
                    ]
                ];
                return json_encode($response);
            } else {
                $a = $req->params['account'];
                $order = Order::where('id', $a['order_id'])->first();
                $account = $a;
                $ts = Transaction::getTransactionsByOrderIdAndState($account['order_id']);
                if (empty($order)) {
                    $response = [
                        'id' => $req->id,
                        'error' => [
                            'code' => -31050,
                            'message' => [
                                "uz" => "Buyurtma topilmadi",
                                "ru" => "Заказ не найден",
                                "en" => "Order not found"
                            ]
                        ]
                    ];
                    return json_encode($response);
                } elseif ("{$order->total}" != "{$req->params['amount']}") {
                    $response = [
                        'id' => $req->id,
                        'error' => [
                            'code' => -31001,
                            'message' => [
                                "uz" => "Notogri summa",
                                "ru" => "Неверная сумма",
                                "en" => "Incorrect amount"
                            ]
                        ]
                    ];
                    return json_encode($response);
                } elseif (count($ts) == 0) {
                    $newTransaction = new Transaction();
                    $newTransaction->paycom_transaction_id = $req->params['id'];
                    $newTransaction->paycom_time = $req->params['time'];
                    $newTransaction->paycom_time_datetime = $new;
                    $newTransaction->amount = $req->params['amount'];
                    $newTransaction->state = 1;
                    $newTransaction->order_id = $account['order_id'];
                    $newTransaction->save();

                    $response = response()->json([
                        "result" => [
                            'create_time' => $req->params['time'],
                            'transaction' => strval($newTransaction->id),
                            'state' => $newTransaction->state
                        ]
                    ]);
                    return $response;
                } elseif ((count($ts) == 1) and ($ts[0]->paycom_time == $req->params['time']) and ($ts[0]->paycom_transaction_id == $req->params['id'])) {
                    $response = [
                        'result' => [
                            "create_time" => $req->params['time'],
                            "transaction" => "{$ts[0]->id}",
                            "state" => $ts[0]->state
                        ]
                    ];

                    return json_encode($response);
                } else {
                    $response = [
                        'id' => $req->id,
                        'error' => [
                            'code' => -31099,
                            'message' => [
                                "uz" => "Buyurtma tolovi hozirda amalga oshrilmoqda",
                                "ru" => "Оплата заказа в данный момент обрабатывается",
                                "en" => "Order payment is currently being processed"
                            ]
                        ]
                    ];
                    return json_encode($response);
                }
            }
        } else if ($req->method == "CheckTransaction") {
            $ldate = date('Y-m-d H:i:s');
            $transaction = Transaction::where('paycom_transaction_id', $req->params['id'])->first();

            if (empty($transaction)) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -32504,
                        'message' => "Недостаточно привилегий для выполнения метода"
                    ]
                ];
                return json_encode($response);
            } else if ($transaction->state == 2) {
                return response()->json([
                    "result" => [
                        'create_time' => intval($transaction->paycom_time),
                        'perform_time' => intval($transaction->perform_time_unix),
                        'cancel_time' => 0,
                        'transaction' => strval($transaction->id),
                        'state' => $transaction->state,
                        'reason' => null
                        // 'perform_time' => 0,
                    ]
                ]);
            } else {
                $response =  response()->json([
                    "result" => [
                        'create_time' => intval($transaction->paycom_time),
                        'perform_time' => 0,
                        'cancel_time' => 0,
                        'transaction' => strval($transaction->id),
                        'state' => $transaction->state,
                        'reason' => null
                    ]
                ]);
                return $response;
            }
        } elseif ($req->method == "PerformTransaction") {
            $ldate = date('Y-m-d H:i:s');
            $t = Transaction::where('paycom_transaction_id', $req->params['id'])->first();

            if (empty($t)) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -32504,
                        'message' => "Недостаточно привилегий для выполнения метода"
                    ]
                ];
                return json_encode($response);
            } elseif ($t->state == 1) {
                DB::table('transactions')
                    ->where('paycom_transaction_id', $req->params['id'])
                    ->update([
                        'state' => 2,
                        'perform_time' => $ldate,
                        'perform_time_unix' => intval(microtime(true) * 1000)
                    ]);
                $transaction = DB::table('transactions')
                    ->where('paycom_transaction_id', $req->params['id'])
                    ->first();
                // $user = DB::table('users')
                //     ->where('phone', $transaction->phone)
                //     ->first();
                // $price = $user->money + ($transaction->amount / 100);
                // DB::table('users')
                //     ->where('id', $user->id)
                //     ->update([
                //         'money' => $price
                //     ]);
                // DB::table('orders')
                //     ->where('user_id', $user->id)
                //     ->delete();
                $response = [
                    'result' => [
                        'transaction' => "{$transaction->id}",
                        'perform_time' => intval($transaction->perform_time_unix),
                        'state' => $transaction->state
                    ]
                ];
            } elseif ($t->state == 2) {
                $response = [
                    'result' => [
                        'transaction' => "{$t->id}",
                        'perform_time' => intval($t->perform_time_unix),
                        'state' => $t->state
                    ]
                ];
            }
            return json_encode($response);
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
            if (($transaction->state == PaymeState::Pending || $transaction->state == PaymeState::Done) && $transaction->transaction !== $request->params['id']) {
                throw new PaymeException(PaymeException::USER_NOT_FOUND);
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

            return $this->successCreateTransaction(
                $transaction->create_time,
                $transaction->id,
                $transaction->state
            );
        }
        $transaction = PaymeTransaction::where('transaction', $id)->first();
        if ($transaction) {
            if ($transaction->state != PaymeState::Pending) {
                return response()->json([
                    'error' => [
                        'code' => -31008,
                        'message' => 'Error'
                    ]
                ]);
            }
        } elseif ($transaction->isExpired()) {
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
