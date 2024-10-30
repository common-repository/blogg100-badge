<div class="wrap"><h2><?php print B100EFB_PLUGIN_NAME ." ". B100EFB_CURRENT_VERSION; ?></h2>
<form method="post" action="options.php">
    <?php settings_fields('b100efb-settings-group'); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Badge style</th>
        <td><textarea name="b100efb_style" style="width:400px;height:400px;"><?php echo get_option('b100efb_style'); ?></textarea></td>
        </tr>
        <tr valign="top">
        <th scope="row">Badge placering "left:"</th>
        <td><input type="text" name="b100efb_xOffset" value="<?php echo get_option('b100efb_xOffset'); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Badge placering "top:"</th>
        <td><input type="text" name="b100efb_yOffset" value="<?php echo get_option('b100efb_yOffset'); ?>" /></td>
        </tr>
    </table>
    <?php submit_button(); ?>
</form></div>