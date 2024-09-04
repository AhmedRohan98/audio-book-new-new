<?php
$page_title = "View Comments";

include('includes/header.php');

function total_comments($book_id)
{
    global $mysqli;

    $query = "SELECT COUNT(*) AS total_comments FROM tbl_comments WHERE `book_id`='$book_id'";
    $sql = mysqli_query($mysqli, $query) or die(mysqli_error());
    $row = mysqli_fetch_assoc($sql);
    return stripslashes($row['total_comments']);
}

function get_thumb($filename, $thumb_size)
{

    $file_path = getBaseUrl();

    return $thumb_path = $file_path . 'thumb.php?src=' . $filename . '&size=' . $thumb_size;
}


$id = trim($_GET['book_id']);

$sql = "SELECT * FROM tbl_books LEFT JOIN tbl_category ON tbl_books.cat_id=tbl_category.cid WHERE tbl_books.status='1' AND tbl_books.id='$id'";
$res = mysqli_query($mysqli, $sql);
$row = mysqli_fetch_assoc($res);


$sql1 = "SELECT tbl_comments.*, tbl_users.`user_image` FROM tbl_comments,tbl_users WHERE tbl_comments.`book_id`='$id' and tbl_users.name=tbl_comments.user_name ORDER BY tbl_comments.`comment_text` DESC";
echo $sql1;
$res_comment = mysqli_query($mysqli, $sql1) or die(mysqli_error($mysqli));
$arr_dates = array();
$i = 0;

