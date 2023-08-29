<? 
    require_once '../header.php';
    require_once '../functions.php';

?>
<!DOCTYPE html>
<html>

<head>
    <title>Список записів на прийом до лікаря</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .appointment-list {
            margin-bottom: 20px;
        }

        .appointment-item {
            padding: 10px;
            border: 1px solid lightgray;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .appointment-item.completed {
            background-color: lightgray;
        }

        .confirmation-list {
            width: 250px;
        }

        .confirmation-item {
            padding: 10px;
            border: 1px solid lightgray;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .confirmation-item .small {
            font-size: 12px;
        }

        .section-divider {
            border-top: 1px solid #000000;
        }

        .editable-field[readonly] {
            background-color: transparent;
            border: none;
        }

        .save-btn,
        .cancel-btn {
            display: none;
        }

        .edit-btn:focus~.save-btn,
        .edit-btn:focus~.cancel-btn {
            display: inline;
        }
    </style>
</head>

<body>
    <div class="container mt-4">

        <div class="row">
            <div class="col-8">
                <?
                $dctr_id = $_SESSION["user-id"];
                $link = DB::connect();
                $msg = null;
                if(isset($_POST["remove-assg"])) {
                    $sql = "DELETE FROM assignments WHERE id =  {$_POST['assg-id']}";
                    if($link->query($sql)) {
                        $msg = "<h6 id=error-msg style=\"color: green;\">Успішно видалено</h6>";
                    } else {
                        $msg = "<h6 id=error-msg style=\"color: red;\">Помилка</h6>";
                    }
                }
                if(isset($_POST["edit-assg"])) {
                    $assg = crypt_str::en(sql_valid($_POST['assigment']));
                    $recomend = crypt_str::en(sql_valid($_POST['recommend']));

                    $sql = "UPDATE assignments SET assignment = '{$assg}', recommendations = '{$recomend}' 
                    WHERE id = {$_POST["edit-assg-id"]}";
                    if($link->query($sql)) {
                        $msg = "<h6 id=error-msg style=\"color: green;\">Успішно змінено</h6>";
                    } else {
                        $msg = "<h6 id=error-msg style=\"color: red;\">Помилка</h6>";
                    }
                }
                
                $sql = "SELECT user_id, full_name, birth_date FROM users WHERE user_id IN (SELECT user_id FROM patients)";
                $ptnts = mysqli_fetch_all($link->query($sql));
                foreach ($ptnts as &$pt) {
                    $pt[1] = crypt_str::de($pt[1]);
                    $pt[2] = crypt_str::de($pt[2]);
                }
                
                $patients = array();
                foreach($ptnts as $d) { 
                  $patients[] = $d[1]." ".$d[2]." (".$d[0].")";  
                }
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
                <h2>Пошук пацієнтів</h2>
                <div class="appointment-list">
                    <form method="GET" action="">
                        <div class="form-group">
                            <div class="form-group" style="position: relative; ">
                                <input autocomplete="off" type="text" style="height: 48px;" class="form-control" id="patientInput" name="pacient-id" placeholder="Введіть ім'я пацієнта">
                                <div id="patientDropdown" class="dropdown-menu" style="position: absolute; width: 100%; top: 100%;"></div>
                            </div>
                    </form>
                    <?if(isset($_GET["ptn-id"])) {

                        $patient_id = $_GET["ptn-id"];


                        $sql = "SELECT full_name, birth_date, phone, email, photo 
                        FROM users WHERE user_id IN (SELECT user_id FROM patients) AND user_id = $patient_id";
                        $p = mysqli_fetch_row($link->query($sql));

                        $sql = "SELECT address FROM patients WHERE user_id = $patient_id";
                        $pa = mysqli_fetch_row($link->query($sql));

                        if(empty($p[4] )) {
                            $p[4] = "../assets/images/profile-default.png";
                        }
                        ?>
                    <div class="card">
                        <h3 class="ml-3 mt-3">Медична карта "<?=crypt_str::de($p[0])?>"</h3>

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
                    <th scope="col">ПІБ</th>
                    <th scope="col">Дата призначення</th>
                    <th scope="col">Лікар</th>
                    <th scope="col">Діагноз</th>
                    <th scope="col">Призначення</th>
                    <th scope="col">Рекомендації</th>
                    <th scope="col">Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php
      foreach ($assig as $a) {
        ?>
                <tr>
                    <td class="border-right"><?=crypt_str::de($p[0])?></td>
                    <td class="border-right"><?=$a[1]?></td>
                    <td class="border-right"><?=$dctrs_names[$a[5]]?></td>
                    <td class="border-right"><?=crypt_str::de($a[3])?></td>
                    <td class="border-right">
                        <?php if ($a[5] == $dctr_id) { ?>
                        <span class="editable-field">
                            <?=crypt_str::de($a[2])?>
                        </span>
                        <input type="text" id="assg-<?=$a[0]?>" class="form-control editable-field" value="<?=crypt_str::de($a[2])?>" style="display: none;">
                        <?php } else { ?>
                        <?=crypt_str::de($a[2])?>
                        <?php } ?>
                    </td>
                    <td class="border-right">
                        <?php if ($a[5] == $dctr_id) { ?>
                        <span class="editable-field">
                            <?=crypt_str::de($a[4])?>
                        </span>
                        <input type="text" id="rec-<?=$a[0]?>" class="form-control editable-field" value="<?=crypt_str::de($a[4])?>" style="display: none;">
                        <?php } else { ?>
                        <?=crypt_str::de($a[4])?>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($a[5] == $dctr_id) { ?>
                        <a onclick="saveEdit(<?=$a[0]?>)" class="btn btn-link save-btn" style="display: none;">
                            <i class="bi bi-check-square"></i> Зберегти<br>
                        </a>
                        <a onclick="cancelEdit(this)" class="btn btn-link cancel-btn" style="display: none;">
                            <i class="bi bi-x-square"></i> Відмінити
                        </a>
                        <a onclick="editAssign(this)" class="btn btn-link edit-btn">
                            <i class="bi bi-pencil-square"></i> Редагувати
                        </a>
                        <a onclick="remove_assign(<?=$a[0]?>)" id=removassg class="btn btn-link text-danger">
                            <i class="bi bi-trash"></i> Видалити
                        </a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <script>
            function editAssign(button) {
                const row = button.parentNode.parentNode;
                const fields = row.querySelectorAll('.editable-field');
                const editBtn = row.querySelector('.edit-btn');
                const saveBtn = row.querySelector('.save-btn');
                const cancelBtn = row.querySelector('.cancel-btn');

                fields.forEach(field => {
                    field.style.display = 'none';
                }); 

                const inputFields = row.querySelectorAll('input.editable-field');
                inputFields.forEach(input => {
                    input.style.display = 'block';
                });

                editBtn.style.display = 'none';
                saveBtn.style.display = 'inline';
                cancelBtn.style.display = 'inline';
            }

            function saveEdit(id) {
                document.getElementById('edit-assg-id').value = id;
                document.getElementById('assigment').value = document.getElementById("assg-"+id).value;
                document.getElementById('recommend').value = document.getElementById("rec-"+id).value;
                document.getElementById('edit-assg').click();
            }

            function cancelEdit(button) {
                const row = button.parentNode.parentNode;
                const fields = row.querySelectorAll('.editable-field');
                const editBtn = row.querySelector('.edit-btn');
                const saveBtn = row.querySelector('.save-btn');
                const cancelBtn = row.querySelector('.cancel-btn');

                fields.forEach(field => {
                    field.style.display = 'inline';
                });

                const inputFields = row.querySelectorAll('input.editable-field');
                inputFields.forEach(input => {
                    input.style.display = 'none';
                });
                editBtn.style.display = 'inline';
                saveBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
            }
        </script>

        <?}}?>

    </div>
    </div>
    </div>
    </div>
    </div>
    <form action="" method="get">
        <input style="display: none;" type="submit" name="find-ptn" id="find-ptn" value="">
        <input style="display: none;" type="text" name="ptn-id" id="ptn-id">
    </form>

    <form action="<?$_SERVER["require-url"]?>" method="post">
        <input style="display: none;" type="submit" name="remove-assg" id="remove-assg">
        <input style="display: none;" type="text" name="assg-id" id="assg-id">
    </form>

    <form action="<?$_SERVER["require-url"]?>" method="post">
        <input style="display: none;" type="submit" name="edit-assg" id="edit-assg">
        <input style="display: none;" type="text" name="edit-assg-id" id="edit-assg-id">
        <input style="display: none;" type="text" name="assigment" id="assigment">
        <input style="display: none;" type="text" name="diagnosis" id="diagnosis">
        <input style="display: none;" type="text" name="recommend" id="recommend">
    </form>

    <script>
        function remove_assign(id) {
            if (confirm("Ви дійсно бажаєте видалити це призначення?")) {
                document.getElementById('assg-id').value = id;
                document.getElementById('remove-assg').click();
            }
        }
        const patientInput = document.getElementById('patientInput');
        const patientDropdown = document.getElementById('patientDropdown');
        var patients = JSON.parse('<?=json_encode($patients)?>');

        patientInput.addEventListener('input', function() {
            const searchText = patientInput.value.toLowerCase();

            patientDropdown.innerHTML = '';

            const filteredPatients = patients.filter(function(patient) {
                return patient.toLowerCase().includes(searchText);
            });

            filteredPatients.forEach(function(patient) {
                const option = document.createElement('a');
                option.classList.add('dropdown-item');
                option.textContent = patient;

                var regex = /\((\d+)\)/g;
                var matches = patient.match(regex);
                option.value = matches[0].substring(1, matches[0].length - 1);

                option.addEventListener('click', function() {
                    patientDropdown.innerHTML = '';
                    document.getElementById('ptn-id').value = option.value;
                    document.getElementById('find-ptn').click();
                });
                patientDropdown.appendChild(option);
            });

            if (filteredPatients.length > 0) {
                patientDropdown.style.display = 'block';
            } else {
                patientDropdown.style.display = 'none';
            }
        });

        window.addEventListener('click', function(event) {
            if (!patientInput.contains(event.target)) {
                patientInput.value = '';
                patientDropdown.style.display = 'none';
            }
        });
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>