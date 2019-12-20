<?php

namespace DigiTickets\StripeTests;

use DigiTickets\Stripe\Messages\CompletePurchaseRequest;
use DigiTickets\Stripe\Messages\CompletePurchaseResponse;
use DigiTickets\StripeTests\Fixtures\PaymentIntent;
use Mockery;
use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    public function creationProvider()
    {
        $request = Mockery::mock(CompletePurchaseRequest::class);
//        $request->shouldReceive('getReturnUrl')->once()->andReturn('http://store.example.com/');

        $statusOther = 'Other';
        return [
            'no payment intent' => [
                $request,
                [],
                false,
                'Could not retrieve payment'
            ],
            'successful' => [
                $request,
                ['paymentIntent' => new PaymentIntent(PaymentIntent::PI_ID, CompletePurchaseResponse::STATUS_SUCCESS)],
                true,
                CompletePurchaseResponse::STATUS_SUCCESS
            ],
            'canceled' => [
                $request,
                ['paymentIntent' => new PaymentIntent(PaymentIntent::PI_ID, CompletePurchaseResponse::STATUS_CANCELED)],
                false,
                CompletePurchaseResponse::STATUS_CANCELED
            ],
            'unknown' => [
                $request,
                ['paymentIntent' => new PaymentIntent(PaymentIntent::PI_ID, $statusOther)],
                false,
                $statusOther
            ],
        ];
    }

    /**
     * @param CompletePurchaseRequest $request
     * @param $data
     * @param bool $expectSuccess
     * @param string $expectMessage
     *
     * @dataProvider creationProvider
     */
    public function testCreation(CompletePurchaseRequest $request, $data, bool $expectSuccess, string $expectMessage)
    {
        $completePurchaseResponse = new CompletePurchaseResponse($request, $data);

        $this->assertEquals($expectSuccess, $completePurchaseResponse->isSuccessful());
        $this->assertEquals($expectMessage, $completePurchaseResponse->getMessage());
        $this->assertNull($completePurchaseResponse->getCode());

        // @TODO: Check getTransactionReference() and ?getTransactionId()
    }
}