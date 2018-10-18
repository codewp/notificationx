<?php 
    $class_name = '';
    if( $display_type ) {
        $class_name = 'fomopress-notification-preview-' . $display_type;
    }
    $settings = FomoPress_MetaBox::get_metabox_settings( $post->ID );
    echo FomoPress_Public::generate_css( $settings );
?>
<div id="fomopress-notification-preview" class="<?php echo $class_name; ?>">
    <div class="fomopress-notification-preview fomopress-notification-preview-conversions <?php echo FomoPress_Extension::get_classes( $settings ); ?>">
        <div class="fomopress-preview-inner <?php echo FomoPress_Extension::get_classes( $settings, 'inner' ); ?>">
            <div class="fomopress-preview-image">
                <img src="<?php echo FOMOPRESS_ADMIN_URL . 'assets/img/placeholder-300x300.png'; ?>" alt="">
            </div>
            <div class="fomopress-preview-content">
                <span class="fomopress-preview-row fomopress-preview-first-row fomopress-highlight"><?php _e( 'John D. recently purchased', 'fomopress' ); ?></span>
                <span class="fomopress-preview-row fomopress-preview-second-row"><?php _e( 'Example Product', 'fomopress' ); ?></span>
                <span class="fomopress-preview-row fomopress-preview-third-row"><?php _e( '1 hour ago', 'fomopress' ); ?></span>
            </div>
            <span class="fomopress-preview-close">x</span>
        </div>
    </div>

    <div class="fomopress-notification-preview fomopress-notification-preview-comments <?php echo FomoPress_Extension::get_classes( $settings ); ?>">
        <div class="fomopress-preview-inner <?php echo FomoPress_Extension::get_classes( $settings, 'inner' ); ?>">
            <div class="fomopress-preview-image">
                <img src="<?php echo FOMOPRESS_ADMIN_URL . 'assets/img/placeholder-300x300.png'; ?>" alt="">
            </div>
            <div class="fomopress-preview-content">
                <span class="fomopress-preview-row fomopress-preview-first-row fomopress-highlight"><?php _e( 'John D. posted a comment', 'fomopress' ); ?></span>
                <span class="fomopress-preview-row fomopress-preview-second-row"><?php _e( 'on Example Post Title', 'fomopress' ); ?></span>
                <span class="fomopress-preview-row fomopress-preview-third-row"><?php _e( '1 hour ago', 'fomopress' ); ?></span>
            </div>
            <span class="fomopress-preview-close">x</span>
        </div>
    </div>
</div>