<?= $renderer->render('header') ?>

<div class="row mt-4">

    <div class="col-lg-4 col-col-md-4 ">
        <?php require('_form.php'); ?>
    </div>

    <div class="col-lg-8 col-col-md-8 ">
        <div class="card card-primary">

            <div class="card-header">
                <h4>Service de l'hopital</h4>
                <div class="card-header-action">
                    <a href="<?= $router->generateUri('service.new') ?>" class="btn btn-primary service_new">Créer Service
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table cellspacing="0" class="table  table-bordered table-hover" id="tableService" width="100%"></table>
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