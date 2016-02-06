<?= view('boomcms::header', ['title' => trans('boomcms::sites.heading')]) ?>
<?= $menu() ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
</div>

<div id="b-sites">
    <h1><?= trans('boomcms::sites.heading') ?></h1>
</div>

<?= view('boomcms::footer') ?>
