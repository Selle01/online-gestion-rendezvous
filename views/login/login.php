<?= $renderer->render('header') ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
                <img src="../../build/img/stisla-fill.svg" alt="logo" width="100" class="shadow-light rounded-circle">
            </div>

            <div class="card card-primary">
                <div class="card-header">
                    <h4>Connexion</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="<?= $router->generateUri('login.auth'); ?>" class="needs-validation" novalidate="">

                        <?= $form->input('text', 'required', 'login', '', 'login', 'off') ?>
                        <?= $form->input('password', 'required', 'password', '', 'password', 'off') ?>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $renderer->render('footer') ?>