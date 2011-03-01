<?php defined('_JEXEC') or die('Restricted access'); ?>

<table class="contentpaneopen<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<tr>
		<td>
		<?php
		foreach( $this->results as $result ) : ?>
			<fieldset>
				<div>
					<span class="small<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
						<?php echo '# '.$this->pagination->limitstart + $result->count.', ';?>
					</span>
					<span class="small<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
						<strong><?php echo JText::_('HITSCORE ').(round($result->score*100,2)).'%, '.JText::_('RATING')." ".$result->rating;?></strong>
					</span>
					<?php if ( $result->href ) :
						?>
							<a href="<?php echo JRoute::_($result->href); ?>">
						<?php 

						echo $this->escape($result->title);

						if ( $result->href ) : ?>
							</a>
						<?php endif;
						if ( $result->sectionid ) : ?>
							<br />
							<span class="small<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
								(<?php echo $this->escape($result->sectionid); ?>)
							</span>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<div>
					<?php echo JText::sprintf('RULENUMBER',$result->position+1); ?><br/>
					<?php echo JText::sprintf('RULE',$result->Text); ?><br/>
				</div>
				<?php
					if ( $this->params->get( 'show_date' )) : ?>
				<div class="small<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
					<?php echo $result->created; ?>
				</div>
				<?php endif; ?>
			</fieldset>
		<?php endforeach; ?>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<div align="center">
				<?php echo $this->pagination->getPagesLinks( ); ?>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<div align="center">
				<?php echo $this->serviceLink; ?>
			</div>
		</td>
	</tr>
</table>
