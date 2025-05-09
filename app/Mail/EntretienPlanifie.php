<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
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
        return $this
            ->subject('Votre entretien Jobgo est planifié')
            ->view('emails.entretien_planifie')
            ->with([
                'prenom'     => $this->candidature->cv->prenom,
                'titreOffre' => $this->candidature->offre->titre,
                'date'       => $this->candidature->date_entretien->format('d/m/Y H:i'),
                'lienVisio'  => $this->candidature->lien_visio ?? null,
            ]);
    }
}
