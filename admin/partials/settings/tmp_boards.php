<?php

$tmp_boards = get_option('tmp_boards', ['todo', 'in_progress', 'review', 'done']);

?>
<p>
	<label for="tmp_board_todo">
		<input type="checkbox" id="tmp_board_todo-checkbox" value="todo" name="tmp_boards[]" <?php if( in_array('todo', $tmp_boards) ){ echo 'checked'; } ?> /> <?php _e('TODO', 'task-manager'); ?>
	</label> &nbsp; &nbsp;
	<label for="tmp_board_in_progress-checkbox">
		<input type="checkbox" id="tmp_board_in_progress-checkbox" value="in_progress" name="tmp_boards[]" <?php if( in_array('in_progress', $tmp_boards) ){ echo 'checked'; } ?> /> <?php _e('In Progress', 'task-manager'); ?>
	</label> &nbsp; &nbsp;
	<label for="tmp_board_review-checkbox">
		<input type="checkbox" id="tmp_board_review-checkbox" value="review" name="tmp_boards[]" <?php if( in_array('review', $tmp_boards) ){ echo 'checked'; } ?> /> <?php _e('Review', 'task-manager'); ?>
	</label> &nbsp; &nbsp;
	<label for="tmp_board_done-checkbox">
		<input type="checkbox" id="tmp_board_done-checkbox" value="done" name="tmp_boards[]" <?php if( in_array('done', $tmp_boards) ){ echo 'checked'; } ?> /> <?php _e('Done', 'task-manager'); ?>
	</label>
</p>
