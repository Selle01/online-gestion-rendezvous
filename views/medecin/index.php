<?= $renderer->render('header') ?>

<div class="row mt-2 justify-content-center">
    <div class="col-lg-12 col-md-12 ">
        <?php require('_form.php'); ?>
    </div>
</div>
<div class="row mt-4 justify-content-center">

    <div class="card card-primary">

        <div class="card-header">
            <h4>medecin de l'hopital</h4>
            <div class="card-header-action">
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
                    <a class="text-white medecin_new" href="<?= $router->generateUri('medecin.new.matricule') ?>">
                        Ajouter un medecin
                    </a>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table cellspacing="0" class="table  table-bordered table-hover responsive nowrap" id="tableMedecin" width="100%"></table>
            </div>
        </div>
        <div class="card-footer text-right">
            <nav class="d-inline-block">
                <ul class="pagination mb-0"></ul>
            </nav>
        </div>
    </div>
</div>
</div>

<?= $renderer->render('footer') ?>