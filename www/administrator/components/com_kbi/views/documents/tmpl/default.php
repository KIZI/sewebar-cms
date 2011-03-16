<form action="index.php" method="post" name="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
				<input type="text" name="search" id="search" value="<?php //echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
			</td>
		</tr>
	</table>

	<table class="adminlist">
	<thead>
		<tr>
			<th width="20">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value=""  onclick="checkAll(<?php echo count( $this->rows ); ?>);" />
			</th>
			<th nowrap="nowrap" class="title">
				<?php echo JText::_( 'Name' ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="13">
				<?php //echo $this->pageNav->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php for ($i=0, $n=count( $this->rows ); $i < $n; $i++) : $row = &$this->rows[$i]; ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td align="center">
				<?php echo $i+1 //echo $this->pageNav->getRowOffset($i); ?>
			</td>
			<td align="center">
				<?php $row->checked_out = false; ?>
				<?php echo JHTML::_('grid.checkedout', $row, $i); ?>
			</td>
			<td align="center">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit' );?>::<?php echo $row->name; ?>">
					<a href="<?php echo JRoute::_("index.php?option=com_kbi&controller=documents&id[]={$this->source->id}&cid[]={$row->name}&task=view"); ?>"><?php echo $row->name; ?></a>
				</span>
			</td>
		</tr>
	<?php endfor; ?>
	</tbody>
	</table>
	<input type="hidden" name="controller" value="documents" />
	<input type="hidden" name="option" value="com_kbi" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->source->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php //echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php //echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
