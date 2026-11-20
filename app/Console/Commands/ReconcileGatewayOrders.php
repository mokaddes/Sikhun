<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Payment\BkashMerchantService;
use App\Services\Payment\MhpayService;
use App\Services\Payment\NagadMerchantService;
use App\Services\PurchaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcileGatewayOrders extends Command
{
    protected $signature = 'orders:reconcile-gateways {--hours=24 : Lookback window in hours}';

    protected $description = 'Reconcile pending gateway orders (MHPay, bKash, Nagad) against authoritative gateway status APIs';

    public function handle(
        PurchaseService $purchases,
        MhpayService $mhpay,
        BkashMerchantService $bkash,
        NagadMerchantService $nagad
    ): int {
        $hours = max(1, (int) $this->option('hours'));
        $orders = Order::query()
            ->whereIn('payment_method', ['mhpay', 'bkash', 'nagad'])
            ->where('status', 'pending')
            ->whereNotNull('gateway_invoice_id')
            ->where('created_at', '>=', now()->subHours($hours))
            ->get();

        $reconciled = 0;
        foreach ($orders as $order) {
            $verifiedData = match ($order->payment_method) {
                'mhpay' => $mhpay->verifiedData($order->gateway_invoice_id),
                'bkash' => $bkash->isEnabled() ? $bkash->queryPayment($order->gateway_invoice_id) : null,
                'nagad' => $nagad->isEnabled() ? $nagad->verifiedData($order->gateway_invoice_id) : null,
                default => null,
            };

            if ($verifiedData) {
                $trxId = $verifiedData['trxID']
                    ?? $verifiedData['issuerPaymentRefNo']
                    ?? data_get($verifiedData, 'verification.transaction_id')
                    ?? $order->gateway_invoice_id;
                $purchases->completeGatewayOrder($order, (string) $trxId);
                $reconciled++;
            }
        }

        Log::info('Daily gateway reconciliation finished', [
            'checked' => $orders->count(),
            'reconciled' => $reconciled,
        ]);

        $this->info("Checked {$orders->count()} pending gateway order(s), reconciled {$reconciled}.");

        return self::SUCCESS;
    }
}
