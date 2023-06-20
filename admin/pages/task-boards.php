<?php

global $wpdb;

$currentUserId = wp_get_current_user()->ID;

$boardTitles = [
       'todo' => __('Todo', 'task-manager'),
       'in_progress' => __('In Progress', 'task-manager'),
       'review' => __('Review', 'task-manager'),
       'done' => __('Done', 'task-manager'),
];
$task_boards = get_option('tmp_boards', ['todo', 'in_progress', 'review', 'done']);
$t_boards = [];
foreach ($task_boards as $tb){
    $t_boards[] = ['title' => $boardTitles[$tb], 'slug' => $tb];
}


/*------------------------------------ For Manage Table Data -----------------------------------*/
require_once plugin_dir_path(__FILE__) . '../partials/task-list-table-class.php';
/**
 * Task Table
 */
//Create an instance of our package class...
$taskTableData = new Task_Manager_Table();
//Fetch, prepare, sort, and filter our data...
$taskTableData->prepare_items();
/*------------------------------------ For Manage Table Data -----------------------------------*/
$icon_dir_url = plugins_url('../images/icons/', __FILE__);
$kanban_js = plugins_url('../js/jkanban.js', __FILE__);


?>

<script src="<?= $kanban_js ?>"></script>

<div id="tmpBoard"></div>

<script>
    var taskItems = <?php echo json_encode($taskTableData) ?>;
    var boards =  <?php echo json_encode($t_boards) ?>;

    var boardTasks = { 'todo': [],  'in_progress': [], 'review': [], 'done': [] }

    for(var i = 0, ti = taskItems.items; i < taskItems.items.length; i++ ){
        if(!!ti[i].board_name){
            boardTasks[ti[i].board_name].push({
                id: ti[i].id,
                title: ti[i].name,
                due_date: ti[i].due_date,
                amount: 'amount' in ti[i] ? ti[i].amount : '',
                project_id: ti[i].project_id,
                project_name: ti[i].project_name,
                board_name: ti[i].board_name,
                task_members: ti[i].task_members,
                status: ti[i].task_status,
                priority_icon: "<?php echo $icon_dir_url; ?>" + ti[i].task_priority_icon,
                progress: ti[i].progress,
                drag: function(el, source) {
                    // console.log("START DRAG: " + el.dataset.eid);
                },
                dragend: function(el, source) {
                    submitChange(el.dataset.eid, el.parentNode.parentNode.dataset.id)
                },
                drop: function(el) {
                    // console.log("DROPPED: " + el.dataset.eid);
                }
            });
        }
    }

    boards = boards.map(function(board){
        return {
            id: "_" + board.slug,
            title: board.title,
            class: "info,good",
            // dragTo: ["_in_progress"],
            item: boardTasks[board.slug]
        }
    })
    var KanbanTest = new jKanban({
        element: "#tmpBoard",
        gutter: "8px",
        widthBoard: 93/boards.length + '%',
        itemHandleOptions:{
            enabled: true,
        },
        context: function(el, e) {
            console.log("Trigger on all items right-click!");
        },
        dropEl: function(el, target, source, sibling){
            // console.log(el, target, source)
        },
        buttonClick: function(el, boardId) {
            console.log(el);
            console.log(boardId);
        },
        itemAddOptions: {
            enabled: false,
            content: '+ Add New Card',
            class: 'add_new_button',
            footer: true
        },
        boards: boards
        // boards: [
        //     {
        //         id: "_todo",
        //         title: "To Do",
        //         class: "info,good",
        //         // dragTo: ["_in_progress"],
        //         item: [
        //             {
        //                 id: "_test_delete",
        //                 title: "Try drag this (Look the console)",
        //                 date: "10/12/2020",
        //                 project_name: "LMRF Website",
        //                 priority_icon: "https://res.cloudinary.com/robinbd/image/upload/v1639021444/test/icon_13.svg",
        //                 progress: 12,
        //                 drag: function(el, source) {
        //                     console.log("START DRAG: " + el.dataset.eid);
        //                 },
        //                 dragend: function(el) {
        //                     console.log("END DRAG: " + el.dataset.eid);
        //                 },
        //                 drop: function(el) {
        //                     console.log("DROPPED: " + el.dataset.eid);
        //                 }
        //             },
        //             {
        //                 title: "Try Click This!",
        //                 class: ["peppe", "bello"]
        //             }
        //         ]
        //     },
        //     {
        //         id: "_in_progress",
        //         title: "In Progress",
        //         class: "warning",
        //         item: [
        //             {
        //                 title: "Do Something!"
        //             },
        //             {
        //                 title: "Run?"
        //             }
        //         ]
        //     },
        //     {
        //         id: "_done",
        //         title: "Done",
        //         class: "success",
        //         // dragTo: ["_in_progress"],
        //     }
        // ]
    });


    var allEle = KanbanTest.getBoardElements("_todo");
    allEle.forEach(function(item, index) {
        //console.log(item);
    });

    function submitChange(task_id, board_name){
        var data, user_id;
        console.log(task_id, board_name);

        if( !!task_id && !!board_name && typeof ajax_object.user_id !== 'undefined' ){
            user_id = parseInt(ajax_object.user_id);
            data = {
                'action': 'submit_board',
                'task_data': {task_id: task_id, board_name: board_name.substring(1), 'last_update_by': user_id}
            };

            jQuery.post(ajax_object.ajax_url, data, function(response) {
                console.log('The changes are saved!', response);
            });
        }
    }

    document.querySelectorAll(".kanban-item .t_title").forEach(el=>{
        el.addEventListener('click', function (e){
            e.preventDefault();
            window.location.href = '?page=task-details&task=' + e.target.parentElement.dataset.eid;
        })
    })

    document.querySelectorAll(".kanban-item .t_bottom .t_project").forEach(el=>{
        el.addEventListener('click', function (e){
            e.preventDefault();
            window.location.href = '?page=project-details&project=' + e.target.parentElement.parentElement.dataset.project_id;
        })
    })

</script>
