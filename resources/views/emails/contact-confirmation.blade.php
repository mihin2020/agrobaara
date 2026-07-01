<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de message</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; line-height: 1.6; color: #1e1b18; margin: 0; padding: 0; background-color: #f5ece7; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #2c6904; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0; }
        .content { padding: 30px; }
        .content p { margin: 0 0 16px; color: #41493b; }
        .recap { background: #fbf2ed; border: 1px solid #c1c9b6; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .recap p { margin: 4px 0; font-size: 14px; }
        .recap strong { color: #1e1b18; }
        .footer { background: #f5ece7; padding: 20px 30px; text-align: center; font-size: 12px; color: #717a69; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Agro Eco BAARA</h1>
        </div>
        <div class="content">
            <p>Bonjour <strong>{{ $contactMessage->full_name }}</strong>,</p>
            <p>Nous avons bien reçu votre message et nous vous en remercions. Notre équipe le traitera dans les meilleurs délais.</p>

            <div class="recap">
                <p><strong>Récapitulatif de votre message :</strong></p>
                <p><strong>Nom :</strong> {{ $contactMessage->full_name }}</p>
                <p><strong>Email :</strong> {{ $contactMessage->email }}</p>
                @if($contactMessage->phone)
                    <p><strong>Téléphone :</strong> {{ $contactMessage->phone }}</p>
                @endif
                <p style="margin-top: 12px;"><strong>Message :</strong></p>
                <p>{{ $contactMessage->message }}</p>
            </div>

            <p>Si vous n'êtes pas à l'origine de ce message, vous pouvez ignorer cet e-mail.</p>
            <p>Cordialement,<br><strong>L'équipe Agro Eco BAARA</strong></p>
        </div>
        <div class="footer">
            <p>Cet e-mail a été envoyé automatiquement, merci de ne pas y répondre directement.</p>
        </div>
    </div>
</body>
</html>
