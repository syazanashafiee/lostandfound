<html>
<head>
<title>Database Connection</title>
</head>
<body>
<?php



//connect to server
$connect = mysqli_connect("localhost", "root", "", "lostandfound");

if(!$connect)
{
	die('ERROR:' .mysqli_connect_error());
}


session_start();

// Cek apakah user login sebagai admin
if (!isset($_SESSION['is_admin'])) {
    $_SESSION['is_admin'] = false;
}

// Fungsi untuk membersihkan input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>
</body>
</html>

