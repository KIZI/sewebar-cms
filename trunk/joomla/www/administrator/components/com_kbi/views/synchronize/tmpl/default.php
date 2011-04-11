<form action="index.php" name="adminForm" id="adminForm" method="post">
	<div style="position:relative;">
		<?php print JText::_('FILTER') ?>:
		<input type="text" name="filter" value="<?php print JRequest::getString('filter','') ?>" id="filter" />
		<button onclick="this.form.submit();">OK</button>
		<button onclick="document.getElementById('filter').value='';this.form.submit();">Reset</button>

		<div style="display:inline;position:absolute;right:5px;top:2px;">
			<select name="section" onchange="document.adminForm.submit();">
				<option value="-1">--<?php print JText::_('SELECT_SECTION') ?>--</option>
				<?php foreach ($this->sections as $key=>$value) : ?>
				<option value="<?php print $key ?>"	<?php print JRequest::getInt('section', -1) == $key ? ' selected="selected" ' : '' ?>>
					<?php print $value?>
				</option>
				<?php endforeach ?>
			</select>
			<select name="categorie" onchange="document.adminForm.submit();">
				<option value="-1">--<?php print JText::_('SELECT_CATEGORY') ?>--</option>
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
				<th width="20"><?php echo JText::_( 'Num' ) ?></th>
				<th width="20">
					<input type="checkbox" name="toggle" value=""  onclick="checkAll(<?php echo count( $this->rows ); ?>);" />
				</th>
				<th><a href="javascript:tableOrdering('1','<?php print $this->orderDir2 ?>','');"><?php print JText::_('TITLE') ?></a></th>
				<th width="150"><a href="javascript:tableOrdering('2','<?php print $this->orderDir2 ?>', '');"><?php print JText::_('SECTION') ?></a></th>
				<th width="150"><a href="javascript:tableOrdering('3','<?php print $this->orderDir2 ?>', '');"><?php print JText::_('CATEGORY') ?></a></th>
				<th width="80"><a href="javascript:tableOrdering('created','<?php print $this->orderDir2 ?>', '');"><?php print JText::_('DATE') ?></a></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php print $this->pageNav->getListFooter() ?></td>
			</tr>
		</tfoot>
		<?php if ($this->total > 0 && count($this->articles) > 0): ?>
			<?php for ($i=0, $n=count( $this->articles ); $i < $n; $i++) : $article = &$this->articles[$i]; $article->checked_out = false; ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td align="center"><?php echo $this->pageNav->getRowOffset($i); ?></td>
					<td align="center"><?php echo JHTML::_('grid.checkedout', $article, $i); ?></td>
					<td><?php print $article->title ?></td>
					<td><?php print $article->section ?></td>
					<td><?php print $article->categorie ?></td>
					<td><?php print $article->cdate ?></td>
				</tr>
			<?php endfor; ?>
		<?php endif; ?>
	</table>

	<input type="hidden" name="controller" value="documents" />
	<input type="hidden" name="option" value="com_kbi" />
	<input type="hidden" name="task" value="synchronize" />
	<input type="hidden" name="id" value="<?php print $this->source->id ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php print JRequest::getCmd('filter_order','title') ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php //print $orderDir ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>