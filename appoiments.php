<?
require_once 'dbconnect.php';


function get_appointments_time_for_date($doctorId,  $date) {
  $conn = DB::connect();
  $sql = "SELECT appointment_time FROM appointments WHERE doctor_id = $doctorId AND appointment_date = '$date'  AND status != 'Canceled'";
  $tm = mysqli_fetch_all($conn->query($sql));
  $appointments = array();
  foreach($tm as $t){
    $appointments[] = substr($t[0], 0, -3);
  }
  $conn->close();
  return $appointments;
}


function get_appointments_time_for_doctor($doctorId)
{
  $conn = DB::connect();
  $sql = "SELECT appointment_date, appointment_time FROM appointments WHERE doctor_id = $doctorId  AND status != 'Canceled'";
  $tm = mysqli_fetch_all($conn->query($sql));
  $map = [];
  foreach ($tm as $t) {
    $date = $t[0];
    $time = $t[1];
    if (!array_key_exists($date, $map)) {
      $map[$date] = []; 
    }
    $map[$date][] = substr($time, 0, -3); 
  }
  $conn->close();
  return $map;
}


function makeAppointment($doctor_id, $patient_id, $date, $time, $descr = 'NULL') {
  if($descr != 'NULL') {
    $descr = '\''.$descr.'\'';
  }
  $sql = "INSERT INTO appointments (doctor_id, patient_id, appointment_date, appointment_time, description) 
          VALUES ('$doctor_id', '$patient_id', '$date', '$time', $descr)";
  $link = DB::connect();

  $r = mysqli_query($link, $sql);
  $link->close();
  return $r;  
}

function removeAppointment($id) {
  $link = DB::connect();
  $sql = "DELETE FROM `appointments` WHERE `appointments`.`id` = $id";

  $r = mysqli_query($link, $sql);
  $link->close();
  return $r;  
}



