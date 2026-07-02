<?php

namespace Cray\Laravel\Tests\Feature;

use Cray\Laravel\Tests\TestCase;
use Cray\Laravel\Facades\Cray;
use Illuminate\Support\Facades\Http;

class ClientTest extends TestCase
{
    public function test_requests_use_correct_base_url_sandbox()
    {
        config(['cray.env' => 'sandbox']);
        config(['cray.base_url' => null]);
        config(['cray.api_key' => 'test_key']);

        Http::fake([
            'dev-gateman.v3.connectramp.com/*' => Http::response(['success' => true], 200),
        ]);

        Cray::wallets()->balances();

        Http::assertSent(function ($request) {
            return str_starts_with($request->url(), 'https://dev-gateman.v3.connectramp.com');
        });
    }

    public function test_requests_use_correct_base_url_live()
    {
        config(['cray.env' => 'live']);
        config(['cray.base_url' => null]);
        config(['cray.api_key' => 'test_key']);

        Http::fake([
            'pay.connectramp.com/*' => Http::response(['success' => true], 200),
        ]);

        Cray::wallets()->balances();

        Http::assertSent(function ($request) {
            return str_starts_with($request->url(), 'https://pay.connectramp.com');
        });
    }

    public function test_requests_use_custom_base_url()
    {
        config(['cray.env' => 'live']); // Should be ignored
        config(['cray.base_url' => 'https://custom-proxy.com']);
        config(['cray.api_key' => 'test_key']);

        Http::fake([
            'custom-proxy.com/*' => Http::response(['success' => true], 200),
        ]);

        Cray::wallets()->balances();

        Http::assertSent(function ($request) {
            return str_starts_with($request->url(), 'https://custom-proxy.com');
        });
    }

