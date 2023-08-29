<?
require_once '../header.php';
require_once '../dbconnect.php';
require_once '../appoiments.php';
require_once '../functions.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Запис на прийом</title>
</head>

<?
    $msg = null;
    $dctr_id = $_SESSION["user-id"];
    $link = DB::connect();

    if(isset($_POST["make-app"])) {  
      preg_match_all('/\((\d+)\)/', $_POST["pacient-id"], $matches);
      $patient_id = $matches[1][0];
      $descr = crypt_str::en(sql_valid($_POST['descr']));
      $sql = "  INSERT INTO `appointments`(`doctor_id`, `patient_id`, `appointment_time`, `description`, `appointment_date`, `status`) 
      VALUES ($dctr_id, $patient_id,'{$_POST['time']}', '{$descr}', '{$_POST['date']}', 'Confrimed')";
      if($link->query($sql)) {
        $msg = "<h5 id=error-msg style=\"color: green;\">Запис створено успішно</h5>";
      }
      else {
        $msg = "<h5 id=error-msg style=\"color: green;\">Помилка створення запису: ".$link->error."</h5>";
      }
    }

    $sql = "SELECT user_id, full_name, birth_date FROM users WHERE user_id IN (SELECT user_id FROM patients)";
    $ptnts = mysqli_fetch_all($link->query($sql));
    $apps = get_appointments_time_for_doctor($dctr_id);
  
?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <h2 class="text-center">Запис на прийом</h2>
                <?=$msg?>
                <script>
                    window.addEventListener('load', function() {
                        setTimeout(function() {
                            var element = document.getElementById('error-msg');
                            element.style.display = 'none';
                        }, 3000);
                    });
                </script>
                <form method="POST" action="">
                    <div class="form-group">
                    <div class="form-group" style="position: relative;">
                      <label for="patientInput">Пацієнт</label>
                      <input type="text" autocomplete="off" class="form-control" id="patientInput" name="pacient-id" placeholder="Введіть ім'я пацієнта">
                      <div id="patientDropdown" class="dropdown-menu" style="position: absolute; width: 100%; top: 100%;"></div>
                    </div>                      
                    </div>
                    <div class="form-group">
                        <label for="date">Дата:</label>
                        <input type="date" name="date" class="form-control" id="date" min="<?php echo date('Y-m-d'); ?>" onchange="updateTimeSlots()">
                    </div>
                    <div id="availability-msg" class="mt-3 mb-2"></div>

                    <div class="form-group" id=time-lbl>
                        <label for="time">Час:</label>
                        <select name="time" class="form-control" id="time">
                            <!-- Початково пустий список часу -->
                        </select>
                    </div>
                    <div class="form-group" id=descr-lbl>
                        <label for="descr">Опис:</label>
                        <input type="text" class="form-control" name="descr" id="descr">
                    </div>
                    <button type="submit" disabled class="btn btn-primary mt-3" name="make-app" id=enter-btn>Записати пацієнта</button>
                </form>
            </div>
        </div>
    </div>


    <?
    $patients = array();
    foreach($ptnts as $d) { 
      $patients[] = crypt_str::de($d[1])." ".crypt_str::de($d[2])." (".$d[0].")";  
    }
    
    ?>
    <script>
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
                option.href = '#';
                option.textContent = patient;
                option.addEventListener('click', function() {
                  patientInput.value = patient;
                  patientDropdown.innerHTML = '';
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
                patientDropdown.style.display = 'none';
              }
            });

</script>

<?
require_once '../new_appointment.php';

?>
        
</body>
</html>
