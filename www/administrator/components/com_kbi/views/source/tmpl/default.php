<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm">
	<div class="col100">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'Details' ); ?></legend>

			<table class="admintable">
			<tbody>
				<tr>
					<td width="20%" class="key">
						<label for="name">
							<?php echo JText::_( 'Name' ); ?>:
						</label>
					</td>
					<td width="80%">
						<input class="inputbox" type="text" name="name" id="name" size="50" value="<?php echo isset($this->row->name) ? htmlspecialchars($this->row->name) : '' ;?>" />
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="type">
							<?php echo JText::_( 'Type' ); ?>:
						</label>
					</td>
					<td width="80%">
						<?php echo $this->lists['types']; ?>
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="method">
							<?php echo JText::_( 'Method' ); ?>:
						</label>
					</td>
					<td width="80%">
						<?php echo $this->lists['methods']; ?>
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="name">
							<?php echo JText::_( 'URL' ); ?>:
						</label>
					</td>
					<td width="80%">
						<input class="inputbox" type="text" name="url" id="url" size="100" value="<?php echo isset($this->row->url) ? htmlspecialchars($this->row->url) : '' ;?>" />
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="params">
							<?php echo JText::_( 'Custom Parameters' ); ?>:
						</label>
					</td>
					<td width="80%">
						<textarea class="inputbox" name="params" id="params" cols="80" rows="7" style="width:90%"><?php echo isset($this->row->params) ? htmlspecialchars($this->row->params, ENT_NOQUOTES) : '' ;?></textarea>
					</td>
				</tr>
				<?php if(isset($this->source) && ($this->source instanceof ISynchronable)):?>
				<tr>
					<td width="20%" class="key">
						<label for="documents">
							<?php echo JText::_( 'Documents' ); ?>:
						</label>
					</td>
					<td width="80%">
						<a id="documents" href="<?php echo JRoute::_("index.php?option={$option}&controller=documents&id[]={$this->row->id}") ?>"><?php echo JText::_( 'Documents' ); ?></a>
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="dictionaryLink">
							<?php echo JText::_( 'Get DataDictionary' ); ?>:
						</label>
					</td>
					<td width="80%">
						<script type="text/javascript">
							function getDataDictionary() {
								var url = '<?php echo JRoute::_("/sewebar/index.php?option={$option}&task=dataDescription&format=raw&source={$this->row->id}") ?>';

								var result = $('dictionaryquery');
								var loader = $('dictionaryLink');

								result.empty();

								loader
									.addClass('ajax-loading')
									.removeClass('ajax-error')
									.removeClass('hidden');

								loader.addEvent('click', function(){loader.removeClass('ajax-loading');});

								var myAjax = new Ajax(url,
										{
											method : 'post',
											//update : result,
											onComplete : function(response) {
												loader.removeClass('ajax-loading');
												result.value = response;
											},
											onFailure : function(error) {
												loader.removeClass('ajax-loading');
												loader.addClass('ajax-error');
												loader.setAttribute('title', error.responseText);
											}
										}
									).request();
							}
						</script>
						<a id="dictionaryLink" href="javascript:getDataDictionary();">
							<?php echo JText::_( 'Get DataDictionary' ); ?>
						</a>
					</td>
				</tr>
				<?php endif ?>
				<tr>
					<td width="20%" class="key">
						<label for="dictionaryquery" id="dictionaryquerylabel">
							<span class="editlinktip hasTip" title="Data Dictionary::Data dictionary for the designer."><?php echo JText::_('Data Dictionary'); ?></span>
						</label>
					</td>
					<td width="80%">
						<textarea class="text_area" name="dictionaryquery" id="dictionaryquery" cols="80" rows="20" style="width:90%"><?php echo $this->row->dictionaryquery;?></textarea>
					</td>
				</tr>
			</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="controller" value="sources" />
	<input type="hidden" name="option" value="com_kbi" />
	<input type="hidden" name="id" value="<?php echo isset($this->row->id) ? $this->row->id : ''; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>