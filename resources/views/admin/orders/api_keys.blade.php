<?php $this->assign('title', 'Update Binance API Keys For ' . $theuser->name); ?>

<section id="horizontal-form-layouts">
    <div class="row">
        <div class="col-sm-9">
            <div class="content-header">Update Binance API Keys For
                <?= $theuser->name ?>
            </div>
        </div>
        <div class="col-sm-3">
            <div>
                <a href="<?= $this->Url->build(['prefix' => 'Admin', 'controller' => 'Users', 'action' => 'view', $theuser->id]) ?>"
                    class="btn mt-1 btn-info gradient-nepal white">
                    User Dashboard
                </a>
            </div>
        </div>
    </div>
    <?= $this->element('api_binding_form') ?>

</section>
