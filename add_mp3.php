<?php
$page_title = (isset($_GET['mp3_id'])) ? 'Edit Chapter' : 'Add Chapter';
include("includes/header.php");
require_once("thumbnail_images.class.php");

$file_path = getBaseUrl();

$cat_qry = "SELECT * FROM tbl_category ORDER BY category_name";
$cat_result = mysqli_query($mysqli, $cat_qry);

$album_qry = "SELECT * FROM tbl_album ORDER BY album_name";
$album_result = mysqli_query($mysqli, $album_qry);

$art_qry = "SELECT * FROM tbl_artist ORDER BY artist_name";
$art_result = mysqli_query($mysqli, $art_qry);

if (isset($_POST['submit']) and isset($_GET['add'])  && PURCHASE == '') {
    $mp3_type = trim($_POST['mp3_type']);
    if (
        empty(validate_input($_POST['mp3_title'])) ||
        empty(validate_input($_POST['album_id'])) ||
        empty(validate_input($mp3_type)) ||
        ($mp3_type == 'server_url' && empty($_POST['mp3_url'])) ||
        ($mp3_type == 'local' && (!isset($_FILES['mp3_local']) || $_FILES['mp3_local']['size'] == 0)) ||
        empty(validate_input($_POST['mp3_duration'])) ||
        empty(validate_input($_POST['mp3_description'], true))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:add_mp3.php?add=yes");
        exit;
    }

    if ($mp3_type == 'server_url') {
        $mp3_url = validate_input(trim($_POST['mp3_url']));
    } else {
        $path = "uploads/"; //set your folder path

        $mp3_local = rand(0, 99999) . "_" . str_replace(" ", "-", $_FILES['mp3_local']['name']);

        $tmp = $_FILES['mp3_local']['tmp_name'];

        if (move_uploaded_file($tmp, $path . $mp3_local)) {
            $mp3_url = $file_path . 'uploads/' . $mp3_local;
        } else {
            _e("Error in uploading mp3 file !!");
            exit;
        }
    }

    $mp3_thumbnail = '';

    $data = array(
        'cat_id'  =>  '0',
        'album_id'  =>  validate_input($_POST['album_id']),
        'mp3_title'  =>  validate_input($_POST['mp3_title'], ENT_QUOTES, "UTF-8"),
        'mp3_type'  =>  validate_input($mp3_type),
        'mp3_url'  =>  validate_input($mp3_url),
        'mp3_thumbnail'  =>  validate_input($mp3_thumbnail),
        'mp3_duration'  =>  validate_input($_POST['mp3_duration']),
        'mp3_artist'  => '0',
        'mp3_description'  =>  validate_input($_POST['mp3_description'], ENT_QUOTES, "UTF-8"),
    );

    $qry = Insert('tbl_mp3', $data);

    $_SESSION['msg'] = "10";
    $_SESSION['class'] = "success";
    header("Location:manage_mp3.php");
    exit;
}
$count = 0;
if (isset($_GET['mp3_id'])) {

    $qry = "SELECT * FROM tbl_mp3 where id='" . $_GET['mp3_id'] . "'";
    $result = mysqli_query($mysqli, $qry);
    $row = mysqli_fetch_assoc($result);
    $count = $result->num_rows;
}
if (isset($_POST['submit']) and isset($_GET['mp3_id'])  && PURCHASE == '') {
    $mp3_type = trim($_POST['mp3_type']);
    if (
        empty(validate_input($_POST['mp3_title'])) ||
        empty(validate_input($_POST['album_id'])) ||
        empty(validate_input($mp3_type)) ||
        empty(validate_input($_POST['mp3_duration'])) ||
        empty(validate_input($_POST['mp3_description'], true))
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:add_mp3.php?mp3_id=" . $_GET['mp3_id']);
        exit;
    }

    if ($mp3_type == 'server_url') {
        $mp3_url = htmlentities(trim($_POST['mp3_url']));
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
                $mp3_url = $file_path . 'uploads/' . $mp3_local;
            } else {
                _e("Error in uploading mp3 file !!");
                exit;
            }
        } else {
            $mp3_url = basename($row['mp3_url']);
        }
    }

    $mp3_thumbnail = '';


    $data = array(
        'cat_id'  =>  '0',
        'album_id'  =>  validate_input($_POST['album_id']),
        'mp3_title'  =>  validate_input($_POST['mp3_title'], ENT_QUOTES, "UTF-8"),
        'mp3_type'  =>  validate_input($_POST['mp3_type']),
        'mp3_url'  =>  validate_input($mp3_url),
        'mp3_thumbnail'  =>  validate_input($mp3_thumbnail),
        'mp3_duration'  =>  validate_input($_POST['mp3_duration']),
        'mp3_artist'  => '0',
        'mp3_description'  =>  validate_input($_POST['mp3_description'], true),
    );

    $qry = Update('tbl_mp3', $data, "WHERE id = '" . $_POST['mp3_id'] . "'");


    $_SESSION['msg'] = "11";
    $_SESSION['class'] = "success";
    header("Location:manage_mp3.php");
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
                            <input type="hidden" name="mp3_id" value="<?php if ($count > 0) {
                                                                            _e($_GET['mp3_id']);
                                                                        } ?>" />
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Title</label>
                                <input type="text" class="form-control" name="mp3_title" value="<?php if ($count > 0) {
                                                                                                    _e($row['mp3_title']);
                                                                                                } ?>" id="floatingName" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Book</label>
                                <select name="album_id" class="form-control label ui selection fluid dropdown">
                                    <option value="" disabled>--Select Book--</option>
                                    <?php
                                    while ($album_row = mysqli_fetch_array($album_result)) {
                                    ?>
                                        <option value="<?php _e($album_row['aid']); ?>" <?php if ($count > 0) {
                                                                                            if ($album_row['aid'] == $row['album_id']) { ?>selected<?php }
                                                                                                                                                                } ?>><?php _e($album_row['album_name']); ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="inputName4" class="form-label">Upload Type</label>
                                <select name="mp3_type" id="mp3_type" class="form-control label ui selection fluid dropdown" required>
                                    <option value="server_url" <?php if ($count > 0 && $row['mp3_type'] == 'server_url') {
                                                                    echo 'selected';
                                                                } ?>>From Server(URL)</option>
                                    <option value="local" <?php if ($count > 0 && $row['mp3_type'] == 'local') {
                                                                echo 'selected';
                                                            } ?>>Browse From Device</option>
                                </select><br>
                            </div>

                            <div id="mp3_url_display" class="form-group row col-12" <?php if ($count > 0 && $row['mp3_type'] == 'local') {
                                                                                        echo 'style="display:none;"';
                                                                                    } ?>>
                                <label for="inputName4" class="form-label">Chapter URL</label>
                                <input type="text" class="form-control" name="mp3_url" id="mp3_url" value="<?php if ($count > 0) {
                                                                                                                echo htmlspecialchars($row['mp3_url']);
                                                                                                            } ?>">
                            </div>

                         <div id="mp3_local_display" class="row col-12" <?php if ($count > 0 && $row['mp3_type'] != 'local') { echo 'style="display:none;"'; } ?>>
    <label for="inputName4" class="form-label">Chapter File</label>
    <?php
    if ($count > 0 && $row['mp3_type'] == 'local') {
        $mp3_file = $file_path . 'uploads/' . basename($row['mp3_url']);
    }
    ?>
    <input type="file" class="form-control" name="mp3_local" id="mp3_local" accept=".mp3" onchange="audioFileValidation()">
    <div class="col-12">
        <br><audio id="audioPreview" src="<?php if ($count > 0 && $row['mp3_type'] == 'local') { echo htmlspecialchars($mp3_file); } ?>" controls></audio>
    </div>
