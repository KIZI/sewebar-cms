<form action="index.php" method="post" name="adminForm">
	<table>
		</table>

	<table class="adminlist">
	<thead>
		<tr>
			<th width="2%">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<th width="3%">
		
			</th>
			<th nowrap="nowrap" width="65%" class="title">
				<?php 
        echo JText::_( 'Name' );
        // echo JHTML::_('grid.sort',  'Name', 'name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th nowrap="nowrap" width="10%" class="Extension">
				<?php 
        echo JText::_( 'Extension' );
        // echo JHTML::_('grid.sort',  'Extension', 'extension', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th nowrap="nowrap" width="5%" class="File size">
				<?php 
        echo JText::_( 'File Size' );
        // echo JHTML::_('grid.sort',  'Extension', 'extension', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
						<th nowrap="nowrap" width="15%" class="modified">
				<?php 
        echo JText::_( 'Last Modified' );
        // echo JHTML::_('grid.sort',  'Extension', 'extension', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++) : $row = &$this->rows[$i]; 
        if ($row->type=='Folder'){
            $class='folder';
        }else{
            $class='file';
        }
    ?>
		<tr class="row<?php echo $i % 2; ?> <?php echo $class; ?>">
			<td align="center">
				<?php echo $this->pageNav->getRowOffset($i); ?>
			</td>
			<td align="center">
				<?php  
        // echo JHTML::_('grid.checkedout', $row, $i);
         if ($row->name!='...'){
            echo $this->checkedoutRadio($row, $i);
          }
          ?>
			</td>
			<td align="left">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit' );?>::<?php echo $row->name; ?>">
					<a href="<?php echo $row->link; ?>"><?php echo $row->name; ?></a>
				</span>
			</td>
			<td align="center">
				<span class="extensionlinktip hasTip" title="<?php echo JText::_( 'Edit' );?>::<?php echo $row->type; ?>">
					<?php echo $row->type; ?>
				</span>
			</td>
			<td align="center">
				<span class="extensionlinktip hasTip" title="<?php echo JText::_( 'Edit' );?>::<?php echo $row->type; ?>">
					<?php echo $row->fileSize; ?>
				</span>
			</td>
						<td align="center">
				<span class="extensionlinktip hasTip" title="<?php echo JText::_( 'Edit' );?>::<?php echo $row->type; ?>">
					<?php echo $row->modified; ?>
				</span>
			</td>
		</tr>
	<?php endfor; ?>
	</tbody>
	</table>
	<input type="hidden" name="controller" value="xslts" />
	<input type="hidden" name="option" value="com_xsltmagic" />
	<input type="hidden" name="jump" value="huh" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
