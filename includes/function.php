<?php //error_reporting(0);

#Admin Login
function adminUser($username, $password)
{

    global $mysqli;

    $sql = "SELECT id,username FROM tbl_admin where username = '" . $username . "' and password = '" . md5($password) . "'";
    $result = mysqli_query($mysqli, $sql);
    $num_rows = mysqli_num_rows($result);

    if ($num_rows > 0) {
        while ($row = mysqli_fetch_array($result)) {
            echo $_SESSION['ADMIN_ID'] = $row['id'];
            echo $_SESSION['ADMIN_USERNAME'] = $row['username'];

            return true;
        }
    }
}
function checkSignSalt($data_info)
{

    $key = "vocsyinfotech";

    $data_json = $data_info;

    $data_arr = json_decode(urldecode($data_json), true);

    return $data_arr;
}

# Insert Data 
function Insert($table, $data)
{

    global $mysqli;
    //print_r($data);

    $fields = array_keys($data);
    $values = array_map(array($mysqli, 'real_escape_string'), array_values($data));

    //echo "INSERT INTO $table(".implode(",",$fields).") VALUES ('".implode("','", $values )."');";
    //exit;  
    mysqli_query($mysqli, "INSERT INTO $table(" . implode(",", $fields) . ") VALUES ('" . implode("','", $values) . "');") or die(mysqli_error($mysqli));
}

// Update Data, Where clause is left optional
function Update($table_name, $form_data, $where_clause = '')
{
    global $mysqli;
    // check for optional where clause
    $whereSQL = '';
    if (!empty($where_clause)) {
        // check to see if the 'where' keyword exists
        if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
            // not found, add key word
            $whereSQL = " WHERE " . $where_clause;
        } else {
            $whereSQL = " " . trim($where_clause);
        }
    }
    // start the actual SQL statement
    $sql = "UPDATE " . $table_name . " SET ";
   

    // loop and build the column /
    $sets = array();
    foreach ($form_data as $column => $value) {
        $sets[] = "`" . $column . "` = '" . $value . "'";
    }
    $sql .= implode(', ', $sets);

    // append the where statement
    $sql .= $whereSQL;
    // run and return the query result
    return mysqli_query($mysqli, $sql);
}
function thousandsNumberFormat($num)
{

    if ($num > 1000) {

        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array(' K', ' M', ' B', ' T');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;
    }

    return $num;
}

//Delete Data, the where clause is left optional incase the user wants to delete every row!
function Delete($table_name, $where_clause = '')
{
    global $mysqli;
    // check for optional where clause
    $whereSQL = '';
    if (!empty($where_clause)) {
        // check to see if the 'where' keyword exists
        if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
            // not found, add keyword
            $whereSQL = " WHERE " . $where_clause;
        } else {
            $whereSQL = " " . trim($where_clause);
        }
    }
    // build the query
    $sql = "DELETE FROM " . $table_name . $whereSQL;

    // run and return the query result resource
    return mysqli_query($mysqli, $sql);
}

//GCM function
function Send_GCM_msg($registration_id, $data)
{
    $data1['data'] = $data;

    $url = 'https://fcm.googleapis.com/fcm/send';

    $registatoin_ids = array($registration_id);
    // $message = array($data);

    $fields = array(
        'registration_ids' => $registatoin_ids,
        'data' => $data1,
    );

    $headers = array(
        'Authorization: key=' . APP_GCM_KEY . '',
        'Content-Type: application/json'
    );
    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    // Execute post
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }

    // Close connection
    curl_close($ch);
    //echo $result;exit;
}


//Image compress
function compress_image($source_url, $destination_url, $quality)
{

    $info = getimagesize($source_url);

    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source_url);

    elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($source_url);

    elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($source_url);

    imagejpeg($image, $destination_url, $quality);
    return $destination_url;
}

