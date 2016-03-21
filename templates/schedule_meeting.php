<div id="schedule-meeting-container">
    <div id="schedule-meeting-form">
        <input id="meeting-type" type="hidden" value="<?php echo \Zoom\Meeting::TYPE_SCHEDULED; ?>" />
        <div class="form-item">
            <label for="meeting-topic"><?php echo __( 'Meeting Topic', 'zoom' ); ?></label>
            <input id="meeting-topic" type="text" />
        </div>
        <div class="form-item">
            <label for="meeting-date"><?php echo __( 'Meeting Date', 'zoom' ); ?></label>
            <input id="meeting-date" type="text" />
        </div>
        <div class="form-item">
            <label for="meeting-time"><?php echo __( 'Meeting Time', 'zoom' ); ?></label>
            <input id="meeting-time" type="time" />
            <label for="meeting-timezone"><?php echo __( 'Meeting Timezone', 'zoom' ); ?></label>
            <select id="meeting-timezone" name="timezone">
                <?php foreach ( $vars['timezones'] as $timezone => $label ) : ?>
                    <option value="<?php echo $timezone; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-item">
            <label for="meeting-duration"><?php echo __( 'Meeting Duration', 'zoom' ); ?></label>
            <input id="meeting-duration" type="number" />
        </div>
        <div class="form-item">
            <a href="" id="schedule-meeting" class="schedule-meeting-link buttn"><?php echo __( 'Schedule a Meeting', 'zoom' ); ?></a>
        </div>
    </div>
    <div id="schedule-meeting-response"></div>
</div>