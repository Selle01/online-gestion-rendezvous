<?php
$role =  !is_null($session) && $session->get('auth.user') !== null ? $session->get('auth.user')->getRole()->getTitle() : null;
//dd($role);
?>
<?= $renderer->render('header') ?>

<div class="row mt-2">
    <?php if (isset($role) &&  $role === "ROLE_ADMIN" || $role === "ROLE_SECRETARY") :  ?>
        <div class="col-lg-4 col-col-md-4 ">
            <?php require('_form.php'); ?>
        </div>
    <?php endif  ?>
    <div class="col-lg-8 col-col-md-8 ">
        <div class="card card-primary">
            <div class="card-header">
                <h4>Rendez Vous du Service :</h4>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table cellspacing="0" class="table  table-bordered table-hover" id="tableRendezVous" width="100%"></table>
                </div>
            </div>
            <div class="card-footer">

            </div>
        </div>
    </div>

</div>
<div class="row mt-2">
    <div class="col-lg-12 col-col-md-12 ">
        <div class="card card-primary">
            <div class="card-header">

                <h4>Calendrier des rendez Vous du Service :</h4>
            </div>

            <div class="card-body">
                <div id="myEvent"></div>
            </div>
        </div>
    </div>
</div>

<?= $renderer->render('footer') ?>