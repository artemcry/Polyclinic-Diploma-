
<?
    require_once '../importcss.html';
    require_once '../functions.php';
    $link = DB::connect();
    if (isset($_POST["registration"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $full_name = $_POST["full-name"];
        $birth_date = $_POST["birth-date"];
        $specialization = crypt_str::en($_POST["specialization"]);
        $education = crypt_str::en($_POST["education"]);
        $phone = $_POST["phone"];
        $email = $_POST["email"];
        $photo = $_POST["profile-photo"];

        $img = saveIMG($_FILES['profile-photo']['name'], $_FILES['profile-photo']['size'], $_FILES['profile-photo']['tmp_name']);
        $r = registerUser($username, $password, $phone, $email, 'Doctor', $full_name, $birth_date, $img);                 
        $msg = null;
        if(!$r[0]) {
            $msg = "<h5 id=error-msg style=\"color: red;\">".$r[1]."</h5>";
            unlink($img);
        } else {
            $sql = "INSERT INTO doctors (`user_id`, `specialization`, `education`) VALUES
            ('{$r[2]}', '{$specialization}', '{$education}')"; 
            $m = mysqli_query($link, $sql);
            if($m) {
                $sql = "INSERT INTO doctor_schedules (doctor_id, day_of_week) VALUES 
                ($r[2], 'Monday'), ($r[2], 'Tuesday'), ($r[2], 'Wednesday'), ($r[2], 'Thursday'), ($r[2], 'Friday'), ($r[2], 'Saturday'), ($r[2], 'Sunday')";

                if (mysqli_query($link, $sql)) {
                  $msg = "<h5 id=error-msg style=\"color: green;\">Реєстрація успішна</h5>";
                }

            }         
            else{
                $msg = "<h5 id=error-msg style=\"color: red;\">".mysqli_error($link)."</h5>";
                unlink($img);
                $sql = "DELETE FROM `users` WHERE `users`.`user_id` = {$r[2]}"; 
                $m = mysqli_query($link, $sql);                
            }
        }
        $link->close();
    }
   
?>



<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">
                

    <div class="d-flex align-items-center mb-5">
    <button onclick="window.location.href = 'main.php'" type="button" class="btn btn-secondary ">
        <i class="bi bi-arrow-left"></i> Назад
    </button>
    <h2 class="ms-3 mt-1">Реєстрація лікаря</h2>
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
      <form action="" method="post" enctype="multipart/form-data">
        <h5 class="mt-4 text-center">Дані для входу:</h5>
        <div class="form-group">
          <label for="username">Логін</label>
          <input type="text" id="username" name="username" class="form-control mb-2">
        </div>
        <div class="form-group">
          <label for="password">Пароль</label>
          <input type="password" id="password" name="password"  class="form-control mb-2">
        </div>
          <h5 class="mt-4 text-center">Дані особи лікаря:</h5>
        <div class="form-group">
          <label for="full-name">Повне ім'я</label>
          <input type="text" id="full-name" name="full-name"  class="form-control mb-2">
        </div>
        <div class="form-group">
          <label for="birth-date">Дата народження</label>
          <input type="date" id="birth-date" name="birth-date"  class="form-control mb-2">
        </div>
        <div class="form-group">
          <label for="specialization">Спеціалізація</label>
          <input type="text" id="specialization" name="specialization"  class="form-control mb-2">
        </div>
        <div class="form-group">
          <label for="education">Освіта</label>
          <input type="text" id="education" name="education"  class="form-control mb-2">
        </div>
        <div class="form-group">
          <label for="phone">Телефон</label>
          <input type="tel" id="phone" name="phone"  class="form-control mb-2">
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email"  class="form-control mb-2">
        </div>
        <div class="form-group mt-1">
          <label for="profile-photo">Фото профілю</label><br>
          <input type="file" id="profile-photo" name="profile-photo"  class="form-control mb-2-file mt-1 mb-2" accept="image/*">
        </div>
        <input type="submit" name="registration" id="registration" style="display: none;">
        </form>

        <button onclick="submitForm()" class="btn btn-primary mx-auto d-block w-50 mt-3 mb-3">Зареєструвати</button>
    </div>
  </div>
</div>


<script>
    function validateForm() {
        var username = document.getElementById("username").value;
        var password = document.getElementById("password").value;
        var fullName = document.getElementById("full-name").value;
        var birthDate = document.getElementById("birth-date").value;
        var specialization = document.getElementById("specialization").value;
        var education = document.getElementById("education").value;
        var phone = document.getElementById("phone").value;
        var email = document.getElementById("email").value;
        var profile_photo = document.getElementById("profile-photo").value;

        if (profile_photo === '' || username === '' || password === '' ||
         fullName === '' || birthDate === '' || specialization === '' || education === '' || phone === '' || email === '') {
            alert("Будь ласка, заповніть всі поля");
            return false; 
        }


        return true; 
    }

    function submitForm() {
        if (validateForm()) {
            document.getElementById("registration").click();
        }
    }
</script>