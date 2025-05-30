<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use App\Models\Candidature;

class EntretienPlanifie extends Mailable
{
    use Queueable, SerializesModels;

    public Candidature $candidature;

    public function __construct(Candidature $candidature)
    {
        $this->candidature = $candidature;
    }

public function build()
{
    // On transforme l'objet Carbon en chaîne formatée
    $dt = Carbon::parse($this->candidature->date_entretien)
                ->locale('fr')
                ->setTimezone('Europe/Paris');

    return $this
        ->subject('Votre entretien Jobgo est planifié')
        ->view('emails.entretien_planifie')
        ->with([
            'prenom'     => $this->candidature->cv->prenom,
            'titreOffre' => $this->candidature->offre->titre,
            'date'       => $dt->translatedFormat('d F Y'),
            'lienVisio'  => $this->candidature->lien_visio ?? null,
        ]);
}
}