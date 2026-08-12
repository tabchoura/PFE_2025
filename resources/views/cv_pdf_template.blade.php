<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV de {{ $cv->nom }} {{ $cv->prenom }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin-bottom: 20px; }
        h1 { text-align: center; }
        .header { font-size: 18px; font-weight: bold; }
        .content { margin-left: 20px; }
    </style>
</head>
<body>
    <h1>Curriculum Vitae</h1>
    <div class="section header">Nom :</div>
    <div class="section content">{{ $cv->nom }} {{ $cv->prenom }}</div>

    <div class="section header">Email :</div>
    <div class="section content">{{ $cv->email }}</div>

    <div class="section header">Date de naissance :</div>
    <div class="section content">{{ $cv->date_naissance }}</div>

    <div class="section header">Adresse :</div>
    <div class="section content">{{ $cv->adresse }}</div>

    <div class="section header">Présentation :</div>
    <div class="section content">{{ $cv->presentation }}</div>

    <div class="section header">Compétences :</div>
    <div class="section content">{{ implode(', ', $cv->competences) }}</div>

    <div class="section header">Langues :</div>
    <div class="section content">{{ implode(', ', $cv->langues) }}</div>

    <div class="section header">Expériences :</div>
    <div class="section content">{{ implode(', ', $cv->experiences) }}</div>

    <div class="section header">Formations :</div>
    <div class="section content">{{ implode(', ', $cv->educations_formations) }}</div>

    <div class="section header">Projets :</div>
    <div class="section content">{{ implode(', ', $cv->projets) }}</div>
</body>
</html>

//donnees recuperer bl compat dakhalnelou $cv bech ynajm tsir generation pdf 