</div>

                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Chapter Duration</label>
                                <input type="text" class="form-control" name="mp3_duration" value="<?php if ($count > 0) {
                                                                                                        _e($row['mp3_duration']);
                                                                                                    } ?>" id="floatingName" class="form-control">
                                <div class="col-12">
                                    <br><label for="inputNanme4" class="form-label">Book Description</label>
                                    <textarea name="mp3_description" id="mp3_description" class="form-control">
                                    <?php if ($count > 0) {
                                        _e($row['mp3_description'], true);
                                    } ?>
                                </textarea>
                                    <script>
                                        CKEDITOR.replace('mp3_description');
                                    </script>
                                </div><br>
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
                $("#audio").attr('src', '');
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
                $("#audio").attr('src', '');
            } else {
                $("#mp3_url_display").hide();
                $("#mp3_local_display").show();
            }
        });
    });
</script>

<script type="text/javascript">
  function audioFileValidation() {
        var fileInput = document.getElementById('mp3_local');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.mp3)$/i;
        if (!allowedExtensions.exec(filePath)) {
            alert('Please upload an audio file with extension .mp3 only.');
            fileInput.value = '';
            return false;
        } else {
            // Audio preview
            if (fileInput.files && fileInput.files[0]) {
                var audioElement = document.getElementById('audioPreview');
                var reader = new FileReader();
                reader.onload = function(e) {
                    audioElement.src = e.target.result;
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }
</script>


<?php include("includes/footer.php"); ?>