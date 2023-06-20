<?php $tmp_only_selected_users_can_assign_for_a_task = get_option('tmp_only_selected_users_can_assign_for_a_task'); ?>

<p><label for="tmp_only_selected_users_can_assign_for_a_task-checkbox">
		<input type="checkbox" id="tmp_only_selected_users_can_assign_for_a_task-checkbox" value="yes" name="tmp_only_selected_users_can_assign_for_a_task" <?php if( $tmp_only_selected_users_can_assign_for_a_task == 'yes' ){ echo 'checked'; } ?> /> <?php _e('Yes', 'task-manager'); ?>
	</label></p>
