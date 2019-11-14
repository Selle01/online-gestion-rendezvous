<?php

namespace App\Components\Validators;

use App\Dao\RendezVousDao;


class RendezVousValidator extends CommonValidator
{
    public function __construct(array $data, RendezVousDao $rendezVousDao, ?int $rendezvousID = null, $intervalles, $medecins, $patients, $secretaries)
    {
        parent::__construct($data);
        $this->validator->rule('required', [
            'heure_rv', 'medecin_id', 'patient_id',
            'secretary_id'
        ]);
        $this->validator->rule('subset', 'heure_rv', $intervalles);
        $this->validator->rule('subset', 'medecin_id', $medecins);
        $this->validator->rule('subset', 'patient_id', $patients);
        $this->validator->rule('subset', 'secretary_id', $secretaries);
        $this->validator->rule('dateFormat', 'date_rv', "Y-m-d H:i:s");
    }
}
