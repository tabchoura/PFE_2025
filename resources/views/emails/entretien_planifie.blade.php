<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Entretien planifié</title>
</head>
<body>
  <p>Bonjour {{ $prenom }},</p>

  <p>
    Votre entretien pour le poste <strong>{{ $titreOffre }}</strong> est fixé le <strong>{{ $date }}</strong>.
  </p>

  <p>
    Connectez-vous via ce lien : <a href="{{ $lienVisio }}">{{ $lienVisio }}</a>
  </p>

  <p>À bientôt,<br>L’équipe Jobgo</p>
</body>
</html>
