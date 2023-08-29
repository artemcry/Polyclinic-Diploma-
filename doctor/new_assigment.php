
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

    if(isset($_POST["make-assg"])) {  
      preg_match_all('/\((\d+)\)/', $_POST["pacient-id"], $matches);
      $patient_id = $matches[1][0];
      $assg = crypt_str::en(sql_valid($_POST['assg']));
      $diagnoses = crypt_str::en(sql_valid($_POST['diagnoses']));
      $recomend = crypt_str::en(sql_valid($_POST['recomend']));

      $sql = "INSERT INTO assignments(doctor_id, patient_id, date, assignment, diagnosis, recommendations) VALUES
       ('$dctr_id','$patient_id', CURRENT_DATE(), '{$assg}','{$diagnoses}','{$recomend}')";
      
      if($link->query($sql)) {
        $msg = "<h5 id=error-msg style=\"color: green;\">Призначення збережено</h5>";
      }
      else {
        $msg = "<h5 id=error-msg style=\"color: red;\">Помилка збереження призначення: ".$link->error."</h5>";
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
                <h2 class="text-center">Нове призначення</h2>
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
                    <div class="form-group" id=descr-lbl>
                        <label for="descr">Призначення:</label>
                        <input type="text" class="form-control" name="assg" id="assg">
                    </div>
                    <div class="form-group" id=descr-lbl>
                        <label for="descr">Діагноз:</label>
                        <input type="text" class="form-control" name="diagnoses" id="diagnoses">
                    </div>
                    <div class="form-group" id=descr-lbl>
                        <label for="descr">Рекомендації:</label>
                        <input type="text" class="form-control" name="recomend" id="recomend">
                    </div>
                    <button type="submit" class="btn btn-primary mt-3 mx-auto d-block w-50" name="make-assg" id="enter-btn">Зберегти</button>
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
