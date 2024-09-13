<?php
include("includes/header.php");
$page_title = "Chapter Download";
$qry = "SELECT * FROM tbl_settings where id='1'";
$result = mysqli_query($mysqli, $qry);
$settings_row = mysqli_fetch_assoc($result);

if (isset($_POST['download_submit'])  && PURCHASE == '') {

  $data = array(
    'song_download' => $_POST['song_download']
  );

  $settings_edit = Update('tbl_settings', $data, "WHERE id = '1'");

  $_SESSION['msg'] = "11";
  header("Location:app_song_download_settings.php");
  exit;
}
?>
<main id="main" class="main">
    <section class="section">
        <div class="row  g-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body g-3"><br>
                        <div class="pagetitle mb-5">
                            <h1><?php _e($page_title); ?></h1>

                        </div>
                        <form class="row g-3" action=""  name="download_api" method="post" class="form form-horizontal" enctype="multipart/form-data" id="api_form">
                            <br><br>
                            <div class="col-md-12">
                                <div class="form-floating">
                                <select name="song_download" id="api_cat_post_order_by" class="form-control label ui selection fluid dropdown" style="width: 100%; height: 100%;">
                                    <option value="true" <?php if ($settings_row['song_download'] == 'true') { ?>selected<?php } ?>>On</option>
                                    <option value="false" <?php if ($settings_row['song_download'] == 'false') { ?>selected<?php } ?>>Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-center">
                                <button  type="submit" name="download_submit" class="btn btn-primary <?php _e(PURCHASE); ?>">Save</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>
<?php include("includes/footer.php"); ?>