<?= View::factory('boom/header', array('title' => $page->getTitle())) ?>

<div id="b-topbar" class='b-page-toolbar b-toolbar b-toolbar-vertical'>
	<?= Form::hidden('csrf', Security::token(), array('id' => 'b-csrf')) ?>
	<?= new \Boom\UI\MenuButton() ?>
	<?= new \Boom\Menu\Menu  ?>

	<div id="b-topbar-page-buttons">
		<? if ($page->wasCreatedBy($person) || $auth->loggedIn('edit_page_content', $page)): ?>
			<div id="b-page-actions" class="b-page-container">
				<span id="b-page-publish-menu">
					<button id="b-page-version-status" class="b-button" data-status="<?= $page->getCurrentVersion()->status() ?>">
						<?= $page->getCurrentVersion()->status() ?>
					</button>
				</span>

				<?= new \Boom\UI\Button('preview', __("Preview the current version of the page even if it hasn't been published"), array('id' => 'boom-page-preview', 'class' => 'b-button-preview','data-preview' => 'preview')) ?>
				<?= new \Boom\UI\Button('options', __("Changed the template used by the page"), array('id' => 'b-page-template')) ?>
			</div>
		<? endif; ?>

		<? if ($auth->loggedIn('add_page', $page)): ?>
			<div class="b-page-container">
				<?= new \Boom\UI\Button('add', __('Add a new page as a child of the current page'), array('id' => 'b-page-addpage')) ?>
			</div>
		<? endif; ?>

		<div class="b-page-container">
			<? if ($auth->loggedIn('edit_page', $page)): ?>
				<?= new \Boom\UI\Button('visible', __('This page is visible. The content displayed will depend on which version of the page is published'), array('id' => 'b-page-visible', 'class' => $page->isVisible()? 'b-page-visibility ' : 'b-page-visibility ui-helper-hidden')) ?>
				<?= new \Boom\UI\Button('invisible', __('This page is hidden regardless of whether there is a published version'), array('id' => 'b-page-invisible', 'class' => $page->isVisible()? 'b-page-visibility ui-helper-hidden' : 'b-page-visibility')) ?>

				<span id="b-page-settings-menu">
					<?= new \Boom\UI\Button('settings', __('Page settings which apply whichever version is published'), array('id' => 'boom-page-settings')) ?>
				</span>
			<? endif ?>

			<? if (($page->wasCreatedBy($person) || $auth->loggedIn('delete_page', $page)) && ! $page->getMptt()->is_root()): ?>
				<?= new \Boom\UI\Button('delete', __('Delete this page'), array('id' => 'b-page-delete')) ?>
			<? endif; ?>
		</div>

		<? if ($readability): ?>
			<button id="b-page-readability" class="b-button">
				<?= $readability ?>
			</button>
		<? endif ?>

		<div class="b-page-container">
			<?/*<button id="boom-page-editlive" class="ui-button boom-button" data-icon="ui-icon-boom-edit-live">
				<?=__('Edit live')?>
			</button>*/?>

			<?= new \Boom\UI\Button('view-live', __('View the page as it appears on the live site'), array('id' => 'boom-page-viewlive', 'class' => 'b-button-preview', 'data-preview' => 'disabled')) ?>
		</div>

		<div id="b-topbar-pagesettings">
			<div>
				<?= View::factory('boom/editor/page/settings/index');?>
			</div>
		</div>
	</div>

        <div id="wysihtml5-toolbar" class="b-toolbar b-toolbar-vertical b-toolbar-text">
            <? foreach (Boom\UI\TextEditorToolbar::getAvailableButtonSets() as $set): ?>

                <?= new Boom\UI\TextEditorToolbar($set) ?>
            <? endforeach ?>
        </div>
</div>

<?= View::factory('boom/editor/footer', array('register_page' => true)) ?>