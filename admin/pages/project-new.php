<?php

//if (!current_user_can('upload_files')) {
//    return;
//}

function pr($arg){
    echo "<pre>";
    print_r($arg);
    echo "</pre>";
    exit;
}


global $wpdb; //This is used only if making any database queries


/*--------------------------------------------------------------------
--------------------- Additional Query for create project ------------
---------------------------------------------------------------------*/
// Get Project Category
$project_categories = $wpdb->get_results("
        SELECT pc.id, pc.name
        FROM {$wpdb->prefix}tmp_project_categories as pc
         ");

// Get Project Status
$project_status = $wpdb->get_results("
        SELECT ps.id, ps.name
        FROM {$wpdb->prefix}tmp_project_status as ps
         ");

// Get  User Groups
$user_groups = $wpdb->get_results("
        SELECT ug.id, ug.name
        FROM {$wpdb->prefix}tmp_user_groups as ug
         ");


// Get TMP Users
//$wp_users = $wpdb->get_results("
//        SELECT usr.id as id, usr.display_name as name
//        FROM {$wpdb->prefix}users as usr
//         ");

/** Get TMP Users */
$tmp_users_arr = $wpdb->get_results("SELECT tu.user_id FROM {$wpdb->prefix}tmp_users as tu", ARRAY_A);
$pu_arr = [];
foreach ($tmp_users_arr as $pu){
    $pu_arr[] = $pu['user_id'];
}
if(!empty($pu_arr)){
    $pu_arr = implode(',', $pu_arr);
    $wp_users = $wpdb->get_results("SELECT usr.id as id, usr.display_name as name FROM {$wpdb->prefix}users as usr
            WHERE usr.id IN ({$pu_arr})
         ");
}else{
    $wp_users = [];
}
/** Get TMP Users */

/*--------- END - Additional Query for create project -----------------------*/

?>
<div class="wrap tmp" id="newProject">

    <form name="project-new" method="post" action="?page=projects">

        <input type="hidden" name="act" value="add_project">

        <h1 class="wp-heading-inline"><?php _e( 'Create A New Project', 'task-manager' ); ?></h1>
        <hr class="wp-header-end">
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-16-24 p-u-lg-16-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Project Name', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-image-filter"></span>
                        <input type="text" name="project_name" size="30" value="" spellcheck="true" autocomplete="off">
                    </div>
                    <p><?php _e( 'The name of the project - where you can define your project.', 'task-manager' ); ?></p>
                </div>
            </div>

            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Project Category', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-schedule"></span>
                        <select id="" data-custom="" name="project_category">
                            <?php foreach ($project_categories as $category): ?>
                                <option value="<?php echo $category->id; ?>">
                                    <?php esc_html_e($category->name, 'Task_Manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The type of project you are creating.', 'task-manager' ); ?></p>
                </div>
            </div>
        </div>
        <div class="clr"></div>
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Project Status', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-nametag"></span>
                        <select id="" data-custom="" name="project_status">
                            <?php foreach ($project_status as $status): ?>
                                <option value="<?php echo $status->id; ?>">
                                    <?php esc_html_e($status->name, 'Task_Manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The project status - current status of project.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Project Live Url', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-editor-unlink"></span>
                        <input type="text" name="live_url" size="30" value="" spellcheck="true" autocomplete="off">
                    </div>
                    <p><?php _e( 'Project live url - where you can check the live project.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Project Test Url', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-editor-unlink"></span>
                        <input type="text" name="test_url" size="30" value="" spellcheck="true" autocomplete="off">
                    </div>
                    <p><?php _e( 'The development env url - where you found dev preview.', 'task-manager' ); ?></p>
                </div>
            </div>
        </div>
        <div class="clr"></div>
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Design Date', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <input class="datepicker" type="text" name="design_date" size="30" value="<?php echo date("Y-m-d"); ?>" spellcheck="true"
                               autocomplete="off">
                    </div>
                    <p><?php _e( 'Design completion date for the project.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-input ">
                    <h2 class="title"><?php _e( 'Development Date', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <input class="datepicker" type="text" name="development_date" size="30" value="<?php echo date("Y-m-d"); ?>"
                               spellcheck="true"
                               autocomplete="off">
                    </div>
                    <p><?php _e( 'Development Completion Date.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Project Testing Date', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <input class="datepicker" type="text" name="test_date" size="30" value="<?php echo date("Y-m-d"); ?>" spellcheck="true"
                               autocomplete="off">
                    </div>
                    <p><?php _e( 'Testing phase completion date.', 'task-manager' ); ?></p>
                </div>
            </div>
        </div>
        <div class="clr"></div>
        <div class="wrapper p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-16-24 p-u-lg-16-24">
                <div class="details">
                    <h2 class="title"><?php _e( 'Project Details', 'task-manager' ); ?></h2>
                    <?php wp_editor('', 'projectDetails', array('media_buttons' => true, 'editor_height' => 220, 'textarea_rows' => 10,)); ?>
                </div>
            </div>

            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="assign form-wrap">
<!--                    <h2 class="title">--><?php //_e( 'Members/Groups', 'task-manager' ); ?><!--</h2>-->
                    <h2 class="title"><?php _e( 'Assign User', 'task-manager' ); ?></h2>
<!--                    <div class="user-groups">-->
<!--                        --><?php //foreach ($user_groups as $group): ?>
<!--                            <div class="group-item">-->
<!--                                <input type="checkbox" name="user_group[]"-->
<!--                                       value="--><?php //echo $group->id; ?><!--"> --><?php //echo $group->name; ?>
<!--                            </div>-->
<!--                        --><?php //endforeach; ?>
<!--                    </div>-->
<!--                    <div class="clr"></div>-->
<!--                    <hr>-->
                    <div class="form-input">
                        <div class="input">
                            <span class="dashicons dashicons-search"></span>
                            <input type="text" size="30" value="" id="userSearch"
                                   placeholder="<?php _e( 'Search for specific users..', 'task-manager' ); ?>"
                                   autocomplete="off">
                        </div>
                        <div id="userList" style="display: none;">
                            <ul>
                                <?php foreach ($wp_users as $user): ?>
                                    <li><a href="javascript:void(0)" data-id="<?php echo $user->id; ?>"
                                           data-name="<?php echo $user->name; ?>"><?php echo $user->name; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <p><?php _e( 'Total involved team members.', 'task-manager' ); ?></p>
                        <div class="clr"></div>
                        <div id="userListAdded">
                            <ul>
                            </ul>
                        </div>
                        <input type="hidden" id="finalUserListHere" name="team_members">
                        <div class="clr"></div>
<!-- Commented February 3 2018 -->
<!--                        <div class="form-wrap">-->
<!--                            <h2 class="title">--><?php //_e( 'Prevent to add new user', 'task-manager' ); ?><!--</h2>-->
<!--                            <div class="input">-->
<!--                                <input type="checkbox" name="prevent_add_user" value="1">-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="clr"></div>
                        <div class="form-input">
                            <h2 class="title"><?php _e( 'Project Will Go Live', 'task-manager' ); ?></h2>
                            <div class="input">
                                <span class="dashicons dashicons-calendar-alt"></span>
                                <input class="datepicker" type="text" name="go_live" size="30" value="<?php echo date("Y-m-d"); ?>"
                                       spellcheck="true"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <hr>

        <div class="p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="form-input">
                <div id="submit-action">
                    <span class="spinner"></span>
                    <input type="submit" name="publish" id="publish" class="button button-primary button-large"
                           value="<?php _e( 'Create', 'task-manager' ); ?>">
                </div>
            </div>
        </div>


    </form>

</div>
<!----------------------------------------------------
------------------ Page Specific Script --------------
----------------------------------------------------->
<script>
    jQuery('#wpcontent').css('background', '#ffffff');
    jQuery(document).ready(function () {
        jQuery('.tmp .datepicker').datepicker({
            dateFormat: "yy-mm-dd"
        });
    });
</script>