while ($comment = mysqli_fetch_assoc($res_comment)) {
    $dates = date('d M Y', $comment['comment_on']);
    $arr_dates[$dates][$i++] = $comment;
}
?>
<style>
    .app-messaging-container {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -ms-flex-align: start;
        align-items: flex-start;
        -ms-flex-pack: start;
        justify-content: flex-start;
        padding: 0 30px;
        margin-bottom: 30px;
        -ms-flex: 1;
        flex: 1;
    }

    a {
        color: #337ab7;
        text-decoration: none;
    }

    .pull-left {
        float: left !important;
    }

    .fa {
        display: inline-block;
        font: normal normal normal 14px/1 FontAwesome;
        font-size: inherit;
        text-rendering: auto;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .app-messaging {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: row;
        flex-direction: row;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -ms-flex-align: start;
        align-items: flex-start;
        -ms-flex-pack: start;
        justify-content: flex-start;
        -ms-flex: 1;
        flex: 1;
        width: 100%;
        background-color: #FFF;
        border-radius: 3px;
        box-shadow: 0 1px 2px #c8d1d3;
    }

    .app-messaging .chat-group {
        height: 100% !important;
        width: 300px;
        min-width: 0;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -ms-flex-align: start;
        align-items: flex-start;
        -ms-flex-pack: start;
        justify-content: flex-start;
        transition: all 0.3s ease;
    }

    .app-messaging .chat-group .heading {
        width: 100%;
        padding: 0 20px;
        height: 60px;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: row;
        flex-direction: row;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-pack: start;
        justify-content: flex-start;
        color: #8d9293;
        border-right: 1px solid #dfe6e8;
        border-bottom: 1px solid #dfe6e8;
    }

    .app-messaging .chat-group ul.group {
        background-color: #FFF;
        -ms-flex: 1;
        flex: 1;
        width: 100%;
        padding: 0;
        list-style: none;
        margin-bottom: 0;
        border-right: 1px solid #dfe6e8;
        overflow: auto;
        position: relative;
    }

    .full-height {
        height: 100%;
    }

    .app-messaging .chat-group ul.group>li.message {
        padding: 0;
        border-bottom: 1px solid #dfe6e8;
    }

    .app-messaging .chat-group ul.group>li {
        padding: 20px;
    }

    .app-messaging .chat-group ul.group>li.message a {
        display: block;
        text-decoration: none;
        color: #444;
        padding: 20px;
    }

    .app-messaging .chat-group ul.group>li.message a .message {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: row;
        flex-direction: row;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-pack: start;
        justify-content: flex-start;
    }

    .app-messaging .chat-group ul.group>li.message a .message .content {
        -ms-flex: 1;
        flex: 1;
    }

    .app-messaging .messaging {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -ms-flex-align: start;
        align-items: flex-start;
        -ms-flex-pack: start;
        justify-content: flex-start;
        height: 100%;
        width: auto;
        min-width: auto;
        -ms-flex: 1;
        flex: 1;
        transition: all 0.3s ease;
    }

    .app-messaging .messaging>.heading {
        padding: 0 20px;
        height: 60px;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: row;
        flex-direction: row;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-pack: start;
        justify-content: flex-start;
        width: 100%;
        color: #8d9293;
        border-bottom: 1px solid #dfe6e8;
    }

    .app-messaging .messaging ul.chat {
        -ms-flex: 1;
        flex: 1;
        min-width: 0;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -ms-flex-align: start;
        align-items: flex-start;
        -ms-flex-pack: start;
        justify-content: flex-start;
        background-color: #FFF;
        width: 100%;
        list-style: none;
        padding: 0;
        margin: 0;
        overflow: auto;
        position: relative;
    }

    .app-messaging .messaging>.heading .title {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: row;
        flex-direction: row;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-pack: start;
        justify-content: flex-start;
    }

    .app-messaging .messaging>.heading .title .btn-back {
        font-size: 2em;
        color: #444;
        display: inline-block;
        text-decoration: none;
        width: 30px;
        text-align: center;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: row;
        flex-direction: row;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-pack: start;
        justify-content: flex-start;
    }

    .app-messaging .messaging ul.chat>li.line {
        width: 100%;
        position: relative;
        text-align: center;
        font-size: 0.9em;
        z-index: 2;
        padding-right: 20px;
        margin-top: 20px;
        margin-bottom: 20px;
        color: #8d9293;
    }

    .app-messaging .messaging ul.chat>li.line .title {
        background-color: #FFF;
        position: relative;
        z-index: 2;
        width: 140px;
        margin: 0 auto;
    }

    .app-messaging .messaging ul.chat>li.line:after {
        content: '';
        position: absolute;
        width: 50%;
        bottom: 50%;
        left: 50%;
        transform: translate(-50%, 0);
        z-index: 1;
        border-bottom: 1px solid #dfe6e8;
    }

    .app-messaging .messaging ul.chat>li .message {
        background-color: #e7edee;
        border-radius: 3px;
        padding: 15px;
    }

    .app-messaging .messaging ul.chat>li .info {
        padding: 5px 0;
        font-size: 0.85em;
        color: #8d9293;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: row;
        flex-direction: row;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -ms-flex-align: start;
        align-items: flex-start;
        -ms-flex-pack: start;
        justify-content: flex-start;
    }

    .app-messaging .messaging ul.chat>li .info>* {
        margin-right: 10px;
    }

    .btn_edit,
    .btn_delete,
    .btn_cust {
        padding: 5px 10px !important;
    }

    div#collapseMessaging {
        margin-top: 22px !important;
        border: 1px solid gray;
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
                <div class="card card_height">
                    <div class="card-body">
                        <div class="app-messaging-container">
                            <div class="app-messaging" id="collapseMessaging">
                                <div class="chat-group">
                                    <div class="heading" style="font-size: 16px">Books Description</div>
                                    <ul class="group full-height">
                                        <li class="message">
                                            <a href="javascript:void(0)">
                                                <div class="message">
                                                    <i class="fa fa-tags"></i>
                                                    <div class="content">
                                                        <div class="title">&nbsp;&nbsp;<?= $row['category_name'] ?></div>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="message">
                                            <a href="javascript:void(0)">
                                                <div class="message">
                                                    <i class="fa fa-eye"></i>
                                                    <div class="content">
                                                        <div class="title">&nbsp;&nbsp;<?= $row['book_views'] ?> Views</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="message">
                                            <a href="javascript:void(0)">
                                                <div class="message">
                                                    <i class="fa fa-comments-o"></i>
                                                    <div class="content">
                                                        <div class="title">&nbsp;&nbsp;<span class="total_comments"><?= total_comments($id) ?></span> Comments</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="messaging">
                                    <div class="heading">
                                        <div class="title" style="font-size: 16px">
                                            <a class="btn-back" href="manage_comments.php">
                                                <i class="fa fa-angle-left" aria-hidden="true"></i>
                                            </a>
                                            <strong style="font-weight: 600">Title: </strong>&nbsp;&nbsp;<?= $row['book_title'] ?>
                                        </div>
                                        <div class="action"></div>
                                    </div>
                                    <ul class="chat" style="flex: unset;height: 500px;">
                                        <?php
                                        if (!empty($arr_dates)) {
                                            foreach ($arr_dates as $key => $val) {
                                        ?>
                                                <li class="line">
                                                    <div class="title"><?= $key ?></div>
                                                </li>
                                                <?php
                                                foreach ($val as $key1 => $value) {

                                                    $img = '';
                                                    if (!file_exists('images/' . $value['user_profile']) || $value['user_profile'] == '') {
                                                        $img = 'user-icons.jpg';
                                                    } else {
                                                        $img = $value['user_profile'];
                                                    }
                                                ?>
                                                    <li class="<?= $value['id'] ?>" style="padding-right: 20px;width: 100%;">
                                                        <div class="message" style="padding: 5px 10px 15px 5px;min-height: 60px">
                                                            <!--<img src="<//?=get_thumb('images/'.$img,'50x50')?>" style="float: left;margin-right: 10px;border-radius: 50%;box-shadow: 0px 0px 2px 1px #ccc">-->
                                                            <span style="color: #000;font-weight: 600"><? //=$value['user_name']
                                                                                                        ?></span>
                                                            <br />
                                                            <span style="display: flex;justify-content: space-between;">
                                                                <span>
                                                                    <?= $value['comment_text'] ?>
                                                                </span>
                                                                <span>
                                                                    <a href="" class="btn btn-danger btn_delete" data-id="<?= $value['id'] ?>" data-book="<?= $id ?>"> <i class="bi bi-trash-fill"></i></a>
                                                                </span>
                                                            </span>
                                                        </div>
                                                        <div class="info" style="clear: both;">
                                                            <div class="datetime">
                                                                <?= date('h:i A', $value['comment_on']); ?>

                                                            </div>
                                                        </div>
                                                    </li>
                                            <?php
                                                } // end of inner foreach
                                            }    // end of main foreach
                                        }    // end of if
                                        else {
                                            ?>
                                            <div class="jumbotron" style="width: 100%; text-align: center;">
                                                <h3>Sorry !</h3>
                                                <p>No comments available</p>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        $(".btn_delete").click(function(e) {
            e.preventDefault();

            var _id = $(this).data("id");

            swal({
                    title: "Are you sure?",
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
                                'action': 'removeComment'
                            },
                            success: function(res) {
                                console.log(res);
                                if (res.status == '1') {
                                    swal({
                                        title: "Successfully",
                                        text: res.msg,
                                        type: "success"
                                    }, function() {
                                        location.reload();
                                    });
                                } else if (res.status == '2') {
                                    swal(res.msg);
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
<?php include('includes/footer.php'); ?>