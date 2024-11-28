<?php


namespace JscorpTech\Atmospay\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Env;
use JscorpTech\Atmospay\Exceptions\AtmospayException;

class AtmospayService
{
    public string $domain = "https://partner.atmos.uz";
    public Client $client;
    public string|null $token = null;
    public string $login;
    public int $store_id;
    public string $lang = "uz";
    public string $password;
    public array $config;

    public array $urls = [
        "auth" => "/token",
        "create_transaction" => "/merchant/pay/create",
        "apply_transaction" => "/merchant/pay/apply-ofd",
        "pre_apply_transaction" => "/merchant/pay/pre-apply"
    ];
    function __construct($config = [])
    {
        $this->config = $config;
        $this->login = Env::get("ATMOSPAY_LOGIN");
        $this->password = Env::get("ATMOSPAY_PASSWORD");
        $this->store_id = Env::get("ATMOSPAY_STORE_ID", 0);
        $this->load_config();
        $this->client = $this->get_client();
        $this->auth();
    }

    public function load_config()
    {
        if (isset($this->config['store_id'])) {
            $this->store_id = $this->config['store_id'];
        }
        if (isset($this->config['lang'])) {
            $this->lang = $this->config['lang'];
        }
    }

    public function get_client()
    {
        return new Client([
            "base_uri" => $this->domain,
            "headers" => [
                "Authorization" => "Bearer " . $this->token,
                "content-type" => "application/json",

            ]
        ]);
    }

    public function auth()
    {
        $headers = [
            "content-type" => "application/x-www-form-urlencoded",
            "Authorization" => "Basic " . base64_encode($this->login . ":" . $this->password)
        ];
        $payload = [
            "grant_type" => "client_credentials"
        ];
        $response = json_decode($this->client->request("POST", $this->urls['auth'], [
            "form_params" => $payload,
            "headers" => $headers
        ])->getBody()->getContents());
        $this->token = $response->access_token;
        $this->client = $this->get_client();
        return $this->token;
    }
    public function create_transaction(int $amount, int $account)
    {
        $payload = [
            "amount" => $amount,
            "account" => $account,
            "store_id" => $this->store_id,
            "lang" => $this->lang
        ];
        $response = json_decode($this->client->request("POST", $this->urls['create_transaction'], [
            "json" => $payload
        ])->getBody()->getContents());
        if ($response->result->code != "OK") {
            throw new AtmospayException($response->result->description);
        }
        return $response;
    }

    public function pre_apply_transaction($card, $expiry, $transaction_id)
    {
        $payload = [
            "card_number" => $card,
            "expiry" => $expiry,
            "store_id" => $this->store_id,
            "transaction_id" => $transaction_id
        ];
        $response = json_decode($this->client->request("POST", $this->urls['pre_apply_transaction'], [
            "json" => $payload
        ])->getBody()->getContents());
        if ($response->result->code != "OK") {
            throw new AtmospayException($response->result->description);
        }
        return $response;
    }

    public function apply_transaction($code, $transaction)
    {
        $payload = [
            "otp" => $code,
            "transaction_id" => $transaction,
            "store_id" => $this->store_id
        ];
        $response = json_decode($this->client->request("POST", $this->urls['apply_transaction'], [
            "json" => $payload
        ])->getBody()->getContents());
        if ($response->result->code != "OK") {
            throw new AtmospayException($response->result->description);
        }
        return $response;
    }
}
