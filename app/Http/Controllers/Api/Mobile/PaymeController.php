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

class PaymeController extends Controller
{


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
                // \Log::info($t->total);
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
                    // 'detail' => [
                    //     "receipt_type" => 0
                    // ],
                    // 'items' => [
                    //     [
                    //         'title' => 'Test',
                    //         'price' => $t->total,
                    //         'count' => 1,
                    //         'code' => "10899001001000000",
                    //         "vat_percent" => 0,
                    //         "package_code" => "190309"
                    //     ]
                    // ]
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
                $order_id = $req->params['account']['order_id'];
                $ts = Transaction::where('order_id', $order_id)->where('state', 1)->get();;
                // \Log::info(['Transaction: ',$ts, 'Request: ', $req->params['account'] ]);
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
                    // $paycomTimeInSeconds = floor($req->params['time'] / 1000);
                    // $paycomTimeMilliseconds = $req->params['time'] % 1000 / 1000; // Extract fractions of a second

                    // Insert the transaction into the database
                    // $transaction = DB::table('transactions')
                    //     ->insert([
                    //         'paycom_transaction_id' => $req->params['id'],
                    //         'paycom_time' => str_replace('.', '', $req->params['time']), // Store as UNIX timestamp with fractions
                    //         'paycom_time_datetime' => $new,
                    //         'amount' => $req->params['amount'],
                    //         'state' => 1,
                    //         'order_id' => "{$account['order_id']}"
                    //     ]);
                    $transaction = new Transaction();
                    $transaction->paycom_transaction_id = $req->params['id'];
                    $transaction->paycom_time = str_replace('.', '', $req->params['time']); // Convert and round to milliseconds
                    $transaction->paycom_time_datetime = $new;
                    $transaction->amount = $req->params['amount'];
                    $transaction->state = 1;
                    $transaction->order_id = $account['order_id'];
                    \Log::info($transaction);
                    
                    return response()->json([
                        "result" => [
                            'create_time' => $req->params['time'],
                            'transaction' => strval($transaction->id),
                            'state' => intval($transaction->state)
                        ]
                    ]);
                } elseif ((count($ts) == 1) and ($ts[0]->paycom_time == $req->params['time']) and ($ts[0]->paycom_transaction_id == $req->params['id'])) {
                    $response = [
                        'result' => [
                            "create_time" => $req->params['time'],
                            "transaction" => "{$ts[0]->id}",
                            "state" => intval($ts[0]->state)
                        ]
                    ];

                    return json_encode($response);
                } else {
                    // \Log::info($ts);
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
            } else if ($transaction->state == -1) {
                return response()->json([
                    "result" => [
                        'create_time' => intval($transaction->paycom_time),
                        'perform_time' => intval($transaction->perform_time_unix),
                        'cancel_time' => intval($transaction->cancel_time),
                        'transaction' => strval($transaction->id),
                        'state' => intval($transaction->state),
                        'reason' => intval($transaction->reason)
                        // 'perform_time' => 0,
                    ]
                ]);
            } else if ($transaction->state == 2) {
                return response()->json([
                    "result" => [
                        'create_time' => intval($transaction->paycom_time),
                        'perform_time' => intval($transaction->perform_time_unix),
                        'cancel_time' => 0,
                        'transaction' => strval($transaction->id),
                        'state' => intval($transaction->state),
                        'reason' => intval($transaction->reason)
                        // 'perform_time' => 0,
                    ]
                ]);
            }
            //  else if ($transaction->state == -2) {

            //     $transaction->update(['state' => 2]);

            //     return response()->json([
            //         'result' => [
            //             'code' => -31003,
            //             'message' => "Транзакция не найдена."
            //         ]
            //     ]);
            // }
            else {
                $response =  response()->json([
                    "result" => [
                        'create_time' => intval($transaction->paycom_time),
                        'perform_time' => intval($transaction->perform_time_unix),
                        'cancel_time' => 0,
                        'transaction' => strval($transaction->id),
                        'state' => intval($transaction->state),
                        'reason' => null
                    ]
                ]);
                return $response;
            }
        } elseif ($req->method == "PerformTransaction") {
            $ldate = date('Y-m-d H:i:s');
            $t = Transaction::where('paycom_transaction_id', $req->params['id'])->first();
            // \Log::info($t->state);
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
                $currentMillis = intval(microtime(true) * 1000);
                DB::table('transactions')
                    ->where('paycom_transaction_id', $req->params['id'])
                    ->update([
                        'state' => 2,
                        'perform_time' => $ldate,
                        'perform_time_unix' => str_replace('.', '', $currentMillis)
                    ]);
                $transaction = DB::table('transactions')
                    ->where('paycom_transaction_id', $req->params['id'])
                    ->first();
                Order::where('id', $t->order_id)->update(['status' => 'yakunlandi']);
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
                        'state' => intval($transaction->state)
                    ]
                ];
                return json_encode($response);
            } elseif ($t->state == 2) {


                $response = [
                    'result' => [
                        'transaction' => "{$t->id}",
                        'perform_time' => intval($t->perform_time_unix),
                        'state' => intval($t->state)
                    ]
                ];
                return json_encode($response);
            }
        } elseif ($req->method == "CancelTransaction") {
            $ldate = date('Y-m-d H:i:s');
            $t = DB::table('transactions')
                ->where('paycom_transaction_id', $req->params['id'])
                ->first();
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
                $currentMillis = intval(microtime(true) * 1000);
                DB::table('transactions')
                    ->where('paycom_transaction_id', $req->params['id'])
                    ->update([
                        'reason' => $req->params['reason'],
                        'cancel_time' => str_replace('.', '', $currentMillis),
                        'state' => -1
                    ]);
                $t = DB::table('transactions')
                    ->where('paycom_transaction_id', $req->params['id'])
                    ->first();
                DB::table('orders')
                    ->where('id', $t->order_id)
                    ->update(['status' => 'bekor qilindi']);
                $response = [
                    'result' => [
                        "state" => intval($t->state),
                        "cancel_time" => intval($t->cancel_time),
                        "transaction" => "{$t->id}"
                    ]
                ];
            } elseif (($t->state == -1) or ($t->state == -2)) {
                $response = [
                    'result' => [
                        "state" => intval($t->state),
                        "cancel_time" => intval($t->cancel_time),
                        "transaction" => "{$t->id}"
                    ]
                ];
            } else {
                DB::table('transactions')
                    ->where('paycom_transaction_id', $req->params['id'])
                    ->update([
                        'reason' => $req->params['reason'],
                        'cancel_time' => intval(microtime(true) * 1000),
                        'state' => -2
                    ]);
                $t = DB::table('transactions')
                    ->where('paycom_transaction_id', $req->params['id'])
                    ->first();
                DB::table('orders')
                    ->where('id', $t->order_id)
                    ->update(['status' => 'bajarildi']);
                $response = [
                    'result' => [
                        "state" => intval($t->state),
                        "cancel_time" => intval($t->cancel_time),
                        "transaction" => "{$t->id}"
                    ]
                ];
            }
            return json_encode($response);
        } elseif ($req->method == "GetStatement") {
            $response = [
                'id' => $req->id,
                'error' => [
                    'code' => -32504,
                    'message' => "Недостаточно привилегий для выполнения метода"
                ]
            ];
            return json_encode($response);
        } elseif ($req->method == "ChangePassword") {
            $response = [
                'id' => $req->id,
                'error' => [
                    'code' => -32504,
                    'message' => "Недостаточно привилегий для выполнения метода"
                ]
            ];
            return json_encode($response);
        }
    }


    protected function microtime(): int
    {
        return (time() * 1000);
    }
}
