<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — École Dieu le Père</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .logo { text-align: center; margin-bottom: 30px; }
        .logo h1 { color: #1a73e8; font-size: 22px; font-weight: 700; }
        .logo p { color: #666; font-size: 13px; margin-top: 4px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px; }
        input {
            width: 100%; padding: 12px 16px;
            border: 1px solid #ddd; border-radius: 8px;
            font-size: 14px; outline: none;
        }
        input:focus { border-color: #1a73e8; }
        .btn {
            width: 100%; padding: 13px;
            background: #1a73e8; color: white;
            border: none; border-radius: 8px;
            font-size: 15px; font-weight: 600; cursor: pointer;
        }
        .btn:hover { background: #0d47a1; }
        .error {
            background: #fdecea; color: #c62828;
            padding: 10px 14px; border-radius: 8px;
            font-size: 13px; margin-bottom: 20px;
        }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <h1>🏫 École Dieu le Père</h1>
            <p>Plateforme de gestion scolaire</p>
        </div>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="form-group">
                <label>Adresse email</label>
                <input type="email" name="email" placeholder="exemple@ecole.com" value="{{ old('email') }}" required/>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="mot_de_passe" placeholder="••••••••" required/>
            </div>
            <button type="submit" class="btn">Se connecter</button>
        </form>

        <div class="footer">
            © {{ date('Y') }} École Dieu le Père — Tous droits réservés
        </div>
    </div>
</body>
</html>