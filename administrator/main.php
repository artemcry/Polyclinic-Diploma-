<? 
    session_start();
    require_once '../dbconnect.php';
    require_once '../functions.php';
    ?>
<link rel="stylesheet" type="text/css" href="/css/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<script src="/nodejs/node_modules/jquery/src/jquery.js"></script>

<?
    if(isset($_POST["new-p"])) {
        $p = encryptPassword($_POST['new-pss']);
        $sql = "UPDATE users SET password = '{$p}' WHERE `users`.`user_id` = 1;";
        $link = DB::connect();
        $link->query($sql);
    }

    if($_SESSION["user-role"] === "Administrator") {?>
        <div class="d-flex justify-content-center mt-1">        
        <h4 class="mb-3">Адміністративний функціонал</h4>
        </div>
        <div class="d-flex justify-content-center mt-5">
            <div class="d-flex flex-column align-items-right" style="width: 400px">
                <h5>Лікарі:</h5>
                <button onclick="window.location.href = 'new_doctor.php'" class="btn btn-primary mb-1">Додати нового лікаря</button>
                <button onclick="window.location.href = 'edit_doctors_list.php'" class="btn btn-primary mb-1">Редагувати список лікарів</button>
                <h5>Послуги:</h5>
                <button onclick="window.location.href = 'new_service.php'" class="btn btn-primary mb-1">Додати нову послугу</button>
                <button onclick="window.location.href = 'edit_services_list.php'" class="btn btn-primary mb-1">Редагувати перелік послуг</button>
                <h5>Інше:</h5>
                <button onclick="window.location.href = 'statistic_generation.php'" class="btn btn-primary mb-1">Генерація звітів та статистики</button>
                <button onclick="window.location.href = 'backup.php'" class="btn btn-primary mb-1">Резервне копіювання даних</button>
                <button onclick="window.location.href = '/'" class="btn btn-primary mb-1">Вихід</button>
                <button onclick="vs_click()" class="btn btn-primary mb-1">Змінити пароль</button>
                
                <div class="form-group mt-2" id=newp style="display: none">
                <form action="" method="post">
                <label for="new-pss">Новий пароль</label>
                    <input type="text" id="new-pss" name="new-pss" class="form-control mb-2">
                    <button name="new-p" class="btn btn-primary mb-1">Змінити пароль</button>
                </div>
                </form>
            </div>
        </div>
        <script>
            function vs_click()
            {           
                 var vs = document.getElementById('newp').style; 
                 vs.display = (vs.display === 'block') ?'none' :'block';                
            }
        </script>
    <?}
?>
