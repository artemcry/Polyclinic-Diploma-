<?
    require_once '../importcss.html';
    require_once '../functions.php';
    $link = DB::connect();

?>
<link rel="stylesheet" type="text/css" href="/css/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<script src="/nodejs/node_modules/jquery/src/jquery.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">              
        <div class="d-flex align-items-center mb-5">
            <button onclick="window.location.href = 'main.php'" type="button" class="btn btn-secondary ">
                <i class="bi bi-arrow-left"></i> Назад
            </button>
            <h2 class="ms-3 mt-1">Генерація звітів</h2>
        
        </div>
        <?
        $sql = "SELECT user_id, full_name FROM `users` WHERE user_id IN (SELECT user_id FROM doctors)";
        $dctrs = mysqli_fetch_all($link->query($sql));
        ?>    
        <div class="d-flex justify-content-center mt-5">
            <div class="d-flex flex-column align-items-right" style="width: 600px">
      
                <?if(isset($_POST["show-visits"])){?>
                    <h4>Кількість візитів за період</h4>
                    <div> 
                        <?
                        function find_el($arr, $el) {
                            foreach($arr as $a) {
                                if($a[0] == $el)
                                    return $a[1];
                            }
                            return null;
                        }
                        $did = $_POST["doctor"];
                        $d = " doctor_id = ".$did." AND ";

                        $dname = 'Всі лікарі';
                        if($did === "all") 
                            $d = "";                                                
                        else 
                            $dname = crypt_str::de(find_el($dctrs, $did));


                        $dfrom = $_POST["from-date"];
                        $dto = $_POST["to-date"];
                        $sql = "SELECT COUNT(*) AS total_visits
                        FROM appointments WHERE ".$d." appointment_date >= '{$dfrom}' AND appointment_date <= '$dto'";
                        $cnt = mysqli_fetch_row($link->query($sql))[0];
                        ?>
                       <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Лікар</th>
                                <th>Дата від</th>
                                <th>Дата до</th>
                                <th>Кількість візитів</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?=$dname?></td>
                                <td><?=$dfrom?></td>
                                <td><?=$dto?></td>
                                <td><?=$cnt?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <h4 class="mt-3 mb-2">Інша статистика:</h4>
                <?} 
                else if(isset($_POST["age-stat"])) {            
                    $sql = "SELECT birth_date FROM users WHERE users.user_id IN (SELECT user_id FROM patients)";
                    $dates = mysqli_fetch_all($link->query($sql));
                    foreach ($dates as &$date) {
                        $date[0] = crypt_str::de($date[0]);
                    }
                    ?>                    
                    <h4>Вік пацієнтів</h4>
                    <div class="container">
                        <canvas id="ageChart"></canvas>
                    </div>

                    <script>                        
                        var phpArray = JSON.parse('<?=json_encode($dates)?>');                
                        
                        function calculateAge(birthday) {
                            var ageDate = new Date(Date.now() - new Date(birthday).getTime());
                            return Math.abs(ageDate.getUTCFullYear() - 1970);
                        }                                                
                        var ageLabels = ['0-10', '10-20', '20-30', '30-40', '40-50', '50-60', '60-70', '70-80', '80-90', '90-100', '100+'];
                        var ageData = new Array(ageLabels.length).fill(0);                                        
                        phpArray.forEach(function(date) {
                            if (date !== null) {
                                var age = calculateAge(date);
                                if (age < 100) {
                                    var index = Math.floor(age / 10);
                                    ageData[index]++;
                                } else {
                                    ageData[ageLabels.length - 1]++;
                                }
                            }
                        });                                                
                        var ctx = document.getElementById('ageChart').getContext('2d');
                        var ageChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ageLabels,
                                datasets: [{
                                    label: 'Кількість пацієнтів',
                                    data: ageData,
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        stepSize: 1
                                    }
                                }
                            }
                        });
                    </script>
                    <h4 class="mt-3 mb-2">Інша статистика:</h4>

                <?}             
                else if(isset($_POST["visits-stat"])) {
                    $sql = "SELECT u.full_name, COUNT(a.doctor_id) AS count FROM users u  JOIN appointments a ON a.doctor_id = u.user_id GROUP BY u.full_name";
                    $visits = mysqli_fetch_all($link->query($sql));
                    foreach ($visits as &$visit) {
                        $visit[0] = crypt_str::de($visit[0]);
                    }
                    
                ?>
                <h4>Кількість візитів у лікарів</h4>
                <canvas id="visitsChart"></canvas>
                <script>                    
                    var visitsData = <?php echo json_encode($visits); ?>;                    
                    var labels = visitsData.map(function(row) {
                        return row[0];
                    });
                    var data = visitsData.map(function(row) {
                        return row[1];
                    });                
                    var ctx = document.getElementById('visitsChart').getContext('2d');
                    
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Кількість візитів',
                                data: data,
                                backgroundColor: 'rgba(75, 192, 192, 0.8)'
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    stepSize: 1
                                }
                            }
                        }
                    });
                </script>
                <h4 class="mt-3 mb-2">Інша статистика:</h4>
                <?}?>
                <button onclick="vs_click()" class="btn btn-secondary mb-1">Кількість візитів за період</button>
                <div id="visits" style="display: none;"> 
                    <form action="" method="post">
                    <div class="form-group  mb-2">
                        <label for="doctor">Лікар:</label>
                        <select class="form-control" id="doctor" name="doctor" name="doctor">
                            <option value="all">- Всі лікарі-</option>
                            <?php foreach($dctrs as $d) { ?>
                            <option value="<?=$d[0]?>"><?=crypt_str::de($d[1])?></option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="form-group  mb-2">
                        <label for="fromDate">Від:</label>
                        <input type="date" class="form-control" id="fromDate" name="from-date">
                    </div>
                    
                    <div class="form-group mb-2">
                        <label for="toDate">До:</label>
                        <input type="date" class="form-control" id="toDate" name="to-date">
                    </div>
                    <button name="show-visits" class="btn btn-secondary mb-1 w-100">Генерувати звіт</button>
                    </form>

                </div>
                <form action="" method="post">
                    <button name="age-stat" class="btn btn-secondary mb-1 w-100">Вік пацієнтів</button>
                    <button name="visits-stat" class="btn btn-secondary mb-0 w-100">Візити у лікарів</button>
                </form>
                
            </div>
        </div>
        <script>
            function vs_click()
            {           
                 var vs = document.getElementById('visits').style; 
                 vs.display = (vs.display === 'block') ?'none' :'block';                
            }
        </script>
    </div>
  </div>
</div>