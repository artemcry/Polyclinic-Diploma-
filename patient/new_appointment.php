<?
require_once '../header.php';
require_once '../dbconnect.php';
require_once '../appoiments.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Запис на прийом</title>
</head>

<?
  $link = DB::connect();

  $dctr_id = $_POST["doctor-id"];
  $patient_id = $_SESSION["user-id"];
  $msg = null;

  if(isset($_POST["make-app"])) {   
    $sql = "INSERT INTO `appointments`(`doctor_id`, `patient_id`, `appointment_time`, `description`, `appointment_date`, `status`) 
    VALUES ($dctr_id, $patient_id,'{$_POST['time']}', '{$_POST['descr']}', '{$_POST['date']}', 'Not confrimed')";
    
    if($link->query($sql)) {
        header("Location: appointments.php");
        exit();
    }
    else {
      $msg = "<h5 id=error-msg style=\"color: green;\">Помилка створення запису: ".$link->error."</h5>";
    }
  }


  $sql = "SELECT user_id, full_name FROM `users` WHERE user_id IN (SELECT user_id FROM doctors)";
  $dctrs = mysqli_fetch_all($link->query($sql));
  
  $dctr_id = $dctrs[0][0];
  if(isset($_POST["chdoctor"])){
    $dctr_id = $_POST['doctor-id'];
  }
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
                        <label for="doctor">Лікар:</label>
                        <input type="submit" style="display: none" name="chdoctor" id="chdoctor">
                        <select class="form-control" id="doctor" name="doctor-id" onchange="document.getElementById('chdoctor').click();" name="doctor">
                            <?foreach($dctrs as $d) { ?>
                              <option <?if($d[0] == $dctr_id) echo "selected";?> value="<?=$d[0]?>"><?=crypt_str::de($d[1])?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Дата:</label>
                        <input type="date" name="date" class="form-control" id="date" min="<?php echo date('Y-m-d'); ?>" onchange="updateTimeSlots()">
                    </div>
                    <div id="availability-msg" class="mt-3 mb-2"></div>

                    <div class="form-group" id=time-lbl>
                        <label for="time">Час:</label>
                        <select name="time" class="form-control" id="time">
                        </select>
                    </div>

                    <div class="form-group" id=descr-lbl>
                        <label for="descr">Опис:</label>
                        <input type="text" class="form-control" name="descr" id="descr">
                    </div>
                    <button type="submit" name="make-app" disabled class="btn btn-primary mt-3" id=enter-btn>Записатись</button>
                </form>
            </div>
        </div>
    </div>

    <script>

</script>

<?
require_once '../new_appointment.php';

?>
</body>
</html>
