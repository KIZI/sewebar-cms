<form action="index.php?option=com_kbi&amp;controller=documents" name="adminForm" id="adminForm" method="post">
	<div style="position:relative;">
		<?php print JText::_(FILTER) ?>:
		<input type="text" name="filter" value="<?php print JRequest::getString('filter','') ?>" id="filter" />
		<button onclick="this.form.submit();">OK</button>
		<button onclick="document.getElementById('filter').value='';this.form.submit();">Reset</button>

		<div style="display:inline;position:absolute;right:5px;top:2px;">
			<select name="section" onchange="document.adminForm.submit();">
				<option value="-1">--<?php print JText::_(SELECT_SECTION) ?>--</option>
				<?php foreach ($this->sections as $key=>$value) : ?>
				<option value="<?php print $key ?>"	<?php print JRequest::getInt('section', -1) == $key ? ' selected="selected" ' : '' ?>>
					<?php print $value?>
				</option>
				<?php endforeach ?>
			</select>
			<select name="categorie" onchange="document.adminForm.submit();">
				<option value="-1">--<?php print JText::_(SELECT_CATEGORY) ?>--</option>
				<?php foreach ($this->categories as $key=>$value) : ?>
				<option value="<?php print $key ?>"	<?php print JRequest::getInt('categorie', -1) == $key ? ' selected="selected" ' : '' ?>>
					<?php print $value?>
				</option>
				<?php endforeach ?>
			</select>
		</div>
	</div>

	<table border="0" class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th><a href="javascript:tableOrdering('1','<?php print $this->orderDir2 ?>','');"><?php print JText::_('TITLE') ?></a></th>
				<th width="150"><a href="javascript:tableOrdering('2','<?php print $this->orderDir2 ?>', '');"><?php print JText::_('SECTION') ?></a></th>
				<th width="150"><a href="javascript:tableOrdering('3','<?php print $this->orderDir2 ?>', '');"><?php print JText::_('CATEGORY') ?></a></th>
				<th width="80"><a href="javascript:tableOrdering('created','<?php print $this->orderDir2 ?>', '');"><?php print JText::_('DATE') ?></a></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4"><?php print $this->pageNav->getListFooter() ?></td>
			</tr>
		</tfoot>
		<?php if ($this->total > 0 && count($this->articles) > 0): ?>
			<?php foreach ($this->articles as $article): ?>
				<tr class="<?php if ($rowClass=='row0'){$rowClass='row1';}else{$rowClass='row0';} print $rowClass ?>">
					<td>
						<a href="index.php?option=com_ginclude&amp;tmpl=component&amp;task=insert&amp;article=<?php print $article->id ?>">
							<?php print $article->title ?>
						</a>
					</td>
					<td><?php print $article->section ?></td>
					<td><?php print $article->categorie ?></td>
					<td><?php print $article->cdate ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</table>

	<input type="hidden" name="filter_order" value="<?php print JRequest::getCmd('filter_order','title') ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php print $orderDir ?>" />
</form>