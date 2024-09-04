<?php
include("includes/header.php");
$page_title = "Manage Categories";
$add_page_title = "Add Category";

$_GET = validate_input($_GET);
$tableName = "tbl_category";
$targetpage = "manage_category.php";
$limit = 10;

$searchInput = isset($_GET['keyword']) ? $_GET['keyword'] : '';
if($searchInput != "")
{
    $query = "SELECT COUNT(*) as num  FROM tbl_category WHERE `category_name` LIKE '%$searchInput%'";
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
    $sql_query = "SELECT * FROM tbl_category WHERE `category_name` LIKE '%$searchInput%' ORDER BY tbl_category.`cid` DESC LIMIT $start, $limit";
    $result = mysqli_query($mysqli, $sql_query) or die(mysqli_error($mysqli));
} else {
    $sql_query = "SELECT * FROM tbl_category ORDER BY tbl_category.`cid` DESC LIMIT $start, $limit";
    $result = mysqli_query($mysqli, $sql_query) or die(mysqli_error($mysqli));
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
                <a href="add_category.php?add=yes">
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
                                        <th>#</th>
                                        <th>Category</th>
                                        <th>Category Image</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     $i = $start + 1;
                                    while ($row = mysqli_fetch_array($result)) {
                                    ?>
                                        <tr>
                                            <td><?php _e($i); ?></td>
                                            <td><?php _e($row['category_name']); ?></td>
                                            <td>
                                                <?php if (empty($row['category_image'])) { ?>
                                                    <img src="assets/images/add-image.png" />
                                                <?php } else { ?>
                                                    <img type="image" src="images/thumbs/<?php _e($row['category_image']); ?>" class="image_size" />
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if ($row['status'] != "0") { ?>
                                                    <a title="Change Status" class="toggle_btn_a" href="javascript:void(0)" data-id="<?php _e($row['cid']); ?>" data-action="deactive" data-column="status"><span class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i><span>Enable</span></span></a>

                                                <?php } else { ?>
                                                    <a title="Change Status" class="toggle_btn_a" href="javascript:void(0)" data-id="<?php _e($row['cid']); ?>" data-action="active" data-column="status"><span class="btn btn-danger"><i class="fa fa-check" aria-hidden="true"></i><span>Disable </span></span></a>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="add_category.php?cat_id=<?php _e($row['cid']); ?>" class="btn btn-primary">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="javascript:void(0)" data-id="<?php _e($row['cid']); ?>" class="btn btn-danger btn_delete_a btn_cust" data-toggle="tooltip" data-tooltip="Delete !">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php
                                    $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
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
            var _table = 'tbl_category';

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
                                        text: "Category is deleted.",
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