<?= View::factory('boom/header', array('title' => $page->version()->title)) ?>

<div id="boom-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

	<?= Menu::factory('boom')->sort('priority') ?>

	<div id="boom-topbar-useractions">
		<span id="boom-page-user-menu">
			<button id="b-page-user" class="boom-button" data-icon="ui-icon-boom-person">
				<?=__('Profile')?>
			</button>
		</span>
	</div>

	<div id="b-page-actions">

		<button id="boom-page-menu" class="boom-button" data-icon="ui-icon-boom-menu">
				<?=__('Menu')?>
		</button>

		<? if ($auth->logged_in('edit_page_content', $page)): ?>

				<span id="boom-page-save-menu">
					<button id="b-page-save" class="boom-button" disabled="disabled" title="You have no unsaved changes" data-icon="ui-icon-boom-accept">
						<?=__('Accept')?>
					</button>
				</span>
				<button id="b-page-cancel" class="boom-button" data-icon="ui-icon-boom-cancel">
						<?=__('Cancel')?>
				</button>

				<button id="b-page-version-status" class="boom-button">
					<?= __($page->version()->status()) ?>
				</button>

				<button id="boom-page-preview" class="boom-button b-button-preview" data-icon="ui-icon-boom-preview" data-preview="preview">
					<?=__('Preview')?>
				</button>

				<span id="boom-page-template-menu">
					<button id="boom-page-template-settings" class="boom-button" data-icon="ui-icon-boom-options">
						<?= __('Template settings') ?>
					</button>
				</span>

		<? endif; ?>

	</div>

	<? if ($auth->logged_in('edit_page', $page)): ?>

			<button id="boom-page-visibility" class="boom-button" data-icon="ui-icon-boom-<? echo ($page->is_visible())? 'visible' : 'invisible' ?>">
				<?= __('Visibility') ?>
			</button>
			<span id="boom-page-settings-menu">
				<button id="boom-page-settings" class="boom-button" data-icon="ui-icon-boom-settings">
					<?= __('Settings') ?>
				</button>
			</span>
			<?/*<button id="boom-page-history" class="boom-button" data-icon="ui-icon-boom-history">
				<?= __('History') ?>
			</button>*/?>

			<? if ($auth->logged_in('delete_page', $page) AND ! $page->mptt->is_root()): ?>
				<button class="boom-button" id="b-page-delete" data-icon="ui-icon-boom-delete">
					<?= __('Delete') ?>
				</button>
			<? endif; ?>



		<? if ($auth->logged_in('add_page', $page)): ?>
			<button id="b-page-addpage" class="boom-button" data-icon="ui-icon-boom-add">
				<?=__('Add')?>
			</button>
		<? endif; ?>

		<button id="boom-page-editlive" class="boom-button" data-icon="ui-icon-boom-edit-live">
			<?=__('Edit live')?>
		</button>

		<button id="boom-page-viewlive" class="boom-button b-button-preview" data-icon="ui-icon-boom-view-live" data-preview="disabled">
			<?=__('View live')?>
		</button>
	<? endif; ?>

	<div id="boom-topbar-pagesettings" class="ui-helper-clearfix">
		<div class="ui-helper-center">
			<?= View::factory('boom/editor/page/settings/index');?>
		</div>
	</div>

	<?= View::factory('boom/breadcrumbs'); ?>
</div>

<?= View::factory('boom/editor/footer', array('register_page' => TRUE)) ?>