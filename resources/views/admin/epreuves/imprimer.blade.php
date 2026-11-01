<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impression — {{ $epreuve->matiere ? $epreuve->matiere->nom : 'Épreuve' }} ({{ $epreuve->niveau }})</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #eee; }
        .barre {
            background: #1a73e8; color: white; padding: 12px 20px;
            display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;
        }
        .barre small { opacity: 0.85; }
        .barre button, .barre a {
            background: white; color: #1a73e8; border: none; padding: 8px 16px; border-radius: 6px;
            font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none;
        }
        iframe { width: 100%; height: calc(100vh - 58px); border: 0; display: block; }
        img { max-width: 100%; display: block; margin: 0 auto; background: white; }
        @media print {
            .barre { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body>

<div class="barre">
    <div>
        <strong>{{ $epreuve->matiere ? $epreuve->matiere->nom : 'Épreuve' }}</strong> — {{ $epreuve->niveau }}
        <br><small>{{ $estFinale ? 'Version prête à imprimer' : 'Version originale de l\'enseignant' }}</small>
    </div>
    <div>
        <button type="button" onclick="imprimer()">🖨️ Imprimer</button>
        <a href="javascript:window.close()">Fermer</a>
    </div>
</div>

@if($estImage)
    <img id="doc" src="/admin/epreuves/{{ $epreuve->id }}/fichier" alt="Épreuve">
@else
    <iframe id="doc" src="/admin/epreuves/{{ $epreuve->id }}/fichier"></iframe>
@endif

<script>
    function imprimer() {
        var doc = document.getElementById('doc');
        if (doc.tagName === 'IFRAME') {
            doc.contentWindow.focus();
            doc.contentWindow.print();
        } else {
            window.print();
        }
    }

    // La fenêtre d'impression s'ouvre toute seule ; le bouton sert si le navigateur la bloque
    window.addEventListener('load', function () {
        setTimeout(imprimer, 800);
    });
</script>

</body>
</html>