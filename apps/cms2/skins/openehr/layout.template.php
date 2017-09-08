<?php YuppLoader::load('apps.cms2.helpers', 'CmsHelpers'); ?>

<!-- zonas: top, col1, col2, col3, content, footer -->
<div id="top_bg">
  <div id="top_container">
    <div id="top">
      <a href="https://www.openehr.org.es" title="Comunidad de openEHR en español">&nbsp;</a>
    </div>
    <div id="top_right" class="zone">
      <h2>Trabajando juntos por una mejor salud para todos</h2>
      <?php echo $top_right; ?>
    </div>
  </div>
</div>

<div id="body_container">
  <?php echo CmsHelpers::navbar(array('page'=>$page)); ?>
<div id="body"><!-- table -->

  <!-- http://css-tricks.com/fluid-width-equal-height-columns/ -->
  <div id="container1"><!-- row -->
    <div id="col1" class="zone"><?php echo $col1; ?></div><!-- cell -->
    <div id="col2" class="zone"><?php echo $col2; ?></div><!-- cell -->
    <div id="col3" class="zone"><?php echo $col3; ?></div><!-- cell -->
  </div>
  <div id="container2"><!-- row -->
    <div id="content" class="zone"><?php echo $content; ?></div><!-- cell -->
  </div>
</div>
  <?php echo CmsHelpers::subpages(array('page'=>$page), true); ?>
</div>

<div id="footer_container">
  <div id="footer" class="zone"><?php echo $footer; ?></div>
</div>

<script type="text/javascript">
  var _gaq = _gaq || [];_gaq.push(['_setAccount', 'UA-29870987-1']);_gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>