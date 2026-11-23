<?php

require_once __DIR__ . '/contracts/PaymentProvider.php';
require_once __DIR__ . '/providers/PayPalProcessor.php';
require_once __DIR__ . '/providers/BankTransferProcessor.php';
require_once __DIR__ . '/providers/CreditCardProcessor.php';
require_once __DIR__ . '/CheckoutService.php';
require_once __DIR__ . '/factory.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $amount = (float) $_POST['amount'];
    $providerName = $_POST['payment_provider'] ?? '';

    try {
        $paymentProvider = selectPaymentProvider($providerName);
        $checkoutService = new CheckoutService($paymentProvider);
        $transaction = $checkoutService->checkout($amount);
        echo '<p class="message">Transaction Result:</p>';
        echo '<p class="message"><b>Status:</b> ' . htmlspecialchars($transaction['status']) . '</p>';
        echo '<p class="message"><b>Message:</b> ' . htmlspecialchars($transaction['message']) . '</p>';
        echo '<p class="message"><b>Transaction ID:</b> ' . htmlspecialchars($transaction['transaction_id']) . '</p>';
        //print_r($transaction);
    } catch (InvalidArgumentException $e) {
        echo "Error: " . $e->getMessage();
    }
}


?>

<form action="/Software-Design-And-Architectures/LSP/" method="post">
<label for="t-amount">Amount</label>
<input type="text" name="amount" id="t-amount">
<br>
<label for="provider-paypal">PayPal</label>
<input type="radio" name="payment_provider" id="provider-paypal" value="paypal">
<br>
<label for="provider-bank">Direct Bank</label>
<input type="radio" name="payment_provider" id="provider-bank" value="bank">
<br>
<label for="provider-card">Credit Card</label>
<input type="radio" name="payment_provider" id="provider-card" value="card">
<br>
<button type="submit">Do Transaction</button>
<br><br>






</form>