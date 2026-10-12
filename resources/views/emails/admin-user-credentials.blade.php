<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your BMVL Sports Center account</title>
</head>
<body style="margin:0;padding:24px;background:#f7f5ef;color:#1a2422;font-family:Arial,sans-serif;">
    <main style="max-width:600px;margin:0 auto;padding:32px;background:#fff;border:1px solid #ddd7c9;border-radius:12px;">
        <p>Hi {{ $user->name }},</p>
        <h1 style="margin:0 0 12px;color:#0a3a3c;font-size:24px;">Your account is ready</h1>
        <p>An administrator created a BMVL Sports Center account for you. Use the details below to sign in:</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Temporary password:</strong></p>
        <p style="display:inline-block;padding:12px 16px;background:#edf4f2;border-radius:8px;color:#0a3a3c;font-family:monospace;font-size:18px;">{{ $temporaryPassword }}</p>
        <p>For your account security, change this password after you sign in. Do not share it with anyone.</p>
        <p style="margin-bottom:0;">Regards,<br>BMVL Sports Center Team</p>
    </main>
</body>
</html>
