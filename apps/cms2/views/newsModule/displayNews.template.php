<?php
if ($module->getPaged()) :
  $newsarr = $module->getNewsPaged($offset);
else :
  $newsarr = $module->getNews();
endif;
?>
<?php foreach ($newsarr as $news) : ?>
  <div class="news" style="margin-bottom: 10px; border: 1px solid #CEE3F8; margin-top: 5px;">
    <div class="news_title" style="font-weight: bold; background-color: #CEE3F8; padding: 5px;"><?php echo $news->getTitle(); ?></div>
    <div class="news_text" style="margin-bottom: 5px; padding: 5px;"><?php echo $news->getText(); ?></div>
    <!-- TODO: agregar a YuppDate, que el formato sea configurable -->
    <span class="news_date" style="font-size: 11px; display: inline;"><?php echo date("d/m/Y @ H:i", strtotime($news->getCreationDate()) ); ?></span>
    <?php if ($news->getLink() != NULL) : ?>
      <span class="news_link" style="font-size: 11px; display: inline; float: right;"><a href="<?php echo $news->getLink(); ?>">ver m&aacute;s...</a></span>
    <?php endif; ?>
  </div>
<?php endforeach; ?>