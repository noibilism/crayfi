<?php

namespace Cray\Laravel;

use Cray\Laravel\Http\CrayClient;
use Cray\Laravel\Modules\Cards\CardClient;
use Cray\Laravel\Modules\Momo\MomoClient;
use Cray\Laravel\Modules\Wallets\WalletClient;
use Cray\Laravel\Modules\FX\FxClient;
use Cray\Laravel\Modules\Payouts\PayoutClient;
use Cray\Laravel\Modules\Refunds\RefundClient;
use Cray\Laravel\Modules\VirtualAccounts\VirtualAccountClient;

class Cray
{
    protected CrayClient $client;

    public function __construct(CrayClient $client)
    {
        $this->client = $client;
    }

    public function cards(): CardClient
    {
        return new CardClient($this->client);
    }

    public function momo(): MomoClient
    {
        return new MomoClient($this->client);
    }

    public function wallets(): WalletClient
    {
        return new WalletClient($this->client);
    }

    public function fx(): FxClient
    {
        return new FxClient($this->client);
    }

    public function payouts(): PayoutClient
    {
        return new PayoutClient($this->client);
    }

    public function refunds(): RefundClient
    {
        return new RefundClient($this->client);
    }

    public function virtualAccounts(): VirtualAccountClient
    {
        return new VirtualAccountClient($this->client);
    }
}
