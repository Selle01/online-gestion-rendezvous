<?php

$genres = [
    'FEMME' => 'FEMME',
    'HOMME' => 'HOMME'
]

?>
<div class="collapse  w-100" id="collapseExample">
    <div class="card card-primary w-100">
        <form class="needs-validation patient_form" novalidate="" make="new">
            <div class="card-header text-center">
                <h4 class="action">Ajouter Secretaire</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fieldmatricule">matricule</label>
                            <input type="text" id="matricule" name="matricule" class="form-control " value="<?= $matricule ?>" required="" autocomplete="off">
                        </div>
                        <?= $form->select('required', 'role_id',  'Fonction', $role) ?>
                        <?= $form->input('text', 'required', 'firstName', '', 'Prenom', 'off') ?>
                        <?= $form->input('text', 'required', 'lastName', '', 'Nom', 'off') ?>
                        <?= $form->input('text', 'required', 'dateNais', 'datepicker_dateNais_patient', 'Date de Naissance', 'off') ?>
                        <?= $form->select('required', 'genre', 'genre', $genres) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->input('text', 'required', 'address', '', 'address', 'off') ?>
                        <?= $form->input('email', 'required', 'email', '', 'email', 'off') ?>
                        <?= $form->input('number', 'required', 'tel', '', 'telephone', 'off') ?>
                        <?= $form->input('text', 'required', 'login', '', 'login', 'off') ?>
                        <?= $form->input('password', 'required', 'password', '', 'password', 'off') ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->input('text', 'required', 'CNI', '', 'CNI', 'off') ?>
                        <?= $form->input('text', 'required', 'created_at', 'datepicker_created_at_patient', 'Date de ceration', 'off') ?>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary patient_submit">Créer</button>
                <a type="button" class="btn btn-light patient_reset">Annuler</a>

            </div>
        </form>
    </div>
</div>