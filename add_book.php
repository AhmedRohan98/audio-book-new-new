<?php
$page_title = (isset($_GET['book_id'])) ? 'Edit Book' : 'Add Book';
include("includes/header.php");
require_once("thumbnail_images.class.php");

$cat_qry = "SELECT * FROM tbl_category ORDER BY tbl_category.cid";
$cat_result = mysqli_query($mysqli, $cat_qry);

$author_qry = "SELECT * FROM tbl_artist ORDER BY tbl_artist.id";
$author_result = mysqli_query($mysqli, $author_qry);

$file_path = getBaseUrl();

if (isset($_POST['submit']) and isset($_GET['add'])  && PURCHASE == '') {

    $mp3_type = $_POST['mp3_type'];
    $book_subscription_type = $_POST['book_subscription_type'];
    if (
        empty(validate_input($book_subscription_type)) ||
        (!isset($_POST['cat_ids']) || count($_POST['cat_ids']) <= 0) ||
        empty(validate_input($_POST['album_name'])) ||
        (!isset($_POST['artist_ids']) || count($_POST['artist_ids']) <= 0) ||
        empty(validate_input($_POST['play_time'])) ||
        empty(validate_input($mp3_type)) ||
        ($mp3_type == 'server_url' && empty($_POST['mp3_url'])) ||
        ($mp3_type == 'local' && (!isset($_FILES['mp3_local']) || $_FILES['mp3_local']['size'] == 0)) ||
        empty(validate_input($_POST['book_description'], true))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:add_book.php?add=yes");
        exit;
    }

    try {

        if ($mp3_type == 'server_url') {
            $mp3_url = htmlentities(trim($_POST['mp3_url']));
        } else {
            $path = "uploads/"; //set your folder path
            $mp3_local = rand(0, 99999) . "_" . str_replace(" ", "-", $_FILES['mp3_local']['name']);
            $tmp = $_FILES['mp3_local']['tmp_name'];
            if (move_uploaded_file($tmp, $path . $mp3_local)) {
                $mp3_url = $mp3_local;
            } else {
                _e("Error in uploading mp3 file !!");
                exit;
            }
        }

        $ext = pathinfo($_FILES['album_image']['name'], PATHINFO_EXTENSION);

        $album_image = rand(0, 99999) . "_book." . $ext;

        //Main Image
        $tpath1 = 'images/' . $album_image;

        if ($ext != 'png') {
            $pic1 = compress_image($_FILES["album_image"]["tmp_name"], $tpath1, 80);
        } else {
            $tmp = $_FILES['album_image']['tmp_name'];
            move_uploaded_file($tmp, $tpath1);
        }

        //Thumb Image 
        $thumbpath = 'images/thumbs/' . $album_image;
        $thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '300', '300');


        $data = array(
            'album_name'  =>  validate_input($_POST['album_name'], ENT_QUOTES, "UTF-8"),
            'album_image'  =>  validate_input($album_image),
            'artist_ids'  => implode(',', $_POST['artist_ids']),
            'cat_ids'  => implode(',', $_POST['cat_ids']),
            'book_subscription_type' => validate_input($book_subscription_type),
            'play_time'  =>  validate_input($_POST['play_time'], ENT_QUOTES, "UTF-8"),
            'book_description'  =>  validate_input($_POST['book_description'], true),
            'book_type'  =>  validate_input($mp3_type),
            'book_url'  =>  validate_input($mp3_url)
        );

        $qry = Insert('tbl_album', $data);
    } catch (Exception $e) {
        print_r($e);
    }

    $_SESSION['msg'] = "10";
    $_SESSION['class'] = 'success';
    header("Location:manage_books.php");
    exit;
}
$count = 0;
if (isset($_GET['book_id'])) {

    $qry = "SELECT * FROM tbl_album where aid='" . $_GET['book_id'] . "'";
    $result = mysqli_query($mysqli, $qry);
    $row = mysqli_fetch_assoc($result);
    $count = $result->num_rows;
}
if (isset($_POST['submit']) &&   $count > 0  && PURCHASE == '') {
    $mp3_type = $_POST['mp3_type'];
    $book_subscription_type = $_POST['book_subscription_type'];
    if (
        empty(validate_input($book_subscription_type)) ||
        (!isset($_POST['cat_ids']) || count($_POST['cat_ids']) <= 0) ||
        empty(validate_input($_POST['album_name'])) || !isset($row['album_image']) ||
        (!isset($_POST['artist_ids']) || count($_POST['artist_ids']) <= 0) ||
        empty(validate_input($_POST['play_time'])) ||
        empty(validate_input($mp3_type)) ||
        ($mp3_type == 'server_url' && empty($_POST['mp3_url'])) ||
        ($mp3_type == 'local' && (!isset($_FILES['mp3_local']) || $_FILES['mp3_local']['size'] == 0)) && !isset($row['book_url']) ||
        empty(validate_input($_POST['book_description'], true))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:add_book.php?book_id=" . $_POST['book_id']);
        exit;
    }

    if ($mp3_type == 'server_url') {
        $mp3_url = validate_input(trim($_POST['mp3_url']));

        if ($row['mp3_type'] == 'local') {
            unlink('uploads/' . basename($row['mp3_url']));
        }
    } else {
        $path = "uploads/"; //set your folder path

        if ($_FILES['mp3_local']['name'] != "") {

            unlink('uploads/' . basename($row['mp3_url']));

            $ext = pathinfo($_FILES['mp3_local']['name'], PATHINFO_EXTENSION);

            $mp3_local = rand(0, 99999) . $_POST['mp3_id'] . "_mp3." . $ext;

            $tmp = $_FILES['mp3_local']['tmp_name'];

            if (move_uploaded_file($tmp, $path . $mp3_local)) {
                $mp3_url = $mp3_local;
            } else {
                _e("Error in uploading mp3 file !!");
                exit;
            }
        } else {
            $mp3_url = basename($row['book_url']);
        }
    }

    if ($_FILES['album_image']['name'] != "") {
        if ($row['album_image'] != "") {
            unlink('images/thumbs/' . $row['album_image']);
            unlink('images/' . $row['album_image']);
        }

        $ext = pathinfo($_FILES['album_image']['name'], PATHINFO_EXTENSION);

        $album_image = rand(0, 99999) . "_book." . $ext;

        //Main Image
        $tpath1 = 'images/' . $album_image;

        if ($ext != 'png') {
            $pic1 = compress_image($_FILES["album_image"]["tmp_name"], $tpath1, 80);
        } else {
            $tmp = $_FILES['album_image']['tmp_name'];
            move_uploaded_file($tmp, $tpath1);
        }

        //Thumb Image 
        $thumbpath = 'images/thumbs/' . $album_image;
        $thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '300', '300');
    } else {
        $album_image = $row['album_image'];
    }

    $data = array(
        'album_name'  =>  validate_input($_POST['album_name'], ENT_QUOTES, "UTF-8"),
        'album_image'  =>  validate_input($album_image),
        'artist_ids'  => implode(',', $_POST['artist_ids']),
        'cat_ids'  => implode(',', $_POST['cat_ids']),
        'book_subscription_type' => validate_input($book_subscription_type),
        'play_time'  =>  validate_input($_POST['play_time'], ENT_QUOTES, "UTF-8"),
        'book_description'  =>  validate_input($_POST['book_description'], ENT_QUOTES, "UTF-8"),
        'book_type'  =>  validate_input($mp3_type),
        'book_url'  =>  validate_input($mp3_url),
    );

    $edit = Update('tbl_album', $data, "WHERE aid = '" . $_POST['book_id'] . "'");
    _e(mysqli_error($mysqli));

    $_SESSION['msg'] = "11";
    $_SESSION['class'] = 'success';

    header("Location:manage_books.php");
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

                        </div><!-- End Page Title -->
                        <!-- Floating Labels Form -->

                        <form class="row g-3" action="" method="post" enctype="multipart/form-data">
                            <br><br>
                            <input type="hidden" name="book_id" value="<?php if ($count > 0) {
                                                                            _e($_GET['book_id']);
                                                                        } ?>" />
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Book Subscription Type</label>
                                <select name="book_subscription_type" class="form-control label ui selection fluid dropdown">
                                    <option value="free" <?php if ($count > 0) {
                                                                if ($row['book_subscription_type'] == 'free') { ?>selected<?php }
                                                                                                                    } ?>>FREE</option>
                                    <option value="paid" <?php if ($count > 0) {
                                                                if ($row['book_subscription_type'] == 'paid') { ?>selected<?php }
                                                                                                                    } ?>>PAID</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Category</label>
                                <select name="cat_ids[]" multiple="" class="form-control label ui selection fluid dropdown">
                                    <option value="" disabled>--Select Category--</option>

                                    <?php
                                    while ($cat_row = mysqli_fetch_array($cat_result)) {
                                    ?>
                                        <option value="<?php _e($cat_row['cid']); ?>" <?php if ($count > 0) {
                                                                                            if (in_array($cat_row['cid'], explode(",", $row['cat_ids']))) { ?>selected<?php }
                                                                                                                                                                } ?>><?php _e($cat_row['category_name']); ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Book Name</label>
                                <input type="text" class="form-control" name="album_name" value="<?php if ($count > 0) {
                                                                                                        _e($row['album_name']);
                                                                                                    } ?>" id="floatingName" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Author</label>
                                <select name="artist_ids[]" multiple="" class="form-control label ui selection fluid dropdown">
                                    <option value="" disabled>--Select Author--</option>
                                    <?php
                                    while ($author_row = mysqli_fetch_array($author_result)) {
                                    ?>
                                        <option value="<?php _e($author_row['id']); ?>" <?php if ($count > 0) {
                                                                                            if (in_array($author_row['id'], explode(",", $row['artist_ids']))) { ?>selected<?php }
                                                                                                                                                                    } ?>><?php _e($author_row['artist_name']); ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Book Image</label>
                                <input type="file" name="album_image" class="form-control" value="fileupload_img" accept=".png, .jpg, .JPG , .PNG,.jpeg,.JPEG" onchange="fileValidation()" id="fileupload_img">
                                <?php if ($count > 0) { ?>
                                    <div class="fileupload_img" id="uploadPreview_img"><img type="image" src="images/<?php _e($row['album_image']); ?>" alt="image" style="width: 150px;height: 200px;margin-top:10px;" /></div>
                                <?php } else { ?>
                                    <div class="fileupload_img" id="uploadPreview_img"><img type="image" src="assets/img/add-image.png" alt="image" style="width: 150px;height: 200px;margin-top:10px;" /></div>
                                <?php } ?>
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Play Time</label>
                                <input type="text" class="form-control" name="play_time" id="floatingName" value="<?php if ($count > 0) {
                                                                                                                        _e($row['play_time']);
                                                                                                                    } ?>" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputName4" class="form-label">Upload Type</label>
                                <select name="mp3_type" id="mp3_type" class="form-control label ui selection fluid dropdown" required>
                                    <option value="server_url" <?php if ($count > 0 && $row['book_type'] == 'server_url') {
                                                                    echo 'selected';
                                                                } ?>>From Server(URL)</option>
                                    <option value="local" <?php if ($count > 0 && $row['book_type'] == 'local') {
                                                                echo 'selected';
                                                            } ?>>Browse From Device</option>
                                </select><br>
                            </div>

                            <div id="mp3_url_display" class="form-group row col-12" <?php if ($count > 0 && $row['book_type'] == 'local') {
                                                                                        echo 'style="display:none;"';
                                                                                    } ?>>
                                <label for="inputName4" class="form-label">Book PDF URL</label>
                                <input type="text" class="form-control" name="mp3_url" id="mp3_url" value="<?php if ($count > 0) {
                                                                                                                echo htmlspecialchars($row['book_url']);
                                                                                                            } ?>">
                            </div>

                            <div id="mp3_local_display" class="row col-12" <?php if ($count > 0 && $row['book_type'] != 'local') {
                                                                                echo 'style="display:none;"';
                                                                            } ?>>
                                <label for="inputName4" class="form-label">Book File</label>
                                <?php
                                if ($count > 0 && $row['book_type'] == 'local') {
                                    $mp3_file = $file_path . 'uploads/' . basename($row['book_url']);
                                }
                                ?>
                                <input type="file" class="form-control" name="mp3_local" id="mp3_local" class="form-control" accept=".pdf,.epub">
                                <div class="col-12">
                                    <span class="badge bg-danger">PDF OR EPUB FILE ONLY.</span><br>
                                    <?php if ($count > 0) { ?>
                                        <label for="inputName4" class="form-label">Current URL</label>
                                        <input type="text" value="<?php if ($count > 0) {
                                                                        echo htmlspecialchars($mp3_file);
                                                                    } ?>" disabled class="form-control">
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Book Description</label>
                                <textarea name="book_description" id="book_description" class="form-control">
                                <?php if ($count > 0) {
                                    _e($row['book_description'], true);
                                } ?>
                                </textarea>
                                <script>
                                    CKEDITOR.replace('book_description');
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
        function handleInitialState() {
            var type = $("#mp3_type").val();
            if (type == "server_url") {
                $("#mp3_url_display").show();
                $("#mp3_local_display").hide();
                $("#mp3_local").val('');
            } else {
                $("#mp3_url_display").hide();
                $("#mp3_local_display").show();
            }
        }

        handleInitialState();

        $("#mp3_type").change(function() {
            var type = $(this).val();
            if (type == "server_url") {
                $("#mp3_url_display").show();
                $("#mp3_local_display").hide();
                $("#mp3_local").val('');
            } else {
                $("#mp3_url_display").hide();
                $("#mp3_local_display").show();
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