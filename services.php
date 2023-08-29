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
  <title>Наші послуги</title>
  <style>
    .doctor-card {
      margin-bottom: 20px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1 class="text-center mt-4">Наші послуги</h1>
    <form method="GET" action="">
        <div class="form-group">
            <div class="form-group" style="position: relative; ">
            <div class="input-group">
                <input autocomplete="off" type="text" style="height: 48px;" class="form-control" id="serviceInput" name="pacient-id" placeholder="Пошук послуг">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                </div>
                <div id="serviceDropdown" class="dropdown-menu" style="position: absolute; width: 100%; top: 100%;"></div>
            </div>  
    </form>
    <?
    $link = DB::connect();
    $sql = "SELECT name, id, photo FROM services";
    $srvcs = mysqli_fetch_all($link->query($sql));
    $count = count($srvcs) > 6 ? 6 : count($srvcs);
    if(isset($_POST["more"]))
        $count = count($srvcs);


    ?><div class="row"><?
    for($i = 0; $i < $count; $i ++) {
        $s= $srvcs[$i];
        ?>
    <div class="col-lg-4">
    <div class="card doctor-card">
        <div class="image-container">
        <img src="<?=$s[2]?>" class="card-img-top" alt="Doctor Image">
        </div>
        <div class="card-body">
        <h5 class="card-title"><?=crypt_str::de($s[0])?></h5>
        <a href="/service.php?id=<?=$s[1]?>" class="btn btn-link float-right">Детальніше...</a>
        </div>
    </div>
    </div>
    <?}?> 
    </div>

    <?if(!isset($_POST["more"])) {?>
        <form action="" method="post">
        <button type="submit" class="btn btn-secondary btn-block mx-auto" name="more" value="1">Показати більше</button>
        </form>
    <?}
        
        
        $services = array();
        foreach($srvcs as $d) { 
            $services[] = $d[0]." (".$d[1].")";  
        }
    ?>

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
    <script>


        var services = JSON.parse('<?=json_encode($services)?>');

        serviceInput.addEventListener('input', function() {
            const searchText = serviceInput.value.toLowerCase();

            serviceDropdown.innerHTML = '';

            const filteredServices = services.filter(function(service) {
                return service.toLowerCase().includes(searchText);
            });

            filteredServices.forEach(function(service) {
                const option = document.createElement('a');
                option.classList.add('dropdown-item');
                option.textContent = service;

                var regex = /\((\d+)\)/g;
                var matches = service.match(regex);
                option.value = matches[0].substring(1, matches[0].length - 1);

                option.addEventListener('click', function() {
                    serviceDropdown.innerHTML = '';
                    window.location.href = '/service.php?id='+option.value;
                });
                serviceDropdown.appendChild(option);
            });

            if (filteredServices.length > 0) {
                serviceDropdown.style.display = 'block';
            } else {
                serviceDropdown.style.display = 'none';
            }
        });

        window.addEventListener('click', function(event) {
            if (!serviceInput.contains(event.target)) {
                serviceInput.value = '';
                serviceDropdown.style.display = 'none';
            }
        });
    </script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>

</html>
