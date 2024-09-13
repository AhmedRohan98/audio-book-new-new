<?php
$page_title = "Manage Books";
$add_page_title = "Add Books";
include('includes/header.php');

$_GET = validate_input($_GET);
$tableName = "tbl_album";
$targetpage = "manage_books.php";
$limit = 12;


$searchInput = isset($_GET['keyword']) ? $_GET['keyword'] : '';
if($searchInput != "")
{
    $query = "SELECT COUNT(*) as num  FROM tbl_album WHERE tbl_album.album_name LIKE '%$searchInput%'";
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
    $books_qry = "SELECT *,tbl_album.status AS album_status FROM tbl_album
  LEFT JOIN tbl_category ON tbl_album.cat_ids= tbl_category.cid
  LEFT JOIN tbl_artist ON tbl_album.artist_ids= tbl_artist.id
  WHERE tbl_album.album_name like '%$searchInput%' ORDER BY tbl_album.aid DESC";

    $books_result = mysqli_query($mysqli, $books_qry);
} else {
    $books_qry = "SELECT *,tbl_album.status AS album_status FROM tbl_album
							LEFT JOIN tbl_category ON tbl_album.cat_ids= tbl_category.cid
							LEFT JOIN tbl_artist ON tbl_album.artist_ids= tbl_artist.id
						 ORDER BY tbl_album.aid DESC LIMIT $start, $limit";
    $books_result = mysqli_query($mysqli, $books_qry);
    
}

$cat_qry = "SELECT * FROM tbl_category";
$cat_result = mysqli_query($mysqli, $cat_qry);
if (!$cat_result) {
    die("Error in SQL query: " . mysqli_error($mysqli));
}
$categories = array();
while ($row = mysqli_fetch_array($cat_result)) {
    $categories[$row['cid']] = $row['category_name'];
}

$author_qry = "SELECT * FROM tbl_artist";
$author_result2 = mysqli_query($mysqli, $author_qry);
if (!$author_result2) {
    die("Error in SQL query: " . mysqli_error($mysqli));
}
$authors_name_get = array();
while ($row = mysqli_fetch_array($author_result2)) {
    $authors_name_get[$row['id']] = $row['artist_name'];
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
                <a href="add_book.php?add=yes">
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
                                        <th>Category</th>
                                        <th>Author</th>
                                        <th>Title</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($books_row = mysqli_fetch_array($books_result)) {
                                    ?>
                                        <tr>
                                            <td scope="row">
                                                <?php
                                                $cat_ids = explode(',', $books_row['cat_ids']);
                                                foreach ($cat_ids as $cat_id) {
                                                    if (isset($categories[$cat_id])) {
                                                        _e($categories[$cat_id] . ",");
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td scope="row">
                                                <?php
                                                $cat_ids = explode(',', $books_row['cat_ids']);
                                                foreach ($cat_ids as $cat_id) {
                                                    if (isset($authors_name_get[$cat_id])) {
                                                        _e($authors_name_get[$cat_id] . ",");
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td><?php _e($books_row['album_name']); ?></td>
                                            <td>
                                                <img src="images/<?php _e($books_row['album_image']); ?>" class="image_size" />
                                            </td>
                                             <td>
                                                <?php if ($books_row['album_status'] != "0") { ?>
                                                <a title="Change Status" class="toggle_btn_a" href="javascript:void(0)" data-id="<?php _e($books_row['aid']); ?>" data-action="deactive" data-column="status"><span class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i><span>Enable</span></span></a>
                                                
                                                <?php } else { ?>
                                                <a title="Change Status" class="toggle_btn_a" href="javascript:void(0)" data-id="<?php _e($books_row['aid']); ?>" data-action="active" data-column="status"><span class="btn btn-danger"><i class="fa fa-check" aria-hidden="true"></i><span>Disable </span></span></a>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="add_book.php?book_id=<?php _e($books_row['aid']); ?>" class="btn btn-primary">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="javascript:void(0)" data-id="<?php _e($books_row['aid']); ?>" class="btn btn-danger btn_delete_a btn_cust" data-toggle="tooltip" data-tooltip="Delete !">
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
    </div>>
    <script type="text/javascript">
     $(".toggle_btn_a").on("click", function(e) {
            e.preventDefault();
            var _for = $(this).data("action");
            var _id = $(this).data("id");
            var _column = $(this).data("column");
            var _table = 'tbl_album';

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
                    'tbl_id': 'aid'
                },
                success: function(res) {
                    console.log(res);
                    if (res.status == '1') {
                        location.reload();
                    }
                }
            });

        });
        $(".btn_delete_a").on("click", function(e) {

            e.preventDefault();

            var _id = $(this).data("id");
            var _table = 'tbl_album';

            swal({
                    title: "Are you sure?",
                    text: "Do you want to delete Book?",
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

                        $.ajax({
                            type: 'post',
                            url: 'processdata.php',
                            dataType: 'json',
                            data: {
                                id: _id,
                                'action': 'multi_delete',
                                'tbl_nm': _table
                            },
                            success: function(res) {
                                console.log(res);
                                $('.notifyjs-corner').empty();
                                if (res.status == '1') {
                                    swal({
                                        title: "Successfully",
                                        text: "Book is deleted.",
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