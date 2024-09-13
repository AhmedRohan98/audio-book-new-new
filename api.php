<?php
include("includes/connection.php");
include("includes/function.php");
include("language/app_language.php");
include("includes/smtp_email.php");

define("APP_FROM_EMAIL", $settings_details['email_from']);

define("PACKAGE_NAME", $settings_details['package_name']);

date_default_timezone_set("Asia/Kolkata");

$file_path = getBaseUrl();

function generateRandomPassword($length = 10)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}


function is_favourite($post_id, $user_id, $type = 'fav_post')
{
	global $mysqli;

	$sql_favourite = "SELECT * FROM tbl_favourite WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `type`='$type'";
	$res_favourite = mysqli_query($mysqli, $sql_favourite);

	if (mysqli_num_rows($res_favourite) > 0) {
		return true;
	} else {
		return false;
	}
}


$get_method = checkSignSalt($_POST['data']);

if ($get_method['method_name'] == "home") {
	$limit = API_LATEST_LIMIT;
	$jsonObj_1 = array();

	$query1 = "SELECT * FROM tbl_banner WHERE status='1' ORDER BY tbl_banner.`bid` DESC";
	$sql1 = mysqli_query($mysqli, $query1);
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	while ($data1 = mysqli_fetch_assoc($sql1)) {

		$row1['bid'] = $data1['bid'];
		$row1['banner_title'] = html_entity_decode($data1['banner_title'], ENT_QUOTES, "UTF-8");
		$row1['banner_sort_info'] = $data1['banner_sort_info'];
		$row1['total_views'] = $data1['total_views'];
		$banner_image = $data1['banner_image'];
		if (empty($banner_image)) {
			$row1['banner_image'] = $file_path . 'images/add-image.png';
			$row1['banner_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row1['banner_image'] = $file_path . 'images/' . $data1['banner_image'];
			$row1['banner_image_thumb'] = $file_path . 'images/thumbs/' . $data1['banner_image'];
		}



		$songs_ids = trim($data1['album']);

		// $query01 = "SELECT * FROM tbl_album	LEFT JOIN tbl_category ON tbl_album.`aid`= tbl_category.`cid` WHERE tbl_album.`aid` IN ($songs_ids) AND tbl_category.`status`='1' AND tbl_album.`status`='1'";


		$query01 = "SELECT * FROM tbl_album	LEFT JOIN tbl_category ON tbl_album.`aid`= tbl_category.`cid` WHERE tbl_album.`aid` IN ($songs_ids)";

		$sql01 = mysqli_query($mysqli, $query01);

		$row1['total_books'] = mysqli_num_rows($sql01);


		if (mysqli_num_rows($sql01) > 0) {
			while ($data01 = mysqli_fetch_assoc($sql01)) {


				$total_songs++;
				$row01['aid'] = $data01['aid'];
				$row01['author_ids'] = $data01['artist_ids'];
				$row01['cat_ids'] = $data01['cat_ids'];
				$row01['book_subscription_type'] = $data01['book_subscription_type'];
				$row01['book_name'] = html_entity_decode($data01['album_name'], ENT_QUOTES, "UTF-8");

				$book_description = html_entity_decode($data01['book_description'], ENT_QUOTES, "UTF-8");

				$album_image = $data01['album_image'];
				if (empty($album_image)) {
					$row01['book_image'] = $file_path . 'images/add-image.png';
					$row01['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row01['book_image'] = $file_path . 'images/' . $data01['album_image'];
					$row01['book_image_thumb'] = $file_path . 'images/thumbs/' . $data01['album_image'];
				}

				$row01['is_favourite'] = is_favourite($data01['aid'], $user_id);
				$row01['play_time'] = $data01['play_time'];
				$row01['book_description'] =  html_entity_decode($data01['book_description'], ENT_QUOTES, "UTF-8");

				if ($data01['book_type'] == 'local') {
					$book_file = $file_path . 'uploads/' . basename($data01['book_url']);
				} else if ($data01['book_type'] == 'server_url') {
					$book_file = $data01['book_url'];
				}

				$row01['book_url'] = $book_file;
				$row01['total_views'] = $data01['total_views'];
				$row01['total_rate'] = $data01['total_rate'];
				$row01['rate_avg'] = $data01['rate_avg'];
				$row01['total_download'] = $data01['total_download'];
				$row1['book_list'][] = $row01;
			}
		} else {
			$row1['book_list'] = array();
		}

		$view_qry = mysqli_query($mysqli, "UPDATE tbl_banner SET total_views = total_views + 1 WHERE bid = '" . $data1['bid'] . "'");

		array_push($jsonObj_1, $row1);
		unset($row1['book_list']);
	}

	$row['home_banner'] = $jsonObj_1;

	$jsonObj_123 = array();

	$query1 = "SELECT * FROM tbl_album WHERE status='1' ORDER BY tbl_album.`total_views` DESC LIMIT " . $limit . " ";
	$sql1 = mysqli_query($mysqli, $query1);
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	while ($data123 = mysqli_fetch_assoc($sql1)) {

		$row123['aid'] = $data123['aid'];
		$row123['book_subscription_type'] = $data123['book_subscription_type'];
		$row123['book_name'] = html_entity_decode($data123['album_name'], ENT_QUOTES, "UTF-8");

		$album_image = $data123['album_image'];
		if (empty($album_image)) {
			$row123['book_image'] = $file_path . 'images/add-image.png';
			$row123['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row123['book_image'] = $file_path . 'images/' . $data123['album_image'];
			$row123['book_image_thumb'] = $file_path . 'images/thumbs/' . $data123['album_image'];;
		}

		$row123['is_favourite'] = is_favourite($data123['aid'], $user_id);
		$row123['play_time'] = $data123['play_time'];
		$row123['book_description'] = html_entity_decode($data123['book_description'], ENT_QUOTES, "UTF-8");

		if ($data123['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data123['book_url']);
		} else if ($data123['book_type'] == 'server_url') {
			$book_file = $data123['book_url'];
		}

		$row123['book_url'] = $book_file;

		$row123['total_views'] = $data123['total_views'];
		$row123['total_rate'] = $data123['total_rate'];
		$row123['rate_avg'] = $data123['rate_avg'];
		$row123['total_download'] = $data123['total_download'];

		$artist_ids = trim($data123['artist_ids']);

		$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";

		$sql01 = mysqli_query($mysqli, $query01);

		$row123['total_author'] = mysqli_num_rows($sql01);

		$cat_ids = trim($data123['cat_ids']);

		$query012 = "SELECT * FROM tbl_category WHERE tbl_category.`cid` IN ($cat_ids) ";

		$sql012 = mysqli_query($mysqli, $query012);

		$row123['total_category'] = mysqli_num_rows($sql012);



		if (mysqli_num_rows($sql01) > 0) {
			while ($data0123 = mysqli_fetch_assoc($sql01)) {
				$total_songs++;
				$row0123['id'] = $data0123['id'];
				$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

				$artist_image = $data0123['artist_image'];
				if (empty($artist_image)) {
					$row0123['Author_image'] = $file_path . 'images/add-image.png';
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row0123['Author_image'] = $file_path . 'images/' . $data0123['artist_image'];
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
				}

				$row123['author_list'][] = $row0123;
			}
		} else {
			$row123['author_list'] = array();
		}

		if (mysqli_num_rows($sql012) > 0) {

			while ($data012345 = mysqli_fetch_assoc($sql012)) {
				$total_songs++;
				$row012345['cid'] = $data012345['cid'];
				$row012345['category_name'] =  html_entity_decode($data012345['category_name'], ENT_QUOTES, "UTF-8");
				$row123['cat_list'][] = $row012345;
			}
		} else {
			$row123['cat_list'] = array();
		}

		array_push($jsonObj_123, $row123);

		unset($row123['cat_list']);
		unset($row123['author_list']);
	}

	$row['trending_books'] = $jsonObj_123;

	$jsonObj4 = array();

	$query4 = "SELECT * FROM tbl_album WHERE status='1' ORDER BY tbl_album.`aid` DESC LIMIT " . $limit . "";
	$sql4 = mysqli_query($mysqli, $query4);
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	while ($data4 = mysqli_fetch_assoc($sql4)) {
		$row4['aid'] = $data4['aid'];
		$row4['book_subscription_type'] = $data4['book_subscription_type'];
		$row4['book_name'] =  html_entity_decode($data4['album_name'], ENT_QUOTES, "UTF-8");

		$album_image = $data4['album_image'];
		if (empty($album_image)) {
			$row4['book_image'] = $file_path . 'images/add-image.png';
			$row4['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row4['book_image'] = $file_path . 'images/' . $data4['album_image'];
			$row4['book_image_thumb'] = $file_path . 'images/thumbs/' . $data4['album_image'];
		}
		$row4['is_favourite'] = is_favourite($data4['aid'], $user_id);
		$row4['play_time'] = $data4['play_time'];
		$row4['book_description'] = html_entity_decode($data4['book_description'], ENT_QUOTES, "UTF-8");

		if ($data4['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data4['book_url']);
		} else if ($data4['book_type'] == 'server_url') {
			$book_file = $data4['book_url'];
		}

		$row4['book_url'] = $book_file;
		$row4['total_views'] = $data4['total_views'];
		$row4['total_rate'] = $data4['total_rate'];
		$row4['rate_avg'] = $data4['rate_avg'];
		$row4['total_download'] = $data4['total_download'];

		$artist_ids = trim($data4['artist_ids']);

		$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";

		$sql01 = mysqli_query($mysqli, $query01);

		$row4['total_author'] = mysqli_num_rows($sql01);

		$cat_ids = trim($data4['cat_ids']);

		$query012 = "SELECT * FROM tbl_category WHERE tbl_category.`cid` IN ($cat_ids) ";

		$sql012 = mysqli_query($mysqli, $query012);

		$row4['total_category'] = mysqli_num_rows($sql012);



		if (mysqli_num_rows($sql01) > 0) {
			while ($data0123 = mysqli_fetch_assoc($sql01)) {
				$total_songs++;
				$row0123['id'] = $data0123['id'];
				$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

				$artist_image = $data0123['artist_image'];
				if (empty($artist_image)) {
					$row0123['Author_image'] = $file_path . 'images/add-image.png';
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row0123['Author_image'] = $file_path . 'images/' . $data0123['artist_image'];
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
				}

				$row4['author_list'][] = $row0123;
			}
		} else {
			$row4['author_list'] = array();
		}

		if (mysqli_num_rows($sql012) > 0) {

			while ($data012345 = mysqli_fetch_assoc($sql012)) {
				$total_songs++;
				$row012345['cid'] = $data012345['cid'];
				$row012345['category_name'] =  html_entity_decode($data012345['category_name'], ENT_QUOTES, "UTF-8");
				$row4['cat_list'][] = $row012345;
			}
		} else {
			$row4['cat_list'] = array();
		}

		array_push($jsonObj4, $row4);

		unset($row4['cat_list']);
		unset($row4['author_list']);
	}

	$row['latest_album'] = $jsonObj4;

	$jsonObj3 = array();

	$query3 = "SELECT id,artist_name,artist_image FROM tbl_artist ORDER BY tbl_artist.`id` DESC LIMIT " . $limit . "";
	$sql3 = mysqli_query($mysqli, $query3);

	while ($data3 = mysqli_fetch_assoc($sql3)) {
		$row3['id'] = $data3['id'];
		$row3['Author_name'] = html_entity_decode($data3['artist_name'], ENT_QUOTES, "UTF-8");

		$artist_image = $data3['artist_image'];
		if (empty($artist_image)) {
			$row3['Author_image'] = $file_path . 'images/add-image.png';
			$row3['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row3['Author_image'] = $file_path . 'images/' . $data3['artist_image'];
			$row3['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data3['artist_image'];
		}

		array_push($jsonObj3, $row3);
	}

	$row['latest_artist'] = $jsonObj3;


	$jsonObj345 = array();

	$query_rec123 = "SELECT COUNT(*) as num FROM tbl_playlist WHERE status='1' ORDER BY tbl_playlist.`pid` DESC";

	$total_pages123 = mysqli_fetch_array(mysqli_query($mysqli, $query_rec123));

	$query345 = "SELECT * FROM tbl_playlist WHERE status='1' ORDER BY tbl_playlist.`pid` DESC LIMIT " . $limit . "";
	$sql345 = mysqli_query($mysqli, $query345);

	while ($data345 = mysqli_fetch_assoc($sql345)) {
		$row345['total_records'] = $total_pages123['num'];
		$row345['pid'] = $data345['pid'];
		$row345['playlist_name'] =  html_entity_decode($data345['playlist_name'], ENT_QUOTES, "UTF-8");

		$playlist_image = $data345['playlist_image'];
		if (empty($playlist_image)) {
			$row345['playlist_image'] = $file_path . 'images/add-image.png';
			$row345['playlist_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row345['playlist_image'] = $file_path . 'images/' . cleanInput($data345['playlist_image']);
			$row345['playlist_image_thumb'] = $file_path . 'images/thumbs/' . cleanInput($data345['playlist_image']);
		}

		$row345['playlist_time'] = $data345['playlist_time'];
		$row345['playlist_description'] = $data345['playlist_description'];
		$row345['playlist_books'] = $data345['playlist_songs'];
		$row345['total_views'] = $data345['total_views'];
		$row345['total_rate'] = $data345['total_rate'];
		$row345['rate_avg'] = $data345['rate_avg'];
		$row345['total_download'] = $data345['total_download'];

		$songs_ids = trim($data345['playlist_songs']);

		$query01 = "SELECT * FROM tbl_album	LEFT JOIN tbl_category ON tbl_album.`aid`= tbl_category.`cid` WHERE tbl_album.`aid` IN ($songs_ids) AND tbl_category.`status`='1' AND tbl_album.`status`='1'";


		$sql01 = mysqli_query($mysqli, $query01);

		$row345['total_books'] = mysqli_num_rows($sql01);


		if (mysqli_num_rows($sql01) > 0) {
			while ($data01 = mysqli_fetch_assoc($sql01)) {
				$total_songs++;
				$row01['aid'] = $data01['aid'];
				$row01['author_ids'] = $data01['artist_ids'];
				$row01['cat_ids'] = $data01['cat_ids'];
				$row01['book_subscription_type'] = $data01['book_subscription_type'];
				$row01['book_name'] = html_entity_decode($data01['album_name'], ENT_QUOTES, "UTF-8");

				$book_description = html_entity_decode($data01['book_description'], ENT_QUOTES, "UTF-8");

				$album_image = $data01['album_image'];
				if (empty($album_image)) {
					$row01['book_image'] = $file_path . 'images/add-image.png';
					$row01['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row01['book_image'] = $file_path . 'images/' . $data01['album_image'];
					$row01['book_image_thumb'] = $file_path . 'images/thumbs/' . $data01['album_image'];
				}

				$row01['is_favourite'] = is_favourite($data01['aid'], $user_id);
				$row01['play_time'] = $data01['play_time'];
				$row01['book_description'] =  html_entity_decode($data01['book_description'], ENT_QUOTES, "UTF-8");

				if ($data01['book_type'] == 'local') {
					$book_file = $file_path . 'uploads/' . basename($data01['book_url']);
				} else if ($data01['book_type'] == 'server_url') {
					$book_file = $data01['book_url'];
				}

				$row01['book_url'] = $book_file;
				$row01['total_views'] = $data01['total_views'];
				$row01['total_rate'] = $data01['total_rate'];
				$row01['rate_avg'] = $data01['rate_avg'];
				$row01['total_download'] = $data01['total_download'];
				$row345['book_list'][] = $row01;
			}
		} else {
			$row345['book_list'] = array();
		}

		array_push($jsonObj345, $row345);
		unset($row345['book_list']);
	}

	$row['playlist'] = $jsonObj345;

	$jsonObj11 = array();

	$query_rec = "SELECT COUNT(*) as num FROM tbl_category WHERE status=1";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$cat_order = API_CAT_ORDER_BY;

	$query11 = "SELECT * FROM tbl_category WHERE status='1' ORDER BY tbl_category." . $cat_order . "";
	$sql11 = mysqli_query($mysqli, $query11);

	while ($data11 = mysqli_fetch_assoc($sql11)) {
		$row11['total_records'] = $total_pages['num'];

		$row11['cid'] = $data11['cid'];
		$row11['category_name'] = html_entity_decode($data11['category_name'], ENT_QUOTES, "UTF-8");

		$category_image = $data11['category_image'];
		if (empty($category_image)) {
			$row11['category_image'] = $file_path . 'images/add-image.png';
			$row11['category_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row11['category_image'] = $file_path . 'images/' . $data11['category_image'];
			$row11['category_image_thumb'] = $file_path . 'images/thumbs/' . $data11['category_image'];
		}

		//$row['category_image'] = $file_path . 'images/' . $data['category_image'];
		//	$row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];

		array_push($jsonObj11, $row11);
	}

	$row['categorylist'] = $jsonObj11;

	$set['AUDIO_BOOK'] = $row;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "h_home_banner") {
	$limit = API_LATEST_LIMIT;
	$jsonObj_1 = array();

	$query1 = "SELECT * FROM tbl_banner WHERE status='1' ORDER BY tbl_banner.`bid` DESC";
	$sql1 = mysqli_query($mysqli, $query1);
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	while ($data1 = mysqli_fetch_assoc($sql1)) {

		$row1['bid'] = $data1['bid'];
		$row1['banner_title'] = html_entity_decode($data1['banner_title'], ENT_QUOTES, "UTF-8");
		$row1['banner_sort_info'] = $data1['banner_sort_info'];
		$row1['total_views'] = $data1['total_views'];
		$banner_image = $data1['banner_image'];
		if (empty($banner_image)) {
			$row1['banner_image'] = $file_path . 'images/add-image.png';
			$row1['banner_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row1['banner_image'] = $file_path . 'images/' . $data1['banner_image'];
			$row1['banner_image_thumb'] = $file_path . 'images/thumbs/' . $data1['banner_image'];
		}



		$songs_ids = trim($data1['album']);

		$query01 = "SELECT * FROM tbl_album
				LEFT JOIN tbl_category ON tbl_album.`aid`= tbl_category.`cid` 
				WHERE tbl_album.`aid` IN ($songs_ids) AND tbl_category.`status`='1' AND tbl_album.`status`='1'";

		$sql01 = mysqli_query($mysqli, $query01);

		$row1['total_books'] = mysqli_num_rows($sql01);

		if (mysqli_num_rows($sql01) > 0) {
			while ($data01 = mysqli_fetch_assoc($sql01)) {
				//  $total_songs++;
				$row01['aid'] = $data01['aid'];
				$row01['author_ids'] = $data01['artist_ids'];
				$row01['cat_ids'] = $data01['cat_ids'];
				$row01['book_subscription_type'] = $data01['book_subscription_type'];
				$row01['book_name'] = html_entity_decode($data01['album_name'], ENT_QUOTES, "UTF-8");

				$album_image = $data01['album_image'];
				if (empty($album_image)) {
					$row01['book_image'] = $file_path . 'images/add-image.png';
					$row01['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row01['book_image'] = $file_path . 'images/' . $data01['album_image'];
					$row01['book_image_thumb'] = $file_path . 'images/thumbs/' . $data01['album_image'];
				}

				$row01['is_favourite'] = is_favourite($data01['aid'], $user_id);
				$row01['play_time'] = $data01['play_time'];
				$row01['book_description'] =  html_entity_decode($data01['book_description'], ENT_QUOTES, "UTF-8");

				if ($data01['book_type'] == 'local') {
					$book_file = $file_path . 'uploads/' . basename($data01['book_url']);
				} else if ($data01['book_type'] == 'server_url') {
					$book_file = $data01['book_url'];
				}

				$row01['book_url'] = $book_file;
				$row01['total_views'] = $data01['total_views'];
				$row01['total_rate'] = $data01['total_rate'];
				$row01['rate_avg'] = $data01['rate_avg'];
				$row01['total_download'] = $data01['total_download'];
				$row1['book_list'][] = $row01;
			}
		} else {
			$row1['book_list'] = array();
		}
		$view_qry = mysqli_query($mysqli, "UPDATE tbl_banner SET total_views = total_views + 1 WHERE bid = '" . $data1['bid'] . "'");
		array_push($jsonObj_1, $row1);
		unset($row1['book_list']);
	}

	// $row['home_banner'] = $jsonObj_1;
	// $set['AUDIO_BOOK'] = $row;	

	$set['AUDIO_BOOK'] = $jsonObj_1;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "h_trending_books") {
	// $limit = API_LATEST_LIMIT;
	$jsonObj_123 = array();

	$page_limit = 4;
	$limit = ($get_method['page'] - 1) * $page_limit;

	$query1 = "SELECT * FROM tbl_album WHERE status='1' ORDER BY tbl_album.`aid` DESC LIMIT $limit, $page_limit";
	$sql1 = mysqli_query($mysqli, $query1);
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	while ($data123 = mysqli_fetch_assoc($sql1)) {

		$row123['aid'] = $data123['aid'];
		$row123['book_subscription_type'] = $data123['book_subscription_type'];
		$row123['book_name'] = html_entity_decode($data123['album_name'], ENT_QUOTES, "UTF-8");

		$album_image = $data123['album_image'];
		if (empty($album_image)) {
			$row123['book_image'] = $file_path . 'images/add-image.png';
			$row123['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row123['book_image'] = $file_path . 'images/' . $data123['album_image'];
			$row123['book_image_thumb'] = $file_path . 'images/thumbs/' . $data123['album_image'];
		}

		$row123['is_favourite'] = is_favourite($data123['aid'], $user_id);
		$row123['play_time'] = $data123['play_time'];
		$row123['book_description'] = html_entity_decode($data123['book_description'], ENT_QUOTES, "UTF-8");

		if ($data123['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data123['book_url']);
		} else if ($data123['book_type'] == 'server_url') {
			$book_file = $data123['book_url'];
		}

		$row123['book_url'] = $book_file;

		$row123['total_views'] = $data123['total_views'];
		$row123['total_rate'] = $data123['total_rate'];
		$row123['rate_avg'] = $data123['rate_avg'];
		$row123['total_download'] = $data123['total_download'];

		$artist_ids = trim($data123['artist_ids']);

		$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";

		$sql01 = mysqli_query($mysqli, $query01);

		$row123['total_author'] = mysqli_num_rows($sql01);

		$cat_ids = trim($data123['cat_ids']);

		$query012 = "SELECT * FROM tbl_category WHERE tbl_category.`cid` IN ($cat_ids) ";

		$sql012 = mysqli_query($mysqli, $query012);

		$row123['total_category'] = mysqli_num_rows($sql012);



		if (mysqli_num_rows($sql01) > 0) {
			while ($data0123 = mysqli_fetch_assoc($sql01)) {
				$total_songs++;
				$row0123['id'] = $data0123['id'];
				$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

				$artist_image = $data0123['artist_image'];
				if (empty($artist_image)) {
					$row0123['Author_image'] = $file_path . 'images/add-image.png';
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row0123['Author_image'] = $file_path . 'images/' . $data0123['artist_image'];
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
				}

				$row123['author_list'][] = $row0123;
			}
		} else {
			$row123['author_list'] = array();
		}

		if (mysqli_num_rows($sql012) > 0) {

			while ($data012345 = mysqli_fetch_assoc($sql012)) {
				$total_songs++;
				$row012345['cid'] = $data012345['cid'];
				$row012345['category_name'] =  html_entity_decode($data012345['category_name'], ENT_QUOTES, "UTF-8");

				$category_image = $data012345['category_image'];
				if (empty($category_image)) {
					$row012345['category_image'] = $file_path . 'images/add-image.png';
					$row012345['category_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row012345['category_image'] = $file_path . 'images/' . $data012345['category_image'];
					$row012345['category_image_thumb'] = $file_path . 'images/thumbs/' . $data012345['category_image'];
				}

				$row123['cat_list'][] = $row012345;
			}
		} else {
			$row123['cat_list'] = array();
		}

		array_push($jsonObj_123, $row123);

		unset($row123['cat_list']);
		unset($row123['author_list']);
	}

	// $row['trending_books'] = $jsonObj_123;
	// $set['AUDIO_BOOK'] = $row;	

	$set['AUDIO_BOOK'] = $jsonObj_123;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "h_latest_album") {
	// $limit = API_LATEST_LIMIT;

	$page_limit = 4;
	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj4 = array();

	$query4 = "SELECT * FROM tbl_album WHERE status='1' ORDER BY tbl_album.`aid` DESC  LIMIT $limit, $page_limit";
	$sql4 = mysqli_query($mysqli, $query4);

	while ($data4 = mysqli_fetch_assoc($sql4)) {
		$row4['aid'] = $data4['aid'];
		$row4['book_subscription_type'] = $data4['book_subscription_type'];
		$row4['book_name'] =  html_entity_decode($data4['album_name'], ENT_QUOTES, "UTF-8");

		$album_image = $data4['album_image'];
		if (empty($album_image)) {
			$row4['book_image'] = $file_path . 'images/add-image.png';
			$row4['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row4['book_image'] = $file_path . 'images/' . $data4['album_image'];
			$row4['book_image_thumb'] = $file_path . 'images/thumbs/' . $data4['album_image'];
		}
		$row4['is_favourite'] = is_favourite($data4['aid'], $user_id);

		if ($data4['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data4['book_url']);
		} else if ($data4['book_type'] == 'server_url') {
			$book_file = $data4['book_url'];
		}

		$row4['book_url'] = $book_file;
		$row4['total_views'] = $data4['total_views'];
		$row4['total_rate'] = $data4['total_rate'];
		$row4['rate_avg'] = $data4['rate_avg'];
		$row4['total_download'] = $data4['total_download'];

		array_push($jsonObj4, $row4);
	}

	// $row['latest_album'] = $jsonObj4;
	// $set['AUDIO_BOOK'] = $row;	

	$set['AUDIO_BOOK'] = $jsonObj4;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "h_latest_artist") {
	// $limit = API_LATEST_LIMIT;

	$page_limit = 4;
	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj3 = array();

	$query3 = "SELECT id,artist_name,artist_image FROM tbl_artist ORDER BY tbl_artist.`id` DESC LIMIT $limit,$page_limit";
	$sql3 = mysqli_query($mysqli, $query3);

	while ($data3 = mysqli_fetch_assoc($sql3)) {
		$row3['id'] = $data3['id'];
		$row3['Author_name'] = html_entity_decode($data3['artist_name'], ENT_QUOTES, "UTF-8");

		$artist_image = $data3['artist_image'];
		if (empty($artist_image)) {
			$row3['Author_image'] = $file_path . 'images/add-image.png';
			$row3['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row3['Author_image'] = $file_path . 'images/' . $data3['artist_image'];
			$row3['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data3['artist_image'];
		}

		array_push($jsonObj3, $row3);
	}

	$row['latest_artist'] = $jsonObj3;
	// $set['AUDIO_BOOK'] = $row;	

	$set['AUDIO_BOOK'] = $jsonObj3;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "h_playlist") {
	// $limit = API_LATEST_LIMIT;

	$page_limit = 4;
	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj345 = array();

	$query_rec123 = "SELECT COUNT(*) as num FROM tbl_playlist WHERE status='1' ORDER BY tbl_playlist.`pid` DESC";
	$total_pages123 = mysqli_fetch_array(mysqli_query($mysqli, $query_rec123));

	$query345 = "SELECT * FROM tbl_playlist WHERE status='1' ORDER BY tbl_playlist.`pid` DESC LIMIT $limit,$page_limit";
	$sql345 = mysqli_query($mysqli, $query345);

	while ($data345 = mysqli_fetch_assoc($sql345)) {
		$row345['total_records'] = $total_pages123['num'];
		$row345['pid'] = $data345['pid'];
		$row345['playlist_name'] =  html_entity_decode($data345['playlist_name'], ENT_QUOTES, "UTF-8");

		$playlist_image = $data345['playlist_image'];
		if (empty($playlist_image)) {
			$row345['playlist_image'] = $file_path . 'images/add-image.png';
			$row345['playlist_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row345['playlist_image'] = $file_path . 'images/' . cleanInput($data345['playlist_image']);
			$row345['playlist_image_thumb'] = $file_path . 'images/thumbs/' . cleanInput($data345['playlist_image']);
		}

		$row345['playlist_time'] = $data345['playlist_time'];
		$row345['playlist_description'] = $data345['playlist_description'];
		$row345['total_views'] = $data345['total_views'];
		$row345['total_rate'] = $data345['total_rate'];
		$row345['rate_avg'] = $data345['rate_avg'];
		$row345['total_download'] = $data345['total_download'];

		array_push($jsonObj345, $row345);
	}

	$row['playlist'] = $jsonObj345;
	// $set['AUDIO_BOOK'] = $row;	

	$set['AUDIO_BOOK'] = $jsonObj345;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "home_new") {
	$limit = API_LATEST_LIMIT;

	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$jsonObj = array();
	$data_arr = array();

	$sql = "SELECT * FROM tbl_banner WHERE status='1' ORDER BY tbl_banner.`bid` DESC";
	$result = mysqli_query($mysqli, $sql);

	while ($data = mysqli_fetch_assoc($result)) {

		$data_arr['bid'] = $data['bid'];
		$data_arr['banner_title'] = html_entity_decode($data['banner_title'], ENT_QUOTES, "UTF-8");
		$data_arr['banner_sort_info'] = $data['banner_sort_info'];
		$data_arr['total_views'] = $data['total_views'];

		$banner_image = $data['banner_image'];

		if (empty($banner_image)) {
			$data_arr['banner_image'] = $file_path . 'images/add-image.png';
			$data_arr['banner_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$data_arr['banner_image'] = $file_path . 'images/' . $data['banner_image'];
			$data_arr['banner_image_thumb'] = $file_path . 'images/thumbs/' . $data['banner_image'];
		}

		$songs_ids = trim($data['banner_songs']);

		$query01 = "SELECT * FROM tbl_mp3
				LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid` 
				WHERE tbl_mp3.`id` IN ($songs_ids) AND tbl_category.`status`='1' AND tbl_mp3.`status`='1'";

		$sql01 = mysqli_query($mysqli, $query01);

		$data_arr['total_songs'] = mysqli_num_rows($sql01);

		$data_arr['songs_list'] = array();

		array_push($jsonObj, $data_arr);
	}

	$row['home_banner'] = $jsonObj;

	mysqli_free_result($result);

	$jsonObj = array();
	$data_arr = array();

	$sql = "SELECT * FROM tbl_album WHERE status='1' ORDER BY tbl_album.`aid` DESC LIMIT $limit";
	$result = mysqli_query($mysqli, $sql);

	while ($data = mysqli_fetch_assoc($result)) {
		$data_arr['aid'] = $data['aid'];
		$data_arr['book_subscription_type'] = $data['book_subscription_type'];
		$data_arr['book_name'] =  html_entity_decode($data['album_name'], ENT_QUOTES, "UTF-8");

		$album_image = $data['album_image'];
		if (empty($album_image)) {
			$data_arr['book_image'] = $file_path . 'images/add-image.png';
			$data_arr['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$data_arr['book_image'] = $file_path . 'images/' . $data['album_image'];
			$data_arr['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];
		}

		if ($data['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data['book_url']);
		} else if ($data['book_type'] == 'server_url') {
			$book_file = $data['book_url'];
		}

		$data_arr['book_url'] = $book_file;

		$data_arr['is_favourite'] = is_favourite($data['aid'], $user_id);


		$data_arr['total_views'] = $data['total_views'];
		$data_arr['total_rate'] = $data['total_rate'];
		$data_arr['rate_avg'] = $data['rate_avg'];
		$data_arr['total_download'] = $data['total_download'];

		array_push($jsonObj, $data_arr);
	}

	$row['latest_album'] = $jsonObj;

	mysqli_free_result($result);
	$jsonObj = array();
	$data_arr = array();

	$sql = "SELECT id,artist_name,artist_image FROM tbl_artist ORDER BY tbl_artist.`id` DESC LIMIT $limit";
	$result = mysqli_query($mysqli, $sql);

	while ($data = mysqli_fetch_assoc($result)) {
		$data_arr['id'] = $data['id'];
		$data_arr['Author_name'] = html_entity_decode($data['artist_name'], ENT_QUOTES, "UTF-8");

		$artist_image = $data['artist_image'];
		if (empty($artist_image)) {
			$data_arr['Author_image'] = $file_path . 'images/add-image.png';
			$data_arr['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$data_arr['Author_image'] = $file_path . 'images/' . $data['artist_image'];
			$data_arr['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data['artist_image'];
		}

		//	$data_arr['Author_image'] = $file_path . 'images/' . $data['artist_image'];
		//	$data_arr['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data['artist_image'];

		array_push($jsonObj, $data_arr);
	}

	$row['latest_artist'] = $jsonObj;

	mysqli_free_result($result);
	$jsonObj = array();
	$data_arr = array();

	$sql_views = "SELECT DISTINCT tbl_mp3_views.`mp3_id`, `tbl_mp3_views`.`mp3_id`, `tbl_mp3_views`.`views` FROM tbl_mp3_views WHERE (tbl_mp3_views.`date` BETWEEN DATE_SUB(NOW(), INTERVAL 1 MONTH) AND NOW()) ORDER BY `tbl_mp3_views`.`views` DESC LIMIT 50";
	$res_views = mysqli_query($mysqli, $sql_views);

	while ($row_views = mysqli_fetch_assoc($res_views)) {
		$id = $row_views['mp3_id'];

		$sql = "SELECT * FROM tbl_mp3
				LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid` 
				WHERE tbl_mp3.`status`='1' AND tbl_category.`status`='1' AND tbl_mp3.`id`='$id' ORDER BY tbl_mp3.`id` DESC";

		$result = mysqli_query($mysqli, $sql);

		if (mysqli_num_rows($result) > 0) {
			$data = mysqli_fetch_assoc($result);

			$data_arr['id'] = $data['id'];
			$data_arr['cat_id'] = $data['cat_id'];
			$data_arr['mp3_type'] = $data['mp3_type'];
			$data_arr['mp3_title'] = $data['mp3_title'];



			if ($data['mp3_type'] == 'local') {
				$mp3_file = $file_path . 'uploads/' . basename($data['mp3_url']);
			} else if ($data['mp3_type'] == 'server_url') {
				$mp3_file = $data['mp3_url'];
			}

			$data_arr['mp3_url'] = $mp3_file;


			$mp3_thumbnail = $data['mp3_thumbnail'];
			if (empty($mp3_thumbnail)) {
				$data_arr['mp3_thumbnail_b'] = $file_path . 'images/add-image.png';
				$data_arr['mp3_thumbnail_s'] = $file_path . 'images/thumbs/add-image.png';
			} else {
				$data_arr['mp3_thumbnail_b'] = $file_path . 'images/' . $data['mp3_thumbnail'];
				$data_arr['mp3_thumbnail_s'] = $file_path . 'images/thumbs/' . $data['mp3_thumbnail'];
			}

			// $data_arr['mp3_thumbnail_b'] = $file_path . 'images/' . $data['mp3_thumbnail'];
			// $data_arr['mp3_thumbnail_s'] = $file_path . 'images/thumbs/' . $data['mp3_thumbnail'];

			$data_arr['mp3_artist'] = $data['mp3_artist'];
			$data_arr['mp3_description'] =  html_entity_decode($data['mp3_description'], ENT_QUOTES, "UTF-8");
			$data_arr['total_rate'] = $data['total_rate'];
			$data_arr['rate_avg'] = $data['rate_avg'];
			$data_arr['total_views'] = $data['total_views'];
			$data_arr['total_download'] = $data['total_download'];

			$data_arr['is_favourite'] = is_favourite($data['id'], $user_id);

			$data_arr['cid'] = $data['cid'];
			$data_arr['category_name'] = html_entity_decode($data['category_name'], ENT_QUOTES, "UTF-8");

			$category_image = $data['category_image'];
			if (empty($category_image)) {
				$data_arr['category_image'] = $file_path . 'images/add-image.png';
				$data_arr['category_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
			} else {
				$data_arr['category_image'] = $file_path . 'images/' . $data['category_image'];
				$data_arr['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];
			}

			//	$data_arr['category_image'] = $file_path . 'images/' . $data['category_image'];
			//	$data_arr['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];

			array_push($jsonObj, $data_arr);
		}
	}

	$row['trending_songs'] = $jsonObj;

	$set['AUDIO_BOOK'] = $row;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "single_book") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$query_rec = "SELECT COUNT(*) as num FROM tbl_album WHERE status = '1' ORDER BY tbl_album.`aid` DESC";
	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$book_id = $get_method['Book_id'];

	$total_rate_5 = "SELECT COUNT(*) as num5 FROM tbl_rating WHERE post_id = $book_id AND rate = '5'";
	$total_5 = mysqli_fetch_array(mysqli_query($mysqli, $total_rate_5));
	$total_rate_count_5 = $total_5['num5'];

	$total_rate_4 = "SELECT COUNT(*) as num4 FROM tbl_rating WHERE post_id = $book_id AND rate = '4'";
	$total_4 = mysqli_fetch_array(mysqli_query($mysqli, $total_rate_4));
	$total_rate_count_4 = $total_4['num4'];

	$total_rate_3 = "SELECT COUNT(*) as num3 FROM tbl_rating WHERE post_id = $book_id AND rate = '3'";
	$total_3 = mysqli_fetch_array(mysqli_query($mysqli, $total_rate_3));
	$total_rate_count_3 = $total_3['num3'];

	$total_rate_2 = "SELECT COUNT(*) as num2 FROM tbl_rating WHERE post_id = $book_id AND rate = '2'";
	$total_2 = mysqli_fetch_array(mysqli_query($mysqli, $total_rate_2));
	$total_rate_count_2 = $total_2['num2'];

	$total_rate_1 = "SELECT COUNT(*) as num1 FROM tbl_rating WHERE post_id = $book_id AND rate = '1'";
	$total_1 = mysqli_fetch_array(mysqli_query($mysqli, $total_rate_1));
	$total_rate_count_1 = $total_1['num1'];

	$jsonObj = array();



	$query = "SELECT * FROM tbl_album WHERE aid = $book_id AND status = '1'";

	// echo $query;

	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {

		$row['aid'] = $data['aid'];
		$row['artist_ids'] = $data['artist_ids'];
		$row['cat_ids'] = $data['cat_ids'];
		$row['book_subscription_type'] = $data['book_subscription_type'];
		$row['book_name'] =  html_entity_decode($data['album_name'], ENT_QUOTES, "UTF-8");

		// echo $data['cat_ids'];

		$album_image = $data['album_image'];
		if (empty($album_image)) {
			$row['book_image'] = $file_path . 'images/add-image.png';
			$row['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['book_image'] = $file_path . 'images/' . $data['album_image'];
			$row['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];
		}
		$row['is_favourite'] = is_favourite($data['aid'], $user_id);
		$row['play_time'] = $data['play_time'];
		$row['book_description'] = html_entity_decode($data['book_description'], ENT_QUOTES, "UTF-8");

		if ($data['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data['book_url']);
		} else if ($data['book_type'] == 'server_url') {
			$book_file = $data['book_url'];
		}

		$row['book_url'] = $book_file;

		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['total_5'] = $total_rate_count_5;
		$row['total_4'] = $total_rate_count_4;
		$row['total_3'] = $total_rate_count_3;
		$row['total_2'] = $total_rate_count_2;
		$row['total_1'] = $total_rate_count_1;
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];
		$row['is_favourite'] = is_favourite($data['aid'], $user_id);

		// echo $data['total_download'];

		if ($get_method['user_id']) {
			$query1 = mysqli_query($mysqli, "SELECT * FROM tbl_rating WHERE post_id  = '" . $get_method['Book_id'] . "' AND `ip` = '" . $get_method['user_id'] . "' ");
			$data1 = mysqli_fetch_assoc($query1);
			$count = mysqli_num_rows($query1);

			if ($count != 0) {
				$row['user_rate'] = $data1['rate'];
			} else {
				$row['user_rate'] = 0;
			}
		} else {
			$row['user_rate'] = 0;
		}

		$query012332 = "SELECT * FROM tbl_mp3 WHERE tbl_mp3.`album_id` IN ($book_id) ";
		$sql012332 = mysqli_query($mysqli, $query012332);
		$row['total_chapter'] = mysqli_num_rows($sql012332);

		$artist_ids = trim($data['artist_ids']);

		$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";
		$sql01 = mysqli_query($mysqli, $query01);
		$row['total_author'] = mysqli_num_rows($sql01);

		$cat_ids = trim($data['cat_ids']);

		$query012 = "SELECT * FROM tbl_category WHERE tbl_category.`cid` IN ($cat_ids) ";
		$sql012 = mysqli_query($mysqli, $query012);
		$row['total_category'] = mysqli_num_rows($sql012);

		if (mysqli_num_rows($sql012332) > 0) {
			while ($data012332 = mysqli_fetch_assoc($sql012332)) {
				$total_songs++;
				$row012332['id'] = $data012332['id'];
				$row012332['cat_id'] = $data012332['cat_id'];
				$row012332['album_id'] = $data012332['album_id'];
				$row012332['mp3_type'] = $data012332['mp3_type'];
				$row012332['mp3_title'] = $data012332['mp3_title'];

				//$row012332['mp3_url'] = $data012332['mp3_url'];

				if ($data012332['mp3_type'] == 'local') {
					$mp3_file = $file_path . 'uploads/' . basename($data012332['mp3_url']);
				} else if ($data012332['mp3_type'] == 'server_url') {
					$mp3_file = $data012332['mp3_url'];
				}

				$row012332['mp3_url'] = $mp3_file;

				$mp3_thumbnail = $data012332['mp3_thumbnail'];
				if (empty($mp3_thumbnail)) {
					$row012332['mp3_thumbnail'] = $file_path . 'images/add-image.png';
					$row012332['mp3_thumbnail_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row012332['mp3_thumbnail'] = $file_path . 'images/' . $data012332['mp3_thumbnail'];
					$row012332['mp3_thumbnail_thumb'] = $file_path . 'images/thumbs/' . $data012332['mp3_thumbnail'];
				}


				//$row012332['mp3_thumbnail'] = $data012332['mp3_thumbnail'];

				$row012332['mp3_duration'] = $data012332['mp3_duration'];
				$row012332['mp3_artist'] = $data012332['mp3_artist'];
				$row012332['mp3_description'] = html_entity_decode($data012332['mp3_description'], ENT_QUOTES, "UTF-8");
				$row012332['total_rate'] = $data012332['total_rate'];
				$row012332['total_views'] = $data012332['total_views'];
				$row012332['rate_avg'] = $data012332['rate_avg'];
				$row012332['total_download'] = $data012332['total_download'];
				$row['chapter_list'][] = $row012332;
			}
		} else {
			$row['chapter_list'] = array();
		}


		if (mysqli_num_rows($sql01) > 0) {
			while ($data0123 = mysqli_fetch_assoc($sql01)) {
				$total_songs++;
				$row0123['id'] = $data0123['id'];
				$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

				$followI = $data0123['artist_name'];
				$resultArray[] = $followI;

				$artist_image = $data0123['artist_image'];
				if (empty($artist_image)) {
					$row0123['author_image'] = $file_path . 'images/add-image.png';
					$row0123['author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row0123['author_image'] = $file_path . 'images/' . $data0123['artist_image'];
					$row0123['author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
				}
				//	$row0123['author_image'] = $data0123['artist_image'];
				$row['author_list'][] = $row0123;
			}

			$groupConcat = implode(",", $resultArray);
			$row['artist_list'] =  $groupConcat;
		} else {
			$row['author_list'] = array();
		}

		//array_push($jsonObj_123, $row123);


		if (mysqli_num_rows($sql012) > 0) {
			while ($data012345 = mysqli_fetch_assoc($sql012)) {
				$total_songs++;
				$row012345['cid'] = $data012345['cid'];
				$row012345['category_name'] = html_entity_decode($data012345['category_name'], ENT_QUOTES, "UTF-8");
				$row['cat_list'][] = $row012345;
			}
		} else {
			$row['cat_list'] = array();
		}

		array_push($jsonObj, $row);

		unset($row['cat_list']);
		unset($row['author_list']);
	}

	$view_qry = mysqli_query($mysqli, "UPDATE tbl_album SET total_views = total_views + 1 WHERE aid = '" . $book_id . "'");
	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "chapter_by_book_id") {

	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$query_rec = "SELECT COUNT(*) as num FROM tbl_album WHERE status = '1' ORDER BY tbl_album.`aid` DESC";
	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$book_id = $get_method['Book_id'];

	$jsonObj = array();

	$query = "SELECT * FROM tbl_mp3 WHERE album_id = $book_id AND status = '1'";

	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {

		$row['id'] = $data['id'];
		$row['cat_id'] = $data['cat_id'];
		$row['album_id'] = $data['album_id'];
		$row['mp3_type'] = $data['mp3_type'];
		$row['mp3_title'] = $data['mp3_title'];
		if ($data['mp3_type'] == 'local') {
			$mp3_file = $file_path . 'uploads/' . basename($data['mp3_url']);
		} else if ($data['mp3_type'] == 'server_url') {
			$mp3_file = $data['mp3_url'];
		}

		$row['mp3_url'] = $mp3_file;

		$mp3_thumbnail = $data['mp3_thumbnail'];
		if (empty($mp3_thumbnail)) {
			$row['mp3_thumbnail'] = $file_path . 'images/add-image.png';
			$row['mp3_thumbnail_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['mp3_thumbnail'] = $file_path . 'images/' . $data['mp3_thumbnail'];
			$row['mp3_thumbnail_thumb'] = $file_path . 'images/thumbs/' . $data['mp3_thumbnail'];
		}

		//	$row['mp3_thumbnail'] = $data['mp3_thumbnail'];

		$row['mp3_duration'] = $data['mp3_duration'];
		$row['mp3_artist'] = $data['mp3_artist'];
		$row['mp3_description'] = html_entity_decode($data['mp3_description'], ENT_QUOTES, "UTF-8");
		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];


		$query01 = "SELECT * FROM tbl_mp3 WHERE tbl_mp3.`album_id` IN ($book_id) ";
		$sql01 = mysqli_query($mysqli, $query01);
		$row['total_chapter'] = mysqli_num_rows($sql01);

		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "all_songs") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$query_rec = "SELECT COUNT(*) as num FROM tbl_mp3
		LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid` 
		WHERE tbl_mp3.`status`='1' AND tbl_category.`status`='1'";
	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$query = "SELECT * FROM tbl_mp3
		LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid` 
		WHERE tbl_mp3.`status`='1' AND tbl_category.`status`='1' ORDER BY tbl_mp3.`id` DESC LIMIT $limit, $page_limit";

	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {

		$row['total_songs'] = $total_pages['num'];
		$row['id'] = $data['id'];
		$row['cat_id'] = $data['cat_id'];
		$row['mp3_type'] = $data['mp3_type'];
		$row['mp3_title'] = $data['mp3_title'];

		//$mp3_file=$data['mp3_url'];

		if ($data['mp3_type'] == 'local') {
			$mp3_file = $file_path . 'uploads/' . basename($data['mp3_url']);
		} else if ($data['mp3_type'] == 'server_url') {
			$mp3_file = $data['mp3_url'];
		}

		$row['mp3_url'] = $mp3_file;

		$mp3_thumbnail = $data['mp3_thumbnail'];
		if (empty($mp3_thumbnail)) {
			$row['mp3_thumbnail_b'] = $file_path . 'images/add-image.png';
			$row['mp3_thumbnail_s'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['mp3_thumbnail_b'] = $file_path . 'images/' . $data['mp3_thumbnail'];
			$row['mp3_thumbnail_s'] = $file_path . 'images/thumbs/' . $data['mp3_thumbnail'];
		}

		//	$row['mp3_thumbnail_b'] = $file_path . 'images/' . $data['mp3_thumbnail'];
		//	$row['mp3_thumbnail_s'] = $file_path . 'images/thumbs/' . $data['mp3_thumbnail'];

		$row['mp3_artist'] = $data['mp3_artist'];
		$row['mp3_description'] = html_entity_decode($data['mp3_description'], ENT_QUOTES, "UTF-8");
		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];

		$row['is_favourite'] = is_favourite($data['id'], $user_id);

		$row['cid'] = $data['cid'];
		$row['category_name'] = html_entity_decode($data['category_name'], ENT_QUOTES, "UTF-8");

		$category_image = $data['category_image'];
		if (empty($category_image)) {
			$row['category_image'] = $file_path . 'images/add-image.png';
			$row['category_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['category_image'] = $file_path . 'images/' . $data['category_image'];
			$row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];
		}

		//$row['category_image'] = $file_path . 'images/' . $data['category_image'];
		//	$row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];


		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "home_section") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$query_rec = "SELECT COUNT(*) as num FROM tbl_home_section WHERE status = '1' ORDER BY tbl_home_section.`id` DESC";
	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$query = "SELECT * FROM tbl_home_section WHERE status = '1' ORDER BY tbl_home_section.`id` DESC";

	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {

		$row['id'] = $data['id'];
		$row['section_title'] =  html_entity_decode($data['section_title'], ENT_QUOTES, "UTF-8");
		$row['Book_list'] = $data['section_books'];
		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "home_section_2") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$query_rec = "SELECT COUNT(*) as num FROM tbl_home_section_2 WHERE status = '1' ORDER BY tbl_home_section_2.`id` DESC";
	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$query = "SELECT * FROM tbl_home_section_2 WHERE status = '1' ORDER BY tbl_home_section_2.`id` DESC";

	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['id'] = $data['id'];
		$row['section_title'] = html_entity_decode($data['section_title'], ENT_QUOTES, "UTF-8");
		$row['Book_list'] = $data['section_books'];
		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "latest") {

	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$query_rec = "SELECT COUNT(*) as num FROM tbl_album
		LEFT JOIN tbl_category ON tbl_album.`aid`= tbl_category.`cid` 
		WHERE tbl_album.`status`='1' AND tbl_category.`status`='1' ORDER BY tbl_album.`aid`";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = API_LATEST_LIMIT;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$query = "SELECT * FROM tbl_album
		LEFT JOIN tbl_category ON tbl_album.`aid`= tbl_category.`cid` 
		WHERE tbl_album.`status`='1' AND tbl_album.`status`='1' ORDER BY tbl_album.`aid` DESC LIMIT $limit, $page_limit";

	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['total_records'] = $total_pages['num'];

		$row['aid'] = $data['aid'];
		$row['author_ids'] = $data['artist_ids'];
		$row['cat_ids'] = $data['cat_ids'];
		$row['book_subscription_type'] = $data['book_subscription_type'];
		$row['book_name'] =  html_entity_decode($data['album_name'], ENT_QUOTES, "UTF-8");

		$album_image = $data['album_image'];
		if (empty($album_image)) {
			$row['book_image'] = $file_path . 'images/add-image.png';
			$row['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['book_image'] = $file_path . 'images/' . $data['album_image'];
			$row['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];
		}
		$row['is_favourite'] = is_favourite($data['aid'], $user_id);
		$row['play_time'] = $data['play_time'];
		$row['book_description'] = html_entity_decode($data['book_description'], ENT_QUOTES, "UTF-8");

		if ($data['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data['book_url']);
		} else if ($data['book_type'] == 'server_url') {
			$book_file = $data['book_url'];
		}

		$row['book_url'] = $book_file;

		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];




		$artist_ids = trim($data['artist_ids']);

		$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";

		$sql01 = mysqli_query($mysqli, $query01);

		$row['total_author'] = mysqli_num_rows($sql01);

		$cat_ids = trim($data['cat_ids']);

		$query012 = "SELECT * FROM tbl_category WHERE tbl_category.`cid` IN ($cat_ids) ";

		$sql012 = mysqli_query($mysqli, $query012);

		$row['total_category'] = mysqli_num_rows($sql012);



		if (mysqli_num_rows($sql01) > 0) {

			while ($data0123 = mysqli_fetch_assoc($sql01)) {
				$total_songs++;
				$row0123['id'] = $data0123['id'];
				$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

				$artist_image = $data0123['artist_image'];
				if (empty($artist_image)) {
					$row0123['Author_image'] = $file_path . 'images/add-image.png';
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row0123['Author_image'] = $file_path . 'images/' . $data0123['artist_image'];
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
				}

				//	$row0123['author_image'] = $data0123['artist_image'];
				$row['author_list'][] = $row0123;
			}
		} else {
			$row['author_list'] = array();
		}

		//	array_push($jsonObj_123, $row123);


		if (mysqli_num_rows($sql012) > 0) {

			while ($data012345 = mysqli_fetch_assoc($sql012)) {
				$total_songs++;
				$row012345['cid'] = $data012345['cid'];
				$row012345['category_name'] = html_entity_decode($data012345['category_name'], ENT_QUOTES, "UTF-8");
				$row['cat_list'][] = $row012345;
			}
		} else {
			$row['cat_list'] = array();
		}

		array_push($jsonObj, $row);

		unset($row['cat_list']);
		unset($row['author_list']);



		//	array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "banner_songs") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$post_order_by = API_CAT_POST_ORDER_BY;

	$banner_id = $get_method['banner_id'];

	$sql_banner = "SELECT * FROM tbl_banner WHERE status='1' AND `bid`='$banner_id' ORDER BY tbl_banner.`bid` DESC";
	$res_banner = mysqli_query($mysqli, $sql_banner);
	$row_banner = mysqli_fetch_assoc($res_banner);

	$songs_ids = trim($row_banner['album']);


	$query_rec = "SELECT COUNT(*) as num FROM tbl_album WHERE tbl_album.`aid` IN ($songs_ids)";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$sql = "SELECT * FROM tbl_album WHERE tbl_album.`aid` IN ($songs_ids)";
	$result = mysqli_query($mysqli, $sql);

	while ($data = mysqli_fetch_assoc($result)) {
		$row['total_records'] = $total_pages['num'];
		$row['aid'] = $data['aid'];
		$row['author_ids'] = $data['artist_ids'];
		$row['cat_ids'] = $data['cat_ids'];
		$row['book_subscription_type'] = $data['book_subscription_type'];
		$row['book_name'] =  html_entity_decode($data['album_name'], ENT_QUOTES, "UTF-8");

		$album_image = $data['album_image'];
		if (empty($album_image)) {
			$row['book_image'] = $file_path . 'images/add-image.png';
			$row['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['book_image'] = $file_path . 'images/' . $data['album_image'];
			$row['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];
		}
		$row['is_favourite'] = is_favourite($data['aid'], $user_id);

		if ($data['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data['book_url']);
		} else if ($data['book_type'] == 'server_url') {
			$book_file = $data['book_url'];
		}

		$row['book_url'] = $book_file;

		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];
		$row['status'] = $data['status'];

		$artist_ids = trim($data['artist_ids']);

		$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";

		$sql01 = mysqli_query($mysqli, $query01);

		$row['total_author'] = mysqli_num_rows($sql01);

		$cat_ids = trim($data['cat_ids']);

		$query012 = "SELECT * FROM tbl_category WHERE tbl_category.`cid` IN ($cat_ids) ";

		$sql012 = mysqli_query($mysqli, $query012);

		$row['total_category'] = mysqli_num_rows($sql012);



		if (mysqli_num_rows($sql01) > 0) {

			while ($data0123 = mysqli_fetch_assoc($sql01)) {
				$total_songs++;
				$row0123['id'] = $data0123['id'];
				$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

				$artist_image = $data0123['artist_image'];
				if (empty($artist_image)) {
					$row0123['Author_image'] = $file_path . 'images/add-image.png';
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row0123['Author_image'] = $file_path . 'images/' . $data0123['artist_image'];
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
				}
				$row['author_list'][] = $row0123;
			}
		} else {
			$row['author_list'] = array();
		}



		if (mysqli_num_rows($sql012) > 0) {

			while ($data012345 = mysqli_fetch_assoc($sql012)) {
				$total_songs++;
				$row012345['cid'] = $data012345['cid'];
				$row012345['category_name'] = html_entity_decode($data012345['category_name'], ENT_QUOTES, "UTF-8");
				$row['cat_list'][] = $row012345;
			}
		} else {
			$row['cat_list'] = array();
		}

		array_push($jsonObj, $row);

		unset($row['cat_list']);
		unset($row['author_list']);
	}

	$set['AUDIO_BOOK'] = $jsonObj;
	mysqli_free_result($result);

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "home_section_id") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$post_order_by = API_CAT_POST_ORDER_BY;

	$banner_id = $get_method['homesection_id'];

	$sql_banner = "SELECT * FROM tbl_home_section WHERE status='1' AND `id`='$banner_id' ORDER BY tbl_home_section.`id` DESC";
	$res_banner = mysqli_query($mysqli, $sql_banner);
	$row_banner = mysqli_fetch_assoc($res_banner);

	$songs_ids = trim($row_banner['section_books']);
	$query_rec = "SELECT COUNT(*) as num FROM tbl_album WHERE tbl_album.`aid` IN ($songs_ids)";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$sql = "SELECT * FROM tbl_album WHERE tbl_album.`aid` IN ($songs_ids)";
	$result = mysqli_query($mysqli, $sql);

	while ($data = mysqli_fetch_assoc($result)) {
		$row123['total_records'] = $total_pages['num'];
		$row123['aid'] = $data['aid'];
		$row123['artist_ids'] = $data['artist_ids'];
		$row123['cat_ids'] = $data['cat_ids'];
		$row123['book_subscription_type'] = $data['book_subscription_type'];
		$row123['book_name'] =  html_entity_decode($data['album_name'], ENT_QUOTES, "UTF-8");

		$album_image = $data['album_image'];
		if (empty($album_image)) {
			$row123['book_image'] = $file_path . 'images/add-image.png';
			$row123['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row123['book_image'] = $file_path . 'images/' . $data['album_image'];
			$row123['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];
		}
		$row123['is_favourite'] = is_favourite($data['aid'], $user_id);

		$row123['play_time'] = $data['play_time'];
		$row123['book_description'] = html_entity_decode($data['book_description'], ENT_QUOTES, "UTF-8");

		if ($data['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data['book_url']);
		} else if ($data['book_type'] == 'server_url') {
			$book_file = $data['book_url'];
		}

		$row123['book_url'] = $book_file;

		$row123['total_rate'] = $data['total_rate'];
		$row123['total_views'] = $data['total_views'];
		$row123['rate_avg'] = $data['rate_avg'];
		$row123['total_download'] = $data['total_download'];
		$row123['status'] = $data['status'];


		$artist_ids = trim($data['artist_ids']);

		$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";

		$sql01 = mysqli_query($mysqli, $query01);

		$row123['total_author'] = mysqli_num_rows($sql01);

		$cat_ids = trim($data['cat_ids']);

		$query012 = "SELECT * FROM tbl_category WHERE tbl_category.`cid` IN ($cat_ids) ";

		$sql012 = mysqli_query($mysqli, $query012);

		$row123['total_category'] = mysqli_num_rows($sql012);



		if (mysqli_num_rows($sql01) > 0) {

			while ($data0123 = mysqli_fetch_assoc($sql01)) {
				$total_songs++;
				$row0123['id'] = $data0123['id'];
				$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

				$artist_image = $data0123['artist_image'];
				if (empty($artist_image)) {
					$row0123['Author_image'] = $file_path . 'images/add-image.png';
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row0123['Author_image'] = $file_path . 'images/' . $data0123['artist_image'];
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
				}

				//	$row0123['author_image'] = $data0123['artist_image'];
				$row123['author_list'][] = $row0123;
			}
		} else {
			$row123['author_list'] = array();
		}

		//	array_push($jsonObj_123, $row123);


		if (mysqli_num_rows($sql012) > 0) {

			while ($data012345 = mysqli_fetch_assoc($sql012)) {
				$total_songs++;
				$row012345['cid'] = $data012345['cid'];
				$row012345['category_name'] = html_entity_decode($data012345['category_name'], ENT_QUOTES, "UTF-8");
				$row123['cat_list'][] = $row012345;
			}
		} else {
			$row123['cat_list'] = array();
		}

		array_push($jsonObj, $row123);

		unset($row123['cat_list']);
		unset($row123['author_list']);
	}

	$set['AUDIO_BOOK'] = $jsonObj;
	mysqli_free_result($result);

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "home_section_id_2") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$post_order_by = API_CAT_POST_ORDER_BY;

	$banner_id = $get_method['homesection_id_2'];

	$sql_banner = "SELECT * FROM tbl_home_section_2 WHERE status='1' AND `id`='$banner_id' ORDER BY tbl_home_section_2.`id` DESC";
	$res_banner = mysqli_query($mysqli, $sql_banner);
	$row_banner = mysqli_fetch_assoc($res_banner);

	$songs_ids = trim($row_banner['section_books']);

	$query_rec = "SELECT COUNT(*) as num FROM tbl_album WHERE tbl_album.`aid` IN ($songs_ids)";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$sql = "SELECT * FROM tbl_album WHERE tbl_album.`aid` IN ($songs_ids)";
	$result = mysqli_query($mysqli, $sql);
	while ($data = mysqli_fetch_assoc($result)) {
		$row123['total_records'] = $total_pages['num'];
		$row123['aid'] = $data['aid'];
		$row123['artist_ids'] = $data['artist_ids'];
		$row123['cat_ids'] = $data['cat_ids'];
		$row123['book_subscription_type'] = $data['book_subscription_type'];
		$row123['book_name'] = html_entity_decode($data['album_name'], ENT_QUOTES, "UTF-8");

		$album_image = $data['album_image'];
		if (empty($album_image)) {
			$row123['book_image'] = $file_path . 'images/add-image.png';
			$row123['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row123['book_image'] = $file_path . 'images/' . $data['album_image'];
			$row123['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];
		}
		$row123['is_favourite'] = is_favourite($data['aid'], $user_id);
		$row123['play_time'] = $data['play_time'];
		$row123['book_description'] = html_entity_decode($data['book_description'], ENT_QUOTES, "UTF-8");

		if ($data['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data['book_url']);
		} else if ($data['book_type'] == 'server_url') {
			$book_file = $data['book_url'];
		}

		$row123['book_url'] = $book_file;

		$row123['total_rate'] = $data['total_rate'];
		$row123['total_views'] = $data['total_views'];
		$row123['rate_avg'] = $data['rate_avg'];
		$row123['total_download'] = $data['total_download'];
		$row123['status'] = $data['status'];

		$artist_ids = trim($data['artist_ids']);

		$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";

		$sql01 = mysqli_query($mysqli, $query01);

		$row123['total_author'] = mysqli_num_rows($sql01);

		$cat_ids = trim($data['cat_ids']);

		$query012 = "SELECT * FROM tbl_category WHERE tbl_category.`cid` IN ($cat_ids) ";

		$sql012 = mysqli_query($mysqli, $query012);

		$row123['total_category'] = mysqli_num_rows($sql012);



		if (mysqli_num_rows($sql01) > 0) {

			while ($data0123 = mysqli_fetch_assoc($sql01)) {
				$total_songs++;
				$row0123['id'] = $data0123['id'];
				$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

				$artist_image = $data0123['artist_image'];
				if (empty($artist_image)) {
					$row0123['Author_image'] = $file_path . 'images/add-image.png';
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row0123['Author_image'] = $file_path . 'images/' . $data0123['artist_image'];
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
				}
				$row123['author_list'][] = $row0123;
			}
		} else {
			$row123['author_list'] = array();
		}

		if (mysqli_num_rows($sql012) > 0) {

			while ($data012345 = mysqli_fetch_assoc($sql012)) {
				$total_songs++;
				$row012345['cid'] = $data012345['cid'];
				$row012345['category_name'] = html_entity_decode($data012345['category_name'], ENT_QUOTES, "UTF-8");
				$row123['cat_list'][] = $row012345;
			}
		} else {
			$row123['cat_list'] = array();
		}

		array_push($jsonObj, $row123);

		unset($row123['cat_list']);
		unset($row123['author_list']);
	}

	$set['AUDIO_BOOK'] = $jsonObj;
	mysqli_free_result($result);

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "cat_list") {
	$query_rec = "SELECT COUNT(*) as num FROM tbl_category WHERE status=1";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$cat_order = API_CAT_ORDER_BY;

	$query = "SELECT * FROM tbl_category WHERE status='1' ORDER BY tbl_category." . $cat_order . " LIMIT $limit, $page_limit";
	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['total_records'] = $total_pages['num'];

		$row['cid'] = $data['cid'];
		$row['category_name'] = html_entity_decode($data['category_name'], ENT_QUOTES, "UTF-8");

		$category_image = $data['category_image'];
		if (empty($category_image)) {
			$row['category_image'] = $file_path . 'images/add-image.png';
			$row['category_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['category_image'] = $file_path . 'images/' . $data['category_image'];
			$row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];
		}

		//$row['category_image'] = $file_path . 'images/' . $data['category_image'];
		//	$row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];

		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "cat_songs") {
	$post_order_by = API_CAT_POST_ORDER_BY;

	$cat_id = $get_method['cat_id'];

	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$query_rec = "SELECT COUNT(*) as num FROM tbl_mp3
		LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid` 
		WHERE tbl_mp3.`cat_id`='$cat_id' AND tbl_category.`status`='1' AND tbl_mp3.`status`='1'";
	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$query2 = "SELECT * FROM tbl_album WHERE tbl_album.`status`='1'";
	$result2 = mysqli_query($mysqli, $query2);
	while ($data = mysqli_fetch_assoc($result2)) {
		$songs_list = explode(",",  $data['cat_ids']);
		foreach ($songs_list as $song_id) {
			if ($cat_id == $song_id) {

				$row['aid'] = $data['aid'];
				$row['author_ids'] = $data['artist_ids'];
				$row['cat_ids'] = $data['cat_ids'];
				$row['book_subscription_type'] = $data['book_subscription_type'];
				$row['book_name'] =  html_entity_decode($data['album_name'], ENT_QUOTES, "UTF-8");

				$album_image = $data['album_image'];
				if (empty($album_image)) {
					$row['book_image'] = $file_path . 'images/add-image.png';
					$row['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row['book_image'] = $file_path . 'images/' . $data['album_image'];
					$row['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];
				}
				$row['is_favourite'] = is_favourite($data['aid'], $user_id);

				if ($data['book_type'] == 'local') {
					$book_file = $file_path . 'uploads/' . basename($data['book_url']);
				} else if ($data['book_type'] == 'server_url') {
					$book_file = $data['book_url'];
				}

				$row['book_url'] = $book_file;
				$row['total_rate'] = $data['total_rate'];
				$row['total_views'] = $data['total_views'];
				$row['rate_avg'] = $data['rate_avg'];
				$row['total_download'] = $data['total_download'];
				$row['play_time'] = $data['play_time'];
				$row['book_description'] = html_entity_decode($data['book_description'], ENT_QUOTES, "UTF-8");
				$row['status'] = $data['status'];

				$artist_ids = trim($data['artist_ids']);

				$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";

				$sql01 = mysqli_query($mysqli, $query01);

				$row['total_author'] = mysqli_num_rows($sql01);

				$cat_ids = trim($data['cat_ids']);

				$query012 = "SELECT * FROM tbl_category WHERE tbl_category.`cid` IN ($cat_ids) ";

				$sql012 = mysqli_query($mysqli, $query012);

				$row['total_category'] = mysqli_num_rows($sql012);



				if (mysqli_num_rows($sql01) > 0) {

					while ($data0123 = mysqli_fetch_assoc($sql01)) {
						$total_songs++;
						$row0123['id'] = $data0123['id'];
						$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

						$artist_image = $data0123['artist_image'];
						if (empty($artist_image)) {
							$row0123['Author_image'] = $file_path . 'images/add-image.png';
							$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
						} else {
							$row0123['Author_image'] = $file_path . 'images/' . $data0123['artist_image'];
							$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
						}

						$row['author_list'][] = $row0123;
					}
				} else {
					$row['author_list'] = array();
				}

				if (mysqli_num_rows($sql012) > 0) {
					while ($data012345 = mysqli_fetch_assoc($sql012)) {
						$total_songs++;
						$row012345['cid'] = $data012345['cid'];
						$row012345['category_name'] = html_entity_decode($data012345['category_name'], ENT_QUOTES, "UTF-8");
						$row['cat_list'][] = $row012345;
					}
				} else {
					$row['cat_list'] = array();
				}

				array_push($jsonObj, $row);

				unset($row['cat_list']);
				unset($row['author_list']);
			}
		}
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "recent_artist_list") {
	$jsonObj = array();

	$query = "SELECT id,artist_name,artist_image FROM tbl_artist ORDER BY tbl_artist.`id` DESC LIMIT 10";
	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {

		$row['id'] = $data['id'];
		$row['Author_name'] = html_entity_decode($data['artist_name'], ENT_QUOTES, "UTF-8");

		$artist_image = $data['artist_image'];
		if (empty($artist_image)) {
			$row['Author_image'] = $file_path . 'images/add-image.png';
			$row['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['Author_image'] = $file_path . 'images/' . $data['artist_image'];
			$row['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data['artist_image'];
		}

		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "artist_list") {

	$query_rec = "SELECT COUNT(*) as num FROM tbl_artist ORDER BY tbl_artist.`id`";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$query = "SELECT id,artist_name,artist_image FROM tbl_artist ORDER BY tbl_artist.`id` LIMIT $limit, $page_limit";
	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['total_records'] = $total_pages['num'];

		$row['id'] = $data['id'];
		$row['Author_name'] = html_entity_decode($data['artist_name'], ENT_QUOTES, "UTF-8");

		$artist_image = $data['artist_image'];
		if (empty($artist_image)) {
			$row['Author_image'] = $file_path . 'images/add-image.png';
			$row['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['Author_image'] = $file_path . 'images/' . $data['artist_image'];
			$row['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data['artist_image'];
		}

		//	$row['Author_image'] = $file_path . 'images/' . $data['artist_image'];
		//	$row['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data['artist_image'];

		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "Summery_list") {

	$query_rec = "SELECT COUNT(*) as num FROM tbl_summery ORDER BY tbl_summery.`id`";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$query = "SELECT * FROM tbl_summery ORDER BY tbl_summery.`id` LIMIT $limit, $page_limit";
	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['total_records'] = $total_pages['num'];

		$row['id'] = $data['id'];
		$row['mp3_type'] = $data['mp3_type'];
		$row['mp3_title'] = $data['mp3_title'];

		$mp3_thumbnail = $data['mp3_thumbnail'];
		if (empty($mp3_thumbnail)) {
			$row['mp3_thumbnail'] = $file_path . 'images/add-image.png';
			$row['mp3_thumbnail_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['mp3_thumbnail'] = $file_path . 'images/' . $data['mp3_thumbnail'];
			$row['mp3_thumbnail_thumb'] = $file_path . 'images/thumbs/' . $data['mp3_thumbnail'];
		}

		if ($data['mp3_type'] == 'local') {
			$mp3_file = $file_path . 'uploads/' . basename($data['mp3_url']);
		} else if ($data['mp3_type'] == 'server_url') {
			$mp3_file = $data['mp3_url'];
		}

		$row['mp3_url'] = $mp3_file;

		$row['mp3_description'] = html_entity_decode($data['mp3_description'], ENT_QUOTES, "UTF-8");
		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];
		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "Summery_list_by_playlist_id") {

	$playlist_id = $get_method['playlist_id'];

	$query_rec = "SELECT COUNT(*) as num FROM tbl_summery WHERE album_id = $playlist_id ORDER BY tbl_summery.`id`";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$query = "SELECT * FROM tbl_summery  WHERE album_id = $playlist_id ORDER BY tbl_summery.`id` LIMIT $limit, $page_limit";
	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['total_records'] = $total_pages['num'];

		$row['id'] = $data['id'];

		$row['mp3_type'] = $data['mp3_type'];
		$row['mp3_title'] = $data['mp3_title'];

		$mp3_thumbnail = $data['mp3_thumbnail'];
		if (empty($mp3_thumbnail)) {
			$row['mp3_thumbnail'] = $file_path . 'images/add-image.png';
			$row['mp3_thumbnail_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['mp3_thumbnail'] = $file_path . 'images/' . preg_replace("/\r|\n/", "", $data['mp3_thumbnail']);
			$row['mp3_thumbnail_thumb'] = $file_path . 'images/thumbs/' . preg_replace("/\r|\n/", "", $data['mp3_thumbnail']);
		}

		if ($data['mp3_type'] == 'local') {
			$mp3_file = $file_path . 'uploads/' . basename($data['mp3_url']);
		} else if ($data['mp3_type'] == 'server_url') {
			$mp3_file = $data['mp3_url'];
		}

		$row['mp3_url'] = $mp3_file;

		$row['artist_ids'] = $data['mp3_artist'];
		$row['mp3_description'] = html_entity_decode($data['mp3_description'], ENT_QUOTES, "UTF-8");
		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];



		$artist_ids = trim($data['mp3_artist']);

		$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";

		$sql01 = mysqli_query($mysqli, $query01);

		$row['total_author'] = mysqli_num_rows($sql01);

		if (mysqli_num_rows($sql01) > 0) {

			while ($data0123 = mysqli_fetch_assoc($sql01)) {
				$total_songs++;
				$row0123['id'] = $data0123['id'];
				$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

				$artist_image = $data0123['artist_image'];
				if (empty($artist_image)) {
					$row0123['Author_image'] = $file_path . 'images/add-image.png';
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row0123['Author_image'] = $file_path . 'images/' . $data0123['artist_image'];
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
				}

				//	$row0123['author_image'] = $data0123['artist_image'];
				$row['author_list'][] = $row0123;
			}
		} else {
			$row['author_list'] = array();
		}

		array_push($jsonObj, $row);
		unset($row['author_list']);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "artist_album_list") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;
	$query_rec = "SELECT COUNT(*) as num FROM tbl_album WHERE status='1' AND FIND_IN_SET(" . $get_method['artist_id'] . ",tbl_album.artist_ids) ORDER BY tbl_album.`aid` DESC";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();


	$query = "SELECT * FROM tbl_album WHERE status='1' AND FIND_IN_SET(" . $get_method['artist_id'] . ",tbl_album.artist_ids) ORDER BY tbl_album.`aid` DESC LIMIT $limit, $page_limit";

	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['total_records'] = $total_pages['num'];

		$row['aid'] = $data['aid'];
		$row['author_ids'] = $data['artist_ids'];
		$row['cat_ids'] = $data['cat_ids'];
		$row['book_subscription_type'] = $data['book_subscription_type'];
		$row['book_name'] =  html_entity_decode($data['album_name'], ENT_QUOTES, "UTF-8");

		$album_image = $data['album_image'];
		if (empty($album_image)) {
			$row['book_image'] = $file_path . 'images/add-image.png';
			$row['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['book_image'] = $file_path . 'images/' . $data['album_image'];
			$row['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];
		}

		$row['is_favourite'] = is_favourite($data['aid'], $user_id);

		if ($data['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data['book_url']);
		} else if ($data['book_type'] == 'server_url') {
			$book_file = $data['book_url'];
		}

		$row['book_url'] = $book_file;
		//$row['book_image'] = $file_path . 'images/' . $data['album_image'];
		//$row['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];


		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];

		$artist_ids = trim($data['artist_ids']);

		$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";

		$sql01 = mysqli_query($mysqli, $query01);

		$row['total_author'] = mysqli_num_rows($sql01);

		$cat_ids = trim($data['cat_ids']);

		$query012 = "SELECT * FROM tbl_category WHERE tbl_category.`cid` IN ($cat_ids) ";

		$sql012 = mysqli_query($mysqli, $query012);

		$row['total_category'] = mysqli_num_rows($sql012);



		if (mysqli_num_rows($sql01) > 0) {

			while ($data0123 = mysqli_fetch_assoc($sql01)) {
				$total_songs++;
				$row0123['id'] = $data0123['id'];
				$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

				$artist_image = $data0123['artist_image'];
				if (empty($artist_image)) {
					$row0123['Author_image'] = $file_path . 'images/add-image.png';
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row0123['Author_image'] = $file_path . 'images/' . $data0123['artist_image'];
					$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
				}

				//	$row0123['author_image'] = $data0123['artist_image'];
				$row['author_list'][] = $row0123;
			}
		} else {
			$row['author_list'] = array();
		}

		//	array_push($jsonObj_123, $row123);


		if (mysqli_num_rows($sql012) > 0) {

			while ($data012345 = mysqli_fetch_assoc($sql012)) {
				$total_songs++;
				$row012345['cid'] = $data012345['cid'];
				$row012345['category_name'] = html_entity_decode($data012345['category_name'], ENT_QUOTES, "UTF-8");
				$row['cat_list'][] = $row012345;
			}
		} else {
			$row['cat_list'] = array();
		}

		array_push($jsonObj, $row);

		unset($row['cat_list']);
		unset($row['author_list']);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "artist_name_songs") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$post_order_by = API_CAT_POST_ORDER_BY;

	$artist_name = $get_method['artist_name'];

	$query_rec = "SELECT COUNT(*) as num FROM tbl_mp3
		LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid`
		WHERE FIND_IN_SET('" . $artist_name . "',tbl_mp3.`mp3_artist`) AND tbl_category.`status`='1' AND tbl_mp3.`status`='1'";
	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$query = "SELECT * FROM tbl_mp3
		LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid`
		WHERE FIND_IN_SET('" . $artist_name . "',tbl_mp3.`mp3_artist`) AND tbl_category.`status`='1' AND tbl_mp3.`status`='1' ORDER BY tbl_mp3.`id` DESC LIMIT $limit, $page_limit";

	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['total_records'] = $total_pages['num'];
		$row['id'] = $data['id'];
		$row['cat_id'] = $data['cat_id'];
		$row['mp3_type'] = $data['mp3_type'];
		$row['mp3_title'] = $data['mp3_title'];



		if ($data['mp3_type'] == 'local') {
			$mp3_file = $file_path . 'uploads/' . basename($data['mp3_url']);
		} else if ($data['mp3_type'] == 'server_url') {
			$mp3_file = $data['mp3_url'];
		}

		$row['mp3_url'] = $mp3_file;


		$mp3_thumbnail = $data['mp3_thumbnail'];
		if (empty($mp3_thumbnail)) {
			$row['mp3_thumbnail_b'] = $file_path . 'images/add-image.png';
			$row['mp3_thumbnail_s'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['mp3_thumbnail_b'] = $file_path . 'images/' . $data['mp3_thumbnail'];
			$row['mp3_thumbnail_s'] = $file_path . 'images/thumbs/' . $data['mp3_thumbnail'];
		}




		$row['mp3_artist'] = $data['mp3_artist'];
		$row['mp3_description'] = html_entity_decode($data['mp3_description'], ENT_QUOTES, "UTF-8");
		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];

		$row['is_favourite'] = is_favourite($data['id'], $user_id);

		$row['cid'] = $data['cid'];
		$row['category_name'] = html_entity_decode($data['category_name'], ENT_QUOTES, "UTF-8");

		$category_image = $data['category_image'];
		if (empty($category_image)) {
			$row['category_image'] = $file_path . 'images/add-image.png';
			$row['category_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['category_image'] = $file_path . 'images/' . $data['category_image'];
			$row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];
		}

		//$row['category_image'] = $file_path . 'images/' . $data['category_image'];
		//$row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];


		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "album_list") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;
	$query_rec = "SELECT COUNT(*) as num FROM tbl_album WHERE `status`='1' ORDER BY tbl_album.`aid` DESC";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();


	$query = "SELECT * FROM tbl_album WHERE `status`='1' ORDER BY tbl_album.`aid` DESC LIMIT $limit, $page_limit";
	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {

		$book_id =  $data['aid'];

		$total_rate_5 = "SELECT COUNT(*) as num5 FROM tbl_rating WHERE post_id = $book_id AND rate = '5'";
		$total_5 = mysqli_fetch_array(mysqli_query($mysqli, $total_rate_5));
		$total_rate_count_5 = $total_5['num5'];

		$total_rate_4 = "SELECT COUNT(*) as num4 FROM tbl_rating WHERE post_id = $book_id AND rate = '4'";
		$total_4 = mysqli_fetch_array(mysqli_query($mysqli, $total_rate_4));
		$total_rate_count_4 = $total_4['num4'];

		$total_rate_3 = "SELECT COUNT(*) as num3 FROM tbl_rating WHERE post_id = $book_id AND rate = '3'";
		$total_3 = mysqli_fetch_array(mysqli_query($mysqli, $total_rate_3));
		$total_rate_count_3 = $total_3['num3'];

		$total_rate_2 = "SELECT COUNT(*) as num2 FROM tbl_rating WHERE post_id = $book_id AND rate = '2'";
		$total_2 = mysqli_fetch_array(mysqli_query($mysqli, $total_rate_2));
		$total_rate_count_2 = $total_2['num2'];

		$total_rate_1 = "SELECT COUNT(*) as num1 FROM tbl_rating WHERE post_id = $book_id AND rate = '1'";
		$total_1 = mysqli_fetch_array(mysqli_query($mysqli, $total_rate_1));
		$total_rate_count_1 = $total_1['num1'];

		$row['total_records'] = $total_pages['num'];

		$row['aid'] = $data['aid'];
		$row['book_subscription_type'] = $data['book_subscription_type'];
		$row['book_name'] = html_entity_decode($data['album_name'], ENT_QUOTES, "UTF-8");
		$row['cat_ids'] = $data['cat_ids'];

		$album_image = $data['album_image'];
		if (empty($album_image)) {
			$row['book_image'] = $file_path . 'images/add-image.png';
			$row['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['book_image'] = $file_path . 'images/' . $data['album_image'];
			$row['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];
		}

		$row['is_favourite'] = is_favourite($data['aid'], $user_id);

		if ($data['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data['book_url']);
		} else if ($data['book_type'] == 'server_url') {
			$book_file = $data['book_url'];
		}

		$row['book_url'] = $book_file;

		//	$row['book_image'] = $file_path . 'images/' . $data['album_image'];
		//	$row['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];
		$row['total_5'] = $total_rate_count_5;
		$row['total_4'] = $total_rate_count_4;
		$row['total_3'] = $total_rate_count_3;
		$row['total_2'] = $total_rate_count_2;
		$row['total_1'] = $total_rate_count_1;
		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];

		$artist_ids = trim($data['artist_ids']);

		$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";
		$sql01 = mysqli_query($mysqli, $query01);
		$row['total_author'] = mysqli_num_rows($sql01);

		if (mysqli_num_rows($sql01) > 0) {

			while ($data0123 = mysqli_fetch_assoc($sql01)) {
				$total_songs++;
				$row0123['id'] = $data0123['id'];
				$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

				$followI = $data0123['artist_name'];
				$resultArray[] = $followI;

				$artist_image = $data0123['artist_image'];
				if (empty($artist_image)) {
					$row0123['author_image'] = $file_path . 'images/add-image.png';
					$row0123['author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row0123['author_image'] = $file_path . 'images/' . $data0123['artist_image'];
					$row0123['author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
				}

				//	$row0123['author_image'] = $data0123['artist_image'];
				$row['author_list'][] = $row0123;
			}
			$groupConcat = implode(",", $resultArray);

			$row['artist_list'] =  $groupConcat;

			unset($resultArray);
		} else {
			$row['author_list'] = array();
		}

		array_push($jsonObj, $row);
		unset($row['author_list']);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "album_songs") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$post_order_by = API_CAT_POST_ORDER_BY;

	$album_id = $get_method['album_id'];

	$query_rec = "SELECT COUNT(*) as num FROM tbl_mp3
		LEFT JOIN tbl_category ON tbl_mp3.`album_id`= tbl_category.`cid`
		LEFT JOIN tbl_album ON tbl_mp3.`album_id`= tbl_album.`aid` 
		WHERE tbl_mp3.`album_id`='$album_id' AND tbl_category.`status`='1' AND tbl_album.`status`='1' AND tbl_mp3.`status`='1'";
	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;


	$jsonObj = array();

	$query = "SELECT * FROM tbl_mp3
		LEFT JOIN tbl_category ON tbl_mp3.`album_id`= tbl_category.`cid`
		LEFT JOIN tbl_album ON tbl_mp3.`album_id`= tbl_album.`aid` 
		WHERE tbl_mp3.`album_id`='$album_id' AND tbl_category.`status`='1' AND tbl_album.`status`='1' AND tbl_mp3.`status`='1' ORDER BY tbl_mp3.`id` " . $post_order_by . " LIMIT $limit, $page_limit";

	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['total_records'] = $total_pages['num'];
		$row['id'] = $data['id'];
		$row['cat_id'] = $data['cat_id'];
		$row['album_id'] = $data['album_id'];
		$row['mp3_type'] = $data['mp3_type'];
		$row['mp3_title'] = $data['mp3_title'];



		if ($data['mp3_type'] == 'local') {
			$mp3_file = $file_path . 'uploads/' . basename($data['mp3_url']);
		} else if ($data['mp3_type'] == 'server_url') {
			$mp3_file = $data['mp3_url'];
		}

		$row['mp3_url'] = $mp3_file;


		$mp3_thumbnail = $data['mp3_thumbnail'];
		if (empty($mp3_thumbnail)) {
			$row['mp3_thumbnail_b'] = $file_path . 'images/add-image.png';
			$row['mp3_thumbnail_s'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['mp3_thumbnail_b'] = $file_path . 'images/' . $data['mp3_thumbnail'];
			$row['mp3_thumbnail_s'] = $file_path . 'images/thumbs/' . $data['mp3_thumbnail'];
		}


		$row['mp3_artist'] = $data['mp3_artist'];
		$row['mp3_description'] = html_entity_decode($data['mp3_description'], ENT_QUOTES, "UTF-8");
		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];

		$row['is_favourite'] = is_favourite($data['id'], $user_id);

		$row['cid'] = $data['cid'];
		$row['category_name'] = html_entity_decode($data['category_name'], ENT_QUOTES, "UTF-8");

		$category_image = $data['category_image'];
		if (empty($category_image)) {
			$row['category_image'] = $file_path . 'images/add-image.png';
			$row['category_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['category_image'] = $file_path . 'images/' . $data['category_image'];
			$row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];
		}

		//	$row['category_image'] = $file_path . 'images/' . $data['category_image'];
		//	$row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];

		$row['aid'] = $data['aid'];
		$row['book_subscription_type'] = $data['book_subscription_type'];
		$row['book_name'] =  html_entity_decode($data['album_name'], ENT_QUOTES, "UTF-8");


		$album_image = $data['album_image'];
		if (empty($album_image)) {
			$row['book_image'] = $file_path . 'images/add-image.png';
			$row['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['book_image'] = $file_path . 'images/' . $data['album_image'];
			$row['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];
		}


		//	$row['book_image'] = $file_path . 'images/' . $data['album_image'];
		//	$row['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];

		if ($data['book_type'] == 'local') {
			$book_file = $file_path . 'uploads/' . basename($data['book_url']);
		} else if ($data['book_type'] == 'server_url') {
			$book_file = $data['book_url'];
		}

		$row['book_url'] = $book_file;


		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];

		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "playlist") {

	$query_rec = "SELECT COUNT(*) as num FROM tbl_playlist WHERE status='1' ORDER BY tbl_playlist.`pid` DESC";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$page_limit = 10;

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$query = "SELECT * FROM tbl_playlist WHERE status='1' ORDER BY tbl_playlist.`pid` DESC LIMIT $limit, $page_limit";
	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {

		$row['total_records'] = $total_pages['num'];

		$row['pid'] = $data['pid'];
		$row['playlist_name'] = html_entity_decode($data['playlist_name'], ENT_QUOTES, "UTF-8");

		$playlist_image = $data['playlist_image'];
		if (empty($playlist_image)) {
			$row['playlist_image'] = $file_path . 'images/add-image.png';
			$row['playlist_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['playlist_image'] = $file_path . 'images/' . cleanInput($data['playlist_image']);
			$row['playlist_image_thumb'] = $file_path . 'images/thumbs/' . cleanInput($data['playlist_image']);
		}
		$row['playlist_time'] = $data['playlist_time'];
		$row['playlist_description'] = $data['playlist_description'];
		$row['total_rate'] = $data['total_rate'];
		$row['total_views'] = $data['total_views'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];

		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "playlist_songs") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$post_order_by = API_CAT_POST_ORDER_BY;

	$playlist_id = $get_method['playlist_id'];

	$jsonObj = array();

	$query = "SELECT * FROM tbl_playlist where tbl_playlist.`status`='1' AND pid='" . $playlist_id . "'";

	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {

		$row['pid'] = $data['pid'];
		$row['playlist_name'] = html_entity_decode($data['playlist_name'], ENT_QUOTES, "UTF-8");

		$playlist_image = $data['playlist_image'];
		if (empty($playlist_image)) {
			$row['playlist_image'] = $file_path . 'images/add-image.png';
			$row['playlist_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['playlist_image'] = $file_path . 'images/' . cleanInput($data['playlist_image']);
			$row['playlist_image_thumb'] = $file_path . 'images/thumbs/' . cleanInput($data['playlist_image']);
		}

		$row['playlist_time'] = $data['playlist_time'];
		$row['playlist_description'] = $data['playlist_description'];
		$row['total_rate'] = $data['total_rate'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];
		$row['playlist_songs'] = $data['playlist_songs'];

		$songs_list = explode(",", $data['playlist_songs']);

		$total_records = count($songs_list);

		foreach ($songs_list as $song_id) {
			$page_limit = 10;

			$limit = ($get_method['page'] - 1) * $page_limit;

			$query1 = "SELECT * FROM tbl_mp3
				LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid` 
				WHERE tbl_mp3.`id`='$song_id' AND tbl_category.`status`='1' AND tbl_mp3.`status`='1' LIMIT $limit, $page_limit";

			$sql1 = mysqli_query($mysqli, $query1);

			while ($data1 = mysqli_fetch_assoc($sql1)) {
				$row1['total_records'] = "$total_records";

				$row1['id'] = $data1['id'];
				$row1['cat_id'] = $data1['cat_id'];
				$row1['mp3_type'] = $data1['mp3_type'];
				$row1['mp3_title'] = $data1['mp3_title'];


				if ($data1['mp3_type'] == 'local') {
					$mp3_file = $file_path . 'uploads/' . basename($data1['mp3_url']);
				} else if ($data1['mp3_type'] == 'server_url') {
					$mp3_file = $data1['mp3_url'];
				}

				$row1['mp3_url'] = $mp3_file;


				$mp3_thumbnail = $data1['mp3_thumbnail'];
				if (empty($mp3_thumbnail)) {
					$row1['mp3_thumbnail_b'] = $file_path . 'images/add-image.png';
					$row1['mp3_thumbnail_s'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row1['mp3_thumbnail_b'] = $file_path . 'images/' . $data1['mp3_thumbnail'];
					$row1['mp3_thumbnail_s'] = $file_path . 'images/thumbs/' . $data1['mp3_thumbnail'];
				}

				$row1['mp3_artist'] = $data1['mp3_artist'];
				$row1['mp3_description'] = html_entity_decode($data1['mp3_description'], ENT_QUOTES, "UTF-8");
				$row1['total_rate'] = $data1['total_rate'];
				$row1['rate_avg'] = $data1['rate_avg'];
				$row1['total_views'] = $data1['total_views'];
				$row1['total_download'] = $data1['total_download'];

				$row1['is_favourite'] = is_favourite($data1['id'], $user_id);

				$row1['cid'] = $data1['cid'];
				$row1['category_name'] = html_entity_decode($data1['category_name'], ENT_QUOTES, "UTF-8");

				$category_image = $data1['category_image'];
				if (empty($category_image)) {
					$row1['category_image'] = $file_path . 'images/add-image.png';
					$row1['category_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row1['category_image'] = $file_path . 'images/' . $data1['category_image'];
					$row1['category_image_thumb'] = $file_path . 'images/thumbs/' . $data1['category_image'];
				}
				$row['songs_list'][] = $row1;
			}
		}

		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "playlist_summery") {
	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$post_order_by = API_CAT_POST_ORDER_BY;

	$playlist_id = $get_method['playlist_id'];

	$jsonObj = array();

	$query = "SELECT * FROM tbl_playlist
		where tbl_playlist.`status`='1' AND pid='" . $playlist_id . "'";

	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {

		$row['pid'] = $data['pid'];
		$row['playlist_name'] = html_entity_decode($data['playlist_name'], ENT_QUOTES, "UTF-8");

		$playlist_image = $data['playlist_image'];
		if (empty($playlist_image)) {
			$row['playlist_image'] = $file_path . 'images/add-image.png';
			$row['playlist_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['playlist_image'] = $file_path . 'images/' . cleanInput($data['playlist_image']);
			$row['playlist_image_thumb'] = $file_path . 'images/thumbs/' . cleanInput($data['playlist_image']);
		}

		$row['playlist_time'] = $data['playlist_time'];
		$row['playlist_description'] = $data['playlist_description'];
		$row['total_rate'] = $data['total_rate'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_download'] = $data['total_download'];
		$row['playlist_songs'] = $data['playlist_songs'];

		$songs_list = explode(",", $data['playlist_songs']);

		$total_records = count($songs_list);

		foreach ($songs_list as $song_id) {
			$page_limit = 10;

			$limit = ($get_method['page'] - 1) * $page_limit;

			$query1 = "SELECT * FROM tbl_summery where tbl_summery.`status`='1' AND id='" . $song_id . "'";

			$sql1 = mysqli_query($mysqli, $query1);

			while ($data1 = mysqli_fetch_assoc($sql1)) {
				$row1['total_records'] = "$total_records";
				$row1['id'] = $data1['id'];
				$row1['mp3_type'] = $data1['mp3_type'];
				$row1['mp3_title'] = $data1['mp3_title'];

				$mp3_thumbnail = $data1['mp3_thumbnail'];
				if (empty($mp3_thumbnail)) {
					$row1['mp3_thumbnail'] = $file_path . 'images/add-image.png';
					$row1['mp3_thumbnail_thumb'] = $file_path . 'images/thumbs/add-image.png';
				} else {
					$row1['mp3_thumbnail'] = $file_path . 'images/' . $data1['mp3_thumbnail'];
					$row1['mp3_thumbnail_thumb'] = $file_path . 'images/thumbs/' . $data1['mp3_thumbnail'];
				}

				if ($data1['mp3_type'] == 'local') {
					$mp3_file = $file_path . 'uploads/' . basename($data1['mp3_url']);
				} else if ($data1['mp3_type'] == 'server_url') {
					$mp3_file = $data1['mp3_url'];
				}

				$row1['mp3_url'] = $mp3_file;

				$row1['mp3_description'] = html_entity_decode($data1['mp3_description'], ENT_QUOTES, "UTF-8");;
				$row1['total_rate'] = $data1['total_rate'];
				$row1['rate_avg'] = $data1['rate_avg'];
				$row1['total_views'] = $data1['total_views'];
				$row1['total_download'] = $data1['total_download'];

				$row['summery_list'][] = $row1;
			}
		}

		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "single_song") {

	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	$jsonObj = array();

	$query = "SELECT * FROM tbl_mp3
		LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid` 
		WHERE tbl_mp3.`id`='" . $get_method['song_id'] . "' AND tbl_category.`status`='1' AND tbl_mp3.status='1'";

	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['id'] = $data['id'];
		$row['cat_id'] = $data['cat_id'];
		$row['mp3_type'] = $data['mp3_type'];
		$row['mp3_title'] = $data['mp3_title'];

		if ($data['mp3_type'] == 'local') {
			$mp3_file = $file_path . 'uploads/' . basename($data['mp3_url']);
		} else if ($data['mp3_type'] == 'server_url') {
			$mp3_file = $data['mp3_url'];
		}

		$row['mp3_url'] = $mp3_file;

		$mp3_thumbnail = $data['mp3_thumbnail'];
		if (empty($mp3_thumbnail)) {
			$row['mp3_thumbnail_b'] = $file_path . 'images/add-image.png';
			$row['mp3_thumbnail_s'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['mp3_thumbnail_b'] = $file_path . 'images/' . $data['mp3_thumbnail'];
			$row['mp3_thumbnail_s'] = $file_path . 'images/thumbs/' . $data['mp3_thumbnail'];
		}

		$row['mp3_artist'] = $data['mp3_artist'];
		$row['mp3_description'] = html_entity_decode($data['mp3_description'], ENT_QUOTES, "UTF-8");
		$row['total_rate'] = $data['total_rate'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_views'] = $data['total_views'];
		$row['total_download'] = $data['total_download'];

		$row['is_favourite'] = is_favourite($data['id'], $user_id);

		$row['cid'] = $data['cid'];
		$row['category_name'] = html_entity_decode($data['category_name'], ENT_QUOTES, "UTF-8");

		$category_image = $data['category_image'];
		if (empty($category_image)) {
			$row['category_image'] = $file_path . 'images/add-image.png';
			$row['category_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['category_image'] = $file_path . 'images/' . $data['category_image'];
			$row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];
		}

		if ($get_method['user_id']) {
			$query1 = mysqli_query($mysqli, "SELECT * FROM tbl_rating WHERE post_id  = '" . $get_method['song_id'] . "' AND `ip` = '" . $get_method['user_id'] . "' ");
			$data1 = mysqli_fetch_assoc($query1);

			if (@count($data1) != 0) {
				$row['user_rate'] = $data1['rate'];
			} else {
				$row['user_rate'] = 0;
			}
		}

		array_push($jsonObj, $row);
	}

	$view_qry = mysqli_query($mysqli, "UPDATE tbl_mp3 SET total_views = total_views + 1 WHERE id = '" . $get_method['song_id'] . "'");

	$mp3_id = $get_method['song_id'];
	$date = date('Y-m-d');

	$start = (date('D') != 'Mon') ? date('Y-m-d', strtotime('last Monday')) : date('Y-m-d');
	$finish = (date('D') != 'Sat') ? date('Y-m-d', strtotime('next Saturday')) : date('Y-m-d');

	$query = "SELECT * FROM tbl_mp3_views WHERE mp3_id='$mp3_id' and date BETWEEN '$start' AND '$finish'";
	$sql = mysqli_query($mysqli, $query);


	if (mysqli_num_rows($sql) > 0) {

		$query1 = "UPDATE tbl_mp3_views SET views=views+1 WHERE mp3_id='$mp3_id' and date BETWEEN '$start' AND '$finish'";
		$sql1 = mysqli_query($mysqli, $query1);
	} else {

		$data = array(
			'mp3_id'  =>  $mp3_id,
			'views'  =>  1,
			'date'  =>  $date
		);

		$qry = Insert('tbl_mp3_views', $data);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "song_download") {
	$jsonObj = array();

	$view_qry = mysqli_query($mysqli, "UPDATE tbl_mp3 SET total_download = total_download + 1 WHERE id = '" . $get_method['song_id'] . "'");

	$total_dw_sql = "SELECT * FROM tbl_mp3 WHERE id='" . $get_method['song_id'] . "'";
	$total_dw_res = mysqli_query($mysqli, $total_dw_sql);
	$total_dw_row = mysqli_fetch_assoc($total_dw_res);


	$jsonObj = array('total_download' => $total_dw_row['total_download']);

	$set['AUDIO_BOOK'][] = $jsonObj;
	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "book_download") {
	$jsonObj = array();

	$view_qry = mysqli_query($mysqli, "UPDATE tbl_album SET total_download = total_download + 1 WHERE aid = '" . $get_method['book_id'] . "'");

	$total_dw_sql = "SELECT * FROM tbl_album WHERE aid='" . $get_method['book_id'] . "'";
	$total_dw_res = mysqli_query($mysqli, $total_dw_sql);
	$total_dw_row = mysqli_fetch_assoc($total_dw_res);


	$jsonObj = array('total_download' => $total_dw_row['total_download']);

	$set['AUDIO_BOOK'][] = $jsonObj;
	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "playlist_download") {
	$jsonObj = array();

	$view_qry = mysqli_query($mysqli, "UPDATE tbl_playlist SET total_download = total_download + 1 WHERE pid = '" . $get_method['playlist_id'] . "'");

	$total_dw_sql = "SELECT * FROM tbl_playlist WHERE pid='" . $get_method['playlist_id'] . "'";
	$total_dw_res = mysqli_query($mysqli, $total_dw_sql);
	$total_dw_row = mysqli_fetch_assoc($total_dw_res);


	$jsonObj = array('total_download' => $total_dw_row['total_download']);

	$set['AUDIO_BOOK'][] = $jsonObj;
	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "song_search") {

	$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

	if ($get_method['search_type'] == "songs") {

		$query_rec = "SELECT COUNT(*) as num FROM tbl_mp3
			LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid`
			WHERE tbl_mp3.`status`='1' AND tbl_category.`status`='1' AND tbl_mp3.`mp3_title` like '%" . addslashes($get_method['search_text']) . "%' 
			ORDER BY tbl_mp3.mp3_title";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

		$page_limit = 10;

		$limit = ($get_method['page'] - 1) * $page_limit;

		$jsonObj0 = array();

		$query0 = "SELECT * FROM tbl_mp3
			LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid`
			WHERE tbl_mp3.`status`='1' AND tbl_category.`status`='1' AND tbl_mp3.`mp3_title` like '%" . addslashes($get_method['search_text']) . "%' 
			ORDER BY tbl_mp3.mp3_title LIMIT $limit, $page_limit";

		$sql0 = mysqli_query($mysqli, $query0) or die(mysqli_error());

		while ($data0 = mysqli_fetch_assoc($sql0)) {
			$row0['total_data'] = $total_pages['num'];
			$row0['id'] = $data0['id'];
			$row0['cat_id'] = $data0['cat_id'];
			$row0['mp3_type'] = $data0['mp3_type'];
			$row0['mp3_title'] = $data0['mp3_title'];

			if ($data0['mp3_type'] == 'local') {
				$mp3_file = $file_path . 'uploads/' . basename($data0['mp3_url']);
			} else if ($data0['mp3_type'] == 'server_url') {
				$mp3_file = $data0['mp3_url'];
			}

			$row0['mp3_url'] = $mp3_file;


			$mp3_thumbnail = $data0['mp3_thumbnail'];
			if (empty($mp3_thumbnail)) {
				$row0['mp3_thumbnail_b'] = $file_path . 'images/add-image.png';
				$row0['mp3_thumbnail_s'] = $file_path . 'images/thumbs/add-image.png';
			} else {
				$row0['mp3_thumbnail_b'] = $file_path . 'images/' . $data0['mp3_thumbnail'];
				$row0['mp3_thumbnail_s'] = $file_path . 'images/thumbs/' . $data0['mp3_thumbnail'];
			}

			$row0['mp3_artist'] = $data0['mp3_artist'];
			$row0['mp3_description'] = html_entity_decode($data0['mp3_description'], ENT_QUOTES, "UTF-8");
			$row0['total_rate'] = $data0['total_rate'];
			$row0['rate_avg'] = $data0['rate_avg'];
			$row0['total_views'] = $data0['total_views'];
			$row0['total_download'] = $data0['total_download'];

			$row0['is_favourite'] = is_favourite($data0['id'], $user_id);

			$row0['cid'] = $data0['cid'];
			$row0['category_name'] = html_entity_decode($data0['category_name'], ENT_QUOTES, "UTF-8");


			$category_image = $data0['category_image'];
			if (empty($category_image)) {
				$row0['category_image'] = $file_path . 'images/add-image.png';
				$row0['category_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
			} else {
				$row0['category_image'] = $file_path . 'images/' . $data0['category_image'];
				$row0['category_image_thumb'] = $file_path . 'images/thumbs/' . $data0['category_image'];
			}

			array_push($jsonObj0, $row0);
		}

		$set['AUDIO_BOOK'] = $jsonObj0;
	} else if ($get_method['search_type'] == "artist") {

		$query_rec = "SELECT COUNT(*) as num FROM tbl_artist
			WHERE tbl_artist.`artist_name` like '%" . addslashes($get_method['search_text']) . "%' 
			ORDER BY tbl_artist.artist_name";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

		$page_limit = 10;

		$limit = ($get_method['page'] - 1) * $page_limit;

		$jsonObj2 = array();

		$query2 = "SELECT * FROM tbl_artist
			WHERE tbl_artist.`artist_name` like '%" . addslashes($get_method['search_text']) . "%' 
			ORDER BY tbl_artist.artist_name LIMIT $limit, $page_limit";

		$sql2 = mysqli_query($mysqli, $query2) or die(mysqli_error());

		while ($data2 = mysqli_fetch_assoc($sql2)) {

			$row2['total_data'] = $total_pages['num'];
			$row2['id'] = $data2['id'];
			$row2['Author_name'] = html_entity_decode($data2['artist_name'], ENT_QUOTES, "UTF-8");

			$artist_image = $data2['artist_image'];
			if (empty($artist_image)) {
				$row2['Author_image'] = $file_path . 'images/add-image.png';
				$row2['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
			} else {
				$row2['Author_image'] = $file_path . 'images/' . $data2['artist_image'];
				$row2['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data2['artist_image'];
			}

			//$row2['Author_image'] = $file_path . 'images/' . $data2['artist_image'];
			//	$row2['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data2['artist_image'];

			array_push($jsonObj2, $row2);
		}

		$set['AUDIO_BOOK'] = $jsonObj2;
	} else if ($get_method['search_type'] == "album") {

		$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;

		$query_rec = "SELECT COUNT(*) as num FROM tbl_album
			WHERE tbl_album.`album_name` AND tbl_album.`status`='1' like '%" . addslashes($get_method['search_text']) . "%' 
			ORDER BY tbl_album.album_name";
		//echo $query_rec;
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

		$page_limit = 10;

		$limit = ($get_method['page'] - 1) * $page_limit;

		$jsonObj1 = array();

		$query1 = "SELECT * FROM tbl_album
			WHERE tbl_album.`album_name` like '%" . addslashes($get_method['search_text']) . "%' AND tbl_album.`status`='1'
			ORDER BY tbl_album.album_name LIMIT $limit, $page_limit";

		$sql1 = mysqli_query($mysqli, $query1) or die(mysqli_error());

		while ($data1 = mysqli_fetch_assoc($sql1)) {
			// $row1['total_data'] = $total_pages['num'];
			$row1['aid'] = $data1['aid'];
			$row1['artist_ids'] = $data1['artist_ids'] ? $data1['artist_ids'] : '';
			$row1['cat_ids'] = $data1['cat_ids'] ? $data1['cat_ids'] : '';
			$row1['book_subscription_type'] = $data1['book_subscription_type'];
			$row1['book_name'] =  html_entity_decode($data1['album_name'], ENT_QUOTES, "UTF-8");

			$album_image = $data1['album_image'];
			if (empty($album_image)) {
				$row1['book_image'] = $file_path . 'images/add-image.png';
				$row1['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
			} else {
				$row1['book_image'] = $file_path . 'images/' . $data1['album_image'];
				$row1['book_image_thumb'] = $file_path . 'images/thumbs/' . $data1['album_image'];
			}
			$row1['is_favourite'] = is_favourite($data1['aid'], $user_id);

			if ($data1['book_type'] == 'local') {
				$book_file = $file_path . 'uploads/' . basename($data1['book_url']);
			} else if ($data1['book_type'] == 'server_url') {
				$book_file = $data1['book_url'];
			}

			$row1['book_url'] = $book_file;


			// $row1['book_image'] = $file_path . 'images/' . $data1['album_image'];
			// $row1['book_image_thumb'] = $file_path . 'images/thumbs/' . $data1['album_image'];


			$row1['total_rate'] = $data1['total_rate'];
			$row1['total_views'] = $data1['total_views'];
			$row1['rate_avg'] = $data1['rate_avg'];
			$row1['total_download'] = $data1['total_download'];

			array_push($jsonObj1, $row1);
		}

		$set['AUDIO_BOOK'] = $jsonObj1;
	} else {
		$user_id = isset($get_method['user_id']) ? $get_method['user_id'] : 0;
		$query_rec = "SELECT COUNT(*) as num FROM tbl_mp3
			LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid`
			WHERE tbl_mp3.`status`='1' AND tbl_category.`status`='1' AND tbl_mp3.`mp3_title` like '%" . addslashes($get_method['search_text']) . "%' 
			ORDER BY tbl_mp3.mp3_title";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

		$page_limit = 10;

		$limit = ($get_method['page'] - 1) * $page_limit;

		$jsonObj0 = array();

		$query0 = "SELECT * FROM tbl_mp3
			LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid`
			WHERE tbl_mp3.`status`='1' AND tbl_category.`status`='1' AND tbl_mp3.`mp3_title` like '%" . addslashes($get_method['search_text']) . "%' 
			ORDER BY tbl_mp3.mp3_title LIMIT $limit, $page_limit";

		$sql0 = mysqli_query($mysqli, $query0) or die(mysqli_error());

		while ($data0 = mysqli_fetch_assoc($sql0)) {
			$row0['total_data'] = $total_pages['num'];
			$row0['id'] = $data0['id'];
			$row0['cat_id'] = $data0['cat_id'];
			$row0['mp3_type'] = $data0['mp3_type'];
			$row0['mp3_title'] = $data0['mp3_title'];

			if ($data0['mp3_type'] == 'local') {
				$mp3_file = $file_path . 'uploads/' . basename($data0['mp3_url']);
			} else if ($data0['mp3_type'] == 'server_url') {
				$mp3_file = $data0['mp3_url'];
			}

			$row0['mp3_url'] = $mp3_file;


			$mp3_thumbnail = $data0['mp3_thumbnail'];
			if (empty($mp3_thumbnail)) {
				$row0['mp3_thumbnail_b'] = $file_path . 'images/add-image.png';
				$row0['mp3_thumbnail_s'] = $file_path . 'images/thumbs/add-image.png';
			} else {
				$row0['mp3_thumbnail_b'] = $file_path . 'images/' . $data0['mp3_thumbnail'];
				$row0['mp3_thumbnail_s'] = $file_path . 'images/thumbs/' . $data0['mp3_thumbnail'];
			}
			$row0['mp3_artist'] = $data0['mp3_artist'];
			$row0['mp3_description'] = html_entity_decode($data0['mp3_description'], ENT_QUOTES, "UTF-8");
			$row0['total_rate'] = $data0['total_rate'];
			$row0['rate_avg'] = $data0['rate_avg'];
			$row0['total_views'] = $data0['total_views'];
			$row0['total_download'] = $data0['total_download'];

			$row0['is_favourite'] = is_favourite($data0['id'], $user_id);

			$row0['cid'] = $data0['cid'];
			$row0['category_name'] = html_entity_decode($data0['category_name'], ENT_QUOTES, "UTF-8");

			$category_image = $data0['category_image'];
			if (empty($category_image)) {
				$row['category_image'] = $file_path . 'images/add-image.png';
				$row['category_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
			} else {
				$row0['category_image'] = $file_path . 'images/' . $data0['category_image'];
				$row0['category_image_thumb'] = $file_path . 'images/thumbs/' . $data0['category_image'];
			}

			array_push($jsonObj0, $row0);
		}

		$row['search_songs'] = $jsonObj0;


		$jsonObj1 = array();

		$query1 = "SELECT * FROM tbl_album
			WHERE tbl_album.`status`='1' AND tbl_album.`album_name` like '%" . addslashes($get_method['search_text']) . "%' 
			ORDER BY tbl_album.album_name LIMIT 20";

		$sql1 = mysqli_query($mysqli, $query1) or die(mysqli_error());

		while ($data1 = mysqli_fetch_assoc($sql1)) {
			$row1['aid'] = $data1['aid'];
			$row1['artist_ids'] = $data1['artist_ids'] ? $data1['artist_ids'] : '';
			$row1['cat_ids'] = $data1['cat_ids'] ? $data1['cat_ids'] : '';
			$row1['book_subscription_type'] = $data1['book_subscription_type'];
			$row1['book_name'] =  html_entity_decode($data1['album_name'], ENT_QUOTES, "UTF-8");

			$album_image = $data1['album_image'];
			if (empty($album_image)) {
				$row1['book_image'] = $file_path . 'images/add-image.png';
				$row1['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
			} else {
				$row1['book_image'] = $file_path . 'images/' . $data1['album_image'];
				$row1['book_image_thumb'] = $file_path . 'images/thumbs/' . $data1['album_image'];
			}
			$row1['is_favourite'] = is_favourite($data1['aid'], $user_id);

			//$row1['book_image'] = $file_path . 'images/' . $data1['album_image'];
			//$row1['book_image_thumb'] = $file_path . 'images/thumbs/' . $data1['album_image'];


			$row1['total_rate'] = $data1['total_rate'];
			$row1['total_views'] = $data1['total_views'];
			$row1['rate_avg'] = $data1['rate_avg'];
			$row1['total_download'] = $data1['total_download'];

			array_push($jsonObj1, $row1);
		}

		$row['search_album'] = $jsonObj1;

		$jsonObj2 = array();

		$query2 = "SELECT * FROM tbl_artist WHERE tbl_artist.`artist_name` LIKE '%" . addslashes($get_method['search_text']) . "%' 
			ORDER BY tbl_artist.artist_name LIMIT 20";

		$sql2 = mysqli_query($mysqli, $query2) or die(mysqli_error());

		while ($data2 = mysqli_fetch_assoc($sql2)) {
			$row2['id'] = $data2['id'];
			$row2['Author_name'] = html_entity_decode($data2['artist_name'], ENT_QUOTES, "UTF-8");

			$artist_image = $data2['artist_image'];
			if (empty($artist_image)) {
				$row2['Author_image'] = $file_path . 'images/add-image.png';
				$row2['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
			} else {
				$row2['Author_image'] = $file_path . 'images/' . $data2['artist_image'];
				$row2['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data2['artist_image'];
			}

			//	$row2['Author_image'] = $file_path . 'images/' . $data2['artist_image'];
			//	$row2['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data2['artist_image'];

			array_push($jsonObj2, $row2);
		}

		$row['search_artist'] = $jsonObj2;

		$set['AUDIO_BOOK'] = $row;
	}

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "song_rating") {

	$user_id = trim($get_method['user_id']);
	$post_id = trim($get_method['post_id']);
	$therate = trim($get_method['rate']);

	$query1 = mysqli_query($mysqli, "SELECT * FROM tbl_rating WHERE `post_id`  = '$post_id' AND `ip` = '$user_id'");
	while ($data1 = mysqli_fetch_assoc($query1)) {
		$rate_db1[] = $data1;
	}
	if (@count($rate_db1) == 0) {

		$data = array(
			'post_id'  => $post_id,
			'rate'  =>  $therate,
			'ip'  => $user_id,
		);

		$qry = Insert('tbl_rating', $data);

		//Total rate result

		$query = mysqli_query($mysqli, "SELECT * FROM tbl_rating WHERE `post_id` = '$post_id' ");

		while ($data = mysqli_fetch_assoc($query)) {
			$rate_db[] = $data;
			$sum_rates[] = $data['rate'];
		}

		if (@count($rate_db)) {
			$rate_times = count($rate_db);
			$sum_rates = array_sum($sum_rates);
			$rate_value = $sum_rates / $rate_times;
			$rate_bg = (($rate_value) / 5) * 100;
		} else {
			$rate_times = 0;
			$rate_value = 0;
			$rate_bg = 0;
		}

		$rate_avg = round($rate_value);

		$sql = "UPDATE tbl_mp3 SET `total_rate`=`total_rate` + 1, `rate_avg`='$rate_avg' WHERE `id`='$post_id'";

		mysqli_query($mysqli, $sql);

		$total_rat_sql = "SELECT * FROM tbl_mp3 WHERE id='" . $post_id . "'";
		$total_rat_res = mysqli_query($mysqli, $total_rat_sql);
		$total_rat_row = mysqli_fetch_assoc($total_rat_res);

		$set['AUDIO_BOOK'][] = array('total_rate' => $total_rat_row['total_rate'], 'rate_avg' => $total_rat_row['rate_avg'], 'msg' => $app_lang['rate_success'], 'success' => '1');
	} else {

		$set['AUDIO_BOOK'][] = array('msg' => $app_lang['rate_already'], 'success' => '0');
	}

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "book_rating") {

	$user_id = trim($get_method['user_id']);
	$post_id = trim($get_method['post_id']);
	$therate = trim($get_method['rate']);

	$query1 = mysqli_query($mysqli, "SELECT * FROM tbl_rating WHERE `post_id`  = '$post_id' AND `ip` = '$user_id' ");
	$data1 = mysqli_fetch_assoc($query1);
	$rate_db1 = mysqli_num_rows($query1);

	if ($rate_db1 == 0) {
		$data = array(
			'post_id'  => $post_id,
			'rate'  =>  $therate,
			'ip'  => $user_id,
		);

		$qry = Insert('tbl_rating', $data);

		//Total rate result

		$query = mysqli_query($mysqli, "SELECT * FROM tbl_rating WHERE `post_id` = '$post_id' ");

		while ($data = mysqli_fetch_assoc($query)) {
			// $rate_db[] = $data;
			$sum_rates[] = $data['rate'];
		}

		$rate_db = mysqli_num_rows($query);

		if ($rate_db != 0) {
			$rate_times = $rate_db;
			$sum_rates = array_sum($sum_rates);
			$rate_value = $sum_rates / $rate_times;
			$rate_bg = (($rate_value) / 5) * 100;
		} else {
			$rate_times = 0;
			$rate_value = 0;
			$rate_bg = 0;
		}

		$rate_avg = round($rate_value);

		$sql = "UPDATE tbl_album SET `total_rate`=`total_rate` + 1, `rate_avg`='$rate_avg' WHERE `aid`='$post_id'";

		mysqli_query($mysqli, $sql);

		$total_rat_sql = "SELECT * FROM tbl_album WHERE aid='" . $post_id . "'";
		$total_rat_res = mysqli_query($mysqli, $total_rat_sql);
		$total_rat_row = mysqli_fetch_assoc($total_rat_res);

		$set['AUDIO_BOOK'][] = array('total_rate' => $total_rat_row['total_rate'], 'rate_avg' => $total_rat_row['rate_avg'], 'msg' => $app_lang['rate_success'], 'success' => '1');
	} 
	else 
	{

        // Sanitize input data
        $user_id = mysqli_real_escape_string($mysqli, $get_method['user_id']);
        $post_id = mysqli_real_escape_string($mysqli, $get_method['post_id']);
        $rate = mysqli_real_escape_string($mysqli, $get_method['rate']);
        
        // Update tbl_rating table
        $user_edit = "UPDATE tbl_rating 
                      SET rate = '$rate'
                      WHERE ip = '$user_id'
                      AND post_id = '$post_id'";
        
        $user_res = mysqli_query($mysqli, $user_edit);
        if (!$user_res) {
            die('Error updating rating: ' . mysqli_error($mysqli));
        }
        
        // Fetch all ratings for the post
        $query = mysqli_query($mysqli, "SELECT * FROM tbl_rating WHERE `post_id` = '$post_id' ");
        if (!$query) {
            die('Error fetching ratings: ' . mysqli_error($mysqli));
        }
        
        $sum_rates = [];
        while ($data = mysqli_fetch_assoc($query)) {
            $sum_rates[] = $data['rate'];
        }
        
        $rate_db = mysqli_num_rows($query);
        
        // Calculate rating statistics
        if ($rate_db > 0) {
            $sum_rates = array_sum($sum_rates);
            $rate_value = $sum_rates / $rate_db;
            $rate_bg = ($rate_value / 5) * 100;
        } else {
            $rate_value = 0;
            $rate_bg = 0;
        }
        
        $rate_avg = round($rate_value);
        
        $sql = "UPDATE tbl_album SET total_rate = total_rate + 1, rate_avg = '$rate_avg' WHERE aid = '$post_id'";
        mysqli_query($mysqli, $sql);
        
        $total_rat_sql = "SELECT * FROM tbl_album WHERE aid = '$post_id'";
        $total_rat_res = mysqli_query($mysqli, $total_rat_sql);
        if (!$total_rat_res) {
            die('Error fetching album details: ' . mysqli_error($mysqli));
        }
        
        $total_rat_row = mysqli_fetch_assoc($total_rat_res);
        
        $set['AUDIO_BOOK'][] = array(
            'total_rate' => $total_rat_row['total_rate'],
            'rate_avg' => $total_rat_row['rate_avg'],
            'msg' => $app_lang['rate_success'],
            'success' => '1'
        );

	}

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "playlist_rating") {

	$user_id = trim($get_method['user_id']);
	$post_id = trim($get_method['post_id']);
	$therate = trim($get_method['rate']);

	$query1 = mysqli_query($mysqli, "SELECT * FROM tbl_playlist_rating WHERE `post_id`  = '$post_id' AND `ip` = '$user_id'");
	while ($data1 = mysqli_fetch_assoc($query1)) {
		$rate_db1[] = $data1;
	}
	if (@count($rate_db1) == 0) {

		$data = array(
			'post_id'  => $post_id,
			'rate'  =>  $therate,
			'ip'  => $user_id,
		);

		$qry = Insert('tbl_playlist_rating', $data);

		//Total rate result

		$query = mysqli_query($mysqli, "SELECT * FROM tbl_playlist_rating WHERE `post_id` = '$post_id' ");

		while ($data = mysqli_fetch_assoc($query)) {
			$rate_db[] = $data;
			$sum_rates[] = $data['rate'];
		}

		if (@count($rate_db)) {
			$rate_times = count($rate_db);
			$sum_rates = array_sum($sum_rates);
			$rate_value = $sum_rates / $rate_times;
			$rate_bg = (($rate_value) / 5) * 100;
		} else {
			$rate_times = 0;
			$rate_value = 0;
			$rate_bg = 0;
		}

		$rate_avg = round($rate_value);

		$sql = "UPDATE tbl_playlist SET `total_rate`=`total_rate` + 1, `rate_avg`='$rate_avg' WHERE `pid`='$post_id'";

		mysqli_query($mysqli, $sql);

		$total_rat_sql = "SELECT * FROM tbl_playlist WHERE pid='" . $post_id . "'";
		$total_rat_res = mysqli_query($mysqli, $total_rat_sql);
		$total_rat_row = mysqli_fetch_assoc($total_rat_res);

		$set['AUDIO_BOOK'][] = array('total_rate' => $total_rat_row['total_rate'], 'rate_avg' => $total_rat_row['rate_avg'], 'msg' => $app_lang['rate_success'], 'success' => '1');
	} else {

		$set['AUDIO_BOOK'][] = array('msg' => $app_lang['rate_already'], 'success' => '0');
	}

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "song_report") {

	$report = addslashes(trim($get_method['report']));

	$qry1 = "INSERT INTO tbl_reports (`user_id`,`song_id`,`report`) VALUES ('" . $get_method['user_id'] . "','" . $get_method['song_id'] . "','" . $report . "')";

	$result1 = mysqli_query($mysqli, $qry1);


	$set['AUDIO_BOOK'][] = array('msg' => $app_lang['report_success'], 'success' => '1');

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "song_suggest") {
	$user_id = $get_method['user_id'];
	$song_title = $get_method['song_title'];
	$message = $get_method['message'];

	if ($_FILES['song_image']['name'] != "") {
		$song_image = rand(0, 99999) . "_" . $_FILES['song_image']['name'];

		//Main Image
		$tpath1 = 'images/' . $song_image;
		$pic1 = compress_image($_FILES["song_image"]["tmp_name"], $tpath1, 80);

		$qry1 = "INSERT INTO tbl_song_suggest (`user_id`,`song_title`,`song_image`,`message`) VALUES ('" . $user_id . "','" . $song_title . "','" . $song_image . "','" . $message . "')";
		$result1 = mysqli_query($mysqli, $qry1);


		$set['AUDIO_BOOK'][] = array('msg' => 'Song Suggest Sucessfully', 'success' => '1');
	} else {
		$qry1 = "INSERT INTO tbl_song_suggest (`user_id`,`song_title`,`message`) VALUES ('" . $user_id . "','" . $song_title . "','" . $message . "')";
		$result1 = mysqli_query($mysqli, $qry1);

		$set['AUDIO_BOOK'][] = array('msg' => 'Song Suggest Sucessfully', 'success' => '1');
	}

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "user_register") {

	$user_type = trim($get_method['type']); //Google, Normal, Facebook

	$email = addslashes(trim($get_method['email']));
	$auth_id = addslashes(trim($get_method['auth_id']));

	$to = $get_method['email'];
	$recipient_name = $get_method['name'];
	// subject

	$subject = str_replace('###', APP_NAME, $app_lang['register_mail_lbl']);

	if ($user_type == 'Google' || $user_type == 'google') {
		// register with google

		$sql = "SELECT * FROM tbl_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND `user_type`='Google'";
		$res = mysqli_query($mysqli, $sql);
		$num_rows = mysqli_num_rows($res);
		$row = mysqli_fetch_assoc($res);

		if ($num_rows == 0) {
			// data is not available
			$data = array(
				'user_type' => 'Google',
				'user_image'  =>  addslashes(trim($get_method['user_image'])),
				'name'  => addslashes(trim($get_method['name'])),
				'email'  =>  addslashes(trim($get_method['email'])),
				'password'  =>  password_hash($get_method['password'], PASSWORD_ARGON2I),
				'phone'  =>  addslashes(trim($get_method['phone'])),
				'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')),
				'status'  =>  '1'
			);

			$qry = Insert('tbl_users', $data);

			$user_id = mysqli_insert_id($mysqli);

			$sql_activity_log = "SELECT * FROM tbl_active_log WHERE `user_id`='$user_id'";
			$res_activity_log = mysqli_query($mysqli, $sql_activity_log);

			if (mysqli_num_rows($res_activity_log) == 0) {
				// insert active log

				$data_log = array(
					'user_id'  =>  $user_id,
					'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
				);

				$qry = Insert('tbl_active_log', $data_log);
			} else {
				// update active log
				$data_log = array(
					'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
				);

				$update = Update('tbl_active_log', $data_log, "WHERE user_id = '$user_id'");
			}

			mysqli_free_result($res_activity_log);

			$message = '<div style="background-color: #eee;" align="center"><br />
							  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
							    <tbody>
							      <tr>
							        <td colspan="2" bgcolor="#FFFFFF" align="center" ><img src="' . $file_path . 'images/' . APP_LOGO . '" alt="logo" style="width:100px;height:auto"/></td>
							      </tr>
							      <br>
							      <br>
							      <tr>
							        <td colspan="2" bgcolor="#FFFFFF" align="center" style="padding-top:25px;">
							          <img src="' . $file_path . 'assets/images/thankyoudribble.gif" alt="header" auto-height="100" width="50%"/>
							        </td>
							      </tr>
							      <tr>
							        <td width="600" valign="top" bgcolor="#FFFFFF">
							          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
							            <tbody>
							              <tr>
							                <td valign="top">
							                  <table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
							                    <tbody>
							                      <tr>
							                        <td>
							                          <p style="color: #717171; font-size: 24px; margin-top:0px; margin:0 auto; text-align:center;"><strong>' . $app_lang['welcome_lbl'] . ', ' . addslashes(trim($get_method['name'])) . '</strong></p>
							                          <br>
							                          <p style="color:#15791c; font-size:18px; line-height:32px;font-weight:500;margin-bottom:30px; margin:0 auto; text-align:center;">' . $app_lang['google_register_msg'] . '<br /></p>
							                          <br/>
							                          <p style="color:#999; font-size:17px; line-height:32px;font-weight:500;">' . $app_lang['thank_you_lbl'] . ' ' . APP_NAME . '</p>
							                            </td>
							                      </tr>
							                    </tbody>
							                  </table>
							                </td>
							              </tr>
							            </tbody>
							          </table>
							        </td>
							      </tr>
							      <tr>
							        <td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">' . $app_lang['email_copyright'] . ' ' . APP_NAME . '.</td>
							      </tr>
							    </tbody>
							  </table>
							</div>';

			$user_image = $get_method['user_image'];

			if ($user_image == NULL) {
				$user_image = $file_path . 'images/add-image.png';
			} else {
				$user_image = $get_method['user_image'];
			}

			$set['AUDIO_BOOK'][] = array('user_id' => strval($user_id), 'name' => $get_method['name'], 'user_image' => $user_image, 'email' => $get_method['email'], 'success' => '1', 'msg' => '', 'auth_id' => $auth_id);
		} else {
			$user_image = $get_method['user_image'];

			if (empty($user_image)) {
				$data = array(
					'auth_id'  =>  $auth_id,
				);
			} else {
				$data = array(
					'auth_id'  =>  $auth_id,
					'user_image' => $user_image
				);
			}

			$update = Update('tbl_users', $data, "WHERE id = '" . $row['id'] . "'");

			$user_id = $row['id'];

			$sql_activity_log = "SELECT * FROM tbl_active_log WHERE `user_id`='$user_id'";
			$res_activity_log = mysqli_query($mysqli, $sql_activity_log);

			if (mysqli_num_rows($res_activity_log) == 0) {
				// insert active log

				$data_log = array(
					'user_id'  =>  $user_id,
					'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
				);

				$qry = Insert('tbl_active_log', $data_log);
			} else {
				// update active log
				$data_log = array(
					'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
				);

				$update = Update('tbl_active_log', $data_log, "WHERE user_id = '$user_id'");
			}

			mysqli_free_result($res_activity_log);


			if ($row['status'] == 0) {
				$set['AUDIO_BOOK'][] = array('msg' => $app_lang['account_deactive'], 'success' => '0');
			} else if ($row['is_deleted'] == 1) {
				$user_image = $row['user_image'];

				if ($user_image == NULL) {
					$user_image = $file_path . 'images/add-image.png';
				} else {
					$user_image = $row['user_image'];
				}

				// $set['AUDIO_BOOK'][]=array('msg'  => $app_lang['user_deleted'],'success'=>'0');
				$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'user_image' => $user_image, 'email' => $row['email'], 'msg' => $app_lang['user_deleted'], 'auth_id' => $auth_id, 'success' => '0');
			} else {
				$user_image = $row['user_image'];

				if ($user_image == NULL) {
					$user_image = $file_path . 'images/add-image.png';
				} else {
					$user_image = $row['user_image'];
				}

				$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'user_image' => $user_image, 'email' => $row['email'], 'msg' => $app_lang['login_success'], 'auth_id' => $auth_id, 'success' => '1');
			}
		}
	} else if ($user_type == 'Facebook' || $user_type == 'facebook') {
		// register with facebook

		$sql = "SELECT * FROM tbl_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND `user_type`='Facebook'";
		$res = mysqli_query($mysqli, $sql);
		$num_rows = mysqli_num_rows($res);
		$row = mysqli_fetch_assoc($res);

		if ($num_rows == 0) {
			// data is not available
			$data = array(
				'user_type' => 'Facebook',
				'user_image'  =>  addslashes(trim($get_method['user_image'])),
				'name'  => addslashes(trim($get_method['name'])),
				'email'  =>  addslashes(trim($get_method['email'])),
				'password'  =>  password_hash($get_method['password'], PASSWORD_ARGON2I),
				'phone'  =>  addslashes(trim($get_method['phone'])),
				'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')),
				'status'  =>  '1'
			);

			$qry = Insert('tbl_users', $data);

			$user_id = mysqli_insert_id($mysqli);

			$sql_activity_log = "SELECT * FROM tbl_active_log WHERE `user_id`='$user_id'";
			$res_activity_log = mysqli_query($mysqli, $sql_activity_log);

			if (mysqli_num_rows($res_activity_log) == 0) {
				// insert active log

				$data_log = array(
					'user_id'  =>  $user_id,
					'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
				);

				$qry = Insert('tbl_active_log', $data_log);
			} else {
				// update active log
				$data_log = array(
					'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
				);

				$update = Update('tbl_active_log', $data_log, "WHERE user_id = '$user_id'");
			}

			mysqli_free_result($res_activity_log);

			$message = '<div style="background-color: #eee;" align="center"><br />
							  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
							    <tbody>
							      <tr>
							        <td colspan="2" bgcolor="#FFFFFF" align="center" ><img src="' . $file_path . 'images/' . APP_LOGO . '" alt="logo" style="width:100px;height:auto"/></td>
							      </tr>
							      <br>
							      <br>
							      <tr>
							        <td colspan="2" bgcolor="#FFFFFF" align="center" style="padding-top:25px;">
							          <img src="' . $file_path . 'assets/images/thankyoudribble.gif" alt="header" auto-height="100" width="50%"/>
							        </td>
							      </tr>
							      <tr>
							        <td width="600" valign="top" bgcolor="#FFFFFF">
							          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
							            <tbody>
							              <tr>
							                <td valign="top">
							                  <table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
							                    <tbody>
							                      <tr>
							                        <td>
							                          <p style="color: #717171; font-size: 24px; margin-top:0px; margin:0 auto; text-align:center;"><strong>' . $app_lang['welcome_lbl'] . ', ' . addslashes(trim($get_method['name'])) . '</strong></p>
							                          <br>
							                          <p style="color:#15791c; font-size:18px; line-height:32px;font-weight:500;margin-bottom:30px; margin:0 auto; text-align:center;">' . $app_lang['facebook_register_msg'] . '<br /></p>
							                          <br/>
							                          <p style="color:#999; font-size:17px; line-height:32px;font-weight:500;">' . $app_lang['thank_you_lbl'] . ' ' . APP_NAME . '</p>
							                            </td>
							                      </tr>
							                    </tbody>
							                  </table>
							                </td>
							              </tr>
							            </tbody>
							          </table>
							        </td>
							      </tr>
							      <tr>
							        <td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">' . $app_lang['email_copyright'] . ' ' . APP_NAME . '.</td>
							      </tr>
							    </tbody>
							  </table>
							</div>';

			$user_image = $get_method['user_image'];

			if ($user_image == NULL) {
				$user_image = $file_path . 'images/add-image.png';
			} else {
				$user_image = $get_method['user_image'];
			}

			$set['AUDIO_BOOK'][] = array('user_id' => strval($user_id), 'name' => $get_method['name'], 'user_image' => $user_image, 'email' => $get_method['email'], 'success' => '1', 'msg' => '', 'auth_id' => $auth_id);
		} else {
			$user_image = $get_method['user_image'];

			if (empty($user_image)) {
				$data = array(
					'auth_id'  =>  $auth_id,
				);
			} else {
				$data = array(
					'auth_id'  =>  $auth_id,
					'user_image' => $user_image
				);
			}

			$update = Update('tbl_users', $data, "WHERE id = '" . $row['id'] . "'");

			$user_id = $row['id'];

			$sql_activity_log = "SELECT * FROM tbl_active_log WHERE `user_id`='$user_id'";
			$res_activity_log = mysqli_query($mysqli, $sql_activity_log);

			if (mysqli_num_rows($res_activity_log) == 0) {
				// insert active log

				$data_log = array(
					'user_id'  =>  $user_id,
					'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
				);

				$qry = Insert('tbl_active_log', $data_log);
			} else {
				// update active log
				$data_log = array(
					'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
				);

				$update = Update('tbl_active_log', $data_log, "WHERE user_id = '$user_id'");
			}

			mysqli_free_result($res_activity_log);

			$user_image = $row['user_image'];

			if ($user_image == NULL) {
				$user_image = $file_path . 'images/add-image.png';
			} else {
				$user_image = $row['user_image'];
			}

			if ($row['status'] == 0) {
				$set['AUDIO_BOOK'][] = array('msg' => $app_lang['account_deactive'], 'success' => '0');
			} else if ($row['is_deleted'] == 1) {
				// $set['AUDIO_BOOK'][]=array('msg'  => $app_lang['user_deleted'],'success'=>'0');
				$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'user_image' => $user_image, 'email' => $row['email'], 'msg' => $app_lang['user_deleted'], 'auth_id' => $auth_id, 'success' => '0');
			} else {
				$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'email' => $row['email'], 'user_image' => $user_image, 'msg' => $app_lang['login_success'], 'auth_id' => $auth_id, 'success' => '1');
			}
		}
	} else if ($user_type == 'Apple' || $user_type == 'apple') {
		// register with Apple
		$sql = "SELECT * FROM tbl_users WHERE `auth_id`='$auth_id' AND `user_type`='Apple'";
		$res = mysqli_query($mysqli, $sql);
		$num_rows = mysqli_num_rows($res);
		$row = mysqli_fetch_assoc($res);

		if ($num_rows == 0) {
			// data is not available
			$data = array(
				'user_type' => 'Apple',
				'name'  => addslashes(trim($get_method['name'])),
				'user_image'  =>  addslashes(trim($get_method['user_image'])),
				'email'  =>  addslashes(trim($get_method['email'])),
				'phone'  =>  addslashes(trim($get_method['phone'])),
				'auth_id'  =>  addslashes(trim($get_method['auth_id'])),
				'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')),
				'status'  =>  '1'
			);

			$qry = Insert('tbl_users', $data);
			$user_id = mysqli_insert_id($mysqli);

			$user_image = $get_method['user_image'];

			if ($user_image == NULL) {
				$user_image = $file_path . 'images/add-image.png';
			} else {
				$user_image = $get_method['user_image'];
			}

			$set['AUDIO_BOOK'][] = array('user_id' => strval($user_id), 'name' => $get_method['name'], 'user_image' => $user_image, 'email' => $get_method['email'], 'success' => '1', 'msg' => 'Register successflly...!', 'auth_id' => $auth_id);
		} else {
			$user_image = $get_method['user_image'];
			$nameee = $get_method['name'];
			$email = addslashes(trim($get_method['email']));
			if (empty($nameee) && empty($email)) {
				$data = array(
					'auth_id'  =>  $auth_id
				);
			} else if (empty($nameee)) {
				$data = array(
					'auth_id'  =>  $auth_id,
					'email' => $email
				);
			} elseif (empty($email)) {
				$data = array(
					'auth_id'  =>  $auth_id,
					'name' => $nameee
				);
			} else {
				$data = array(
					'auth_id'  =>  $auth_id,
					'name' => $nameee,
					'email' => $email
				);
			}
			$update = Update('tbl_users', $data, "WHERE id = '" . $row['id'] . "'");

			$user_id = $row['id'];

			if ($row['status'] == 0) {
				$set['AUDIO_BOOK'][] = array('MSG' => $app_lang['account_deactive'], 'success' => '0');
			} else if ($row['is_deleted'] == 1) {
				$user_image = $row['user_image'];

				if ($user_image == NULL) {
					$user_image = $file_path . 'images/add-image.png';
				} else {
					$user_image = $row['user_image'];
				}
				// $set['AUDIO_BOOK'][]=array('msg'  => $app_lang['user_deleted'],'success'=>'0');
				$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'user_image' => $user_image, 'email' => $row['email'], 'success' => '1', 'msg' => $app_lang['user_deleted'], 'auth_id' => $auth_id, 'referral_code' => false, 'success' => '0');
			} else {
				$sql = "SELECT * FROM tbl_users WHERE (`auth_id`='$auth_id') AND `user_type`='Apple'";
				$res = mysqli_query($mysqli, $sql);
				$num_rows = mysqli_num_rows($res);
				$row = mysqli_fetch_assoc($res);

				$user_image = $row['user_image'];

				if ($user_image == NULL) {
					$user_image = $file_path . 'images/add-image.png';
				} else {
					$user_image = $row['user_image'];
				}

				$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'user_image' => $user_image, 'email' => $row['email'], 'msg' => $app_lang['login_success'], 'auth_id' => $auth_id, 'success' => '1');
			}
		}
	} else {
		// for normal registration

		$sql = "SELECT * FROM tbl_users WHERE email = '$email'";
		$result = mysqli_query($mysqli, $sql);
		$row = mysqli_fetch_assoc($result);

		if (!filter_var($get_method['email'], FILTER_VALIDATE_EMAIL)) {
			$set['AUDIO_BOOK'][] = array('msg' => $app_lang['invalid_email_format'], 'success' => '0');
		} else if ($row['email'] != "") {
			$set['AUDIO_BOOK'][] = array('msg' => $app_lang['email_exist'], 'success' => '0');
		} else {
			if (isset($_FILES['user_image']['name']) && !empty($_FILES['user_image']['name'])) {
				$ext = pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION);
				$user_image = rand(0, 99999) . "_userimage." . $ext;
				//Main Image
				$tpath1 = 'images/' . $user_image;

				if ($ext != 'png') {
					$pic1 = compress_image($_FILES["user_image"]["tmp_name"], $tpath1, 80);
				} else {
					$tmp = $_FILES['user_image']['tmp_name'];
					move_uploaded_file($tmp, $tpath1);
				}

				//Thumb Image 
				$thumbpath = 'images/thumbs/' . $user_image;
				$thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '300', '300');

				$data = array(
					'user_type' => 'Normal',
					'user_image'  => $user_image,
					'name'  => addslashes(trim($get_method['name'])),
					'email'  =>  addslashes(trim($get_method['email'])),
					'password'  =>  password_hash($get_method['password'], PASSWORD_ARGON2I),
					'phone'  =>  addslashes(trim($get_method['phone'])),
					'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')),
					'status'  =>  '1'
				);
			} else {
				$data = array(
					'user_type' => 'Normal',
					'name'  => addslashes(trim($get_method['name'])),
					'email'  =>  addslashes(trim($get_method['email'])),
					'password'  =>  password_hash($get_method['password'], PASSWORD_ARGON2I),
					'phone'  =>  addslashes(trim($get_method['phone'])),
					'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')),
					'status'  =>  '1'
				);
			}

			$qry = Insert('tbl_users', $data);

			$message = '<div style="background-color: #eee;" align="center"><br />
							  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
							    <tbody>
							      <tr>
							        <td colspan="2" bgcolor="#FFFFFF" align="center" ><img src="' . $file_path . '/assets/img/' . APP_LOGO . '" alt="logo" style="width:100px;height:auto"/></td>
							      </tr>
							      <br>
							      <br>
							      <tr>
							        <td colspan="2" bgcolor="#FFFFFF" align="center" style="padding-top:25px;">
							          <img src="' . $file_path . 'assets/img/thankyoudribble.gif" alt="header" auto-height="100" width="50%"/>
							        </td>
							      </tr>
							      <tr>
							        <td width="600" valign="top" bgcolor="#FFFFFF">
							          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
							            <tbody>
							              <tr>
							                <td valign="top">
							                  <table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
							                    <tbody>
							                      <tr>
							                        <td>
							                        	<p style="color: #717171; font-size: 24px; margin-top:0px; margin:0 auto; text-align:center;"><strong>' . $app_lang['welcome_lbl'] . ', ' . addslashes(trim($get_method['name'])) . '</strong></p>
							                          	<br>
							                          	<p style="color:#15791c; font-size:18px; line-height:32px;font-weight:500;margin-bottom:30px; margin:0 auto; text-align:center;">' . $app_lang['normal_register_msg'] . '<br /></p>
							                          	<br/>
							                          	<p style="color:#999; font-size:17px; line-height:32px;font-weight:500;">' . $app_lang['thank_you_lbl'] . ' ' . APP_NAME . '</p>
							                            </td>
							                      </tr>
							                    </tbody>
							                  </table>
							                </td>
							              </tr>
							            </tbody>
							          </table>
							        </td>
							      </tr>
							      <tr>
							        <td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">' . $app_lang['email_copyright'] . ' ' . APP_NAME . '.</td>
							      </tr>
							    </tbody>
							  </table>
							</div>';

			$set['AUDIO_BOOK'][] = array('msg' => $app_lang['register_success'], 'success' => '1');
		}
	}

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: <vocsytech.com>' . "\r\n";

        mail($email, $app_lang['welcome_lbl'], $message, $headers);

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "user_login") {
	$email = htmlentities(trim($get_method['email']));
    $password = trim($get_method['password']);

	$auth_id = htmlentities(trim($get_method['auth_id']));

	$user_type = htmlentities(trim($get_method['type']));

	if ($user_type == 'normal' or $user_type == 'Normal') {

		// simple login
	$stmt = $mysqli->prepare("SELECT * FROM tbl_users WHERE email = ? AND (`user_type`='Normal' OR `user_type`='normal') AND `id` <> 0");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        $stmt->close();

		if ($num_rows > 0) {
			$row = mysqli_fetch_assoc($result);
			if ($row['status'] == 1) {
				if ($row['is_deleted'] == 0) {
				    $stored_hashed_password = $row['password'];
                    if (password_verify($password, $stored_hashed_password)) {

						$user_id = $row['id'];

						$sql_activity_log = "SELECT * FROM tbl_active_log WHERE `user_id`='$user_id'";
						$res_activity_log = mysqli_query($mysqli, $sql_activity_log);

						if (mysqli_num_rows($res_activity_log) == 0) {
							// insert active log

							$data_log = array(
								'user_id'  =>  $user_id,
								'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
							);

							$qry = Insert('tbl_active_log', $data_log);
						} else {
							// update active log
							$data_log = array(
								'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
							);

							$update = Update('tbl_active_log', $data_log, "WHERE user_id = '$user_id'");
						}

						mysqli_free_result($res_activity_log);

						if ($row['user_image'] == NULL) {
							$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'user_image' => $file_path . 'images/add-image.png', 'email' => $row['email'], 'msg' => $app_lang['login_success'], 'auth_id' => '', 'success' => '1');
						} else {
							$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'user_image' => $file_path . 'images/' . $row['user_image'], 'email' => $row['email'], 'msg' => $app_lang['login_success'], 'auth_id' => '', 'success' => '1');
						}
					} else {
						// invalid password
						$set['AUDIO_BOOK'][] = array('user_id' => '', 'name' => '', 'user_image' => '', 'email' => '', 'msg' => $app_lang['invalid_password'], 'auth_id' => '', 'success' => '0');
					}
				} else {
					// account is deleted
					if ($row['user_image'] == NULL) {
						$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'user_image' => $file_path . 'images/add-image.png', 'email' => $row['email'], 'msg' => $app_lang['user_deleted'], 'auth_id' => '', 'success' => '0');
					} else {
						$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'user_image' => $file_path . 'images/' . $row['user_image'], 'email' => $row['email'], 'msg' => $app_lang['user_deleted'], 'auth_id' => '', 'success' => '0');
					}
				}
			} else {
				// account is deactivated
				$set['AUDIO_BOOK'][] = array('user_id' => '', 'name' => '', 'user_image' => '', 'email' => '', 'msg' => $app_lang['account_deactive'], 'auth_id' => '', 'success' => '0');
			}
		} else {
			// email not found
			$set['AUDIO_BOOK'][] = array('user_id' => '', 'name' => '', 'user_image' => '', 'email' => '', 'msg' => $app_lang['email_not_found'], 'auth_id' => '', 'success' => '0');
		}
	} else if ($user_type == 'google' or $user_type == 'Google') {

		// login with google

		$sql = "SELECT * FROM tbl_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND (`user_type`='Google' OR `user_type`='google')";

		$res = mysqli_query($mysqli, $sql);

		if (mysqli_num_rows($res) > 0) {
			$row = mysqli_fetch_assoc($res);

			if ($row['status'] == 0) {
				$set['AUDIO_BOOK'][] = array('user_id' => '', 'name' => '', 'user_image' => '', 'email' => '', 'msg' => $app_lang['account_deactive'], 'auth_id' => '', 'success' => '0');
			} else if ($row['is_deleted'] == 1) {
				// $set['AUDIO_BOOK'][]=array('msg'  => $app_lang['user_deleted'],'success'=>'0');
				$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'email' => $row['email'], 'msg' => $app_lang['user_deleted'], 'auth_id' => $auth_id, 'success' => '0');
			} else {
				$user_id = $row['id'];

				$sql_activity_log = "SELECT * FROM tbl_active_log WHERE `user_id`='$user_id'";
				$res_activity_log = mysqli_query($mysqli, $sql_activity_log);

				if (mysqli_num_rows($res_activity_log) == 0) {
					// insert active log

					$data_log = array(
						'user_id'  =>  $user_id,
						'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
					);

					$qry = Insert('tbl_active_log', $data_log);
				} else {
					// update active log
					$data_log = array(
						'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
					);

					$update = Update('tbl_active_log', $data_log, "WHERE user_id = '$user_id'");
				}

				mysqli_free_result($res_activity_log);

				$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'email' => $row['email'], 'msg' => $app_lang['login_success'], 'auth_id' => $auth_id, 'success' => '1');

				$data = array(
					'auth_id'  =>  $auth_id
				);

				$updatePlayerID = Update('tbl_users', $data, "WHERE `id` = '" . $row['id'] . "'");
			}
		} else {
			$set['AUDIO_BOOK'][] = array('user_id' => '', 'name' => '', 'user_image' => '', 'email' => '', 'msg' => $app_lang['email_not_found'], 'auth_id' => '', 'success' => '0');
		}
	} else if ($user_type == 'facebook' or $user_type == 'Facebook') {

		// login with google

		$sql = "SELECT * FROM tbl_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND (`user_type`='Facebook' OR `user_type`='facebook')";

		$res = mysqli_query($mysqli, $sql);

		if (mysqli_num_rows($res) > 0) {
			$row = mysqli_fetch_assoc($res);

			if ($row['status'] == 0) {
				$set['AUDIO_BOOK'][] = array('user_id' => '', 'name' => '', 'user_image' => '', 'email' => '', 'msg' => $app_lang['account_deactive'], 'auth_id' => '', 'success' => '0');
			} else if ($row['is_deleted'] == 1) {
				// $set['AUDIO_BOOK'][]=array('msg'  => $app_lang['user_deleted'],'success'=>'0');
				$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'email' => $row['email'], 'msg' => $app_lang['user_deleted'], 'auth_id' => $auth_id, 'success' => '0');
			} else {

				$user_id = $row['id'];

				$sql_activity_log = "SELECT * FROM tbl_active_log WHERE `user_id`='$user_id'";
				$res_activity_log = mysqli_query($mysqli, $sql_activity_log);

				if (mysqli_num_rows($res_activity_log) == 0) {
					// insert active log

					$data_log = array(
						'user_id'  =>  $user_id,
						'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
					);

					$qry = Insert('tbl_active_log', $data_log);
				} else {
					// update active log
					$data_log = array(
						'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
					);

					$update = Update('tbl_active_log', $data_log, "WHERE user_id = '$user_id'");
				}

				mysqli_free_result($res_activity_log);

				$set['AUDIO_BOOK'][] = array('user_id' => $row['id'], 'name' => $row['name'], 'email' => $row['email'], 'msg' => $app_lang['login_success'], 'auth_id' => $auth_id, 'success' => '1');

				$data = array(
					'auth_id'  =>  $auth_id
				);

				$updatePlayerID = Update('tbl_users', $data, "WHERE `id` = '" . $row['id'] . "'");
			}
		} else {
			$set['AUDIO_BOOK'][] = array('user_id' => '', 'name' => '', 'user_image' => '', 'email' => '', 'msg' => $app_lang['email_not_found'], 'auth_id' => '', 'success' => '0');
		}
	} else {
		$set['AUDIO_BOOK'][] = array('user_id' => '', 'name' => '', 'user_image' => '', 'email' => '', 'msg' => $app_lang['invalid_user_type'], 'auth_id' => '', 'success' => '0');
	}

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} 
else if ($get_method['method_name'] == "user_profile") {
	$jsonObj = array();

	$user_id = $get_method['user_id'];

	$qry = "SELECT * FROM tbl_users WHERE id = '$user_id'";

	// echo $qry ;

	$result = mysqli_query($mysqli, $qry);

	$row = mysqli_fetch_assoc($result);

	// $user_image = $row['user_image'];

	// if(empty($row['user_image']))
	//  	{
	//         $user_image = $file_path . 'images/add-image.png';
	//  	}
	//    else
	//    {
	//         $user_image =  $file_path.'images/'.$row['user_image'];
	//    }

	$user_type = $row['user_type'];
	if ($user_type == 'Normal' || $user_type == 'normal') {
		if (empty($row['user_image'])) {
			$user_image = $file_path . 'images/add-image.png';
		} else {
			$user_image = $file_path . 'images/' . $row['user_image'];
		}
	} else if ($user_type == 'Google' || $user_type == 'Facebook' || $user_type == 'Apple' || $user_type == 'google' || $user_type == 'facebook' || $user_type == 'apple') {
		if ($row['user_image'] == NULL) {
			$user_image = $file_path . 'images/add-image.png';
		} else {
			$user_image = $row['user_image'];
		}
	}


	$data['success'] = "1";
	$data['user_id'] = $row['id'];
	$data['user_image'] = $user_image;
	$data['name'] = $row['name'];
	$data['email'] = ($row['email'] != '') ? $row['email'] : '';
	$data['phone'] = ($row['phone'] != '') ? $row['phone'] : '';

	array_push($jsonObj, $data);

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE));
	die();
} 
  else if($get_method['method_name']=="user_profile_update")
  {
		$qry = "SELECT * FROM tbl_users WHERE id = '".$get_method['user_id']."'"; 
		$result = mysqli_query($mysqli,$qry);		 
		$row = mysqli_fetch_assoc($result);

		$old_image='';

		if($_FILES['user_image']['name']!="")
        {	
	        $old_image="images/".$row['user_image'];

	        $ext = pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION);

			$user_image=date('dmYhis').'_'.rand(0,99999).".".$ext;

			$tpath1='images/'.$user_image;        
			if($ext!='png'){
				$pic1=compress_image($_FILES["user_image"]["tmp_name"], $tpath1, 80);
			}else{
				move_uploaded_file($_FILES['user_image']['tmp_name'], $tpath1);
			}
			
			if($row['user_type'] == 'Normal' || $row['user_type'] == 'normal')
			{
			    $user_image = $user_image;
			}
			else
			{
			    $user_image = $file_path.'images/'.$user_image;
			}

        }		
        else
        {
        	$user_image=$row['user_image'];
        }

		if($get_method['password']!="")
		{
		    	$u_password= password_hash($get_method['password'], PASSWORD_ARGON2I);
			$user_edit= "UPDATE tbl_users SET name='".$get_method['name']."',email='".$get_method['email']."',password='".$u_password."',phone='".$get_method['phone']."',user_image='".$user_image."' WHERE id = '".$get_method['user_id']."'";	 
		}
		else
		{
			$user_edit= "UPDATE tbl_users SET name='".$get_method['name']."',email='".$get_method['email']."',phone='".$get_method['phone']."',user_image='".$user_image."' WHERE id = '".$get_method['user_id']."'";	 
		}
   		
   		$user_res = mysqli_query($mysqli,$user_edit) or die(mysqli_error($mysqli));	

   		if($user_res){
   			if($old_image!=''){
   				unlink($old_image);
   			}
   		}

	  				 
		$set['AUDIO_BOOK'][]=array('msg'=>$app_lang['update_success'],'success'=>'1');

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}
  else if($get_method['method_name']=="forgot_pass")
  {
	
		$email=htmlentities(trim($get_method['user_email']));
	 	 
		$qry = "SELECT * FROM tbl_users WHERE `email` = '$email' AND `user_type`='Normal'"; 
		$result = mysqli_query($mysqli,$qry);
		$row = mysqli_fetch_assoc($result);

		if($result->num_rows > 0)
		{

			$password=generateRandomPassword(7);

			$new_password= password_hash($password, PASSWORD_ARGON2I);
 
			$to = $row['email'];
			$recipient_name=$row['name'];
			// subject
			$subject = '[IMPORTANT] '.APP_NAME.' Forgot Password Information';
 			
			$message='<div style="background-color: #f9f9f9;" align="center"><br />
					  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
					    <tbody>
					      <tr>
					        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path.'images/'.APP_LOGO.'" alt="header" style="width:100px;height:auto"/></td>
					      </tr>
					      <tr>
					        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
					          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
					            <tbody>
					              <tr>
					                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
					                    <tbody>
					                      <tr>
					                        <td>
					                        	<p style="color: #262626; font-size: 24px; margin-top:0px;"><strong>'.$app_lang['dear_lbl'].' '.$row['name'].'</strong></p>
					                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;margin-top:5px;"><br>'.$app_lang['your_password_lbl'].': <span style="font-weight:400;">'.$password.'</span></p>
					                          <p style="color:#262626; font-size:17px; line-height:32px;font-weight:500;margin-bottom:30px;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>

					                        </td>
					                      </tr>
					                    </tbody>
					                  </table></td>
					              </tr>
					               
					            </tbody>
					          </table></td>
					      </tr>
					      <tr>
					        <td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
					      </tr>
					    </tbody>
					  </table>
					</div>';
 
			$headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	
			//	$msg = 'Yourpassword is '.$new_password;
            $headers .= 'From: <info@vocsyinfotech.website>' . "\r\n";
            $headers .= 'Cc: info@vocsyinfotech.website' . "\r\n";
        	//	if(send_email($to,$recipient_name,$subject,$message))
			if(mail($to,$subject,$message,$headers))
			{
				$sql="UPDATE tbl_users SET `password`='$new_password' WHERE `id`='".$row['id']."'";
      			mysqli_query($mysqli,$sql);

      			$set['AUDIO_BOOK'][]=array('msg' => $app_lang['password_sent_mail'],'success'=>'1');
			}
			else{

				$set['AUDIO_BOOK'][]=array('msg' => $app_lang['email_not_found'],'success'=>'0');
			}
		}
		else
		{  	 	
			$set['AUDIO_BOOK'][]=array('msg' => $app_lang['email_not_found'],'success'=>'0');		
		}
	
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}	
	
 else if ($get_method['method_name'] == "update_pass") 
{
    $user_id = htmlentities(trim($get_method['user_id']));
    $current_password = htmlentities(trim($get_method['current_password']));
    $new_password = htmlentities(trim($get_method['new_password']));
    $confirm_password = htmlentities(trim($get_method['confirm_password']));

    if($user_id != "")
    {
        // Use prepared statements to prevent SQL injection
        $stmt = $mysqli->prepare("SELECT password FROM tbl_users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    


        if ($row) 
        {
            // Check if the current password matches the stored hash
            if (password_verify($current_password, $row['password'])) 
            { 
                if ($new_password === $confirm_password) 
                {
                    // Hash the new password before storing it
                    $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

                    // Update the password in the database
                    $stmt = $mysqli->prepare("UPDATE tbl_users SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $new_password_hash, $user_id);
                    $stmt->execute();
            
                    if ($stmt->affected_rows > 0) 
                    {
                        $set['AUDIO_BOOK'][] = array('msg' => 'Password updated successfully!', 'success' => '1');
                    } 
                    else 
                    {
                        $set['AUDIO_BOOK'][] = array('msg' => 'Failed to update password!', 'success' => '0');
                    }
                } 
                else 
                {
                    $set['AUDIO_BOOK'][] = array('msg' => 'New password and confirmation do not match!', 'success' => '0');
                }
            } 
            else 
            {
                $set['AUDIO_BOOK'][] = array('msg' => 'Current password does not match!', 'success' => '0');
            }
        } 
        else 
        {
            $set['AUDIO_BOOK'][] = array('msg' => 'User ID not found!', 'success' => '0');
        }

        $stmt->close();
    } 
    else 
    {
        $set['AUDIO_BOOK'][] = array('msg' => 'User ID is empty!', 'success' => '0');
    }
    	header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
}

else if ($get_method['method_name'] == "favourite_post") {
	$jsonObj = array();

	$post_id = $get_method['post_id'];
	$type = $get_method['type'];
	$user_id = $get_method['user_id'];

	$sql = "SELECT * FROM tbl_favourite WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `type`='$type'";
	$res = mysqli_query($mysqli, $sql);

	if (mysqli_num_rows($res) == 0) {

		$data = array(
			'post_id'  =>  $post_id,
			'user_id'  =>  $user_id,
			'type'  =>  $type,
			'created_at'  =>  strtotime(date('d-m-Y h:i:s A')),
		);

		$qry = Insert('tbl_favourite', $data);

		$set['AUDIO_BOOK'][] = array('msg' => $app_lang['favourite_success'], 'success' => '1');
	} else {
		$deleteSql = "DELETE FROM tbl_favourite WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `type`='$type'";

		if (mysqli_query($mysqli, $deleteSql)) {
			$set['AUDIO_BOOK'][] = array('msg' => $app_lang['favourite_remove_success'], 'success' => '0');
		} else {
			$set['AUDIO_BOOK'][] = array('msg' => $app_lang['favourite_remove_error'], 'success' => '1');
		}
	}

	header('Content-Type: application/json; charset=utf-8');
	$json = json_encode($set);
	echo $json;
	exit;
} else if ($get_method['method_name'] == "get_favourite_post") {
	$jsonObj = array();

	$type = trim($get_method['type']);
	$user_id = trim($get_method['user_id']);

	$page_limit = 10;

	switch ($type) {
		case 'fav_post': {
				$query_rec = "SELECT COUNT(*) as num FROM tbl_album
								LEFT JOIN tbl_favourite ON tbl_album.`aid`= tbl_favourite.`post_id`
								WHERE tbl_album.`status`='1' AND tbl_favourite.`user_id`='$user_id' AND tbl_favourite.`type`='$type' ORDER BY tbl_favourite.`id` DESC";

				$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

				$limit = ($get_method['page'] - 1) * $page_limit;

				$jsonObj = array();

				$query = "SELECT tbl_album.* FROM tbl_album
							LEFT JOIN tbl_favourite ON tbl_album.`aid`= tbl_favourite.`post_id`
							WHERE tbl_album.`status`='1' AND tbl_favourite.`user_id`='$user_id' AND tbl_favourite.`type`='$type' ORDER BY tbl_favourite.`id` DESC LIMIT $limit, $page_limit";

				$sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

				while ($data = mysqli_fetch_assoc($sql)) {

					$row['total_records'] = $total_pages['num'];
					$row['aid'] = $data['aid'];
					$row['author_ids'] = $data['artist_ids'];
					$row['cat_ids'] = $data['cat_ids'];
					$row['book_subscription_type'] = $data['book_subscription_type'];
					$row['book_name'] =  html_entity_decode($data['album_name'], ENT_QUOTES, "UTF-8");

					$album_image = $data['album_image'];
					if (empty($album_image)) {
						$row['book_image'] = $file_path . 'images/add-image.png';
						$row['book_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
					} else {
						$row['book_image'] = $file_path . 'images/' . $data['album_image'];
						$row['book_image_thumb'] = $file_path . 'images/thumbs/' . $data['album_image'];
					}
					$row['play_time'] = $data['play_time'];
					$row['book_description'] = html_entity_decode($data['book_description'], ENT_QUOTES, "UTF-8");

					if ($data['book_type'] == 'local') {
						$book_file = $file_path . 'uploads/' . basename($data['book_url']);
					} else if ($data['book_type'] == 'server_url') {
						$book_file = $data['book_url'];
					}

					$row['book_url'] = $book_file;

					$row['total_rate'] = $data['total_rate'];
					$row['rate_avg'] = $data['rate_avg'];
					$row['total_views'] = $data['total_views'];
					$row['total_download'] = $data['total_download'];

					$row['is_favourite'] = is_favourite($data['aid'], $user_id);

					$artist_ids = trim($data['artist_ids']);

					$query01 = "SELECT * FROM tbl_artist WHERE tbl_artist.`id` IN ($artist_ids) ";

					$sql01 = mysqli_query($mysqli, $query01);

					$row['total_author'] = mysqli_num_rows($sql01);

					$cat_ids = trim($data['cat_ids']);

					$query012 = "SELECT * FROM tbl_category WHERE tbl_category.`cid` IN ($cat_ids) ";

					$sql012 = mysqli_query($mysqli, $query012);

					$row['total_category'] = mysqli_num_rows($sql012);



					if (mysqli_num_rows($sql01) > 0) {

						while ($data0123 = mysqli_fetch_assoc($sql01)) {
							$total_songs++;
							$row0123['id'] = $data0123['id'];
							$row0123['author_name'] = html_entity_decode($data0123['artist_name'], ENT_QUOTES, "UTF-8");

							$artist_image = $data0123['artist_image'];
							if (empty($artist_image)) {
								$row0123['Author_image'] = $file_path . 'images/add-image.png';
								$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
							} else {
								$row0123['Author_image'] = $file_path . 'images/' . $data0123['artist_image'];
								$row0123['Author_image_thumb'] = $file_path . 'images/thumbs/' . $data0123['artist_image'];
							}

							//	$row0123['author_image'] = $data0123['artist_image'];
							$row['author_list'][] = $row0123;
						}
					} else {
						$row['author_list'] = array();
					}

					//	array_push($jsonObj_123, $row123);


					if (mysqli_num_rows($sql012) > 0) {

						while ($data012345 = mysqli_fetch_assoc($sql012)) {
							$total_songs++;
							$row012345['cid'] = $data012345['cid'];
							$row012345['category_name'] = html_entity_decode($data012345['category_name'], ENT_QUOTES, "UTF-8");
							$row['cat_list'][] = $row012345;
						}
					} else {
						$row['cat_list'] = array();
					}

					array_push($jsonObj, $row);

					unset($row['cat_list']);
					unset($row['author_list']);

					//	array_push($jsonObj, $row);
				}
			}
			break;

		default: {
			}
			break;
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	$json = json_encode($set);
	echo $json;
	exit;
} else if ($get_method['method_name'] == "get_recent_songs") {
	$jsonObj = array();

	$ids = trim($get_method['songs_ids']);

	$user_id = trim($get_method['user_id']);

	$page_limit = 10;

	$query_rec = "SELECT COUNT(*) as num FROM tbl_mp3
				LEFT JOIN tbl_category ON tbl_mp3.`cat_id`=tbl_category.`cid` 
				WHERE tbl_mp3.`status`='1' AND tbl_category.`status`='1' AND tbl_mp3.`id` IN ($ids) ORDER BY tbl_mp3.`id` DESC";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

	$limit = ($get_method['page'] - 1) * $page_limit;

	$jsonObj = array();

	$query = "SELECT * FROM tbl_mp3
				LEFT JOIN tbl_category ON tbl_mp3.`cat_id`= tbl_category.`cid` 
				WHERE tbl_mp3.`status`='1' AND tbl_category.`status`='1' AND tbl_mp3.`id` IN ($ids) ORDER BY tbl_mp3.`id` DESC LIMIT $limit, $page_limit";

	$sql = mysqli_query($mysqli, $query) or die(mysqli_error());

	while ($data = mysqli_fetch_assoc($sql)) {

		$row['total_songs'] = $total_pages['num'];
		$row['id'] = $data['id'];
		$row['cat_id'] = $data['cat_id'];
		$row['mp3_type'] = $data['mp3_type'];
		$row['mp3_title'] = $data['mp3_title'];

		if ($data['mp3_type'] == 'local') {
			$mp3_file = $file_path . 'uploads/' . basename($data['mp3_url']);
		} else if ($data['mp3_type'] == 'server_url') {
			$mp3_file = $data['mp3_url'];
		}

		$row['mp3_url'] = $mp3_file;

		$mp3_thumbnail = $data['mp3_thumbnail'];
		if (empty($mp3_thumbnail)) {
			$row['mp3_thumbnail_b'] = $file_path . 'images/add-image.png';
			$row['mp3_thumbnail_s'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['mp3_thumbnail_b'] = $file_path . 'images/' . $data['mp3_thumbnail'];
			$row['mp3_thumbnail_s'] = $file_path . 'images/thumbs/' . $data['mp3_thumbnail'];
		}

		$row['mp3_artist'] = $data['mp3_artist'];
		$row['mp3_description'] = html_entity_decode($data['mp3_description'], ENT_QUOTES, "UTF-8");
		$row['total_rate'] = $data['total_rate'];
		$row['rate_avg'] = $data['rate_avg'];
		$row['total_views'] = $data['total_views'];
		$row['total_download'] = $data['total_download'];

		$row['is_favourite'] = is_favourite($data['id'], $user_id);

		$row['cid'] = $data['cid'];
		$row['category_name'] = html_entity_decode($data['category_name'], ENT_QUOTES, "UTF-8");

		$category_image = $data['category_image'];
		if (empty($category_image)) {
			$row['category_image'] = $file_path . 'images/add-image.png';
			$row['category_image_thumb'] = $file_path . 'images/thumbs/add-image.png';
		} else {
			$row['category_image'] = $file_path . 'images/' . $data['category_image'];
			$row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];
		}

		array_push($jsonObj, $row);
	}

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	$json = json_encode($set);
	echo $json;
	exit;
} else if ($get_method['method_name'] == "app_details") {
	$jsonObj = array();

	$query = "SELECT * FROM tbl_settings WHERE id='1'";
	$sql = mysqli_query($mysqli, $query);

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['package_name'] = $data['package_name'];
		$row['app_name'] = $data['app_name'];
		$row['app_logo'] = $data['app_logo'];
		$row['app_version'] = $data['app_version'];
		$row['app_author'] = $data['app_author'];
		$row['app_contact'] = $data['app_contact'];
		$row['app_email'] = $data['app_email'];
		$row['app_website'] = $data['app_website'];
		$row['app_description'] = stripslashes($data['app_description']);
		$row['app_developed_by'] = $data['app_developed_by'];

		$row['app_privacy_policy'] = stripslashes($data['app_privacy_policy']);

		$row['publisher_id'] = $data['publisher_id'];
		$row['interstital_ad'] = $data['interstital_ad'];
		$row['interstital_ad_id'] = $data['interstital_ad_id'];
		$row['interstital_ad_click'] = $data['interstital_ad_click'];
		$row['banner_ad'] = $data['banner_ad'];
		$row['banner_ad_id'] = $data['banner_ad_id'];
		$row['app_open_ad_id'] = $data['app_open_ad_id'];

		$row['ios_interstital_ad'] = $data['ios_interstital_ad'];
		$row['ios_interstital_ad_id'] = $data['ios_interstital_ad_id'];
		$row['ios_interstital_ad_click'] = $data['ios_interstital_ad_click'];
		$row['ios_banner_ad'] = $data['ios_banner_ad'];
		$row['ios_banner_ad_id'] = $data['ios_banner_ad_id'];
		$row['ios_app_open_ad_id'] = $data['ios_app_open_ad_id'];
		
		$row['banner_ad_id_status'] = $data['banner_ad_id_status'];
		$row['interstital_ad_id_status'] = $data['interstital_ad_id_status'];
		$row['app_open_ad_id_status'] = $data['app_open_ad_id_status'];
		$row['ios_banner_ad_id_status'] = $data['ios_banner_ad_id_status'];
		$row['ios_interstital_ad_id_status'] = $data['ios_interstital_ad_id_status'];
		$row['ios_app_open_ad_id_status'] = $data['ios_app_open_ad_id_status'];

		$row['song_download'] = $data['song_download'];


		array_push($jsonObj, $row);
	}

	// while ($data = mysqli_fetch_assoc($sql)) {

	// 	$row['app_name'] =  html_entity_decode($data['app_name'], ENT_QUOTES, "UTF-8");
	// 	$row['app_logo'] = $data['app_logo'];
	// 	$row['app_version'] = $data['app_version'];
	// 	$row['app_author'] = $data['app_author'];
	// 	$row['app_contact'] = $data['app_contact'];
	// 	$row['app_email'] = $data['app_email'];
	// 	$row['app_website'] = $data['app_website'];
	// 	$row['app_description'] = stripslashes($data['app_description']);
	// 	$row['app_developed_by'] = $data['app_developed_by'];

	// 	$row['app_privacy_policy'] = stripslashes($data['app_privacy_policy']);

	// 	$row['package_name'] = $data['package_name'];
	// 	$row['publisher_id'] = $data['publisher_id'];

	// 	$row['interstital_ad'] = $data['interstital_ad'];
	// 	$row['interstital_ad_type'] = $data['interstital_ad_type'];

	// 	$row['interstital_ad_id'] = ($data['interstital_ad_type'] == 'facebook') ? $data['interstital_facebook_id'] : $data['interstital_ad_id'];

	// 	$row['interstital_ad_click'] = $data['interstital_ad_click'];

	// 	$row['banner_ad'] = $data['banner_ad'];
	// 	$row['banner_ad_type'] = $data['banner_ad_type'];

	// 	$row['banner_ad_id'] = ($data['banner_ad_type'] == 'facebook') ? $data['banner_facebook_id'] : $data['banner_ad_id'];

	// 	$row['native_ad'] = $data['native_ad'];
	// 	$row['native_ad_type'] = $data['native_ad_type'];

	// 	$row['native_ad_id'] = ($data['native_ad_type'] == 'facebook') ? $data['native_facebook_id'] : $data['native_ad_id'];

	// 	$row['native_position'] = $data['native_position'];

	// 	$row['song_download'] = $data['song_download'];

	// 	$row['app_update_status'] = $data['app_update_status'];
	//           $row['app_new_version'] = $data['app_new_version'];
	//           $row['app_update_desc'] = stripslashes($data['app_update_desc']);
	//           $row['app_redirect_url'] = $data['app_redirect_url'];
	//           $row['cancel_update_status'] = $data['cancel_update_status'];

	// 	array_push($jsonObj, $row);
	// }

	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "get_active_subscription_plan") {

	$jsonObj = array();

	$query = "SELECT * FROM tbl_subscription WHERE tbl_subscription.`status`='1'";
	$sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['id'] = $data['id'];
		$row['sku_id'] = $data['sku_id'];
		$row['plan_name'] = $data['plan_name'];
		$row['plan_duration'] = $data[plan_duration];
		$row['plan_type'] = $data['plan_type'];
		$row['plan_price'] = $data['plan_price'];
		$row['plan_description'] = $data['plan_description'];
		$row['status'] = $data['status'];

		array_push($jsonObj, $row);
	}
	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "buy_subscription") {
	$user_id = $get_method['user_id'];
	$transaction_id = $get_method['transaction_id'];

	$user_qry = "SELECT * FROM tbl_users where id='" . $user_id . "' AND status='1'";
	$user_result = mysqli_query($mysqli, $user_qry);
	$user_row = mysqli_fetch_assoc($user_result);

	$user_count = mysqli_num_rows($user_result);


	$plan_id = $get_method['plan_id'];

	$plan_qry = "SELECT * FROM tbl_subscription where id='" . $plan_id . "' AND status='1'";
	$plan_result = mysqli_query($mysqli, $plan_qry);
	$plan_row = mysqli_fetch_assoc($plan_result);

	$plan_count = mysqli_num_rows($plan_result);

	$transaction_qry = "SELECT * FROM tbl_transaction where user_id='" . $user_id . "'";
	$transaction_result = mysqli_query($mysqli, $transaction_qry);
	$transaction_row = mysqli_fetch_assoc($transaction_result);

	$transaction_count = mysqli_num_rows($transaction_result);


	if (empty($user_id)) {
		$set['AUDIO_BOOK'][] = array('msg' => 'Please enter user id', 'success' => '0');
	} else if (empty($plan_id)) {
		$set['AUDIO_BOOK'][] = array('msg' => 'Please enter plan id text', 'success' => '0');
	} else if ($user_count == '0') {
		$set['AUDIO_BOOK'][] = array('msg' => 'Please enter valid user id', 'success' => '0');
	} else if ($plan_count == '0') {
		$set['AUDIO_BOOK'][] = array('msg' => 'Please enter valid plan id', 'success' => '0');
	} else if ($transaction_count == '1' || $transaction_count >= '1') {

		$date =  $transaction_row['plan_expiry_date'];

		$duration = $plan_row['plan_duration'];
		$type = $plan_row['plan_type'];

		$add_time = $duration . ' ' . $type;

		$new_expiry_date = date('Y-m-d', strtotime("+$add_time $date"));

		$view_qry = mysqli_query($mysqli, "UPDATE tbl_transaction SET plan_name = '" . $plan_row['plan_name'] . "',transaction_id = '" . $transaction_id . "' , plan_amount = '" . $plan_row['plan_price'] . "' , plan_duration = '" . $plan_row['plan_duration'] . "' , plan_type = '" . $plan_row['plan_type'] . "' , plan_expiry_date = '" . $new_expiry_date . "' WHERE user_id = '" . $user_id . "'");

		$data = array(
			'user_id'  => $user_id,
			'transaction_id' => $transaction_id,
			'user_name'  => $user_row['name'],
			'user_email'  =>  $user_row['email'],
			'plan_id'  =>  $plan_id,
			'plan_name'  =>  $plan_row['plan_name'],
			'plan_amount'  =>  $plan_row['plan_price'],
			'plan_duration'  =>  $plan_row['plan_duration'],
			'plan_type'  =>  $plan_row['plan_type'],
			'payment_date'  =>  date('Y-m-d'),
		);

		$qry = Insert('tbl_transaction_all', $data);




		$set['AUDIO_BOOK'][] = array('msg' => 'Plan update Sucess', 'success' => '1');
	} else {
		$date =  date('Y-m-d');

		$duration = $plan_row['plan_duration'];
		$type = $plan_row['plan_type'];

		$add_time = $duration . ' ' . $type;

		$expiry_date = date('Y-m-d', strtotime("+$add_time $date"));


		$data = array(
			'user_id'  => $user_id,
			'transaction_id' => $transaction_id,
			'user_name'  => $user_row['name'],
			'user_email'  =>  $user_row['email'],
			'plan_id'  =>  $plan_id,
			'plan_name'  =>  $plan_row['plan_name'],
			'plan_amount'  =>  $plan_row['plan_price'],
			'plan_duration'  =>  $plan_row['plan_duration'],
			'plan_type'  =>  $plan_row['plan_type'],
			'payment_date'  =>  $date,
			'plan_expiry_date'  =>  $expiry_date,
		);

		$qry = Insert('tbl_transaction', $data);

		$data_all = array(
			'user_id'  => $user_id,
			'transaction_id' => $transaction_id,
			'user_name'  => $user_row['name'],
			'user_email'  =>  $user_row['email'],
			'plan_id'  =>  $plan_id,
			'plan_name'  =>  $plan_row['plan_name'],
			'plan_amount'  =>  $plan_row['plan_price'],
			'plan_duration'  =>  $plan_row['plan_duration'],
			'plan_type'  =>  $plan_row['plan_type'],
			'payment_date'  =>  date('Y-m-d'),
		);

		$qry_all = Insert('tbl_transaction_all', $data_all);

		$set['AUDIO_BOOK'][] = array('msg' => 'Transaction Sucess', 'success' => '1');
	}

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "user_subscription_status") {
	$user_id = $get_method['user_id'];

	$user_qry = "SELECT * FROM tbl_users where id='" . $user_id . "' AND status='1'";
	$user_result = mysqli_query($mysqli, $user_qry);
	$user_row = mysqli_fetch_assoc($user_result);

	$user_count = mysqli_num_rows($user_result);

	$transaction_qry = "SELECT * FROM tbl_transaction where user_id='" . $user_id . "'";
	$transaction_result = mysqli_query($mysqli, $transaction_qry);
	$transaction_row = mysqli_fetch_assoc($transaction_result);

	$transaction_count = mysqli_num_rows($transaction_result);

	$date = date('Y-m-d');
	$e_date = $transaction_row['plan_expiry_date'];

	$coins_qry = "SELECT * FROM tbl_settings where id='1'";
	$coins_result = mysqli_query($mysqli, $coins_qry);
	$coins_row = mysqli_fetch_assoc($coins_result);
	$book_coins = $coins_row['book_coins'];


	if (empty($user_id)) {
		$set['AUDIO_BOOK'][] = array('msg' => 'Please enter user id', 'status'  => 'deactive', 'book_coins' => $book_coins, 'success' => '0');
	} else if ($user_count == '0') {
		$set['AUDIO_BOOK'][] = array('msg' => 'Please enter valid user id', 'status'  => 'deactive', 'book_coins' => $book_coins, 'success' => '0');
	} else if ($transaction_count == '0') {
		$set['AUDIO_BOOK'][] = array('msg' => 'No plan buy by user', 'status'  => 'deactive', 'book_coins' => $book_coins, 'success' => '0');
	} else if ($e_date < $date) {
		$set['AUDIO_BOOK'][] = array('msg' => 'Your Plan is Expired', 'status'  => 'deactive', 'book_coins' => $book_coins, 'success' => '0');
	} else {
		// Creates DateTime objects
		$datetime1 = date_create($date);
		$datetime2 = date_create($e_date);

		// Calculates the difference between DateTime objects
		$interval = date_diff($datetime1, $datetime2);

		// Display the result
		$remain_days = $interval->format('%R%a');

		$set['AUDIO_BOOK'][] = array('msg' => 'Your plan is active', 'status'  => 'active', 'days_remain' => $remain_days, 'book_coins' => $book_coins, 'success' => '1');
	}

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "user_coin_add_update") {
	$user_id = $get_method['user_id'];
	$coins = $get_method['coins'];

	//       $coins_qry="SELECT * FROM tbl_settings where id='1'";
	// $coins_result=mysqli_query($mysqli,$coins_qry);
	// $coins_row=mysqli_fetch_assoc($coins_result);
	// $book_coins = $coins_row['book_coins'];

	$coins_qry = "SELECT * FROM tbl_coins where user_id='" . $user_id . "'";
	$coins_result = mysqli_query($mysqli, $coins_qry);
	$coins_row = mysqli_fetch_assoc($coins_result);

	$user_coins = $coins_row['coins'];

	$coins_count = mysqli_num_rows($coins_result);

	if ($coins_count == 1) {
		if ($get_method['type'] == 'add') {
			$coins = $user_coins + $coins;
		} else if ($get_method['type'] == 'minus') {
			$coins = $user_coins - $coins;
		}

		$data = array(
			'coins'  =>  $coins,
		);

		$user_edit = Update('tbl_coins', $data, "WHERE user_id = '" . $get_method['user_id'] . "'");
		$set['AUDIO_BOOK'][] = array('msg' => 'User Coins Update Sucessfully!', 'success' => '1');
	} else {
		$data = array(
			'user_id'  =>  $user_id,
			'coins'  =>  $coins,
		);

		$qry = Insert('tbl_coins', $data);
		$set['AUDIO_BOOK'][] = array('msg' => 'User Coins Insert Sucessfully!', 'success' => '1');
	}
	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "get_user_coins") {

	$jsonObj = array();

	$query = "SELECT * FROM tbl_coins WHERE tbl_coins.`user_id`='" . $get_method['user_id'] . "'";
	$sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

	while ($data = mysqli_fetch_assoc($sql)) {
		$row['id'] = $data['id'];
		$row['user_id'] = $data['user_id'];
		$row['coins'] = $data['coins'];
		array_push($jsonObj, $row);
	}
	$set['AUDIO_BOOK'] = $jsonObj;

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else if ($get_method['method_name'] == "delete_userdata") {
	$user_id = $get_method['user_id'];
	$sql = "SELECT * FROM tbl_users WHERE id = '$user_id'";
	$res = mysqli_query($mysqli, $sql);

	if (mysqli_num_rows($res) > 0) {
		$data = array(
			'is_deleted'  => 1,
		);

		$updatePlayerID = Update('tbl_users', $data, "WHERE `id` =" . $user_id);
		$set['AUDIO_BOOK'][] = array('MSG' => 'This user Is Deleted Please Contact Admin', 'success' => '1');
	} else {
		$set['AUDIO_BOOK'][] = array('MSG' => "User Not Found", 'success' => '0');
	}

	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
} else {
	$get_method = checkSignSalt($_POST['data']);
	$set['AUDIO_BOOK'][] = array('msg' => 'Access denied OR Data not found', 'success' => '1');
	header('Content-Type: application/json; charset=utf-8');
	echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
