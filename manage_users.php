<?php
$page_title = "Manage Users";
$add_page_title = "Add Users";
include("includes/header.php");

$_GET = validate_input($_GET);
$tableName = "tbl_users";
$targetpage = "manage_users.php";
$limit = 12;

$searchInput = isset($_GET['keyword']) ? $_GET['keyword'] : '';
if($searchInput != "")
{
    $query = "SELECT COUNT(*) as num  FROM tbl_users WHERE tbl_users.name LIKE '%$searchInput%'";
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


if ($searchInput != "") {
    $user_qry = "SELECT * FROM tbl_users WHERE tbl_users.name like '%$searchInput%' or tbl_users.email like '%$searchInput%' ORDER BY tbl_users.id DESC";
    $users_result = mysqli_query($mysqli, $user_qry);
} else {
    $users_qry = "SELECT * FROM tbl_users ORDER BY tbl_users.id DESC LIMIT $start, $limit";
    $users_result = mysqli_query($mysqli, $users_qry);
}

if (isset($_GET['user_id'])) {
    Delete('tbl_users', 'id=' . $_GET['user_id'] . '');

    $_SESSION['msg'] = "12";
    header("Location:manage_users.php");
    exit;
}

if (isset($_GET['status_deactive_id'])) {
    $data = array('status'  =>  '0');

    $edit_status = Update('tbl_users', $data, "WHERE id = '" . $_GET['status_deactive_id'] . "'");

    $_SESSION['msg'] = "14";
    header("Location:manage_users.php");
    exit;
}
if (isset($_GET['status_active_id'])) {
    $data = array('status'  =>  '1');

    $edit_status = Update('tbl_users', $data, "WHERE id = '" . $_GET['status_active_id'] . "'");

    $_SESSION['msg'] = "13";
    header("Location:manage_users.php");
    exit;
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
            <form method="GET" id="searchForm" action="">
                <input class="form-control input-sm" placeholder="Search here..." aria-controls="DataTables_Table_0" type="search" name="keyword" value="<?php if (isset($_GET['keyword'])) {
                                                                                                                                                                _e($_GET['keyword']);
                                                                                                                                                            } ?>" required>
            </form>
        </div>
        <div class="col-lg-3 float-right">
            <div class="d-flex justify-content-end">
                <a href="add_user.php?add=yes">
                    <button type="button" class="btn btn-outline-primary btn-lg"><?php _e($add_page_title); ?></button>
                </a>
            </div>
        </div>
    </div><br>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User Type</th>
                                        <th>Name</th>
                                        <th>Image</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($users_row = mysqli_fetch_array($users_result)) {
                                    ?>
                                        <tr>
                                            <td><?php _e($users_row['user_type']); ?></td>
                                            <td><?php _e($users_row['name']); ?></td>
                                            <td>
                                                <?php
                                                if ($users_row['user_type'] == 'Google' || $users_row['user_type'] == 'Facebook') {
                                                    if (empty($users_row['user_image'])) {
                                                ?>
                                                        <img src="images/add-image.png" class="rounded-circle" style="border-radius: 60% !important;width: 100px;" />
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <img src="<?php _e($users_row['user_image']); ?>" class="rounded-circle" style="border-radius: 60% !important;width: 100px;" />
                                                    <?php
                                                    }
                                                } else {
                                                    if (empty($users_row['user_image'])) {
                                                    ?>
                                                        <img src="images/add-image.png" class="rounded-circle" style="border-radius: 60% !important;width: 100px;" />
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <img src="images/<?php _e($users_row['user_image']); ?>" class="rounded-circle" style="border-radius: 60% !important;width: 120px;" />
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </td>

                                            <td><?php _e($users_row['email']); ?></td>
                                            <td><?php _e($users_row['phone']); ?></td>
                                            <td>
                                                <?php if ($users_row['status'] != "0") { ?>
                                                    <a href="manage_users.php?status_deactive_id=<?php _e($users_row['id']); ?>" title="Change Status"><span class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i><span>Enable</span></span></a>

                                                <?php } else { ?>
                                                    <a href="manage_users.php?status_active_id=<?php _e($users_row['id']); ?>" title="Change Status"><span class="btn btn-danger"><i class="fa fa-check" aria-hidden="true"></i><span>Disable </span></span></a>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="add_user.php?user_id=<?php _e($users_row['id']); ?>" class="btn btn-primary">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="javascript:void(0)" data-id="<?php _e($users_row['id']); ?>" class="btn btn-danger btn_delete_a btn_cust" data-toggle="tooltip" data-tooltip="Delete !">
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
    <script type="text/javascript">
        $(".toggle_btn_a").on("click", function(e) {
            e.preventDefault();
            var _for = $(this).data("action");
            var _id = $(this).data("id");
            var _column = $(this).data("column");
            var _table = 'tbl_category';

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
                    'tbl_id': 'cid'
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
            var _table = 'tbl_users';

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
                                        text: "User is deleted.",
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
    </script>
</main>
<?php include("includes/footer.php"); ?>