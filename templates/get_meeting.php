<div id="zoom-get-meeting-container">
    <?php if ( isset( $vars['error'] ) ): ?>
        <?php echo __( 'Error', 'zoom' ) ?>: <?php echo $vars['error']; ?>
    <?php else: ?>
        <?php
        $meeting_url = $vars['start_url'] ?: $vars['join_url'];
        $meeting_action = isset( $vars['start_url'] ) ? __( 'Start Meeting', 'zoom' ) : __( 'Join Meeting', 'zoom' );
        ?>
        <a href="<?php echo $meeting_url; ?>" class="zoom-meeting-link"><?php echo $meeting_action; ?></a>
    <?php endif; ?>
</div>