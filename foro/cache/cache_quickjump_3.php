<?php

if (!defined('PUN')) exit;
define('PUN_QJ_LOADED', 1);
$forum_id = isset($forum_id) ? $forum_id : 0;

?>				<form id="qjump" method="get" action="viewforum.php">
					<div><label><span><?php echo $lang_common['Jump to'] ?><br /></span>
					<select name="id" onchange="window.location=('viewforum.php?id='+this.options[this.selectedIndex].value)">
						<optgroup label="General">
							<option value="10"<?php echo ($forum_id == 10) ? ' selected="selected"' : '' ?>>Presentaciones personales</option>
							<option value="11"<?php echo ($forum_id == 11) ? ' selected="selected"' : '' ?>>Notificaciones</option>
							<option value="4"<?php echo ($forum_id == 4) ? ' selected="selected"' : '' ?>>Cafetería</option>
							<option value="13"<?php echo ($forum_id == 13) ? ' selected="selected"' : '' ?>>Capacitación</option>
							<option value="12"<?php echo ($forum_id == 12) ? ' selected="selected"' : '' ?>>Promociones</option>
						</optgroup>
						<optgroup label="openEHR">
							<option value="2"<?php echo ($forum_id == 2) ? ' selected="selected"' : '' ?>>General</option>
							<option value="15"<?php echo ($forum_id == 15) ? ' selected="selected"' : '' ?>>La comunidad</option>
							<option value="3"<?php echo ($forum_id == 3) ? ' selected="selected"' : '' ?>>Arquetipos</option>
							<option value="5"<?php echo ($forum_id == 5) ? ' selected="selected"' : '' ?>>Modelo de información</option>
							<option value="6"<?php echo ($forum_id == 6) ? ' selected="selected"' : '' ?>>Software</option>
						</optgroup>
						<optgroup label="Estándares">
							<option value="8"<?php echo ($forum_id == 8) ? ' selected="selected"' : '' ?>>openEHR y DICOM</option>
							<option value="14"<?php echo ($forum_id == 14) ? ' selected="selected"' : '' ?>>openEHR y ASTM CCR</option>
						</optgroup>
					</select>
					<input type="submit" value="<?php echo $lang_common['Go'] ?>" accesskey="g" />
					</label></div>
				</form>
