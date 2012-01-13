<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<div id="sledge-tagmanager">


	<div class="sledge-tagmanager-main ui-helper-right">
		<div class="sledge-tagmanager-body ui-helper-clearfix">
			<div class="sledge-tagmanager-rightpane">
				<div class="content">
					&nbsp;
				</div>
			</div>
		</div>
	</div>
	<div class="sledge-tagmanager-sidebar ui-helper-left">

		<?//= new View('ui/subtpl_tag_manager_search');?>

		<?//= new View('ui/subtpl_tag_tree')?>
	</div>
</div>

<script type="text/javascript">


	$.sledge.init('people',  {
		person: {
			rid: <?= $person->id?>,
			firstname: '<?= $person->firstname?>',
			lastname: "<?= $person->lastname?>"
		}
	});

	$.sledge.tagmanager.people.init({
		items: {
			tag: $.sledge.tagmanager.items.tag,
			person: $.sledge.tagmanager.items.person
		},
		options: {
			basetagRid: <?= $this->basetag_rid?>, 
			defaultTagRid: <?=$this->default_tag_rid?>,
			edition: '<?= $this->edition?>', 
			type: '<?= $this->type?>',
			selected: [<?= (count($this->selected) ? "'".implode('\',\'', $selected)."'" : '');?>], 
			types: [<?= (count($this->types) ? "'".implode('\',\'', $types)."'" : '');?>],
			excludeSmartTags: <?= (string) (int) $this->exclude_smart_tags?>,
			template: '<?= $this->template?>',
			allowedUploadTypes: [ '<?= implode('\', \'', $allowed_types)?>' ]
		}
	});
</script>
