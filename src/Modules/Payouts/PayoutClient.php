<?php

namespace Cray\Laravel\Modules\Payouts;

use Cray\Laravel\Http\CrayClient;

class PayoutClient
{
    protected CrayClient $client;

    public function __construct(CrayClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get Payment Methods
     * 
     * @param string $countryCode
     * @return array
     */
    public function paymentMethods(string $countryCode): array
    {
        return $this->client->get("/api/payout/payment-methods/{$countryCode}");
    }

    /**
     * Get Banks
     *
     * @param string|null $countryCode
     * @return array
     */
    public function banks(?string $countryCode = null): array
    {
        $query = [];
        if ($countryCode) {
            $query['countryCode'] = $countryCode;
        }
        return $this->client->get('/api/payout/banks', $query);
    }

    /**
     * Account Name Lookup
     *
     * @param array $data ['account_number', 'bank_code', 'country_code']
     * @return array
     */
    public function validateAccount(array $data): array
    {
        return $this->client->post('/api/payout/accounts/validate', $data);
    }

    /**
     * Process Transfer (Disburse)
     *
     * @param array $data
     * @return array
     */
    public function disburse(array $data): array
    {
        return $this->client->post('/api/payout/disburse', $data);
    }

    /**
     * Verify Transaction
     *
     * @param string $transactionId
     * @return array
     */
    public function requery(string $transactionId): array
    {
        return $this->client->get("/api/payout/requery/{$transactionId}");
    }
}
