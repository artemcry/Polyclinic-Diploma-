
<?
    require_once '../importcss.html';
    require_once '../functions.php';
    require_once '../header.php';


    $link = DB::connect();
    if (isset($_POST["update"])) {

        $password = $_POST["password"];
        $full_name = crypt_str::en(sql_valid($_POST["full-name"]));
        $birth_date = crypt_str::en($_POST["birth-date"]);
        $address = crypt_str::en(sql_valid($_POST["address"]));
        $phone = crypt_str::en($_POST["phone"]);
        $email = crypt_str::en($_POST["email"]);
        $id = $_SESSION["user-id"];

        $sqp = "";
        if($_FILES['profile-photo']['name'] != "") {
            $img = saveIMG($_FILES['profile-photo']['name'], $_FILES['profile-photo']['size'], $_FILES['profile-photo']['tmp_name']);
            $sqp .= ", `photo`= '".$img ."'";
        }                        
        if(!empty($password)) {
            $sqp .= ", `password`= '".encryptPassword($password) ."'";
        }
        $sq = "UPDATE `users` SET `full_name` = '{$full_name}', `birth_date` = '{$birth_date}',         
         `phone` = '{$phone}', `email` = '{$email}' ".$sqp." WHERE `user_id` = {$id};";
        $sq2 = "UPDATE `patients` SET `address` = '{$address}' WHERE `user_id` = $id";

        $r = mysqli_query($link, $sq);
        $r2 = mysqli_query($link, $sq2);
        if(!$r || !$r2) {
            $msg = "<h5 id=error-msg style=\"color: red;\">Error: ".mysqli_error($link)."</h5>";
            if($img) 
                unlink($img);
        } else {
            $msg = "<h5 id=error-msg style=\"color: green;\">Дані змінено успішно</h5>";
        }
    }
?>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">
    <title>Мій кабінет</title>           

    <div class="d-flex align-items-center mb-5">

    <h2 class="ms-3 mt-1">Мій кабінет</h2>
    </div>
    <?=$msg?>
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                var element = document.getElementById('error-msg');
                element.style.display = 'none';
            }, 3000);
        });
    </script>
    <?
        $sql = "SELECT address FROM `patients` WHERE `user_id` = '{$_SESSION["user-id"]}'";
        $pte = mysqli_fetch_row(mysqli_query($link, $sql));
        $sql = "SELECT * FROM `users` WHERE `user_id` = '{$_SESSION["user-id"]}'";
        $usr = mysqli_fetch_row(mysqli_query($link, $sql));
        $link->close();
    ?>


      <form action="" method="post" enctype="multipart/form-data">
        <h5 class="mt-4 text-center">Дані для входу:</h5>
        <div class="form-group">
          <label for="username">Логін</label>
          <input disabled value="<?=$usr[1]?>" type="text" id="username" name="username" class="form-control mb-2">
        </div>
        <div class="form-group">
          <label for="password">Пароль</label>
          <input value="" type="password" id="password" name="password" placeholder="Залишіть пустим якщо не потрібно змінювати пароль"  class="form-control mb-2">
        </div>
          <h5 class="mt-4 text-center">Дані особи: </h5>
        <div class="form-group">
          <label for="full-name">Повне ім'я</label>
          <input value="<?=crypt_str::de($usr[6])?>" type="text" id="full-name" name="full-name"  class="form-control mb-2">
        </div>
        <div class="form-group">
          <label for="birth-date">Дата народження</label>
          <input value="<?=crypt_str::de($usr[7])?>" type="date" id="birth-date" name="birth-date"  class="form-control mb-2">
        </div>
        <div class="form-group">
          <label for="address">Місце проживання</label>
          <input value="<?=crypt_str::de($pte[0])?>" type="text" id="address" name="address"  class="form-control mb-2">
        </div>
        <div class="form-group">
          <label for="phone">Телефон</label>
          <input value="<?=crypt_str::de($usr[4])?>" type="tel" id="phone" name="phone"  class="form-control mb-2">
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input value="<?=crypt_str::de($usr[5])?>" type="email" id="email" name="email"  class="form-control mb-2">
        </div>
        
        <div class="form-group mt-1">
          <label for="profile-photo">Нове фото профілю (Не обов'язково)</label><br>
          <input  type="file" id="profile-photo" name="profile-photo"  class="form-control mb-2-file mt-1 mb-2" accept="image/*">
        </div>
        <input type="submit" name="update" id="update" style="display: none;">
        </form>

        <button onclick="submitForm()" class="btn btn-primary mx-auto d-block w-50 mt-3 mb-3">Зберегти зміни</button>
    </div>
  </div>
</div>


<script>
    function validateForm() {
        var username = document.getElementById("username").value;
        var fullName = document.getElementById("full-name").value;
        var birthDate = document.getElementById("birth-date").value;
        var address = document.getElementById("address").value;
        var phone = document.getElementById("phone").value;
        var email = document.getElementById("email").value;

        if ( username === '' || fullName === '' || birthDate === '' || 
        address === '' || phone === '' || email === '') {
            alert("Будь ласка, заповніть всі поля");
            return false;
        }
        return true;
    }

    function submitForm() {
        if (validateForm()) {
            document.getElementById("update").click();
        }
    }
</script>