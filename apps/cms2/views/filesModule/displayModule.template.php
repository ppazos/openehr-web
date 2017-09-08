
<?php

global $_base_dir;

if (!function_exists('formatBytes') )
{
    // idem que en edit.view.php
    // http://stackoverflow.com/questions/2510434/php-format-bytes-to-kilobytes-megabytes-gigabytes
    function formatBytes($bytes, $precision = 2)
    { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
    
        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow)); 
    
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }
}


if (count($module->getFiles()) == 0)
{
   echo 'El modulo no tiene archivos registrados';
}

// con esta file_exists no funciona bien, pero funciona para img src...
$ipath_base_img = $_base_dir.'/apps/cms2/images/filesModule/';

// con esta img no funciona bien pero si file_exists
$ipath_base = './apps/cms2/images/filesModule/';
?>

<div class="files_module_filter_container">
Filtrar archivos por texto:
<input type="text" class="files_module_file_filter" name="files_filter_<?php echo $module->getId(); ?>" id="files_filter_<?php echo $module->getId(); ?>" />
</div>

<div id="files_content_<?php echo $module->getId(); ?>">
<?php foreach($module->getFiles() as $file) : ?>
  <div class="files_module_file">
    <div class="files_module_file_icon">
      <?php
        $size = $module->getIconSize();
      
        // con esta img no funciona bien pero si file_exists
        $ipath = $ipath_base.$file->getExtension().'-'.$size.'.png';
        // con esta agarra el icono bien pero no file_exists
        $ipath_src = $ipath_base_img.$file->getExtension().'-'.$size.'.png';
        if (!file_exists($ipath))
        {
          $ipath_src = $ipath_base_img.'default-'.$size.'.png';
        }
        echo '<img src="'.$ipath_src.'" />';
      ?>
    </div>
    <div class="files_module_file_data">
      <div class="files_module_file_name">
        <?php
          if ($module->getShowExtension())
          {
             echo $file->getFullName();
          }
          else
          {
             echo $file->getName();
          }
        ?>
      </div>
      <?php if ($module->getShowDescription()) : ?>
      <div class="files_module_file_description">
        <?php echo $file->getDescription(); ?>
      </div>
      <?php endif; ?>
      <?php if ($module->getShowSize()) : ?>
        <div class="files_module_file_size">
          <?php echo formatBytes($file->getSize(), 0); ?>
        </div>
      <?php endif; ?>
      <?php if ($module->getShowLastUpdate()) : ?>
        <div class="files_module_file_lastupdate">
          <?php echo $file->getLastUpdate(); ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="files_module_file_actions">
      <a href="<?php echo h('url', array('controller'=>'filesModule', 'action'=>'download', 'fileId'=>$file->getId(), 'id'=>$module->getId(), 'zone'=>$zone->getName(), 'pageId'=>$page->getId())); ?>" class="download_file">
        <img src="<?php echo $_base_dir.'/apps/cms2/images/download1.png'; ?>" alt="Descargar archivo" />
      </a>
    </div>
  </div>
<?php endforeach; ?>
</div>

<?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.tableFilter-1.0.0')); ?>
<script type="text/javascript">
$('input[name=files_filter_<?php echo $module->getId(); ?>]').tableFilter( $('#files_content_<?php echo $module->getId(); ?>'), 1 );
</script>

<?php if ($mode=='edit') : ?>
  <div class="customModuleActions">
    <a href="<?php echo h('url', array('controller'=>'filesModule', 'action'=>'edit', 'class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$page->getId(), 'zone'=>$zone->getName())); ?>" class="simple_ajax_link"><?php echo h('img', array('app'=>'cms2', 'src'=>'edit.gif')); ?></a>
  </div>
<?php endif; ?>