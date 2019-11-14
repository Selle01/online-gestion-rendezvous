<div class="card card-primary">
    <form class="needs-validation service_form" novalidate="" make="new">
        <div class="card-header text-center">
            <h4 class="action">Créer service</h4>
        </div>
        <div class="card-body">
            <?= $form->input('text', 'required', 'name', '', 'Designation', 'on') ?>
            <?= $form->input('text', 'required', 'created_at', 'datepicker_service', 'Date de création', 'off') ?>
        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary service_submit">Valider</button>
            <button type="reset" class="btn btn-light service_reset">Reset</button>
        </div>
    </form>
</div>