<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Entretien planifié</title>
</head>
<body>
  <p>Bonjour {{ $prenom }},</p>

  <p>Votre entretien pour le poste <strong>{{ $titreOffre }}</strong> est fixé au <strong>{{ $date }}</strong>.</p>

  @if($lienVisio)
    <p>Connectez-vous via ce lien : <a href="{{ $lienVisio }}">{{ $lienVisio }}</a></p>
  @endif

  <p>À bientôt,<br>L’équipe Jobgo</p>
</body>
</html>