//Create Thumb Image
function create_thumb_image($target_folder = '', $thumb_folder = '', $thumb_width = '', $thumb_height = '')
{
    //folder path setup
    $target_path = $target_folder;
    $thumb_path = $thumb_folder;


    $thumbnail = $thumb_path;
    $upload_image = $target_path;

    list($width, $height) = getimagesize($upload_image);
    $thumb_create = imagecreatetruecolor($thumb_width, $thumb_height);
    switch ($file_ext) {
        case 'jpg':
            $source = imagecreatefromjpeg($upload_image);
            break;
        case 'jpeg':
            $source = imagecreatefromjpeg($upload_image);
            break;
        case 'png':
            $source = imagecreatefrompng($upload_image);
            break;
        case 'gif':
            $source = imagecreatefromgif($upload_image);
            break;
        default:
            $source = imagecreatefromjpeg($upload_image);
    }
    imagecopyresized($thumb_create, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
    switch ($file_ext) {
        case 'jpg' || 'jpeg':
            imagejpeg($thumb_create, $thumbnail, 80);
            break;
        case 'png':
            imagepng($thumb_create, $thumbnail, 80);
            break;
        case 'gif':
            imagegif($thumb_create, $thumbnail, 80);
            break;
        default:
            imagejpeg($thumb_create, $thumbnail, 80);
    }
}

function cleanInput($inputText)
{
    return htmlentities(addslashes(trim($inputText)));
}
   
// Get base url
function getBaseUrl($array = false)
{
    $protocol = "http";
    $host = "";
    $port = "";
    $dir = "";

    // Get protocol
    if (array_key_exists("HTTPS", $_SERVER) && $_SERVER["HTTPS"] != "") {
        if ($_SERVER["HTTPS"] == "on") {
            $protocol = "https";
        } else {
            $protocol = "http";
        }
    } elseif (array_key_exists("REQUEST_SCHEME", $_SERVER) && $_SERVER["REQUEST_SCHEME"] != "") {
        $protocol = $_SERVER["REQUEST_SCHEME"];
    }

    // Get host
    if (array_key_exists("HTTP_X_FORWARDED_HOST", $_SERVER) && $_SERVER["HTTP_X_FORWARDED_HOST"] != "") {
        $host = trim(end(explode(',', $_SERVER["HTTP_X_FORWARDED_HOST"])));
    } elseif (array_key_exists("SERVER_NAME", $_SERVER) && $_SERVER["SERVER_NAME"] != "") {
        $host = $_SERVER["SERVER_NAME"];
    } elseif (array_key_exists("HTTP_HOST", $_SERVER) && $_SERVER["HTTP_HOST"] != "") {
        $host = $_SERVER["HTTP_HOST"];
    } elseif (array_key_exists("SERVER_ADDR", $_SERVER) && $_SERVER["SERVER_ADDR"] != "") {
        $host = $_SERVER["SERVER_ADDR"];
    }
    //elseif(array_key_exists("SSL_TLS_SNI", $_SERVER) && $_SERVER["SSL_TLS_SNI"] != "") { $host = $_SERVER["SSL_TLS_SNI"]; }

    // Get port
    if (array_key_exists("SERVER_PORT", $_SERVER) && $_SERVER["SERVER_PORT"] != "") {
        $port = $_SERVER["SERVER_PORT"];
    } elseif (stripos($host, ":") !== false) {
        $port = substr($host, (stripos($host, ":") + 1));
    }
    // Remove port from host
    $host = preg_replace("/:\d+$/", "", $host);

    // Get dir
    if (array_key_exists("SCRIPT_NAME", $_SERVER) && $_SERVER["SCRIPT_NAME"] != "") {
        $dir = $_SERVER["SCRIPT_NAME"];
    } elseif (array_key_exists("PHP_SELF", $_SERVER) && $_SERVER["PHP_SELF"] != "") {
        $dir = $_SERVER["PHP_SELF"];
    } elseif (array_key_exists("REQUEST_URI", $_SERVER) && $_SERVER["REQUEST_URI"] != "") {
        $dir = $_SERVER["REQUEST_URI"];
    }
    // Shorten to main dir
    if (stripos($dir, "/") !== false) {
        $dir = substr($dir, 0, (strripos($dir, "/") + 1));
    }

    // Create return value
    if (!$array) {
        if ($port == "80" || $port == "443" || $port == "") {
            $port = "";
        } else {
            $port = ":" . $port;
        }
        return htmlspecialchars($protocol . "://" . $host . $port . $dir, ENT_QUOTES);
    } else {
        return ["protocol" => $protocol, "host" => $host, "port" => $port, "dir" => $dir];
    }
}

function my_encrypt($data, $key)
{
    // Remove the base64 encoding from our key
    $encryption_key = base64_decode($key);
    // Generate an initialization vector
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
    return base64_encode($encrypted . '::' . $iv);
}

function my_decrypt($data, $key)
{
    // Remove the base64 encoding from our key
    $encryption_key = base64_decode($key);
    // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}
function pre($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}


function __($string, $allow_html = false)
{
    global $mysqli;
    if ($allow_html) {
        $string =  html_entity_decode($string);
    } else {
        $string = mysqli_real_escape_string($mysqli, $string);
    }
    return $string;
}

function _e($string, $allow_html = false)
{
    // global $mysqli;
    if ($allow_html) {
        $string =  html_entity_decode($string);
    } else {
        // $string = mysqli_real_escape_string($mysqli, $string);
        $string = $string;
    }
    echo $string;
}

function validate_input($input, $allow_html = false, $allow_iframe = false)
{
    if ($input == null) {
        return $input;
    }
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = validate_input($value, $allow_html);
        }
        return $input;
    } else {

        if ($allow_html) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.Nofollow', true);
            $config->set('HTML.TargetBlank', true);
            $config->set('Attr.EnableID', true);
            $config->set('CSS.AllowTricky', true);

            if ($allow_iframe) {
                $config->set('HTML.SafeIframe', true);
                $config->set('URI.SafeIframeRegexp', '%^(https?:)?//%'); //allow Safe Url
            }
            $config->set('HTML.Allowed', 'p,b,i,em,br,strong,div,a,span,ul,ol,li,img,table,thead,th,tr,td,iframe');
            $config->set('HTML.AllowedAttributes', 'a.href,a.target,a.rel,a.id,src, height, width, alt, style, class');

            $def = $config->getHTMLDefinition(true);
            $def->addAttribute('iframe', 'allowfullscreen', 'Bool');

            $purifier = new HTMLPurifier($config);
            $input = $purifier->purify($input);
        } else {
            $input = strip_tags($input);
        }
        $input = htmlentities($input);
        $input = trim($input);

        return $input;
    }
}
