<?php
$page_title = "Example API urls";
include("includes/header.php");

$file_path = getBaseUrl();
?>
<style type="text/css">
    /* width */
    ::-webkit-scrollbar {
        width: 10px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #888;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
<main id="main" class="main">
    <section class="section">
        <div class="row  g-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body g-3"><br>
                        <div class="pagetitle mb-5">
                            <h1><?php _e($page_title); ?></h1>

                        </div><!-- End Page Title -->
                        <pre style="overflow-x: scroll !important;">
                        <br><b>API URL</b> <?php echo $file_path . 'api.php'; ?>
                                    <br><b>Home</b> (Method: home) (Parameter: user_id)
                                    <br><b>Single Book</b> (Method: single_book) (Parameter: user_id,page,Book_id)
                                    <br><b>Chapter By Book</b> (Method: single_book) (Parameter: user_id,page,Book_id)
                                    <br><b>Home Section</b> (Method: home_section) (Parameter: page)
                                    <br><b>Home Section 2</b> (Method: home_section_2) (Parameter: page)
                                    <br><b>Latest Books</b> (Method: latest) (Parameter: user_id, page)
                                    <br><b>Books List By Banner</b> (Method: banner_songs) (Parameter: user_id, banner_id, page)
                                    <br><b>Home Section Books</b> (Method: home_section_id) (Parameter: page,homesection_id)
                                    <br><b>Home Section2 Books</b> (Method: home_section_id_2) (Parameter: page,homesection_id_2)
                                    <br><b>Category List</b> (Method: cat_list) (Parameter: page)
                                    <br><b>Books List By Cat ID</b> (Method: cat_songs) (Parameter: user_id, cat_id, page)
                                    <br><b>Recent Author List</b> (Method: recent_artist_list)
                                    <br><b>Author List</b> (Method: artist_list) (Parameter: page)
                                    <br><b>Author Book List</b> (Method: artist_album_list) (Parameter: artist_id,page)
                                    <br><b>Book List</b> (Method: album_list) (Parameter: page)
                                    <br><b>Chapter List By Book ID</b> (Method: album_songs) (Parameter: user_id, album_id, page)
                                    <br><b>Playlist List</b> (Method: playlist) (Parameter: page)
                                    <br><b>Chapter Download </b> (Method: song_download) (Parameter: song_id)
                                    <br><b>Book Download </b> (Method: book_download) (Parameter: book_id)
                                    <br><b>Playlist Download </b> (Method: playlist_download) (Parameter: playlist_id)
                                    <br><b>Search</b> (Method: song_search) (Parameter: user_id, search_text, search_type, page)(For Particualr : search_type=album,artist,songs)
                                    <br><b>Chapter Rating </b> (Method: song_rating) (Parameter: post_id, user_id, rate)
                                    <br><b>Book Rating </b> (Method: book_rating) (Parameter: post_id, user_id, rate)
                                    <br><b>Playlist Rating </b> (Method: playlist_rating) (Parameter: post_id, user_id, rate)
                                    <br><b>Report Book</b>(Method: song_report) (Parameter:user_id,song_id,report)
                                    <br><b>Book Suggestion</b>(Method: song_suggest) (Parameter: user_id, Book_title, message) (File: Book_image)
                                    <br><b>User Register</b>(Method: user_register) (Parameter: name, email, password, phone, auth_id, type(Normal, Google, Facebook))
                                    <br><b>User Login</b>(Method: user_login) (Parameter: email, password, auth_id, type[Normal, Google, Facebook])
                                    <br><b>User Profile</b>(Method: user_profile) (Parameter:user_id)
                                    <br><b>User Profile Update</b>(Method: user_profile_update) (Parameter: user_id, name, email, password, phone)
                                    <br><b>Forgot Password</b>(Method: forgot_pass) (Parameter: user_email)
                                    <br><b>Favourite Post</b>(Method: favourite_post) (Parameter: post_id, user_id, type[fav_post])
                                    <br><b>Get Favourite Post</b>(Method: get_favourite_post) (Parameter: user_id, page, type[fav_post])
                                    <br><b>App Details</b>(Method: app_details)
                                </pre>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>
<?php include("includes/footer.php"); ?>