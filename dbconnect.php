<? 

// $link = mysqli_connect('localhost','root', '', 'clinic');

// for host:
// $link = mysqli_connect('localhost','x', 'x', 'x');
// $link->set_charset("utf8mb4");

// if(mysqli_connect_errno()) {
//     echo('error'. mysqli_connect_error());
//     exit();
// }
require_once 'secure/crypt.php';

class DB {
    public static function connect() {
        return mysqli_connect('localhost','root', '', 'clinic');
    }
}

