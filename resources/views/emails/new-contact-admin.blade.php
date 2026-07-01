<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de contact</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; line-height: 1.6; color: #1e1b18; margin: 0; padding: 0; background-color: #f5ece7; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #875212; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 20px; margin: 0; }
        .content { padding: 30px; }
        .content p { margin: 0 0 14px; color: #41493b; }
        .info-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .info-table td { padding: 10px 12px; border-bottom: 1px solid #e9e1dc; font-size: 14px; vertical-align: top; }
        .info-table td:first-child { font-weight: 600; color: #1e1b18; white-space: nowrap; width: 120px; }
        .message-box { background: #fbf2ed; border: 1px solid #c1c9b6; border-radius: 12px; padding: 16px; margin: 16px 0; font-size: 14px; }
        .btn { display: inline-block; background: #2c6904; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; margin-top: 16px; }
        .footer { background: #f5ece7; padding: 20px 30px; text-align: center; font-size: 12px; color: #717a69; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouveau message de contact</h1>
        </div>
        <div class="content">
            <p>Un nouveau message a été envoyé via le formulaire de contact :</p>

            <table class="info-table">
                <tr><td>Nom</td><td>{{ $contactMessage->full_name }}</td></tr>
                <tr><td>Email</td><td><a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a></td></tr>
                @if($contactMessage->phone)
                    <tr><td>Téléphone</td><td>{{ $contactMessage->phone }}</td></tr>
                @endif
                <tr><td>Date</td><td>{{ $contactMessage->created_at->format('d/m/Y à H:i') }}</td></tr>
                <tr><td>IP</td><td>{{ $contactMessage->ip_address }}</td></tr>
            </table>

            <p><strong>Message :</strong></p>
            <div class="message-box">
                {!! nl2br(e($contactMessage->message)) !!}
            </div>

            <a href="{{ url('/admin/messages') }}" class="btn">Voir dans l'administration</a>
        </div>
        <div class="footer">
            <p>Notification automatique - Agro Eco BAARA</p>
        </div>
    </div>
</body>
</html>
