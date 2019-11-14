<div class="card card-primary">
    <form class="needs-validation specialty_form" novalidate="" make="new">
        <div class="card-header text-center">
            <h4 class="action">Créer Spécialité</h4>
        </div>
        <div class="card-body">
            <?= $form->input('text', 'required', 'name', '', 'Designation', 'on') ?>
            <?= $form->input('text', 'required', 'created_at', 'datepicker_specialty', 'Date de ceration', 'off') ?>
            <?= $form->select('required', 'service_id',  'Service', $services) ?>
        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary specialty_submit">Créer</button>
            <button type="reset" class="btn btn-light specialty_reset">Effacer</button>
        </div>
    </form>
</div>