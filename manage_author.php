<?php
include("includes/header.php");
$page_title = "Manage Author";
$add_page_title = "Add Author";

$_GET = validate_input($_GET);
$tableName = "tbl_artist";
$targetpage = "manage_author.php";
$limit = 12;


$searchInput = isset($_GET['keyword']) ? $_GET['keyword'] : '';
if($searchInput != "")
{
    $query = "SELECT COUNT(*) as num  FROM tbl_artist WHERE `artist_name` LIKE '%$searchInput%'";
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
    $author_qry = "SELECT * FROM tbl_artist WHERE tbl_artist.artist_name like '%$searchInput%' ORDER BY  tbl_artist.id DESC";
    $result = mysqli_query($mysqli, $author_qry);
} else {

    $author_qry = "SELECT * FROM tbl_artist
     ORDER BY tbl_artist.id DESC LIMIT $start, $limit";

    $result = mysqli_query($mysqli, $author_qry);
}

if (isset($_GET['author_id'])) {

    Delete('tbl_author', 'author_id=' . $_GET['author_id'] . '');

    $_SESSION['msg'] = "12";
    header("Location:manage_author.php");
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
                <a href="add_author.php?add=yes">
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
                                        <th>Author</th>
                                        <th>Author Image</th>
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
                                            <td><?php _e($row['artist_name']); ?></td>
                                            <td>
                                                <?php if (empty($row['artist_image'])) { ?>
                                                    <img src="assets/img/add-image.png" />
                                                <?php } else { ?>
                                                    <img src="images/<?php _e($row['artist_image']); ?>" class="image_size" />
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="add_author.php?author_id=<?php _e($row['id']); ?>" class="btn btn-primary">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="javascript:void(0)" data-id="<?php _e($row['id']); ?>" class="btn btn-danger btn_delete_a btn_cust" data-toggle="tooltip" data-tooltip="Delete !">
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
            var _table = 'tbl_artist';

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
            var _table = 'tbl_artist';

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
                                        text: "Author is deleted.",
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