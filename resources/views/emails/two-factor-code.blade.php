<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Code de connexion</title>
</head>
<body style="font-family: Inter, Arial, sans-serif; background:#fff8f5; color:#1e1b18; padding:24px;">
    <div style="max-width:480px;margin:0 auto;background:#ffffff;border:1px solid #c1c9b6;border-radius:16px;padding:28px;">
        <p style="margin:0 0 8px;font-size:14px;color:#41493b;">Bonjour {{ $user->first_name }},</p>
        <p style="margin:0 0 20px;font-size:14px;color:#41493b;">
            Voici votre code de validation pour finaliser la connexion à Agro Eco BAARA :
        </p>
        <p style="margin:0 0 20px;text-align:center;font-size:32px;letter-spacing:8px;font-weight:700;color:#2c6904;">
            {{ $code }}
        </p>
        <p style="margin:0 0 8px;font-size:13px;color:#717a69;">
            Ce code expire dans <strong>10 minutes</strong>. Ne le partagez avec personne.
        </p>
        <p style="margin:0;font-size:12px;color:#717a69;">
            Si vous n’êtes pas à l’origine de cette connexion, ignorez cet e-mail et changez votre mot de passe.
        </p>
    </div>
</body>
</html>
