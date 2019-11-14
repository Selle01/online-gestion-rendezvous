<div class="card card-primary">
    <form class="needs-validation role_form" novalidate="" make="new">
        <div class="card-header text-center">
            <h4 class="action">Créer role</h4>
        </div>
        <div class="card-body">
            <?= $form->input('text', 'required', 'name', '', 'Designation', 'on') ?>
            <?= $form->input('text', 'required', 'created_at', 'datepicker_role', 'Date de création', 'off') ?>
        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary role_submit">Valider</button>
            <button type="reset" class="btn btn-light role_reset">Reset</button>
        </div>
    </form>
</div>