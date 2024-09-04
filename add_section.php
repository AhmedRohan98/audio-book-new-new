<?php
$page_title = 'Add Section';
include("includes/header.php");

$album_qry = "SELECT * FROM tbl_album ORDER BY album_name";
$album_result = mysqli_query($mysqli, $album_qry);
if (isset($_POST['submit']) and isset($_GET['add'])  && PURCHASE == '') {
    if (
        empty(validate_input($_POST['section_title'])) ||
        (!isset($_POST['section_books']) || count($_POST['section_books']) <= 0)
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:add_section.php?add=yes");
        exit;
    }

    $data = array(
        'section_title' =>  validate_input($_POST['section_title']),
        'section_books' =>  implode(',', $_POST['section_books'])
    );
    $qry = Insert('tbl_home_section_2', $data);
    $_SESSION['msg'] = "10";
    header("Location:home_section.php");
    exit;
}
$count = 0;
if (isset($_GET['section_id'])) {
    $sql = "SELECT * FROM tbl_home_section_2 where id='" . $_GET['section_id'] . "'";
    $result = mysqli_query($mysqli, $sql);
    $row = mysqli_fetch_assoc($result);
    $count = $result->num_rows;
}

if (isset($_POST['submit']) && isset($_POST['section_id'])  && PURCHASE == '') {
    if (
        empty(validate_input($_POST['section_title'])) ||
        (!isset($_POST['section_books']) || count($_POST['section_books']) <= 0)
    ) {
        $_SESSION['msg'] = "15";
        $_SESSION['class'] = 'error';
        header("Location:edit_section_2.php?section_id=" . $_GET['section_id']);
        exit;
    }
    $section_title = html_entity_decode($_POST['section_title'], ENT_QUOTES, "UTF-8");

    $data = array(
        'section_title' =>  $section_title,
        'section_books' =>  implode(',', $_POST['section_books'])
    );

    $edit_id = Update('tbl_home_section_2', $data, "WHERE id = '" . $_POST['section_id'] . "'");

    $_SESSION['msg'] = "11";
    header("Location:home_section.php");
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
                            <input type="hidden" name="section_id" value="<?php if ($count > 0) {
                                                                                _e($_GET['section_id']);
                                                                            } ?>" />
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Section Title</label>
                                <input type="text" class="form-control" name="section_title" value="<?php if ($count > 0) {
                                                                                                        _e($row['section_title']);
                                                                                                    } ?>" id="floatingName" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Books</label>
                                <select name="section_books[]" multiple="" class="form-control label ui selection fluid dropdown">
                                    <option value="" disabled>--Select Book--</option>
                                    <?php
                                    while ($album_row = mysqli_fetch_array($album_result)) {
                                        $selected = '';
                                        if ($count > 0 && in_array($album_row['aid'], explode(",", $row['section_books']))) {
                                            $selected = 'selected="selected"';
                                        }
                                    ?>
                                        <option value="<?php echo htmlspecialchars($album_row['aid']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($album_row['album_name']); ?></option>
                                    <?php } ?>
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
<?php include("includes/footer.php"); ?>