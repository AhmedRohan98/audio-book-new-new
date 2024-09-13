<?php
error_reporting(0);
require("includes/connection.php");
require("includes/function.php");
require("language/language.php");

$response = array();
$_SESSION['class'] = "success";

if (PURCHASE == 'disabled') {
	$response['status'] = 1;
	$_SESSION['class'] = "error";
	$_SESSION['msg'] = "demo_mode";
	echo json_encode($response);
	exit();
} else {
	switch ($_POST['action']) {
		case 'toggle_status':
			$id = $_POST['id'];
			$for_action = $_POST['for_action'];
			$column = $_POST['column'];
			$tbl_id = $_POST['tbl_id'];
			$table_nm = $_POST['table'];
			if ($for_action == 'active') {
				$data = array($column  =>  '1');
				$edit_status = Update($table_nm, $data, "WHERE $tbl_id = '$id'");
				$_SESSION['msg'] = "13";
			} else {
				$data = array($column  =>  '0');
				$edit_status = Update($table_nm, $data, "WHERE $tbl_id = '$id'");
				$_SESSION['msg'] = "14";
			}

			$response['status'] = 1;
			$response['action'] = $for_action;
			echo json_encode($response);
			break;

		case 'admob_add':

			$id = $_POST['id'];
			$for_action = $_POST['for_action'];
			$column = $_POST['column'];
			$tbl_id = $_POST['tbl_id'];
			$table_nm = $_POST['table'];

			if ($for_action == 'deactive') {
				$data = array($column  =>  '1');
				$edit_status = Update($table_nm, $data, "WHERE $tbl_id = '$id'");
			} else {
				$data = array($column  =>  '0');
				$edit_status = Update($table_nm, $data, "WHERE $tbl_id = '$id'");
			}


			$response['status'] = 1;
			$response['action'] = $for_action;
			echo json_encode($response);
			break;

		case 'removeData':
			$id = $_POST['id'];
			$tbl_nm = $_POST['tbl_nm'];
			$tbl_id = $_POST['tbl_id'];

			if ($tbl_nm == 'tbl_users') {
				Delete('tbl_comments', 'user_id=' . $id . '');
				Delete('tbl_song_suggest', 'user_id=' . $id . '');
				Delete('tbl_reports', 'user_id=' . $id . '');
			}

			Delete($tbl_nm, $tbl_id . '=' . $id);

			$_SESSION['msg'] = "12";
			$response['status'] = 1;
			echo json_encode($response);
			break;

		case 'multi_delete':

			$ids = $_POST['id'];
			$tbl_nm = $_POST['tbl_nm'];
			if ($tbl_nm == 'tbl_mp3') {

				$sql = "SELECT * FROM $tbl_nm WHERE `id` IN ($ids)";
				$res = mysqli_query($mysqli, $sql);
				while ($row = mysqli_fetch_assoc($res)) {
					if ($row['mp3_thumbnail'] != "") {
						unlink('images/' . $row['mp3_thumbnail']);
						unlink('images/thumbs/' . $row['mp3_thumbnail']);
					}

					if ($row['mp3_type'] == "local") {
						$file_name = basename($row['mp3_url']);
						unlink('uploads/' . $file_name);
					}

					Delete('tbl_favourite', 'post_id=' . $row['id']);
					Delete('tbl_rating', 'post_id=' . $row['id']);
					Delete('tbl_reports', 'song_id=' . $row['id']);
					Delete('tbl_mp3_views', 'mp3_id=' . $row['id']);
				}
				$deleteSql = "DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

				mysqli_query($mysqli, $deleteSql);
			} else if ($tbl_nm == 'tbl_category') {
				$sqlCategory = "SELECT * FROM $tbl_nm WHERE `cid` IN ($ids)";
				$res = mysqli_query($mysqli, $sqlCategory);
				while ($row = mysqli_fetch_assoc($res)) {
					if ($row['category_image'] != "") {
						unlink('images/' . $row['category_image']);
						unlink('images/thumbs/' . $row['category_image']);
					}
				}
				$deleteSql = "DELETE FROM $tbl_nm WHERE `cid` IN ($ids)";

				mysqli_query($mysqli, $deleteSql);
			} else if ($tbl_nm == 'tbl_album') {
				$sql = "SELECT * FROM tbl_mp3 WHERE `album_id` IN ($ids)";
				$res = mysqli_query($mysqli, $sql);
				while ($row = mysqli_fetch_assoc($res)) {
					if ($row['mp3_thumbnail'] != "") {
						unlink('images/' . $row['mp3_thumbnail']);
						unlink('images/thumbs/' . $row['mp3_thumbnail']);
					}

					if ($row['mp3_type'] == "local") {
						$file_name = basename($row['mp3_url']);
						unlink('uploads/' . $file_name);
					}

					Delete('tbl_rating', 'post_id=' . $row['id']);
					Delete('tbl_favourite', 'post_id=' . $row['id']);
					Delete('tbl_reports', 'song_id=' . $row['id']);
					Delete('tbl_mp3_views', 'mp3_id=' . $row['id']);
				}

				$deleteSql = "DELETE FROM tbl_mp3 WHERE `album_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$sqlCategory = "SELECT * FROM $tbl_nm WHERE `aid` IN ($ids)";
				$res = mysqli_query($mysqli, $sqlCategory);

				while ($row = mysqli_fetch_assoc($res)) {
					if ($row['album_image'] != "") {
						unlink('images/' . $row['album_image']);
						unlink('images/thumbs/' . $row['album_image']);
					}
				}

				$deleteSql = "DELETE FROM $tbl_nm WHERE `aid` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);
			} else if ($tbl_nm == 'tbl_playlist') {

				$sql = "SELECT * FROM $tbl_nm WHERE `pid` IN ($ids)";
				$res = mysqli_query($mysqli, $sql);
				while ($row = mysqli_fetch_assoc($res)) {
					if ($row['playlist_image'] != "") {
						unlink('images/' . $row['playlist_image']);
						unlink('images/thumbs/' . $row['playlist_image']);
					}
				}
				$deleteSql = "DELETE FROM $tbl_nm WHERE `pid` IN ($ids)";

				mysqli_query($mysqli, $deleteSql);
			} else if ($tbl_nm == 'tbl_banner') {

				$sql = "SELECT * FROM $tbl_nm WHERE `bid` IN ($ids)";
				$res = mysqli_query($mysqli, $sql);
				while ($row = mysqli_fetch_assoc($res)) {
					if ($row['banner_image'] != "") {
						unlink('images/' . $row['banner_image']);
						unlink('images/thumbs/' . $row['banner_image']);
					}
				}
				$deleteSql = "DELETE FROM $tbl_nm WHERE `bid` IN ($ids)";

				mysqli_query($mysqli, $deleteSql);
			} else if ($tbl_nm == 'tbl_song_suggest') {

				$sql = "SELECT * FROM $tbl_nm WHERE `id` IN ($ids)";
				$res = mysqli_query($mysqli, $sql);
				while ($row = mysqli_fetch_assoc($res)) {
					if ($row['song_image'] != "") {
						unlink('images/' . $row['song_image']);
						unlink('images/thumbs/' . $row['song_image']);
					}
				}
				$deleteSql = "DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

				mysqli_query($mysqli, $deleteSql);
			} else if ($tbl_nm == 'tbl_artist') {

				$sql = "SELECT * FROM $tbl_nm WHERE `id` IN ($ids)";
				$res = mysqli_query($mysqli, $sql);
				while ($row = mysqli_fetch_assoc($res)) {
					if ($row['artist_image'] != "") {
						unlink('images/' . $row['artist_image']);
						unlink('images/thumbs/' . $row['artist_image']);
					}
				}
				$deleteSql = "DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

				mysqli_query($mysqli, $deleteSql);
			} else if ($tbl_nm == 'tbl_banner') {

				$sql = "SELECT * FROM $tbl_nm WHERE `bid` IN ($ids)";
				$res = mysqli_query($mysqli, $sql);
				while ($row = mysqli_fetch_assoc($res)) {
					if ($row['banner_image'] != "") {
						unlink('images/' . $row['banner_image']);
						unlink('images/thumbs/' . $row['banner_image']);
					}
				}
				$deleteSql = "DELETE FROM $tbl_nm WHERE `bid` IN ($ids)";
			} else if ($tbl_nm == 'tbl_users') {
				$id = $_POST['id'];
				$tbl_nm = $_POST['tbl_nm'];
				$tbl_id = $_POST['tbl_id'];

				if ($tbl_nm == 'tbl_users') {
					Delete('tbl_song_suggest', 'user_id=' . $id . '');
					Delete('tbl_reports', 'user_id=' . $id . '');
				}

				Delete('tbl_users', 'id=' . $id . '');

				$_SESSION['msg'] = "12";
				$response['status'] = 1;
				echo json_encode($response);
				break;
			} else if ($tbl_nm == 'tbl_home_section') {
				$deleteSql = "DELETE FROM tbl_home_section WHERE `id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);
			} else if ($tbl_nm == 'tbl_subscription') {
				$deleteSql = "DELETE FROM tbl_subscription WHERE `id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);
			} else if ($tbl_nm == 'tbl_home_section_2') {
				$deleteSql = "DELETE FROM tbl_home_section_2 WHERE `id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);
			} else if ($tbl_nm == 'tbl_summery') {
				$deleteSql = "DELETE FROM tbl_summery WHERE `id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);
			}
			$_SESSION['msg'] = "12";
			$response['status'] = 1;
			echo json_encode($response);
			break;


		case 'multi_action': {
				$action = $_POST['for_action'];
				$ids = implode(",", $_POST['id']);
				$tbl_nm = $_POST['table'];

				if ($ids == '') {
					$ids = $_POST['id'];
				}

				if ($action == 'enable') {
					$sql = "UPDATE $tbl_nm SET `status`='1' WHERE `id` IN ($ids)";
					mysqli_query($mysqli, $sql);
					$_SESSION['msg'] = "13";
				} else if ($action == 'disable') {
					$sql = "UPDATE $tbl_nm SET `status`='0' WHERE `id` IN ($ids)";
					if (mysqli_query($mysqli, $sql)) {
						$_SESSION['msg'] = "14";
					}
				} else if ($action == 'delete') {

					if ($tbl_nm == 'tbl_users') {

						$deleteSql = "DELETE FROM tbl_reports WHERE `user_id` IN ($ids)";
						mysqli_query($mysqli, $deleteSql);

						$deleteSql = "DELETE FROM tbl_favourite WHERE `user_id` IN ($ids)";
						mysqli_query($mysqli, $deleteSql);

						$deleteSql = "DELETE FROM tbl_rating WHERE `ip` IN ($ids)";
						mysqli_query($mysqli, $deleteSql);

						$sql = "SELECT * FROM tbl_song_suggest WHERE `user_id` IN ($ids)";
						$res = mysqli_query($mysqli, $sql);

						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['song_image'] != "") {
								unlink('images/' . $row['song_image']);
							}
						}

						mysqli_free_result($res);

						$deleteSql = "DELETE FROM tbl_song_suggest WHERE `user_id` IN ($ids)";
						mysqli_query($mysqli, $deleteSql);

						$deleteSql = "DELETE FROM tbl_active_log WHERE `user_id` IN ($ids)";
						mysqli_query($mysqli, $deleteSql);

						$deleteSql = "DELETE FROM $tbl_nm WHERE `id` IN ($ids)";
						mysqli_query($mysqli, $deleteSql);
					} else if ($tbl_nm == 'tbl_mp3') {

						$sql = "SELECT * FROM $tbl_nm WHERE `id` IN ($ids)";
						$res = mysqli_query($mysqli, $sql);
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['mp3_thumbnail'] != "") {
								unlink('images/' . $row['mp3_thumbnail']);
								unlink('images/thumbs/' . $row['mp3_thumbnail']);
							}

							if ($row['mp3_type'] == "local") {
								$file_name = basename($row['mp3_url']);
								unlink('uploads/' . $file_name);
							}

							Delete('tbl_favourite', 'post_id=' . $row['id']);
							Delete('tbl_rating', 'post_id=' . $row['id']);
							Delete('tbl_reports', 'song_id=' . $row['id']);
							Delete('tbl_mp3_views', 'mp3_id=' . $row['id']);
						}
						$deleteSql = "DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

						mysqli_query($mysqli, $deleteSql);
					} else if ($tbl_nm == 'tbl_category') {

						$sqlCategory = "SELECT * FROM $tbl_nm WHERE `cid` IN ($ids)";
						$res = mysqli_query($mysqli, $sqlCategory);
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['category_image'] != "") {
								unlink('images/' . $row['category_image']);
								unlink('images/thumbs/' . $row['category_image']);
							}
						}
						$deleteSql = "DELETE FROM $tbl_nm WHERE `cid` IN ($ids)";

						mysqli_query($mysqli, $deleteSql);
					} else if ($tbl_nm == 'tbl_album') {
						$sql = "SELECT * FROM tbl_mp3 WHERE `album_id` IN ($ids)";
						$res = mysqli_query($mysqli, $sql);
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['mp3_thumbnail'] != "") {
								unlink('images/' . $row['mp3_thumbnail']);
								unlink('images/thumbs/' . $row['mp3_thumbnail']);
							}

							if ($row['mp3_type'] == "local") {
								$file_name = basename($row['mp3_url']);
								unlink('uploads/' . $file_name);
							}

							Delete('tbl_rating', 'post_id=' . $row['id']);
							Delete('tbl_favourite', 'post_id=' . $row['id']);
							Delete('tbl_reports', 'song_id=' . $row['id']);
							Delete('tbl_mp3_views', 'mp3_id=' . $row['id']);

							$deleteSql = "DELETE FROM tbl_mp3 WHERE `album_id` IN ($ids)";

							mysqli_query($mysqli, $deleteSql);

							mysqli_free_result($res);
						}

						$sqlCategory = "SELECT * FROM $tbl_nm WHERE `aid` IN ($ids)";
						$res = mysqli_query($mysqli, $sqlCategory);
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['album_image'] != "") {
								unlink('images/' . $row['album_image']);
								unlink('images/thumbs/' . $row['album_image']);
							}
						}
						$deleteSql = "DELETE FROM $tbl_nm WHERE `aid` IN ($ids)";

						mysqli_query($mysqli, $deleteSql);
					} else if ($tbl_nm == 'tbl_playlist') {

						$sql = "SELECT * FROM $tbl_nm WHERE `pid` IN ($ids)";
						$res = mysqli_query($mysqli, $sql);
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['playlist_image'] != "") {
								unlink('images/' . $row['playlist_image']);
								unlink('images/thumbs/' . $row['playlist_image']);
							}
						}
						$deleteSql = "DELETE FROM $tbl_nm WHERE `pid` IN ($ids)";

						mysqli_query($mysqli, $deleteSql);
					} else if ($tbl_nm == 'tbl_banner') {

						$sql = "SELECT * FROM $tbl_nm WHERE `bid` IN ($ids)";
						$res = mysqli_query($mysqli, $sql);
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['banner_image'] != "") {
								unlink('images/' . $row['banner_image']);
								unlink('images/thumbs/' . $row['banner_image']);
							}
						}
						$deleteSql = "DELETE FROM $tbl_nm WHERE `bid` IN ($ids)";

						mysqli_query($mysqli, $deleteSql);
					} else if ($tbl_nm == 'tbl_song_suggest') {

						$sql = "SELECT * FROM $tbl_nm WHERE `id` IN ($ids)";
						$res = mysqli_query($mysqli, $sql);
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['song_image'] != "") {
								unlink('images/' . $row['song_image']);
								unlink('images/thumbs/' . $row['song_image']);
							}
						}
						$deleteSql = "DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

						mysqli_query($mysqli, $deleteSql);
					} else if ($tbl_nm == 'tbl_artist') {

						$sql = "SELECT * FROM $tbl_nm WHERE `id` IN ($ids)";
						$res = mysqli_query($mysqli, $sql);
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['artist_image'] != "") {
								unlink('images/' . $row['artist_image']);
								unlink('images/thumbs/' . $row['artist_image']);
							}
						}
						$deleteSql = "DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

						mysqli_query($mysqli, $deleteSql);
					} else if ($tbl_nm == 'tbl_home_section') {
						$deleteSql = "DELETE FROM tbl_home_section WHERE `id` IN ($ids)";
						mysqli_query($mysqli, $deleteSql);
					} else if ($tbl_nm == 'tbl_subscription') {
						$deleteSql = "DELETE FROM tbl_subscription WHERE `id` IN ($ids)";
						mysqli_query($mysqli, $deleteSql);
					} else if ($tbl_nm == 'tbl_summery') {
						$deleteSql = "DELETE FROM tbl_summery WHERE `id` IN ($ids)";
						mysqli_query($mysqli, $deleteSql);
					} else if ($tbl_nm == 'tbl_home_section_2') {
						$deleteSql = "DELETE FROM $tbl_nm WHERE `id` IN ($ids)";
						mysqli_query($mysqli, $deleteSql);
					}

					$_SESSION['msg'] = "12";
				}

				$response['status'] = 1;

				echo json_encode($response);
				break;
			}

		default:
			# code...
			break;
	}
}
