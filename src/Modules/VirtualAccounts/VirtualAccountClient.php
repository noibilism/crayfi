<?php

namespace Cray\Laravel\Modules\VirtualAccounts;

use Cray\Laravel\Http\CrayClient;

class VirtualAccountClient
{
    protected CrayClient $client;

    public function __construct(CrayClient $client)
    {
        $this->client = $client;
    }

    /**
     * Create / approve a virtual account
     *
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        return $this->client->post('/api/virtual-accounts/create', $data);
    }

    /**
     * Initiate a virtual account request (pre-create)
     *
     * @param array $data
     * @return array
     */
    public function initiate(array $data): array
    {
        return $this->client->post('/api/virtual-accounts/initiate', $data);
    }

    /**
     * List merchant virtual accounts
     *
     * @return array
     */
    public function list(): array
    {
        return $this->client->get('/api/virtual-accounts/list');
    }

    /**
     * Get available virtual account providers
     *
     * @return array
     */
    public function providers(): array
    {
        return $this->client->get('/api/virtual-accounts/providers');
    }

    /**
     * Submit Wema OTP to complete the two-step Wema flow
     *
     * @param array $data ['merchant_id', 'otp', 'customer_email']
     * @return array
     */
    public function submitOtp(array $data): array
    {
        return $this->client->post('/api/virtual-accounts/submit-otp', $data);
    }
}
