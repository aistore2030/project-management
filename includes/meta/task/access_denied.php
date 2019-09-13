<?php

function pto_task_denied_metabox_callback( $post ) {

	echo '<p style="padding:20px">' . __('ACCESS DENIED: You are not assigned to this task.', 'cqpim') . '</p>';

}