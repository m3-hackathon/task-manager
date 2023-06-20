<?php

//if (!current_user_can('upload_files')) {
//    return;
//}


global $wpdb; //This is used only if making any database queries

$task_boards = get_option('tmp_boards', ['todo', 'in_progress', 'review', 'done']);
$boards = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'];


if(isset($_REQUEST['project']) && !empty($_REQUEST['project'])){
    $selected_project = $_REQUEST['project'];
}

/*--------------------------------------------------------------------
--------------------- Additional Query for create task ------------
---------------------------------------------------------------------*/
// Get Project Name
$projects = $wpdb->get_results("
        SELECT pr.id, pr.name
        FROM {$wpdb->prefix}tmp_projects as pr
         ");

// Get task Types
$task_types = $wpdb->get_results("
        SELECT tt.id, tt.name
        FROM {$wpdb->prefix}tmp_task_types as tt
         ");

// Get task Types
$task_labels = $wpdb->get_results("
        SELECT tl.id, tl.name
        FROM {$wpdb->prefix}tmp_task_labels as tl
         ");

// Get task Status
$task_status = $wpdb->get_results("
        SELECT ts.id, ts.name, ts.group_name
        FROM {$wpdb->prefix}tmp_task_status as ts
        ORDER BY ts.group_name DESC
         ");

// Get Task Priority
$task_priority = $wpdb->get_results("
        SELECT tp.id, tp.name, tp.icon
        FROM {$wpdb->prefix}tmp_task_priorities as tp
         ");

// Get  User Groups
$user_groups = $wpdb->get_results("
        SELECT ug.id, ug.name
        FROM {$wpdb->prefix}tmp_user_groups as ug
         ");


/** Get User by Project */
$tmp_users = [];
$tmp_user_by_project_option = get_option('tmp_only_selected_users_can_assign_for_a_task');
$tmp_users_arr = $wpdb->get_results("SELECT tu.user_id FROM {$wpdb->prefix}tmp_users as tu", ARRAY_A);
$pu_arr = [];
foreach ($tmp_users_arr as $pu){
    $pu_arr[] = $pu['user_id'];
}
if(!empty($pu_arr)){
    $pu_arr = implode(',', $pu_arr);
    $tmp_users = $wpdb->get_results("SELECT usr.id as id, usr.display_name as name FROM {$wpdb->prefix}users as usr WHERE usr.id IN ({$pu_arr})");
}
/** Get User by Project */



?>
<div class="wrap tmp" id="newTask">

    <form name="project-new" method="post" action="?page=tasks">

        <input type="hidden" name="act" value="add_task">

        <h1 class="wp-heading-inline"><?php _e( 'Create a New Task', 'task-manager' ); ?></h1>
        <hr class="wp-header-end">
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Task Name', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-editor-paste-text"></span>
                        <input type="text" name="task_name" size="30" value="" placeholder="<?php _e( 'Your task name...', 'task-manager' ); ?>"
                               spellcheck="true"
                               autocomplete="off">
                    </div>
                    <p><?php _e( 'The name of the task - where you can define your task.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Task Status', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-editor-expand"></span>
                        <select id="" data-custom="" name="task_status">
                            <?php foreach ($task_status as $status): ?>
                                <option value="<?php echo $status->id; ?>">
                                    <?php esc_html_e($status->name, 'Task_Manager'); ?>
                                    (<?php echo $status->group_name; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The task status - current status of task.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Priority', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-chart-bar"></span>
                        <select id="" data-custom="" name="task_priority">
                            <?php foreach ($task_priority as $priority): ?>
                                <option value="<?php echo $priority->id; ?>">
                                    <?php esc_html_e($priority->name, 'Task_Manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'Set the priority of the task.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Board', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-clipboard"></span>
                        <select id="" data-custom="" name="board_name">
                            <?php foreach ($task_boards as $task_board): ?>
                                <option value="<?php echo $task_board; ?>">
                                    <?php esc_html_e($boards[$task_board], 'Task_Manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The task status - current status of task.', 'task-manager' ); ?></p>
                </div>
            </div>
        </div>
        <div class="clr"></div>
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Project', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-image-filter"></span>

                        <select id="selectProject" data-custom="" name="project_id">
                            <?php foreach ($projects as $project): ?>
                                <option value="<?php echo $project->id; ?>"
                                    <?php if(isset($selected_project) && $selected_project = $project->id ){ echo 'selected';} ?>
                                >
                                    <?php esc_html_e($project->name, 'Task_Manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The name of the project - where you can define your project.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Task Type', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-welcome-widgets-menus"></span>
                        <select id="" data-custom="" name="task_type">
                            <?php foreach ($task_types as $task_type): ?>
                                <option value="<?php echo $task_type->id; ?>">
                                    <?php esc_html_e($task_type->name, 'Task_Manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The type of project you are creating.', 'task-manager' ); ?></p>
                </div>
            </div>

            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Label', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-editor-spellcheck"></span>
                        <select id="" data-custom="" name="task_label">
                            <?php foreach ($task_labels as $task_label): ?>
                                <option value="<?php echo $task_label->id; ?>">
                                    <?php esc_html_e($task_label->name, 'Task_Manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The type of project you are creating.', 'task-manager' ); ?></p>
                </div>
            </div>

            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Amount', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-money-alt"></span>
                        <input type="text" name="amount" size="30" value="" placeholder="<?php _e( 'e.g. 10', 'task-manager' ); ?>"
                               spellcheck="true"
                               autocomplete="off">
                    </div>
                    <p><?php _e( 'The budget amount for this task', 'task-manager' ); ?></p>
                </div>
            </div>
        </div>
        <div class="clr"></div>
        <input type="hidden" name="task_progress" value="0" />
        <div class="clr"></div>
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Estimation Time', 'task-manager' ); ?></h2>
                    <div class="input t__s">
                        <span class="dashicons dashicons-clock"></span>
<!--                        <input type="text" name="estimation_time" size="30" value=""-->
<!--                               placeholder="--><?php //_e( 'Estimation time with any format...', 'task-manager' ); ?><!--" spellcheck="true" autocomplete="off">-->
                        <div class="time_selection">
                            <select id="estimateHour">
                                <?php
                                for($i = 0; $i<=200; $i++) {
                                    echo '<option value="'.$i.'">';
                                    echo $i . ($i >1 ?' hours':' hour');
                                    echo '</option>';
                                }
                                ?>
                            </select>
                            <select id="estimateMinute">
                                <?php
                                for($i = 0; $i<=59; $i++) {
	                                echo '<option value="'.$i.'">';
	                                echo $i . ($i >1 ?' minutes':' minute');
	                                echo '</option>';
                                }
                                ?>
                            </select>
                            <input type="hidden" name="estimation_time" id="estimationTime" value="0:0">
                        </div>
                    </div>
                    <p><?php _e( 'The estimation for the task.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Start Date', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <input class="datepicker" type="text" name="start_date" size="30" value="<?php echo date('Y-m-d'); ?>" spellcheck="true"
                               autocomplete="off">
                    </div>
                    <p><?php _e( 'The start date for the task.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Due Date', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <input class="datepicker" type="text" name="due_date" size="30" value="<?php echo date('Y-m-d'); ?>" spellcheck="true"
                               autocomplete="off">
                    </div>
                    <p><?php _e( 'The due date for the task.', 'task-manager' ); ?></p>
                </div>
            </div>
        </div>
        <div class="clr"></div>

        <div class="wrapper p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-16-24 p-u-lg-16-24">
                <div class="details">
                    <h2 class="title"><?php _e( 'Task Details', 'task-manager' ); ?></h2>
                    <?php wp_editor('', 'taskDetails', array('media_buttons' => true, 'editor_height' => 220, 'textarea_rows' => 10,)); ?>
                </div>
            </div>

            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="assign form-wrap">
                    <h2 class="title"><?php _e( 'Assign to', 'task-manager' ); ?></h2>
                    <div class="form-input">
                        <div class="input">
                            <span class="dashicons dashicons-search"></span>
                            <input type="text" size="30" value="" id="userSearch"
                                   placeholder="<?php _e( 'Search for specific users..', 'task-manager' ); ?>"
                                   autocomplete="off">
                        </div>
                    </div>

                    <div id="userList" style="display: none;">
                        <ul>
                            <?php foreach ($tmp_users as $user): ?>
                                <li><a href="javascript:void(0)" data-id="<?php echo $user->id; ?>"
                                       data-name="<?php echo $user->name; ?>"><?php echo $user->name; ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <p><?php _e( 'You can only select users who are currently involve on the selected project.', 'task-manager' ); ?></p>
                    <div class="clr"></div>
                    <div id="userListAdded">
                        <ul>
                        </ul>
                    </div>
                    <input type="hidden" id="finalUserListHere" name="team_members">
                    <div class="clr"></div>

                </div>
            </div>
        </div>

        <hr>
        <div class="form-input">
            <div id="submit-action">

                <span class="spinner"></span>
                <input type="submit" name="publish" id="publish" class="button button-primary button-large"
                       value="<?php _e( 'Create', 'task-manager' ); ?>">

            </div>
        </div>
    </form>

</div>
<!----------------------------------------------------
------------------ Page Specific Script --------------
----------------------------------------------------->
<script>
    var tmp_user_by_project_option = "<?php echo $tmp_user_by_project_option; ?>";
    jQuery('#wpcontent').css('background', '#ffffff');
    jQuery(document).ready(function () {
        jQuery('.tmp .datepicker').datepicker({
            dateFormat: "yy-mm-dd"
        });

        var esmSel = jQuery("#estimateMinute");
        var eshSel = jQuery("#estimateHour");

        eshSel.on('change', function (){
            jQuery('#estimationTime').val(jQuery(this).val()+':' + esmSel.val());
        })

        esmSel.on('change', function (){
            jQuery('#estimationTime').val(eshSel.val() + ':' + jQuery(this).val());
        })

        /** Project Users */
        function getUserByProjectId(project_id){
            var userListUl = jQuery('.tmp #userList ul');
            userListUl.html('');
            var userData = [];
            if( !!project_id && typeof ajax_object.ajax_url !== 'undefined' ){
                jQuery.ajax({
                    type:"POST",
                    url: ajax_object.ajax_url,
                    data:{
                        'action':'get_user_by_project_id',
                        'project_id': project_id
                    },
                    success:function(data){
                        data = JSON.parse(data);
                        if(data.success){
                            userData = data.data;
                        }
                        userData.forEach(function (user){
                            userListUl.append(`<li><a href="javascript:void(0)" data-id="${user.id}" data-name="${user.name}">${user.name}</a></li>`);
                        })
                    },
                    error: function(errorThrown){
                        console.log(errorThrown);
                    }
                });
            }
        }

        var selectProject = jQuery("#selectProject");
        if(tmp_user_by_project_option == 'yes' && jQuery(selectProject).val()){
            getUserByProjectId(jQuery(selectProject).val());
        }

        if(tmp_user_by_project_option == 'yes'){
            selectProject.on('change', function (){
                var project_id = jQuery(this).val();
                getUserByProjectId(project_id);
            })
        }
        /** Project Users */
    });

</script>

