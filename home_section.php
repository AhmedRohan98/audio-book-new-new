<?php
$page_title = "Home Sections";
$add_page_title = "Add Sections";
include('includes/header.php');

$_GET = validate_input($_GET);
$tableName = "tbl_home_section_2";
$targetpage = "home_section_2.php";
$limit = 15;


$keyword = '';
if (!isset($_GET['keyword'])) {
    $query = "SELECT COUNT(*) as num FROM $tableName";
} else {

    $keyword = addslashes(trim($_GET['keyword']));

    $query = "SELECT COUNT(*) as num FROM $tableName WHERE (`section_title` LIKE '%$keyword%' ) AND id <> 0";

    $targetpage = "home_section_2.php?keyword=" . $_GET['keyword'];
}

$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query));
$total_pages = $total_pages['num'];
$stages = 3;
$page = 0;
if (isset($_GET['page'])) {
    $page = mysqli_real_escape_string($mysqli, $_GET['page']);
}
if ($page) {
    $start = ($page - 1) * $limit;
} else {
    $start = 0;
}
if (!isset($_GET['keyword'])) {
    $sql_query = "SELECT * FROM tbl_home_section_2 ORDER BY tbl_home_section_2.`id` DESC LIMIT $start, $limit";
} else {
    $sql_query = "SELECT * FROM tbl_home_section_2 WHERE (`section_title` LIKE '%$keyword%') ORDER BY tbl_home_section_2.`id` DESC LIMIT $start, $limit";
}
$result = mysqli_query($mysqli, $sql_query) or die(mysqli_error($mysqli));

