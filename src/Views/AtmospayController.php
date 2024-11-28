<?php


namespace JscorpTech\Atmospay\Views;

use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Response;
use JscorpTech\Atmospay\Exceptions\AtmospayException;
use JscorpTech\Atmospay\Models\Transaction;
use JscorpTech\Atmospay\Services\AtmospayService;

class AtmospayController
{
    public $service;

    function __construct()
    {
        $this->service  = new AtmospayService(Env::get("ATMOSPAY_LOGIN"), Env::get("ATMOSPAY_PASSWORD"), [
            "store_id" => Env::get("ATMOSPAY_STORE_ID", 0),
            "lang" => "uz"
        ]);
    }
    /**
     * Transaction yaratish
     */
    public function create(Request $request)
    {
        try {
            $amount = 5000000;
            $account = 1;
            $transaction_id = $this->service->create_transaction($amount, $account)->transaction_id;
            $transaction = Transaction::query()->create([
                "amount" => $amount,
                "transaction_id" => $transaction_id
            ]);

            return Response::json([
                "detail" => _("Transaction created"),
                "data" => [
                    "transaction_id" => $transaction->id
                ]
            ]);
        } catch (AtmospayException $e) {
            return Response::json([
                "detail" => $e->getMessage()
            ], 403);
        }
    }

    public function pre_apply(Request $request)
    {
        $data = $request->validate([
            "transaction_id" => ['required', "exists:atmospay_transactions,id"],
            "card" => ['required'],
            "expiry" => ['required']
        ]);
        try {
            $transaction = Transaction::query()->where(['id' => $data['transaction_id']])->latest()->first();
            $this->service->pre_apply_transaction($data['card'], $data['expiry'], $transaction->transaction_id);
            return Response::json([
                "detail" => _("Tasdiqlash ko'di yuborildi")
            ]);
        } catch (AtmospayException $e) {
            return Response::json([
                "detail" => $e->getMessage()
            ], 403);
        }
    }

    public function apply(Request $request)
    {
        $data = $request->validate([
            "code" => ["required"],
            "transaction_id" => ["required", "integer", 'exists:atmospay_transactions,id']
        ]);
        try {
            $transaction = Transaction::query()->where(['id' => $data['transaction_id']])->latest()->first();
            $response = $this->service->apply_transaction($data['code'], $transaction->transaction_id);
            return Response::json($response);
        } catch (AtmospayException $e) {
            return Response::json([
                "detail" => $e->getMessage()
            ], 403);
        }
    }
}
