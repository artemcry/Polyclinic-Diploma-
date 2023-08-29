<?
require_once '../header.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Редагування розкладу роботи лікаря</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .day-section {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-3 mb-3">Редагування розкладу роботи</h1>
        <form action="" method="post">
            <div class="row">

                <?
                    $daysUA = ["Понеділок", "Вівторок", "Середа", "Четвер", "П'ятниця", "Субота", "Неділя"];
                    $daysEN = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    $id = $_SESSION["user-id"];
                    $link = DB::connect();

                    if(isset($_POST["save"])) {
                        foreach($daysEN as $d) {
                            $sql = "UPDATE doctor_schedules SET time_from = '{$_POST['time-from-'.$d]}', time_to = '{$_POST['time-to-'.$d]}',
                             break_from = '{$_POST['break-from-'.$d]}', break_to = '{$_POST['break-to-'.$d]}'
                             WHERE doctor_id = $id AND day_of_week = '$d'";
                            $link->query($sql);
                        }   
                        $sql = "UPDATE doctors SET appointment_time = {$_POST['appointment-time']}";
                        $link->query($sql);
                    }                                    
                    $sql = "SELECT day_of_week, time_from, time_to, break_from, break_to FROM doctor_schedules WHERE doctor_id = $id";

                    $sch = $link->query($sql);
                    if($sch) {
                        $sch = mysqli_fetch_all($sch);
                    }
                    for($i = 0; $i < 7; $i++) {
                        $t = ["00:00","00:00","00:00","00:00","00:00"];
                        foreach($sch as $s) {
                            if($s[0] === $daysEN[$i]) {
                                $t = $s;
                            }
                        }                                                                    
                    ?>
                <div class="col-md-4">
                    <div class="day-section">
                        <h3><?=$daysUA[$i]?></h3>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="monday-from">Час роботи від</label>
                                <input type="time" value="<?=$t[1]?>" class="form-control" id="monday-from" name="time-from-<?=$daysEN[$i]?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="monday-to">Час роботи до</label>
                                <input type="time" value="<?=$t[2]?>" class="form-control" id="monday-to" name="time-to-<?=$daysEN[$i]?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="monday-break-from">Перерва від</label>
                                <input type="time" value="<?=$t[3]?>" class="form-control" id="monday-break-from" name="break-from-<?=$daysEN[$i]?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="monday-break-to">Перерва до</label>
                                <input type="time" value="<?=$t[4]?>" class="form-control" id="monday-break-to" name="break-to-<?=$daysEN[$i]?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <button type="button" onclick="resetTimeFields('<?=$daysEN[$i]?>')" class="btn btn-link text-secondary float-right">Встановити вихідним</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?}?>
                <script>
                    function resetTimeFields(day) {
                        var timeInputs = document.getElementsByName("time-from-" + day);
                        for (var i = 0; i < timeInputs.length; i++) {
                            timeInputs[i].value = "00:00";
                        }
                        var timeToInputs = document.getElementsByName("time-to-" + day);
                        for (var i = 0; i < timeToInputs.length; i++) {
                            timeToInputs[i].value = "00:00";
                        }
                        var breakFromInputs = document.getElementsByName("break-from-" + day);
                        for (var i = 0; i < breakFromInputs.length; i++) {
                            breakFromInputs[i].value = "00:00";
                        }
                        var breakToInputs = document.getElementsByName("break-to-" + day);
                        for (var i = 0; i < breakToInputs.length; i++) {
                            breakToInputs[i].value = "00:00";
                        }
                    }
                </script>

            </div>
            <?                 
                $sql = "SELECT appointment_time from doctors WHERE user_id = $id";
                $at = mysqli_fetch_all($link->query($sql))[0][0];
                $link->close();
            ?>

            <div class="form-group mb-4">
                <fieldset class="border p-2 rounded">
                    <h4 class="mt-1 mb-4">Тривалість прийому</h4>
                    <div class="text-left">
                        <select class="form-control mb-3" name="appointment-time">
                      
                        <?for ($i = 10; $i <= 120; $i += 10) {?>
                            "<option <?if($i == $at) echo "selected";?> value="<?=$i?>"><?=$i?> xв.</option>";
                        <?}
                        ?>
                        </select>
                    </div>
                </fieldset>
            </div>


            <div class="form-group">
                <div class="text-center">
                    <input type="submit" class="btn btn-primary" style="width: 350px" name="save" value="Зберегти">
                </div>
            </div>

        </form>
    </div>
</body>

</html>