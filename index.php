<?
require_once 'header.php';?>

<!DOCTYPE html>
<html>
<head>
    <title>Моя Клініка</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
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
<header class="mb-5">
            <img src="/assets/images/banner.png" alt="Банер" class="img-fluid">
        </header>
    <div class="container">

        <?
        $link = DB::connect();
        $sql = "SELECT name, id, photo FROM services LIMIT 3";
        $srvcs = mysqli_fetch_all($link->query($sql));
        $link = DB::connect();
        $sql = "SELECT doctors.specialization, users.full_name, users.photo FROM doctors JOIN users ON doctors.user_id = users.user_id LIMIT 3";
        $dctrs = mysqli_fetch_all($link->query($sql));
    
        ?>
        <main>
            <section class="services mb-5">
                <h2 class="text-center mt-4">Наші Послуги</h2>
                <div class="row">
                  <?  foreach($srvcs as $s) {?>
                <div class="col-lg-4">
                <div class="card doctor-card">
                    <div class="image-container">
                    <img src="<?=$s[2]?>" class="card-img-top" alt="Doctor Image">
                    </div>
                    <div class="card-body">
                    <h5 class="card-title"><?=crypt_str::de($s[0])?></h5>
                    <a href="/service.php?id=<?=$s[1]?>"  style="color: #7ca374" class="btn btn-link float-right">Детальніше...</a>
                    </div>
                </div>
                </div>
                <?}?>   
                <div class="d-flex justify-content-center align-items-center">
                    <button onclick="window.location.href ='services.php'"  type="button" class="btn btn-link text-secondary font-weight-bold btn-lg">Дивитись більше..</button>
                </div>
           
                </div>
            </section>
            
            <section class="doctors mb-5">
                <h2 class="text-center">Наші Лікарі</h2>
                <div class="row">
               <? foreach($dctrs as $d) {?>
            <div class="col-lg-4">
            <div class="card doctor-card">
                <div class="image-container">
                <img src="<?=$d[2]?>" class="card-img-top" alt="Doctor Image">
                </div>
                <div class="card-body">
                <h5 class="card-title"><?=crypt_str::de($d[1])?></h5>
                <p class="card-text">Спеціалізація: <strong><?=crypt_str::de($d[0])?></strong> </p>
                <button onclick="window.location.href ='doctors.php'" class="btn btn-secondary btn-block">Детальніше..</button>
                </div>
            </div>
            </div>   
            <?}?>   
                <div class="d-flex justify-content-center align-items-center">
                    <button type="button" onclick="window.location.href ='doctors.php'" class="btn btn-link text-secondary font-weight-bold btn-lg">Дивитись всіх...</button>
                </div>       
                </div>
            </section>
        </main>
        
        <footer class="mt-5">
            <ul class="list-unstyled d-flex justify-content-center">
                <li class="mx-3"><a href="/">My Clinic 2023</a></li>
            </ul>
        </footer>
    </div>
</body>
</html>
