<?php

namespace App\Support;

/**
 * Card payments. There is no real payment gateway yet: only the test card is approved, with any CVC.
 */
class Payment
{
    public const TEST_CARD = '7111111111111111';

    public static function digits($cardNumber)
    {
        return preg_replace('/\D/', '', (string) $cardNumber);
    }

    /**
     * Charges the card and returns whether the payment went through.
     */
    public static function charge($cardNumber, $amount)
    {
        return self::digits($cardNumber) === self::TEST_CARD;
    }
}
