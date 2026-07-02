<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réponse à votre message</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; line-height: 1.6; color: #1e1b18; margin: 0; padding: 0; background-color: #f5ece7; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #2c6904; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0; }
        .content { padding: 30px; }
        .content p { margin: 0 0 16px; color: #41493b; }
        .reply { background: #f5f9f2; border-left: 4px solid #2c6904; border-radius: 8px; padding: 20px; margin: 20px 0; white-space: pre-wrap; color: #1e1b18; }
        .original { background: #fbf2ed; border: 1px solid #c1c9b6; border-radius: 12px; padding: 16px; margin: 20px 0; font-size: 13px; color: #717a69; }
        .original strong { color: #41493b; }
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
            <p>Suite à votre message, voici notre réponse :</p>

            <div class="reply">{{ $replyMessage }}</div>

            <div class="original">
                <p style="margin:0 0 8px;"><strong>Votre message d'origine :</strong></p>
                <p style="margin:0;">{{ $contactMessage->message }}</p>
            </div>

            <p>Cordialement,<br><strong>L'équipe Agro Eco BAARA</strong></p>
        </div>
        <div class="footer">
            <p>Vous pouvez répondre directement à cet e-mail pour poursuivre l'échange.</p>
        </div>
    </div>
</body>
</html>
