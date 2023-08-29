<? 
    require_once '../header.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Призначення</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">

        <div class="row">
            <div class="col-8">
                <?
                $link = DB::connect();
                $msg = null;
                $patient_id = $_SESSION["user-id"];

                ?>
                <?=$msg?>
                <script>
                    window.addEventListener('load', function() {
                        setTimeout(function() {
                            var element = document.getElementById('error-msg');
                            element.style.display = 'none';
                        }, 3000);
                    });
                </script>

                <div class="appointment-list">
                    <?
                        $sql = "SELECT full_name, birth_date, phone, email, photo 
                        FROM users WHERE user_id = $patient_id";
                        $p = mysqli_fetch_row($link->query($sql));
                        $sql = "SELECT address FROM patients WHERE user_id = $patient_id";
                        $pa = mysqli_fetch_row($link->query($sql));

                        if(empty($p[4] )) {
                            $p[4] = "../assets/images/profile-default.png";
                        }
                        ?>
                    <div class="card">
                        <h3 class="ml-3 mt-3">Медична карта: <?=crypt_str::de($p[0])?></h3>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="<?=$p[4]?>" alt="Фото пацієнта" class="img-fluid" style="width: 180; height: 200">
                                </div>
                                <div class="col-md-8">
                                    <p><strong>Дата народження:</strong> <?=crypt_str::de($p[1])?></p>
                                    <p><strong>Телефон:</strong> <?=crypt_str::de($p[2])?></p>
                                    <p><strong>Пошта:</strong> <?=crypt_str::de($p[3])?></p>
                                    <p><strong>Адреса проживання:</strong> <?=crypt_str::de($pa[0])?></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <button type="button" onclick="window.location.href = '/patient/profile.php'" class="btn btn-link text-secondary float-right">Редагувати</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h3 class="mt-4">Призначення: </h3>

        <?
                        $sql = "SELECT id, date, assignment, diagnosis, recommendations, doctor_id 
                        FROM assignments WHERE patient_id = $patient_id ORDER BY date DESC" ;
                        $assig = mysqli_fetch_all($link->query($sql));
                        if(count($assig) == 0) {
                            echo "<p class='ml-2'>Немає призначень.. </p>";
                        }                    
                        else {                                                     
                            $sql = "SELECT users.user_id, users.full_name FROM users WHERE users.user_id IN (SELECT user_id FROM doctors)";
                            $result = $link->query($sql);
                            $dctrs_names = array();                                                      
                            while ($row = $result->fetch_assoc()) {
                                $dctrs_names[$row['user_id']] = crypt_str::de($row['full_name']);
                            }
                            ?>
        <table class="table">
            <thead>
                <tr>
                    <th class="border-right border-left" scope="col">ПІБ</th>
                    <th class="border-right" scope="col">Дата призначення</th>
                    <th class="border-right" scope="col">Лікар</th>
                    <th class="border-right" scope="col">Призначення</th>
                    <th class="border-right" scope="col">Діагноз</th>
                    <th class="border-right" scope="col">Рекомендації</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($assig as $a) {
                    ?>
                <tr>
                    <td class="border-right border-left"><?=crypt_str::de($p[0])?></td>
                    <td class="border-right"><?=$a[1]?></td>
                    <td class="border-right"><?=$dctrs_names[$a[5]]?></td>
                    <td class="border-right">
                        <?=crypt_str::de($a[2])?>
                    </td>
                    <td class="border-right">
                        <?=crypt_str::de($a[3])?>
                    </td>
                    <td class="border-right">
                        <?=crypt_str::de($a[4])?>
                    </td>
                </tr>
                <?php } }?>
            </tbody>
        </table>

    </div>
    </div>
    </div>
    </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>