<?php


namespace App\Classes\Vandar;


use App\Exceptions\VandarException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Vandar
{
    protected string $base_url;
    protected string $api_key;
    protected string $callback_url;

    public function __construct()
    {
        $this->base_url = config('services.vandar.base_url');
        $this->api_key = config('services.vandar.api_key');
        $this->callback_url = config('services.vandar.callback_url');
        $this->business = config('services.vandar.business');
    }

    public static function use(): Vandar
    {
        return new static();
    }

    private function token()
    {
        $token = Cache::get('vandar_access_token');
        if ($token) {
            return $token;
        } else {
            $refresh_token = Cache::get('vandar_refresh_token');
            return $this->refreshToken($refresh_token);
        }
    }

    /**
     * @throws VandarException
     */
    public function refreshToken($refreshToken)
    {
        $data = [
            'refresh_token' => $refreshToken,
        ];
        $response = $this->post("https://api.vandar.io/v3/refreshtoken", $data);
        Cache::put('vandar_access_token', $response['vandar_access_token'], now()->addDay(5));
        Cache::put('vandar_refresh_token', $response['refresh_token']);
        return $response['access_token'];
    }

    /**
     * @throws VandarException
     */
    public function receiveToken($amount, $callbackUrl)
    {
        $data = [
            'api_key' => $this->api_key,
            'amount' => $amount,
            'callback_url' => $callbackUrl,
        ];
        $response = $this->post($this->base_url . "/api/v3/send", $data);
        return $this->base_url . '/v3/' . $response['token'];
    }

    /**
     * @param $token
     * @return array|mixed
     * @throws VandarException
     */
    public function verifyTransaction($token)
    {
        $data = [
            'api_key' => $this->api_key,
            'token' => $token,

        ];
        $response = $this->post($this->base_url . 'api/v3/verify', $data);
        return $response->json();
    }

    /**
     * @param $amount
     * @param $iban
     * @return array|mixed
     * @throws VandarException
     */
    public function settlement($amount, $iban)
    {
        $data = [
            'amount' => $amount,
            'iban' => $iban,
            'track_id' => Str::uuid(),
            'notify_url' => ''
        ];
        $response = $this->post('https://api.vandar.io/v3/business/' . $this->business . '/settlement/store', $data);
        return $response->json();
    }


    /**
     * @param array $data
     * @param string $batch_id
     * @return array|mixed
     * @throws VandarException
     */
    public function groupClearing($data, $batch_id)
    {
        $list = [
            "batch_id" => $batch_id,
            "batches_settlement" => $data
        ];
        $response = $this->post("https://batch.vandar.io/api/v1/business/" . $this->business . "/batches-settlement", $list);

        return $response->json();
    }

    /**
     * @param $batchId
     * @return array|mixed
     */
    public function groupClearingDetails($batchId)
    {
        $response = $this->post("https://batch.vandar.io/api/v1/business/" . $this->business . "/batch-settlements/" . $batchId . "?per_page=20&page=1&status=SUBMITTED");
        return $response->json();
    }


    /**
     * @throws VandarException
     */
    protected function post(string $url, $data = null): Response
    {
        $response = Http::withToken($this->token())->post($url, $data);
        $this->handle($response);
        return $response->json();
    }

    /**
     * @throws VandarException
     */
    protected function handle($response)
    {
        HandleVandarException::handle($response);
    }
}
