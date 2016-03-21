<div class="list-users-container">
    <?php foreach ( $vars['user_list']['users'] as $user ) : ?>
        <p class="list-users-item">
            <div><?php echo __( 'Host ID', 'zoom' ) . ': ' . $user->id; ?></div>
            <div><?php echo __( 'E-mail', 'zoom' ) . ': ' . $user->email; ?></div>
            <div><?php echo __( 'First Name', 'zoom' ) . ': ' . $user->first_name; ?></div>
            <div><?php echo __( 'Last Name', 'zoom' ) . ': ' . $user->last_name; ?></div>
        </p>
    <?php endforeach; ?>
</div>