function get_books_info($ids)
{
    global $mysqli;

    $ids = implode(',', $ids);
    $query = "SELECT `album_name` FROM tbl_album WHERE `aid` IN ($ids)";

    $sql = mysqli_query($mysqli, $query) or die(mysqli_error());

    $title = '';

    while ($row = mysqli_fetch_assoc($sql)) {
        $title .= stripslashes($row['album_name']) . ',<br/>';
    }
    $title = rtrim($title, ',<br/>');
    return $title;
}
?>
<main id="main" class="main">
    <div class="row">
        <div class="col-lg-3">
            <div class="pagetitle">
                <h1><?php _e($page_title); ?></h1>
            </div>
        </div>
        <div class="col-lg-6">
            <form method="get" id="searchForm" action="">
                <input class="form-control input-sm" placeholder="Search here..." aria-controls="DataTables_Table_0" type="search" name="keyword" value="<?php if (isset($_GET['keyword'])) {
                                                                                                                                                                _e($_GET['keyword']);
                                                                                                                                                            } ?>" required="required">
            </form>
        </div>
        <div class="col-lg-3 float-right">
            <div class="d-flex justify-content-end">
                <a href="add_section.php?add=yes">
                    <button type="button" class="btn btn-outline-primary btn-lg"><?php _e($add_page_title); ?></button>
                </a>
            </div>
        </div>
    </div><br>
    <div class="row">
        <div class="col-lg-12">
            <div style="display:flex;justify-content: flex-end;">
                <div class="checkbox" style="padding-right: 10px;">
                    <input type="checkbox" id="checkall_input">
                    <label for="checkall_input">Select All</label>
                </div>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle btn_cust" type="button" data-toggle="dropdown">
                        Action
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" style="right:0;left:auto;text-align:center;border:1px solid">
                        <li><a href="#" class="actions" data-action="enable">Enable</a></li>
                        <li><a href="#" class="actions" data-action="disable">Disable</a></li>
                        <li><a href="#" class="actions" data-action="delete">Delete</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Books</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                    <tr>
                                        <td scope="row">
                                            <input type="checkbox" name="post_ids[]" id="checkbox<?php _e($i); ?>" value="<?php _e($row['id']); ?>" class="post_ids">
                                        </td>
                                        <td>
                                            <?php _e($row['section_title']); ?>
                                        </td>
                                        <td scope="row">
                                            <?php
                                            _e(get_books_info(explode(',', $row['section_books'])));
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($row['status'] != "0") { ?>
                                                <a title="Change Status" class="toggle_btn_a" href="javascript:void(0)" data-id="<?php _e($row['id']); ?>" data-action="deactive" data-column="status"><span class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i><span>Enable</span></span></a>

                                            <?php } else { ?>
                                                <a title="Change Status" class="toggle_btn_a" href="javascript:void(0)" data-id="<?php _e($row['id']); ?>" data-action="active" data-column="status"><span class="btn btn-danger"><i class="fa fa-check" aria-hidden="true"></i><span>Disable </span></span></a>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <a href="add_section.php?section_id=<?php _e($row['id']); ?>" class="btn btn-primary">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="javascript:void(0)" data-id="<?php _e($row['id']); ?>" class="btn btn-danger btn_delete_a btn_cust" data-toggle="tooltip" data-tooltip="Delete !">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <!-- End Table with stripped rows -->
                    </div>
                    <div class="col-md-12 col-xs-12 d-flex justify-content-end paginationRight">
                        <nav aria-label="Page navigation example">
                            <?php if (!isset($_GET["keyword"])) {
                                include("pagination.php");
                            } ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>>
    <script type="text/javascript">
        $(".toggle_btn_a").on("click", function(e) {
            e.preventDefault();

            var _for = $(this).data("action");
            var _id = $(this).data("id");
            var _column = $(this).data("column");
            var _table = 'tbl_home_section_2';

            $.ajax({
                type: 'post',
                url: 'processdata.php',
                dataType: 'json',
                data: {
                    id: _id,
                    for_action: _for,
                    column: _column,
                    table: _table,
                    'action': 'toggle_status',
                    'tbl_id': 'id'
                },
                success: function(res) {
                    console.log(res);
                    if (res.status == '1') {
                        location.reload();
                    }
                }
            });

        });
        $(".btn_delete_a").click(function(e) {
            e.preventDefault();

            var _ids = $(this).data("id");

            swal({
                    title: "Are you sure?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    cancelButtonClass: "btn-warning",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                },
                function(isConfirm) {
                    if (isConfirm) {

                        $.ajax({
                            type: 'post',
                            url: 'processdata.php',
                            dataType: 'json',
                            data: {
                                id: _ids,
                                'action': 'multi_delete',
                                'tbl_nm': 'tbl_home_section_2'
                            },
                            success: function(res) {
                                console.log(res);
                                if (res.status == '1') {
                                    swal({
                                        title: "Successfully",
                                        text: "Section is deleted...",
                                        type: "success"
                                    }, function() {
                                        location.reload();
                                    });
                                }
                            }
                        });
                    } else {
                        swal.close();
                    }
                });
        });
        $("button[name='delete_rec']").click(function(e) {
            e.preventDefault();

            var _ids = $.map($('.post_ids:checked'), function(c) {
                return c.value;
            });

            if (_ids != '') {
                swal({
                        title: "Do you really want to perform?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        cancelButtonClass: "btn-warning",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: false,
                        closeOnCancel: false,
                        showLoaderOnConfirm: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {

                            $.ajax({
                                type: 'post',
                                url: 'processdata.php',
                                dataType: 'json',
                                data: {
                                    id: _ids,
                                    'action': 'multi_delete',
                                    'tbl_nm': 'tbl_home_section_2'
                                },
                                success: function(res) {
                                    $('.notifyjs-corner').empty();
                                    if (res.status == '1') {
                                        swal({
                                            title: "Successfully",
                                            text: "You have successfully done",
                                            type: "success"
                                        }, function() {
                                            location.reload();
                                        });
                                    }
                                }
                            });
                        } else {
                            swal.close();
                        }

                    });
            } else {
                swal("Sorry no section selected !!")
            }
        });

        $(".actions").click(function(e) {
            e.preventDefault();

            var _ids = $.map($('.post_ids:checked'), function(c) {
                return c.value;
            });
            var _action = $(this).data("action");

            if (_ids != '') {
                swal({
                        title: "Do you really want to perform?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        cancelButtonClass: "btn-warning",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: false,
                        closeOnCancel: false,
                        showLoaderOnConfirm: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {

                            var _table = 'tbl_home_section_2';

                            $.ajax({
                                type: 'post',
                                url: 'processdata.php',
                                dataType: 'json',
                                data: {
                                    id: _ids,
                                    for_action: _action,
                                    table: _table,
                                    'action': 'multi_action'
                                },
                                success: function(res) {
                                    $('.notifyjs-corner').empty();
                                    if (res.status == '1') {
                                        swal({
                                            title: "Successfully",
                                            text: "You have successfully done",
                                            type: "success"
                                        }, function() {
                                            location.reload();
                                        });
                                    }
                                }
                            });
                        } else {
                            swal.close();
                        }

                    });
            } else {
                swal("Sorry no section selected !!")
            }
        });


        var totalItems = 0;

        $("#checkall_input").click(function() {

            totalItems = 0;

            $('input:checkbox').not(this).prop('checked', this.checked);
            $.each($("input[name='post_ids[]']:checked"), function() {
                totalItems = totalItems + 1;
            });

            if ($('input:checkbox').prop("checked") == true) {
                $('.notifyjs-corner').empty();
                $.notify(
                    'Total ' + totalItems + ' item checked', {
                        position: "top center",
                        className: 'success'
                    }
                );
            } else if ($('input:checkbox').prop("checked") == false) {
                totalItems = 0;
                $('.notifyjs-corner').empty();
            }
        });

        var noteOption = {
            clickToHide: false,
            autoHide: false,
        }

        $.notify.defaults(noteOption);

        $(".post_ids").click(function(e) {

            if ($(this).prop("checked") == true) {
                totalItems = totalItems + 1;
            } else if ($(this).prop("checked") == false) {
                totalItems = totalItems - 1;
            }

            if (totalItems == 0) {
                $('.notifyjs-corner').empty();
                exit();
            }

            $('.notifyjs-corner').empty();

            $.notify(
                'Total ' + totalItems + ' item checked', {
                    position: "top center",
                    className: 'success'
                }
            );
        });
    </script>
</main>
<?php include("includes/footer.php"); ?>