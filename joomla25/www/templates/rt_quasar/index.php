<?php
/**
 * @package Quasar Template - RocketTheme
 * @version 1.0 September 11, 2011
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

// load and inititialize gantry class
require_once('lib/gantry/gantry.php');
$gantry->init();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $gantry->language; ?>" lang="<?php echo $gantry->language;?>" >
<head>
	<?php 
		$gantry->displayHead();
		$gantry->addStyles(array('template.css','joomla.css','style.css','typography.css'));
	?>
</head>
	<body <?php echo $gantry->displayBodyTag(array('backgroundLevel','bodyLevel')); ?>>
		<?php /** Begin Top **/ if ($gantry->countModules('top')) : ?>
		<div id="rt-top">
			<div class="rt-container">
				<div id="rt-top2">
					<?php echo $gantry->displayModules('top','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<?php /** End Top **/ endif; ?>
		<?php /** Begin Header **/ if ($gantry->countModules('header')) : ?>
		<div id="rt-header">
			<div class="rt-container">
				<div id="rt-header2">
					<div id="rt-header3">
						<div id="rt-header4">
							<?php echo $gantry->displayModules('header','standard','standard'); ?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php /** End Header **/ endif; ?>
		<?php /** Begin Showcase **/ if ($gantry->countModules('showcase')) : ?>
		<div id="rt-showcase">
			<div class="rt-container">
				<?php echo $gantry->displayModules('showcase','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Showcase **/ endif; ?>
		<?php /** Begin Feature **/ if ($gantry->countModules('feature')) : ?>
		<div id="rt-feature">
			<div class="rt-container">
				<?php echo $gantry->displayModules('feature','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Feature **/ endif; ?>
		<?php /** Begin Top Tab **/ if ($gantry->countModules('ttab')) : ?>
		<div id="rt-toptab">
			<div class="rt-container">
				<?php echo $gantry->displayModules('ttab','basic','basic'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Top Tab **/ endif; ?>
		<?php /** Begin Main Body **/ ?>
		<div id="rt-main-surround">
			<?php /** Begin Main Top **/ if ($gantry->countModules('maintop')) : ?>
			<div id="rt-maintop">
				<div class="rt-container">
					<?php echo $gantry->displayModules('maintop','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Main Top **/ endif; ?>
			<?php /** Begin Breadcrumbs **/ if ($gantry->countModules('breadcrumb')) : ?>
			<div id="rt-breadcrumbs">
				<div class="rt-container">
					<?php echo $gantry->displayModules('breadcrumb','basic','breadcrumbs'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Breadcrumbs **/ endif; ?>
			<?php /** Begin Main Body Columns **/ ?>
		    <?php echo $gantry->displayMainbody('mainbody','sidebar','standard','standard','standard','standard','standard'); ?>
			<?php /** End Main Body Columns **/ ?>
			<?php /** Begin Main Bottom **/ if ($gantry->countModules('mainbottom')) : ?>
			<div id="rt-mainbottom">
				<div class="rt-container">
					<?php echo $gantry->displayModules('mainbottom','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Main Bottom **/ endif; ?>
		</div>
		<div class="clear"></div>
		<?php /** End Main Body **/ ?>
		<?php /** Begin Bottom Tab **/ if ($gantry->countModules('btab') and $gantry->countModules('bottom')) : ?>
		<div id="rt-bottomtab">
			<div class="rt-container">
				<?php echo $gantry->displayModules('btab','basic','basic'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Bottom Tab **/ endif; ?>
		<?php /** Begin Bottom **/ if ($gantry->countModules('bottom')) : ?>
		<div id="rt-bottom">
			<div class="rt-container">
				<?php echo $gantry->displayModules('bottom','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Bottom **/ endif; ?>
		<?php /** Begin Footer **/ if ($gantry->countModules('footer')) : ?>
		<div id="rt-footer">
			<div class="rt-container">
				<?php echo $gantry->displayModules('footer','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Footer **/ endif; ?>
		<?php /** Begin Copyright **/ if ($gantry->countModules('copyright')) : ?>
		<div id="rt-copyright">
			<div class="rt-container">
				<?php echo $gantry->displayModules('copyright','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Copyright **/ endif; ?>
		<?php /** Begin Debug **/ if ($gantry->countModules('debug')) : ?>
		<div id="rt-debug">
			<div class="rt-container">
				<?php echo $gantry->displayModules('debug','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Debug **/ endif; ?>
	</body>
</html>
<?php 
$gantry->finalize();
?>