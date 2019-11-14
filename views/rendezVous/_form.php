<?php

$intervalles = [
    '8h00mn-8h15mn' => '8h00mn-8h15mn',
    '8h15mn-8h30mn' => '8h15mn-8h30mn',
    '8h30mn-8h45mn' => '8h30mn-8h45mn',
    '8h45mn-9h00mn' => '8h45mn-9h00mn',
    '9h00mn-9h15mn' => '9h00mn-9h15mn',
    '9h15mn-9h30mn' => '9h15mn-9h30mn',
];

?>
<div class="card card-primary">

    <form class="needs-validation rendezVous_form" novalidate="" make="new" action="<?= $router->generateUri('rendezVous.new') ?>">
        <div class="card-header text-center">
            <h4 class="action">Créer Rendez-Vous</h4>
        </div>
        <div class="card-body">
            <?= $form->select('required', 'secretary_id', 'Secretaire', $secretaries) ?>
            <?= $form->select('required', 'medecin_id', 'Medecins', $medecins) ?>
            <?= $form->select('required', 'patient_id', 'Patients', $patients) ?>
            <?= $form->select('required', 'heure_rv', 'Heure', $intervalles) ?>
            <?= $form->input('text', 'required', 'date_rv', 'datepicker_date_rv_rendezVous', 'Date de ceration', 'off') ?>

        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary rendezVous_submit ">Valider</button>
            <button type="reset" class="btn btn-light rendezVous_reset">Reset</button>
        </div>
    </form>
</div>