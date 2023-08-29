  <script>
  function updateTimeSlots() {
      var selectedDate = document.getElementById("date").value.replace(".", "-");
      var timeSelect = document.getElementById("time");
      while (timeSelect.firstChild) {
          timeSelect.firstChild.remove();
      }

      var apps = JSON.parse('<?=json_encode($apps)?>');

      var day = getDayOfWeek(selectedDate);
      var timeSlots = generateAvailableTimesByDay(day, getCurrentDate() === selectedDate);

      if (apps[selectedDate] !== undefined) {
          timeSlots = arrayDiff(timeSlots, apps[selectedDate]);
      }

      if (timeSlots.length > 0) {
          for (var i = 0; i < timeSlots.length; i++) {
              var option = document.createElement("option");
              option.text = timeSlots[i];
              timeSelect.add(option);
          }
          document.getElementById("time-lbl").style.display = "block";
          document.getElementById("enter-btn").disabled = "";
          document.getElementById("availability-msg").innerHTML = "";
      } else {

          document.getElementById("time-lbl").style.display = "none";
          document.getElementById("enter-btn").disabled = "disabled";
          document.getElementById("availability-msg").innerHTML = "Немає доступного часу на прийом";
      }
  }

  function arrayDiff(array1, array2) {
      return array1.filter(item => !array2.includes(item));
  }

  function getDayOfWeek(dateString) {
      var date = new Date(dateString);
      var daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

      var dayIndex = date.getDay();
      var dayOfWeek = daysOfWeek[dayIndex];

      return dayOfWeek;
  }

  function generateAvailableTimesByWeek() {
      var weeks = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
      var res = {};

      weeks.forEach(function(day) {
          res[day] = generateAvailableTimesByDay(day);
      });

      return res;
  }

  function generateAvailableTimesByDay(day, is_current_day=false) {                        
    var availableTimes = [];  
    <?
    
    $daysOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $doctorSchedules = array();
    
    $link = DB::connect();
    
    foreach ($daysOfWeek as $day) {
        $sql = "SELECT time_from, time_to, break_from, break_to FROM doctor_schedules WHERE doctor_id = $dctr_id AND day_of_week = '$day'";
        $result = $link->query($sql);
        $row = $result->fetch_assoc();
    
        $schedule = array(
            'time_from' => substr($row['time_from'], 0, -3),
            'time_to' => substr($row['time_to'], 0, -3),
            'break_from' => substr($row['break_from'], 0, -3),
            'break_to' => substr($row['break_to'], 0, -3)
        );
    
        $doctorSchedules[$day] = $schedule;
    }    
      $sql = "SELECT appointment_time FROM doctors WHERE user_id = $dctr_id";
      $tm = mysqli_fetch_row($link->query($sql));
      $appointmentDuration = $tm[0];
      $link->close();
    ?>

    var i = JSON.parse('<?=json_encode($doctorSchedules)?>')[day];
    var appointmentDuration = <?=$appointmentDuration?>;

    var currentTime = i["time_from"];
    while (currentTime < i["time_to"]) {
      if ((is_current_day && currentTime < getCurrentTime()) ||  (currentTime >= i["break_from"] && currentTime < i["break_to"])) {
        
        currentTime = addMinutes(currentTime, appointmentDuration);
        continue;
      }
      availableTimes.push(currentTime);
      currentTime = addMinutes(currentTime, appointmentDuration);
    }

    return availableTimes;
  }
  function getCurrentTime() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    return hours + ':' + minutes;
  }
  function getCurrentDate() {
    const now = new Date();
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0'); 
    const year = String(now.getFullYear());
    return year + '-' + month + '-' + day;
  }
  function addMinutes(time, minutes) {
    var [hours, mins] = time.split(':');
    var date = new Date();
    date.setHours(hours);
    date.setMinutes(mins);
    date.setMinutes(date.getMinutes() + minutes);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }
  </script>