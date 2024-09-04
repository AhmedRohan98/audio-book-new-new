<?php
$page_title = (isset($_GET['banner_id'])) ? 'Edit Banner' : 'Add Banner';
include("includes/header.php");
require_once("thumbnail_images.class.php");

$album_qry = "SELECT * FROM tbl_album ORDER BY aid";
$album_result = mysqli_query($mysqli, $album_qry);

if (isset($_POST['submit']) && isset($_GET['add']) && PURCHASE == '') {
  if (
    empty(validate_input($_POST['banner_title'])) ||
    empty(validate_input($_POST['banner_sort_info'])) ||
    (!isset($_FILES['banner_image']['size']) || $_FILES['banner_image']['size'] == 0) ||
    (!isset($_POST['album_ids']) || count($_POST['album_ids']) <= 0) 
  ) {
    $_SESSION['msg'] = "15";
    $_SESSION['class'] = 'error';
    header("Location:add_banner.php?add=yes");
    exit;
  }

  $banner_image = rand(0, 99999) . "_" . $_FILES['banner_image']['name'];
  $tpath1 = 'images/' . $banner_image;
  $pic1 = compress_image($_FILES["banner_image"]["tmp_name"], $tpath1, 80);

  $thumbpath = 'images/thumbs/' . $banner_image;
  $thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '300', '300');

  $data = array(
    'banner_title'  =>  validate_input($_POST['banner_title'], ENT_QUOTES, "UTF-8"),
    'banner_sort_info'  =>  validate_input($_POST['banner_sort_info'], ENT_QUOTES, "UTF-8"),
    'banner_image'  =>  validate_input($banner_image),
    'album'  =>  implode(',', $_POST['album_ids'])
  );

  $qry = Insert('tbl_banner', $data);

  $_SESSION['msg'] = "10";
  $_SESSION['class'] = 'success';

  header("Location:manage_banners.php");
  exit;
}
$count = 0;
if (isset($_GET['banner_id'])) {
  $qry = "SELECT * FROM tbl_banner where bid='" . $_GET['banner_id'] . "'";
  $result = mysqli_query($mysqli, $qry);
  $row = mysqli_fetch_assoc($result);
  $count = $result->num_rows;
}


if (isset($_POST['submit']) && isset($_POST['banner_id']) && PURCHASE == '') {

  if (
    empty(validate_input($_POST['banner_title'])) ||
    empty(validate_input($_POST['banner_sort_info'])) ||
    (!isset($_FILES['banner_image']['size']) || $_FILES['banner_image']['size'] == 0) && empty($row['banner_image']) ||
    (!isset($_POST['album_ids']) || count($_POST['album_ids']) <= 0) 
  ) {
    $_SESSION['msg'] = "15";
    $_SESSION['class'] = 'error';
    header("Location:add_banner.php?banner_id=" . $_GET['banner_id']);
    exit;
  }
  if ($_FILES['banner_image']['name'] != "") {
    if ($row['banner_image'] != "") {
      unlink('images/thumbs/' . $row['banner_image']);
      unlink('images/' . $row['banner_image']);
    }

    $banner_image = rand(0, 99999) . "_" . $_FILES['banner_image']['name'];

    //Main Image
    $tpath1 = 'images/' . $banner_image;
    $pic1 = compress_image($_FILES["banner_image"]["tmp_name"], $tpath1, 80);

    //Thumb Image 
    $thumbpath = 'images/thumbs/' . $banner_image;
    $thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '300', '300');

    $data = array(
      'banner_title'  => validate_input($_POST['banner_title'], ENT_QUOTES, "UTF-8"),
      'banner_sort_info'  =>  validate_input($_POST['banner_sort_info'], ENT_QUOTES, "UTF-8"),
      'banner_image'  =>  validate_input($banner_image),
      'album'  =>  implode(',', $_POST['album_ids'])
    );

    $update = Update('tbl_banner', $data, "WHERE bid = '" . $_POST['banner_id'] . "'");
  } else {
    $data = array(
      'banner_title'  => html_entity_decode($_POST['banner_title'], ENT_QUOTES, "UTF-8"),
      'banner_sort_info'  =>  html_entity_decode($_POST['banner_sort_info'], ENT_QUOTES, "UTF-8"),
      'album'  =>  implode(',', $_POST['album_ids'])
    );

    $update = Update('tbl_banner', $data, "WHERE bid = '" . $_POST['banner_id'] . "'");
  }

  $_SESSION['msg'] = "11";
  $_SESSION['class'] = 'success';

  header("Location:manage_banners.php");
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
                        <form class="row g-3" action="" method="post" enctype="multipart/form-data">
                            <br><br>
                            <input type="hidden" name="banner_id" value="<?php if ($count > 0) {
                                                                            _e($_GET['banner_id']);
                                                                        } ?>" />
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Banner Title</label>
                                <input type="text" class="form-control" name="banner_title" value="<?php if ($count > 0) {
                                                                                _e($row['banner_title']);
                                                                              } ?>"  id="floatingName" class="form-control">
                            </div>
                                                                  
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Banner Sort Info</label>
                                <input type="text" class="form-control" name="banner_sort_info" value="<?php if ($count > 0) {
                                                                                        _e($row['banner_sort_info']);
                                                                                      } ?>"  id="floatingName" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Select Image</label>
                                <input type="file" name="banner_image" class="form-control" value="fileupload" accept=".png, .jpg, .JPG , .PNG,.jpeg,.JPEG" onchange="fileValidation()" id="fileupload_img">
                                <?php if ($count > 0) { ?>
                                    <div class="fileupload_img" id="uploadPreview_img"><img type="image" class="image_size" src="images/<?php _e($row['banner_image']); ?>" alt="image"  /></div>
                                <?php } else { ?>
                                    <div class="fileupload_img" id="uploadPreview_img"><img type="image" class="image_size" src="assets/img/add-image.png" alt="image"  /></div>
                                <?php } ?>
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Book</label>
                                <select name="album_ids[]" multiple="" class="form-control label ui selection fluid dropdown">
                                    <option value="" disabled>--Select Book--</option>
                                    <?php
                                    while ($mp3_row = mysqli_fetch_array($album_result)) {
                                    ?>
                                        <option value="<?php _e($mp3_row['aid']); ?>" <?php if ($count > 0) { if (in_array($mp3_row['aid'], explode(",", $row['album']))) { ?>selected<?php } } ?>><?php _e($mp3_row['album_name']); ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="submit" class="btn button btn-primary <?php _e(PURCHASE); ?>">Save</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->
<script type="text/javascript">
    $(document).ready(function(e) {
        $("#mp3_type").change(function() {
            var type = $("#mp3_type").val();
            if (type == "server_url") {
                $("#mp3_url_display").show();
                // $("#thumbnail").show();
                $("#mp3_local_display").hide();
                $("#mp3_local").val('');
                $("#audio").attr('src', '');
            } else {
                $("#mp3_url_display").hide();
                $("#mp3_local_display").show();
                // $("#thumbnail").show();
            }
        });
    });

    function fileValidation() {
        var fileInput = document.getElementById('fileupload_img');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.png|.PNG|.jpg|.JPG|.jpeg|.JPEG)$/i;
        if (!allowedExtensions.exec(filePath)) {
            alert('Please upload file having extension .png,.PNG,.jpg,.JPG,.jpeg,.JPEG only.');
            fileInput.value = '';
            return false;
        } else {
            //image preview
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('uploadPreview_img').innerHTML = '<img src="' + e.target.result + '" style="width: 150px;height: 200px;margin-top: 10px;"/>';
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }
</script>
<?php include("includes/footer.php"); ?>