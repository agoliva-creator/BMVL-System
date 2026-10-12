<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
        <h2 style="color: #198754;">Booking Confirmed!</h2>
        <p>Hi {{ $booking->user->name }},</p>
        <p>Thank you for booking with BMVL Sports. Here are the details of your reservation:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Court:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $booking->court->name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Date:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Time:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - 
                    {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Total Price:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; color: #198754; font-weight: bold;">
                    ₱ {{ number_format($booking->total_price, 2) }}
                </td>
            </tr>
        </table>

        <p style="margin-top: 20px;">Please arrive 10 minutes early. We look forward to seeing you!</p>
        <p style="color: #6c757d; font-size: 12px;">- The BMVL Sports Team</p>
    </div>
</body>
</html>