<?php /**  * @copyright	Copyright (C) 2011 JoomlaThemes.co - All Rights Reserved. **/
defined( '_JEXEC' ) or die( 'Restricted access' );
define( 'YOURBASEPATH', dirname(__FILE__) );
?>
<?php // no direct access 
defined( '_JEXEC' ) or die( 'Restricted access' ); 
/*
$footertext	= $this->params->get("footertext");
$footerdisable	= $this->params->get("footerdisable");
$googleanalytics	= $this->params->get("googleanalytics");
$analyticsdisable	= $this->params->get("analyticsdisable");
  */
JHTML::_('behavior.framework', true);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
  <jdoc:include type="head" />
    <!--<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/basicTemplate.css" type="text/css" />-->
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/styles.css" type="text/css" />
  <!-- google analytics -->
  <?php
   /*if ($this->params->get( 'analyticsdisable' )){ ?>
    <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', '<?php echo ($googleanalytics); ?>']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>
  <?php } */?>
  <!-- //google analytics -->

  <!--[if lt IE 9]>
    <script src="<?php echo $this->baseurl?>/templates/<?php echo $this->template?>/js/html5ie.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl?>/templates/<?php echo $this->template?>/css/ie8.css" />

  <![endif]-->
  
  <!-- sewebar specific imports-->
    <link rel="stylesheet" href="<?php echo $this->baseurl?>/xml/bkef/css/bkef.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->baseurl?>/xml/pmml/css/pmml.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->baseurl?>/xml/pmml/css/navigation.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->baseurl?>/components/com_kbi/assets/kbi.css" type="text/css" />
    <script type="text/javascript" src="<?php echo $this->baseurl?>/xml/pmml/js/showhide.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseurl?>/xml/pmml/js/graph_url.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseurl?>/xml/pmml/js/ajax.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseurl?>/xml/bkef/js/influences.js"></script>
  <!-- //sewebar specific imports-->
</head>

<body>
  <header class="page">
      <div class="content">
        <h1 onclick="location.href='<?php echo $this->baseurl;?>/';">EasyMiner <span class="slogan">easy association rule mining</span></h1>
      </div>
  </header>

  <div id="body">
    <div id="right">
      <nav id="menu">
        <jdoc:include type="modules" name="menu" style="jaw" />
      </nav>
      <jdoc:include type="modules" name="right" style="aside" />
    </div>

    <div id="main">
      <?php if ($this->countModules('breadcrumb')){ ?>
        <div id="breadcrumbs">
          <jdoc:include type="modules" name="breadcrumb"  style="none"/>
        </div>
      <?php } ?>
      <jdoc:include type="message" />
      <jdoc:include type="component" />
    </div>
  </div>

  <div id="footerPad"></div>

  <footer class="page">
    <jdoc:include type="modules" name="copyright"/>
    <div id="footerText">
      <?php if ($this->params->get( 'footerdisable' )){ ?><?php echo $footertext; ?><?php } ?>
      Copyright &copy; <a href="http://kizi.vse.cz" title="Department of Information and Knowledge Engineering">KIZI</a>, <a href="http://www.vse.cz">University of Economics</a>, Prague, 2013<?php $year=date('Y');if ($year>2013){echo '&nbsp;-&nbsp;'.$year;}?>
    </div>
  </footer>


</body>
</html>