<?php
$page_title = "Manage Chapter";
$add_page_title = "Add Chapter";
include("includes/header.php");

$_GET = validate_input($_GET);
$tableName = "tbl_mp3";
$targetpage = "manage_mp3.php";
$limit = 10;

$searchInput = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$album = $_GET['album'];

if ($album == "" || $searchInput != "") {
    $query = "SELECT COUNT(*) as num  FROM $tableName WHERE (`mp3_title` LIKE '%$searchInput%')";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query));
    $total_pages = $total_pages['num'];
}
else if ($album != "" || $searchInput == "")
{
    $query = "SELECT COUNT(*) as num FROM $tableName WHERE `album_id` = $album";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query));
    $total_pages = $total_pages['num'];
}
else if ($album != "" || $searchInput != "")
{
    $query = "SELECT COUNT(*) as num  FROM $tableName WHERE (`mp3_title` LIKE '%$searchInput%') AND `album_id` = $album";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query));
    $total_pages = $total_pages['num'];
}
else 
{
    $query = "SELECT COUNT(*) as num FROM $tableName";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query));
    $total_pages = $total_pages['num'];
}

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

if($album == "" || $searchInput != "") {
    $sql_query = "SELECT tbl_mp3.*, tbl_album.`aid`, tbl_album.`album_name` FROM tbl_mp3 LEFT JOIN tbl_album ON tbl_mp3.`album_id` = tbl_album.`aid` WHERE (`mp3_title` LIKE '%$searchInput%') ORDER BY tbl_mp3.`id` DESC LIMIT $start, $limit";
    $result = mysqli_query($mysqli, $sql_query) or die(mysqli_error($mysqli));
}
else if($album != "" || $searchInput == "") 
{
    $sql_query = "SELECT tbl_mp3.*, tbl_album.`aid`, tbl_album.`album_name` FROM tbl_mp3 LEFT JOIN tbl_album ON tbl_mp3.`album_id` = tbl_album.`aid` WHERE tbl_mp3.`album_id` = '$album' ORDER BY tbl_mp3.`id` DESC LIMIT $start, $limit";
    $result = mysqli_query($mysqli, $sql_query) or die(mysqli_error($mysqli));
}
else if($album != "" || $searchInput != "") 
{
    $sql_query = "SELECT tbl_mp3.*, tbl_album.`aid`, tbl_album.`album_name` FROM tbl_mp3 LEFT JOIN tbl_album ON tbl_mp3.`album_id` = tbl_album.`aid` WHERE tbl_mp3.`album_id` = '$album' AND  (`mp3_title` LIKE '%$searchInput%') ORDER BY tbl_mp3.`id` DESC LIMIT $start, $limit";
    $result = mysqli_query($mysqli, $sql_query) or die(mysqli_error($mysqli));
}
else
{
    $sql_query = "SELECT tbl_mp3.*,tbl_mp3.`mp3_title`,tbl_mp3.`total_views`,tbl_mp3.`total_download`,tbl_mp3.`mp3_thumbnail`, tbl_album.`aid`, tbl_album.`album_name` FROM tbl_mp3 LEFT JOIN tbl_album ON tbl_mp3.`album_id` = tbl_album.`aid` ORDER BY tbl_mp3.`id` DESC LIMIT $start, $limit";
    $result = mysqli_query($mysqli, $sql_query) or die(mysqli_error($mysqli));
}
function get_total_songs($cat_id)
{

    global $mysqli;
    $qry_songs = "SELECT COUNT(*) as num FROM tbl_mp3 WHERE cat_id='" . $cat_id . "'";
    $total_songs = mysqli_fetch_array(mysqli_query($mysqli, $qry_songs));
    $total_songs = $total_songs['num'];
    return $total_songs;
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
                <a href="add_mp3.php?add=yes">
                    <button type="button" class="btn btn-outline-primary btn-lg"><?php _e($add_page_title); ?></button>
                </a>
            </div>
        </div>
    </div><br>
    <div class="row">
        <div class="col-lg-6">
            <form id="filterForm" accept="" method="GET" class="col-md-3 col-xs-12">

                <select name="album" class="form-control filter album select2" data-type="album">
                    <option value="">All Book</option>
                    <?php

                    $sql_album = "SELECT * FROM tbl_album ORDER BY album_name LIMIT 10";

                    $res_album = mysqli_query($mysqli, $sql_album);
                    while ($row_album = mysqli_fetch_array($res_album)) {
                    ?>
                        <option value="<?php _e($row_album['aid']); ?>" <?php if (isset($_GET['album']) && $_GET['album'] == $row_album['aid']) {
                                                                            _e('selected');
                                                                        } ?>><?php _e($row_album['album_name']); ?></option>
                    <?php
                    }
                    ?>
                </select>
            </form>
        </div>
        <div class="col-lg-6">
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
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Album Name</th>
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
                                                <?php
                                                if (strlen($row['mp3_title']) > 30) {
                                                    _e(substr(stripslashes($row['mp3_title']), 0, 29) . '...');
                                                } else {
                                                    _e($row['mp3_title']);
                                                }
                                                ?>
                                            </td>
                                            <td scope="row">
                                                <?php
                                                if (strlen($row['album_name']) > 18) {
                                                    _e(substr(stripslashes($row['album_name']), 0, 18) . '...');
                                                } else {
                                                    _e($row['album_name']);
                                                }
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
                                                <a href="add_mp3.php?mp3_id=<?php _e($row['id']); ?>" class="btn btn-primary">
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
                    </div>
                    <div class="col-md-12 col-xs-12 d-flex justify-content-end paginationRight">
                        <nav aria-label="Page navigation example">
                            <?php
                            include("pagination.php");
                            ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>
    <script>
        $("select[name='album']").on("change", function(e) {
            if ($(this).val() != '') {
                window.location.href = "manage_mp3.php?album=" + $(this).val();
            } else {
                window.location.href = "manage_mp3.php";
            }
        });
    </script>
    <script type="text/javascript">
        $(".toggle_btn_a").on("click", function(e) {
            e.preventDefault();

            var _for = $(this).data("action");
            var _id = $(this).data("id");
            var _column = $(this).data("column");
            var _table = 'tbl_mp3';

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
            var _table = 'tbl_mp3';

            swal({
                    title: "Are you sure to delete this?",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonClass: "btn-warning",
                    confirmButtonClass: "btn-danger",
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
                                'tbl_nm': _table
                            },
                            success: function(res) {
                                console.log(res);
                                if (res.status == '1') {
                                    swal({
                                        title: "Successfully",
                                        text: "Chapter is deleted.",
                                        type: "success"
                                    }, function() {
                                        location.reload();
                                    });
                                } else if (res.status == '-2') {
                                    swal(res.message);
                                }
                            }
                        });
                    } else {
                        swal.close();
                    }
                });
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
                        confirmButtonClass: "btn-danger btn_edit",
                        cancelButtonClass: "btn-warning btn_edit",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: false,
                        closeOnCancel: false,
                        showLoaderOnConfirm: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {

                            var _table = 'tbl_mp3';

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
                                    console.log(res);
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
                swal("Sorry no data selected !!")
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
                exit;
            }

            $('.notifyjs-corner').empty();

            $.notify(
                'Total ' + totalItems + ' item checked', {
                    position: "top center",
                    className: 'success'
                }
            );
        });


        $(".filter").on("change", function(e) {
            $("#filterForm *").filter(":input").each(function() {
                if ($(this).val() == '')
                    $(this).prop("disabled", true);
            });
            $("#filterForm").submit();
        });
        $(function() {
            $('.select2').select2({
                ajax: {
                    url: 'getData.php',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        var query = {
                            type: $(this).data("type"),
                            search: params.term,
                            page: params.page || 1
                        }
                        return query;
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 5) < data.total_count
                            }
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
</main>
<?php include("includes/footer.php"); ?>