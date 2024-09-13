<?php
$page_title = "Books Reports";
include("includes/header.php");

$sql_query = "SELECT tbl_reports.*, tbl_users.`name`, tbl_users.`email`, tbl_mp3.`mp3_title` 
                FROM tbl_reports
                LEFT JOIN tbl_mp3 ON tbl_reports.`song_id`=tbl_mp3.`id`
                LEFT JOIN tbl_users ON tbl_reports.`user_id`=tbl_users.`id`
                ORDER BY tbl_reports.`id` DESC";

$result = mysqli_query($mysqli, $sql_query);
?>
<style>
    .datatable-table>thead>tr>th {
        vertical-align: super;
        text-align: center;
        border-bottom: 1px solid #d9d9d9;
    }

    td {
        vertical-align: middle;
        height: 70px !important;
    }
</style>
<main id="main" class="main">

    <div class="row">
        <div class="col-lg-3">
            <div class="pagetitle">
                <h1><?php _e($page_title); ?></h1>
            </div>
        </div>
    </div><br>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table datatable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Book</th>
                                            <th>Report</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        while ($row = mysqli_fetch_array($result)) {
                                        ?>
                                            <tr class="td <?= $row['id'] ?>">
                                                <td>
                                                    <?php _e($i++); ?>
                                                </td>
                                                <td>
                                                    <?php _e($row['name']); ?>
                                                </td>
                                                <td>
                                                    <?php _e($row['email']); ?>
                                                </td>
                                                <td>
                                                    <?php _e($row['mp3_title']); ?>
                                                </td>
                                                <td>
                                                    <?php _e($row['report']); ?>
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" data-id="<?php _e($row['id']); ?>" class="btn_delete_a btn btn-danger btn_delete" data-toggle="tooltip" data-tooltip="Delete"><i class="bi bi-trash-fill"></i></a>
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
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        $(".btn_delete_a").click(function(e) {

            e.preventDefault();

            var _ids = $(this).data("id");
            var _table = 'tbl_reports';

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
                                'action': 'removeData',
                                'tbl_nm': _table,
                                "tbl_id": "id"
                            },
                            success: function(res) {
                                console.log(res);
                                if (res.status == '1') {
                                    swal({
                                        title: "Successfully",
                                        text: "Report is deleted.",
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