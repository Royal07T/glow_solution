<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment receipt {{ $payment->reference }}</title>
    <style>
        @page { margin: 36px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #1e293b; font-family: "DejaVu Sans", sans-serif; font-size: 12px; line-height: 1.55; }
        .brand { color: #0f2f5f; font-size: 24px; font-weight: bold; letter-spacing: 1px; }
        .muted { color: #64748b; }
        .title { color: #0f2f5f; font-size: 22px; font-weight: bold; letter-spacing: 1px; }
        .rule { margin: 20px 0; border-top: 2px solid #d4a72c; }
        .panel { padding: 18px; border: 1px solid #e2e8f0; background: #f8fafc; }
        .amount { padding: 18px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534; }
        .amount-value { font-size: 22px; font-weight: bold; }
        .label { color: #64748b; font-size: 10px; font-weight: bold; letter-spacing: 0.7px; text-transform: uppercase; }
        .value { padding-top: 3px; font-size: 13px; font-weight: bold; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .details td { padding: 9px 0; border-bottom: 1px solid #e2e8f0; }
        .details td:last-child { text-align: right; font-weight: bold; }
        .footer { margin-top: 24px; padding-top: 14px; border-top: 1px solid #e2e8f0; color: #64748b; font-size: 10px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; background: #dcfce7; color: #166534; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .reference { word-wrap: break-word; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td>
                <div class="brand">GLOW</div>
                <div class="muted">Recruitment Agency</div>
            </td>
            <td align="right">
                <div class="title">PAYMENT RECEIPT</div>
                <div class="muted">Receipt #{{ str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div style="padding-top: 8px;"><span class="badge">Paid</span></div>
            </td>
        </tr>
    </table>

    <div class="rule"></div>

    <table>
        <tr>
            <td width="58%" style="padding-right: 18px;">
                <div class="panel">
                    <div class="label">Billed to</div>
                    <div class="value">{{ $payment->user?->name ?? 'Customer' }}</div>
                    <div class="muted">{{ $payment->user?->email }}</div>
                    @if($payment->user?->phone)
                        <div class="muted">{{ $payment->user->phone }}</div>
                    @endif
                </div>
            </td>
            <td width="42%">
                <div class="amount">
                    <div class="label" style="color: #166534;">Amount paid</div>
                    <div class="amount-value">{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div style="height: 22px;"></div>

    <table class="details">
        <tr>
            <td class="muted">Plan</td>
            <td>{{ $payment->plan?->name ?? 'Subscription plan' }}</td>
        </tr>
        <tr>
            <td class="muted">Billing cycle</td>
            <td>{{ ucfirst((string) ($payment->plan?->interval ?? 'one-time')) }}</td>
        </tr>
        <tr>
            <td class="muted">Payment date</td>
            <td>{{ $payment->paid_at?->format('F j, Y g:i A') ?? $payment->updated_at->format('F j, Y g:i A') }}</td>
        </tr>
        <tr>
            <td class="muted">Payment method</td>
            <td>Paystack</td>
        </tr>
        <tr>
            <td class="muted">Payment reference</td>
            <td class="reference">{{ $payment->reference }}</td>
        </tr>
    </table>

    <div class="footer">
        <p style="margin: 0 0 4px;">This is a system-generated receipt for a successful subscription payment. No signature is required.</p>
        <p style="margin: 0 0 4px;">{{ config('company.address') }}</p>
        <p style="margin: 0;">Questions? Call or WhatsApp {{ config('company.phone') }}. WhatsApp: {{ \App\Support\Company::whatsappUrl('Hi Glow Recruitment, I have a question about my payment receipt '.$payment->reference.'.') }}</p>
    </div>
</body>
</html>
