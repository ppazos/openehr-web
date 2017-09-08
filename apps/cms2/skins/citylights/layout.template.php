<!--
Design by Free CSS Templates
http://www.freecsstemplates.org
Released for free under a Creative Commons Attribution 2.5 License

Title      : CityLights
Version    : 1.0
Released   : 20081119
Description: A Web 2.0 design with fluid width suitable for blogs and small websites.
Adapted for YuppCMS by: Pablo Pazos <pablo.swp@gmail.com>
-->

<?php YuppLoader::load('apps.cms2.helpers', 'CmsHelpers'); ?>

<div id="top_container">
  <div id="top" class="zone">
    <h1><a href="#">YuppCMS</a></h1>
    <p>Doing it right</p>
    <?php echo $top; ?>
  </div>
  <div id="top_right" class="zone">
    <ul>
      <li class="current_page_item"><a href="#">Home</a></li>
      <li><a href="#">Blog</a></li>
      <li><a href="#">AAA</a></li>
    </ul>
    <?php echo $top_right; ?>
  </div>
</div>

<div id="banner_container">
  <div id="banner"></div>
</div>

<div class="bar">
  <?php echo CmsHelpers::navbar(array('page'=>$page)); ?>
</div>
  
<div id="page">
  <div id="content_container">
    <div id="content" class="zone">
      <?php echo $content; ?>
      
        <!--
        <div class="post">
          <div class="title">
            <h2><a href="#">About this Template</a></h2>
            <p><small>Posted on August 20th, 2007 by <a href="#">Free CSS Templates</a></small></p>
          </div>
          <div class="entry">
            <p>This is <strong>CityLights</strong>, a free, fully standards-compliant CSS template designed by <a href="http://www.freecsstemplates.org/">Free CSS Templates</a>. This free template is released under a <a href="http://creativecommons.org/licenses/by/2.5/">Creative Commons Attributions 2.5</a> license, so you're pretty much free to do whatever you want with it (even use it commercially) provided you keep the links in the footer intact. Aside from that, have fun with it :)</p>
            <p>This template is also available as a <a href="http://www.freewpthemes.net/preview/CityLights">WordPress theme</a> at <a href="http://www.freewpthemes.net/">Free WordPress Themes</a>.</p>
          </div>
          <p class="links">
            <a href="#" class="more">Read More</a>
            <a href="#" class="comments">No Comments</a>
          </p>
        </div>
        <div class="post">
          <div class="title">
            <h2><a href="#">Praesent  condimentum</a></h2>
            <p><small>Posted on August 20th, 2007 by <a href="#">Free CSS Templates</a></small></p>
          </div>
          <div class="entry">
            <p>Etiam arcu dui, faucibus eget, placerat vel, sodales eget, orci. Donec ornare neque ac sem. Mauris aliquet. Aliquam sem leo, vulputate sed, convallis at, ultricies quis, justo. Donec nonummy magna quis risus. Quisque eleifend. Phasellus tempor vehicula justo. Aliquam lacinia metus ut elit. Suspendisse iaculis mauris nec lorem.</p>
            <ol>
              <li><a href="#">Integer sit amet pede vel arcu aliquet pretium.</a></li>
              <li><a href="#">Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</a></li>
            </ol>
          </div>
          <p class="links">
            <a href="#" class="more">Read More</a>
            <a href="#" class="comments">No Comments</a>
          </p>
        </div>
        -->
    </div><!-- content -->
    <div id="right" class="zone">
      <?php echo $right; ?>
        <!--
        <ul>
          <li id="categories">
            <h2>Categories</h2>
            <ul>
              <li><a href="#">Lorem Ipsum</a> (1) </li>
              <li><a href="#">Uncategorized</a> (4) </li>
            </ul>
          </li>
          <li>
            <h2>Lorem Ipsum Dolor</h2>
            <ul>
              <li><a href="#">Nulla luctus eleifend purus</a></li>
              <li><a href="#">Praesent  scelerisque </a></li>
              <li><a href="#">Fusce ultrices fringilla metus</a></li>
            </ul>
          </li>
          <li>
            <h2>Lorem Ipsum Dolor</h2>
            <ul>
              <li><a href="#">Pellentesque tempus nulla</a></li>
              <li><a href="#">Fusce ultrices fringilla metus</a></li>
            </ul>
          </li>
        </ul>
        -->
    </div><!-- right -->
  </div><!-- content_container -->
</div><!-- page -->

<div class="bar">
  <?php echo CmsHelpers::subpages(array('page'=>$page)); ?>
</div>

<div id="footer_container">
  <div id="footer" class="zone"><?php echo $footer; ?></div>
</div>
