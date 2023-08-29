<?
require_once 'header.php';
require_once 'dbconnect.php';
$link = DB::connect();
$sql = "SELECT name, description, photo, price FROM services WHERE id = {$_GET['id']}";
$srvs = mysqli_fetch_row($link->query($sql));
$link->close();

?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Опис послуги</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="row">
            <div class="d-flex align-items-center mb-5">
                <button onclick="window.location.href = 'services.php'" type="button" class="btn btn-secondary ">
                    <i class="bi bi-arrow-left"></i> Назад
                </button>
                <h2 class="ms-1 mt-2">Огляд послуги</h2>
            </div>
            <div class="cnt">
    <div class="row">
        <div class="col-md-6">
            <div class="float-md-start me-md-3 mb-3 mb-md-0">
                <img src="<?=$srvs[2]?>" alt="Service Image" class="img-fluid">
            </div>
        </div>
        <div class="col-md-6">
            <p class="mb-1" style="font-size: 20px;">Назва: <br> <b><?=crypt_str::de($srvs[0])?></b></p>
            <p class="mb-1" style="font-size: 18px;">Вартість:<br> <b><?=crypt_str::de($srvs[3])?> Грн.</b></p>
            <p style="font-size: 18px;">Опис: <br> <b><?=crypt_str::de($srvs[1])?></b></p>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>
</div>

<style>
    .col-md-8 p {
        font-family: Arial, sans-serif;
        font-size: 16px;
        line-height: 1.5;
    }
    .cnt {
            padding: 10px;
            border: 1px solid lightgray;
            border-radius: 5px;
        }

   
</style>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
