<?php
$page_title = (isset($_GET['playlist_id'])) ? 'Edit Playlist' : 'Add Playlist';

include("includes/header.php");
require_once("thumbnail_images.class.php");

if (isset($_POST['submit']) and isset($_GET['add'])  && PURCHASE == '') {
    if (
        empty(validate_input($_POST['playlist_name'])) ||
        (!isset($_FILES['playlist_image']['size']) || $_FILES['playlist_image']['size'] == 0) ||
        (!isset($_POST['playlist_songs']) || count($_POST['playlist_songs']) <= 0) ||
        empty(validate_input($_POST['playlist_time'])) ||
        empty(validate_input($_POST['playlist_description'], true))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:add_playlist.php?add=yes");
        exit;
    }
    $playlist_image = rand(0, 99999) . "_" . $_FILES['playlist_image']['name'];

    //Main Image
    $tpath1 = 'images/' . $playlist_image;
    $pic1 = compress_image($_FILES["playlist_image"]["tmp_name"], $tpath1, 80);

    //Thumb Image 
    $thumbpath = 'images/thumbs/' . $playlist_image;
    $thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '300', '300');


    $data = array(
        'playlist_name'  =>  validate_input($_POST['playlist_name'], ENT_QUOTES, "UTF-8"),
        'playlist_image'  =>  validate_input($playlist_image),
        'playlist_songs'  =>  implode(',', $_POST['playlist_songs']),
        'playlist_time' => validate_input($_POST['playlist_time'], ENT_QUOTES, "UTF-8"),
        'playlist_description'  =>  validate_input($_POST['playlist_description'], true),
    );

    $qry = Insert('tbl_playlist', $data);

    $_SESSION['msg'] = "10";
    $_SESSION['class'] = 'success';

    header("Location:manage_playlist.php");
    exit;
}
$count = 0;
if (isset($_GET['playlist_id'])) {
    $qry = "SELECT * FROM tbl_playlist where pid='" . $_GET['playlist_id'] . "'";
    $result = mysqli_query($mysqli, $qry);
    $row = mysqli_fetch_assoc($result);
    $count = $result->num_rows;
}


    $mp3_qry = "SELECT * FROM tbl_album ORDER BY tbl_album.`aid` DESC LIMIT 0, 10";

$mp3_result = mysqli_query($mysqli, $mp3_qry);

if (isset($_POST['submit']) and isset($_POST['playlist_id'])  && PURCHASE == '') {
    if (
        empty(validate_input($_POST['playlist_name'])) ||
        (!isset($_FILES['playlist_image']['size']) || $_FILES['playlist_image']['size'] == 0) && empty($row['playlist_image']) ||
        (!isset($_POST['playlist_songs']) || count($_POST['playlist_songs']) <= 0) ||
        empty(validate_input($_POST['playlist_time'])) ||
        empty(validate_input($_POST['playlist_description'], true))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:add_playlist.php?playlist_id=" . $_POST['playlist_id']);
        exit;
    }
    if ($_FILES['playlist_image']['name'] != "") {
        if ($row['playlist_image'] != "") {
            unlink('images/thumbs/' . $row['playlist_image']);
            unlink('images/' . $row['playlist_image']);
        }

        $playlist_image = rand(0, 99999) . "_" . $_FILES['playlist_image']['name'];

        //Main Image
        $tpath1 = 'images/' . $playlist_image;
        $pic1 = compress_image($_FILES["playlist_image"]["tmp_name"], $tpath1, 80);

        //Thumb Image 
        $thumbpath = 'images/thumbs/' . $playlist_image;
        $thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '300', '300');

        $data = array(
            'playlist_name'  =>  validate_input($_POST['playlist_name'], ENT_QUOTES, "UTF-8"),
            'playlist_image'  =>  validate_input($playlist_image),
            'playlist_songs'  =>  implode(',', $_POST['playlist_songs']),
            'playlist_time' => validate_input($_POST['playlist_time'], ENT_QUOTES, "UTF-8"),
            'playlist_description'  =>  validate_input($_POST['playlist_description'], true),
        );

        $update = Update('tbl_playlist', $data, "WHERE pid = '" . $_POST['playlist_id'] . "'");
    } else {

        $data = array(
            'playlist_name'  =>  validate_input($_POST['playlist_name'], ENT_QUOTES, "UTF-8"),
            'playlist_songs'  =>  implode(',', $_POST['playlist_songs'])
        );

        $update = Update('tbl_playlist', $data, "WHERE pid = '" . $_POST['playlist_id'] . "'");
    }

    $_SESSION['msg'] = "11";
    $_SESSION['class'] = 'success';

    header("Location:manage_playlist.php");
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
                            <input type="hidden" name="playlist_id" value="<?php if ($count > 0) {
                                                                            _e($_GET['playlist_id']);
                                                                        } ?>" />
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Playlist Name</label>
                                <input type="text" class="form-control" name="playlist_name" value="<?php if (isset($_GET['playlist_id'])) {
                                                                                                        echo $row['playlist_name'];
                                                                                                    } ?>" id="floatingName" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Select Image</label>
                                <input type="file" name="playlist_image" class="form-control" value="fileupload" accept=".png, .jpg, .JPG , .PNG,.jpeg,.JPEG" onchange="fileValidation()" id="fileupload_img">
                                <?php if ($count > 0) { ?>
                                    <div class="fileupload_img" id="uploadPreview_img"><img type="image" class="image_size" src="images/<?php _e($row['playlist_image']); ?>" alt="image"  /></div>
                                <?php } else { ?>
                                    <div class="fileupload_img" id="uploadPreview_img"><img type="image" class="image_size" src="assets/img/add-image.png" alt="image"  /></div>
                                <?php } ?>
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Book</label>
                                <select name="playlist_songs[]" multiple="" class="form-control label ui selection fluid dropdown">
                                    <option value="" disabled>--Select Book--</option>
                                    <?php
                                    while ($mp3_row = mysqli_fetch_array($mp3_result)) {
                                    ?>
                                        <option value="<?php _e($mp3_row['aid']); ?>" <?php if ($count > 0) {if (in_array($mp3_row['aid'], explode(",", $row['playlist_songs']))) { ?>selected<?php } } ?>><?php _e($mp3_row['album_name']); ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Playlist Time</label>
                                <input type="text" class="form-control" name="playlist_time" id="floatingName" value="<?php if ($count > 0) {
                                                                                                                            _e($row['playlist_time']);
                                                                                                                        } ?>" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Book Description</label>
                                <textarea name="playlist_description" id="playlist_description" class="form-control">
                                <?php if ($count > 0) {
                                    _e($row['playlist_description'], true);
                                } ?>
                                </textarea>
                                <script>
                                    CKEDITOR.replace('playlist_description');
                                </script>
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