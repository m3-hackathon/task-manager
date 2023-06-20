<?php

//if (!current_user_can('delete_pages')) {
//    return;
//}

$modify_ticket_id = filter_input( INPUT_GET, 'ticket', FILTER_SANITIZE_NUMBER_INT );
if(!$modify_ticket_id){
    do_action( 'admin_page_access_denied' );
    wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
}

function pr($arg)
{
//    echo "<pre>";
//    print_r($arg);
//    echo "</pre>";
//    exit;
}


global $wpdb; //This is used only if making any database queries


// Info For Modify Ticket Info
$eTickets = $wpdb->get_results("
        SELECT tt.id, tt.name, tt.description, tt.ticket_type_id, tt.ticket_status_id, tt.ticket_for, 
            tt.created_by, tt.last_update_by
            FROM {$wpdb->prefix}tmp_tickets tt WHERE tt.id = {$modify_ticket_id} limit 1
         ");
if(!empty($eTickets) && count($eTickets)){
    $eTicket = $eTickets[0];
}else{
    wp_die( __( 'Sorry, no ticket found to edit ticket.' ), 403 );
}

/*--------------------------------------------------------------------
--------------------- Additional Query for create project ------------
---------------------------------------------------------------------*/
// Get Project Category
$ticket_types = $wpdb->get_results("
        SELECT tt.id, tt.name
        FROM {$wpdb->prefix}tmp_ticket_types as tt
         ");

// Get Project Status
$project_status = $wpdb->get_results("
        SELECT ps.id, ps.name
        FROM {$wpdb->prefix}tmp_project_status as ps
         ");

// Get  User Groups
$tmp_usersArr = array();
$tmp_users = $wpdb->get_results("SELECT tu.user_id FROM {$wpdb->prefix}tmp_users as tu");
foreach ($tmp_users as $tmp_user) { $tmp_usersArr[] = $tmp_user->user_id; }

$get_system_user_query = new WP_User_Query(array('fields' => array('ID', 'display_name'), 'exclude' => $tmp_usersArr));
$wp_system_users = $get_system_user_query->get_results();

//pr($wp_system_users);


/*--------- END - Additional Query for create project -----------------------*/

?>
<div class="wrap tmp" id="newProject">

    <form name="project-new" method="post" action="?page=tickets">

        <input type="hidden" name="act" value="edit_ticket">
        <input type="hidden" name="ticket_id" value="<?php echo $modify_ticket_id; ?>">

        <h1 class="wp-heading-inline"><?php _e( 'Create A New Ticket', 'task-manager' ); ?></h1>
        <hr class="wp-header-end">
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">

                <div class="form-input">
                    <h2 class="title"><?php _e( 'Subject', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-sos"></span>
                        <input type="text" name="name" size="30" value="<?php echo $eTicket->name; ?>" spellcheck="true" autocomplete="off">
                    </div>
                    <p><?php _e( 'The name of the ticket - where you can define customer ticket.', 'task-manager' ); ?></p>
                </div>

        </div>
        <div class="clr"></div>
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-12-24 p-u-lg-12-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Type', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <select id="" data-custom="" name="ticket_type_id">
                            <?php foreach ($ticket_types as $type): ?>
                                <option value="<?php echo $type->id; ?>" <?php if ($type->id == $eTicket->ticket_type_id) {
                                    echo 'selected';
                                } ?>>
                                    <?php esc_html_e($type->name, 'task-manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The ticket type - type of the ticket.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-12-24 p-u-lg-12-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Status', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-welcome-view-site"></span>
                        <select id="" data-custom="" name="ticket_status_id">
                            <?php foreach ($project_status as $status): ?>
                                <option value="<?php echo $status->id; ?>" <?php if ($status->id == $eTicket->ticket_status_id) {
                                    echo 'selected';
                                } ?>>
                                    <?php esc_html_e($status->name, 'task-manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The ticket status - current status of the ticket.', 'task-manager' ); ?></p>
                </div>
            </div>
        </div>
        <div class="clr"></div>
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-16-24 p-u-lg-16-24">
                <div class="details">
                    <h2 class="title"><?php _e( 'Details', 'task-manager' ); ?></h2>
                    <?php wp_editor($eTicket->description, 'description', array('media_buttons' => true, 'editor_height' => 220, 'textarea_rows' => 10,)); ?>
                </div>
            </div>

            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Ticket For', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-admin-users"></span>
                        <select id="" data-custom="" name="ticket_for">
                            <?php foreach ($wp_system_users as $user): ?>
                                <option value="<?php echo $user->ID; ?>" <?php if ( $user->ID == $eTicket->ticket_for ) {
                                    echo 'selected';
                                } ?>>
                                    <?php esc_html_e($user->display_name, 'task-manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'Select any user who is not included in the ticket.', 'task-manager' ); ?></p>
                </div>
            </div>
        </div>

        <hr>

        <div class="p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="form-input">
                <div id="submit-action">
                    <span class="spinner"></span>
                    <input type="submit" name="publish" id="publish" class="button button-primary button-large"
                           value="<?php _e('Submit', 'task-manager'); ?>">
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
