<? 
session_start();
    require_once 'header.php';

    require_once 'dbconnect.php';
    require_once 'functions.php';?>


<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <title>Наші лікарі</title>
  <style>
    .doctor-card {
      margin-bottom: 20px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1 class="text-center mt-4">Наші лікарі</h1>
    <?
    $link = DB::connect();
    $sql = "SELECT doctors.specialization, doctors.education, users.full_name, users.phone, users.photo FROM doctors JOIN users ON doctors.user_id = users.user_id";
    $dctrs = mysqli_fetch_all($link->query($sql));

    ?><div class="row"><?
    foreach($dctrs as $d) {
        ?>
<div class="col-lg-4">
  <div class="card doctor-card">
    <div class="image-container">
      <img src="<?=$d[4]?>" class="card-img-top" alt="Doctor Image">
    </div>
    <div class="card-body">
      <h5 class="card-title"><?=crypt_str::de($d[2])?></h5>
      <p class="card-text">Спеціалізація: <strong><?=crypt_str::de($d[0])?></strong> </p>
        <p class="card-text">Освіта: <strong><?=crypt_str::de($d[1])?></strong> </p>

      <button onclick="window.location.href ='<?if(isset($_SESSION['user-id'])) echo 'patient/new_appointment.php'; else echo 'login.php';?>'" class="btn btn-secondary btn-block">Записатись на прийом</button>
    </div>
  </div>
</div>

    <?}?> 

    </div>
  </div>
    <style>
    .image-container {
        height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-container img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    </style>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>

</html>