    public function test_api_key_is_injected()
    {
        config(['cray.api_key' => 'secret_token']);
        config(['cray.base_url' => 'https://test.com']);

        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        Cray::wallets()->balances();

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer secret_token');
        });
    }

    public function test_fx_requests_use_public_api_endpoints()
    {
        config(['cray.api_key' => 'test_key']);
        config(['cray.base_url' => 'https://test.com']);

        Http::fake([
            'test.com/*' => Http::response(['success' => true], 200),
        ]);

        Cray::fx()->rates([
            'source_currency' => 'USD',
            'destination_currency' => 'NGN',
        ]);
        Cray::fx()->ratesByDestination([
            'destination_currency' => 'NGN',
        ]);
        Cray::fx()->quote([
            'source_currency' => 'USD',
            'destination_currency' => 'NGN',
            'source_amount' => 100,
        ]);
        Cray::fx()->convert([
            'quote_id' => 'quote_123',
        ]);
        Cray::fx()->conversions();
        Cray::fx()->disputeConversion('conv_123', [
            'reason' => 'settlement_mismatch',
        ]);

        $expectedRequests = [
            ['POST', 'https://test.com/api/rates'],
            ['POST', 'https://test.com/api/rates/destination'],
            ['POST', 'https://test.com/api/quote'],
            ['POST', 'https://test.com/api/conversions'],
            ['GET', 'https://test.com/api/conversions'],
            ['POST', 'https://test.com/api/conversions/conv_123/dispute'],
        ];

        foreach ($expectedRequests as [$method, $url]) {
            Http::assertSent(function ($request) use ($method, $url) {
                return $request->method() === $method
                    && $request->url() === $url
                    && $request->hasHeader('Authorization', 'Bearer test_key');
            });
        }
    }

    public function test_checkout_requests_use_public_api_endpoints()
    {
        config(['cray.api_key' => 'test_key']);
        config(['cray.base_url' => 'https://test.com']);

        Http::fake([
            'test.com/*' => Http::response(['success' => true], 200),
        ]);

        Cray::checkout()->initialize([
            'reference' => 'checkout_123',
            'amount' => 100,
        ]);
        Cray::checkout()->query('checkout_123');

        $expectedRequests = [
            ['POST', 'https://test.com/api/checkout/initialize'],
            ['GET', 'https://test.com/api/checkout/query/checkout_123'],
        ];

        foreach ($expectedRequests as [$method, $url]) {
            Http::assertSent(function ($request) use ($method, $url) {
                return $request->method() === $method
                    && $request->url() === $url
                    && $request->hasHeader('Authorization', 'Bearer test_key');
            });
        }
    }

    public function test_crypto_collection_requests_use_public_api_endpoints()
    {
        config(['cray.api_key' => 'test_key']);
        config(['cray.base_url' => 'https://test.com']);

        Http::fake([
            'test.com/*' => Http::response(['success' => true], 200),
        ]);

        Cray::crypto()->supportedAssets();
        Cray::crypto()->createVault([
            'customer_reference' => 'customer_123',
        ]);

        $expectedRequests = [
            ['GET', 'https://test.com/api/virtual-accounts/crypto/supported-assets'],
            ['POST', 'https://test.com/api/accounts/crypto/vault'],
        ];

        foreach ($expectedRequests as [$method, $url]) {
            Http::assertSent(function ($request) use ($method, $url) {
                return $request->method() === $method
                    && $request->url() === $url
                    && $request->hasHeader('Authorization', 'Bearer test_key');
            });
        }
    }

    public function test_virtual_account_otp_requests_use_generate_wallet_endpoint()
    {
        config(['cray.api_key' => 'test_key']);
        config(['cray.base_url' => 'https://test.com']);

        Http::fake([
            'test.com/*' => Http::response(['success' => true], 200),
        ]);

        Cray::virtualAccounts()->generateWallet([
            'otp' => '123456',
            'customer_email' => 'customer@example.com',
        ]);
        Cray::virtualAccounts()->submitOtp([
            'otp' => '123456',
            'customer_email' => 'customer@example.com',
        ]);

        Http::assertSentCount(2);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && $request->url() === 'https://test.com/api/virtual-accounts/generate-wallet'
                && $request->hasHeader('Authorization', 'Bearer test_key');
        });
    }

    public function test_crypto_payout_requests_use_public_api_endpoints()
    {
        config(['cray.api_key' => 'test_key']);
        config(['cray.base_url' => 'https://test.com']);

        Http::fake([
            'test.com/*' => Http::response(['success' => true], 200),
        ]);

        Cray::cryptoPayouts()->supportedAssets();
        Cray::cryptoPayouts()->addBeneficiary([
            'name' => 'OMU',
            'asset' => 'TRX_USDT_S2UZ',
            'wallet_address' => 'wallet_address',
        ]);
        Cray::cryptoPayouts()->initiatePayout([
            'amount' => '2',
            'currency' => 'TRX_USDT_S2UZ',
            'address_reference' => 'beneficiary_123',
            'customer_reference' => 'customer_ref_123',
        ]);

        $expectedRequests = [
            ['GET', 'https://test.com/api/virtual-accounts/crypto/supported-assets'],
            ['POST', 'https://test.com/api/payout/crypto/beneficiaries'],
            ['POST', 'https://test.com/api/payout/crypto/initiate-payout'],
        ];

        foreach ($expectedRequests as [$method, $url]) {
            Http::assertSent(function ($request) use ($method, $url) {
                return $request->method() === $method
                    && $request->url() === $url
                    && $request->hasHeader('Authorization', 'Bearer test_key');
            });
        }
    }

    public function test_failed_payout_webhook_requests_use_public_api_endpoints()
    {
        config(['cray.api_key' => 'test_key']);
        config(['cray.base_url' => 'https://test.com']);

        Http::fake([
            'test.com/*' => Http::response(['success' => true], 200),
        ]);

        Cray::webhooks()->failedPayoutWebhooks();
        Cray::webhooks()->retryFailedPayoutWebhook('50');

        $expectedRequests = [
            ['GET', 'https://test.com/api/payout/failedWebhook'],
            ['GET', 'https://test.com/api/payout/failedWebhook/50'],
        ];

        foreach ($expectedRequests as [$method, $url]) {
            Http::assertSent(function ($request) use ($method, $url) {
                return $request->method() === $method
                    && $request->url() === $url
                    && $request->hasHeader('Authorization', 'Bearer test_key');
            });
        }
    }
}
