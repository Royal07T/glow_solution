<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment receipt</title>
</head>
<body style="margin: 0; padding: 24px; background: #f8fafc; color: #1e293b; font-family: Arial, sans-serif; font-size: 15px; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 28px; background: #ffffff; border: 1px solid #e2e8f0;">
        <p style="margin: 0 0 4px; color: #0f2f5f; font-size: 22px; font-weight: bold; letter-spacing: 1px;">GLOW</p>
        <p style="margin: 0 0 24px; color: #64748b; font-size: 13px;">Recruitment Agency</p>

        <h1 style="margin: 0 0 12px; color: #0f2f5f; font-size: 20px;">Payment received</h1>
        <p style="margin: 0 0 20px;">Hi {{ $payment->user?->name ?? 'there' }},</p>
        <p style="margin: 0 0 20px;">We received your payment for <strong>{{ $payment->plan?->name ?? 'your subscription' }}</strong>. Your receipt is attached to this email.</p>

        <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Amount</td>
                <td style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: bold;">{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Payment date</td>
                <td style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: bold;">{{ $payment->paid_at?->format('F j, Y') ?? $payment->updated_at->format('F j, Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Reference</td>
                <td style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: bold;">{{ $payment->reference }}</td>
            </tr>
        </table>

        <p style="margin: 0 0 20px;">
            <a href="{{ route('payments.receipt', $payment) }}" style="display: inline-block; padding: 11px 18px; background: #0f2f5f; color: #ffffff; text-decoration: none; font-weight: bold;">Download receipt</a>
        </p>
        <p style="margin: 0 0 12px; color: #64748b; font-size: 13px;">You need to be signed in to download receipts from your account.</p>
        <p style="margin: 0; color: #64748b; font-size: 13px;">
            Questions? <a href="{{ \App\Support\Company::whatsappUrl('Hi Glow Recruitment, I have a question about my payment '.$payment->reference.'.') }}" style="color: #15803d; font-weight: bold;">Chat with us on WhatsApp</a>.
        </p>
    </div>
</body>
</html>
