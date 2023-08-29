<? 
session_start();
    require_once 'header.php';

    require_once 'dbconnect.php';
    require_once 'functions.php';

    $username = $_POST["username"];
    $password = $_POST["password"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $name = $_POST["name"];
    $dob = $_POST["dob"];
    $msg = null;
    
    if(isset($_POST["login"])) {
        $res = authenticateUser($username, $password);
        if ($res[0]){
            if($_SESSION["user-role"] === "Administrator") {
                header("Location: administrator/main.php");
                exit();
            }
            header("Location: index.php");
        }
        else
            $msg = "<h6 id=error-msg style=\"color: red;\">".$res[1]."</h6>";
    }
    else if(isset($_POST["register"])) {
        $res = registerUser($username, $password, $phone, $email, 'Patient', $name, $dob);
        if ($res[0]) {  
            registerPatient($res[2], null);          
            authenticateUser($username, $password);        
            header("Location: index.php");
        }
        else
            $msg = "<h6 id=error-msg style=\"color: red;\">".$res[1]."</h6>";
    }
?>
<html>


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9" style="width: 400px;">

            <head>
                <title>Авторизація</title>
            </head>
            <? if(isset($_GET["register-form"])) {?>
            <h2 class="text-center mt-4" >Реєстрація</h2>
            <? } else {?>
                <h2 class="text-center mt-4" >Авторизація</h2>
            <? }?>
            <form method="POST" action="">
                <div class="form-group mb-2">
                    <label for="username">Ім'я користувача:</label>
                    <input type="text" class="form-control" id="username" name="username">
                </div>
                <div class="form-group mb-2">
                    <label for="password">Пароль:</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>


                <? if(isset($_GET["register-form"])) {?>
                <div class="form-group mb-2">
                    <label for="password2">Підтвердження паролю:</label>
                    <input type="password" class="form-control" id="password2" name="password2">
                </div>   

                <div class="form-group mb-2">
                    <label for="name">ПІБ:</label>
                    <input type="text" class="form-control" name="name" id="name">
                </div>
                <div class="form-group mb-2">
                    <label for="dob">Дата народження:</label>
                    <input type="date" class="form-control" id="dob" name="dob">
                </div>
                <div class="form-group mb-2">
                    <label for="phone">Номер Телефону:</label>
                    <input type="tel" class="form-control" name="phone" id="phone" value="+380">
                </div>
                <div class="form-group mb-2">
                    <label for="email">Поштова адреса:</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>

                <input type="submit" name="register" id="register" style="display: none">
                </form>

                <div class="d-flex justify-content-center align-items-center" >
                    <button type="submit" onclick="submitForm()" class="btn btn-primary mt-1 ml-2" >Зареєструватись</button>
                    <form method="POST" action="login.php">
                        <input type="submit" class="btn btn-link mt-1 align-self-center mt-2" value="Увійти">
                    </form>
                </div>

            <? }                     
                else {?>
            <div class="form-group">
                <div style="width: 200px; margin: 0 auto;" class="x-flex ml-5"> 
                <input type="submit" style="width: 200px; margin: 0 auto;" class="btn btn-primary mt-4" name="login" value="Вхід">
                    
                    </form>
                    <form method="GET" action="">
                        <input type="submit" class="btn btn-link " name="register-form" value="Реєстрація">
                    </form>
                </div>
            </div>
            

            <? }?>
            
      <?=$msg?>
        </div>
    </div>
</div>
<script>
    window.addEventListener('load', function() {
    setTimeout(function() {
        var element = document.getElementById('error-msg');
        element.style.display = 'none';
        }, 3000);
    });

    function validateForm() {
        var username = document.getElementById("username").value;
        var password = document.getElementById("password").value;    
        var password2 = document.getElementById("password2").value;    
        var phone = document.getElementById("phone").value;
        var email = document.getElementById("email").value;
        var name = document.getElementById("name").value;
        var dob = document.getElementById("dob").value;
        if (username === '' || password === '' || phone === '' || email === '' || name === '' || dob === '') {
            alert("Будь ласка, заповніть всі поля");
            return false;
        } else if(password !== password2) {
            alert("Паролі не співпадають");
            return false; 
        }
        
        return true; 
    }

    function submitForm() {
        if (validateForm()) {
            document.getElementById("register").click();
        }
    }

</script>