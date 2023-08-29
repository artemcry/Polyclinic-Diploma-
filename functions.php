<?
session_start();
require_once 'dbconnect.php';

function encryptPassword($password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    return $hash;
}
function sql_valid($str) {
    return str_replace("'", "`", $str);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
function registerUser($username, $password, $phone, $email, $role = 'NULL', $full_name = 'NULL', $birth_date = 'NULL', $photo = 'NULL') {
    $conn = DB::connect();

    $un = "SELECT 1 FROM `users` WHERE username='{$username}'";
    if (mysqli_num_rows(mysqli_query($conn, $un)) != 0) {
        return [false, "Користувач з таким ім'ям вже існує!"];
    }
    else {
        $hashedPassword = encryptPassword($password);    
        $flln = sql_valid($full_name);

        if(empty($birth_date))
            $birth_date = 'NULL';
        if($role != 'NULL') 
            $role = '\''.$role.'\'';
        if($flln != 'NULL') 
            $flln = '\''.crypt_str::en($full_name).'\'';
        if($birth_date != 'NULL') 
            $birth_date = '\''.crypt_str::en($birth_date).'\'';
        if($photo != 'NULL') 
            $photo = '\''.$photo.'\'';

        $email = crypt_str::en($email);
        $phone = crypt_str::en($phone);

                
        $sql = "INSERT INTO users (`username`, `password`, `phone`, `email`, `role`, `full_name`, `birth_date`, `photo`) VALUES
         ('{$username}', '{$hashedPassword}', '{$phone}', '{$email}', {$role}, {$flln}, {$birth_date}, {$photo})";

        $rgstr = mysqli_query($conn, $sql);
        if ($rgstr) {   
            $sql = "SELECT `user_id` FROM `users` WHERE `username` = '$username'";
            $id = mysqli_fetch_row(mysqli_query($conn, $sql));
            return [true, "Реєстрація пройшла успішно!", $id[0]];
        } else {
            return [false, "Помилка при реєстрації"];
        }
    }
    $conn->close();
}
function registerPatient($id, $address = 'NULL') {
    
    $address = sql_valid($address);
    if($address != 'NULL') 
        $address = '\''.crypt_str::en($address).'\'';    
                
    $sql = "INSERT INTO patients (`user_id`, `address`) VALUES
    ('{$id}', {$address})";
    $conn = DB::connect();
    $rgstr = mysqli_query($conn, $sql);
    if ($rgstr) {   
        $sql = "SELECT `user_id` FROM `users` WHERE `username` = '$id'";
        $pss = mysqli_fetch_row(mysqli_query($conn, $sql));
        return [true, "Реєстрація пройшла успішно!", $pss[0]];
    } else {
        return [false, "Помилка при реєстрації"];
    }
}
function saveIMG($name, $size, $tmp_name) {
    $img_ex = pathinfo($name, PATHINFO_EXTENSION);
    $img_ex_lc = strtolower($img_ex);
    $new_img_name = uniqid("IMG-", true).'.'.$img_ex_lc;
    $img_upload_path = '../assets/images/'.$new_img_name;
    move_uploaded_file($tmp_name, $img_upload_path);
    return $img_upload_path;
}

function authenticateUser($username, $password) {
    $conn = DB::connect();

    $sql = "SELECT `password`, `user_id`, `role`, `username` FROM `users` WHERE `username` = '$username'";
    $pss = mysqli_query($conn, $sql);
    $conn->close();
    if (mysqli_num_rows($pss) > 0) {
        $pss = mysqli_fetch_all($pss);
        $hashedPassword = $pss[0][0];   
        if (verifyPassword($password, $hashedPassword)) {
            $_SESSION["user-id"] = $pss[0][1];
            $_SESSION["user-role"] = $pss[0][2];
            $_SESSION["user-name"] = $pss[0][3];

            return [true, "Успішна авторизація!"];
        } else {
            return [false, "Невірний логін або  пароль!"];
        }
    } else {
        return [false, "Невірний логін або  пароль!"];
    }
}
?>
