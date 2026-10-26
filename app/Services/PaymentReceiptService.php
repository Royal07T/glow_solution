<?php

namespace App\Services;

use App\Models\Payment;
use Dompdf\Dompdf;
use Dompdf\Options;

class PaymentReceiptService
{
    public function pdf(Payment $payment): string
    {
        $payment->loadMissing(['user', 'plan']);

        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('billing.receipt', ['payment' => $payment])->render());
        $dompdf->setPaper('A4');
        $dompdf->render();

        return $dompdf->output();
    }

    public function filename(Payment $payment): string
    {
        return 'Glow-Receipt-'.preg_replace('/[^A-Za-z0-9_-]/', '-', $payment->reference).'.pdf';
    }
}
