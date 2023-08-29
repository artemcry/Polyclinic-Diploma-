<? 
    require_once '../header.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Запис на прийом</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .appointment-list {
            margin-bottom: 20px;
        }

        .appointment-item {
            padding: 7px;
            border: 1px solid lightgray;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .appointment-item.completed {
            background-color: lightgray;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }

        .pagination button {
            background-color: #fff;
            color: #333;
            border: 1px solid #ddd;
            padding: 5px 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .pagination button:hover {
            background-color: #f5f5f5;
        }

        .pagination button[disabled] {
            color: #ccc;
            border-color: #ccc;
            cursor: default;
        }

        .pagination #prevBtn[disabled]::before {
            content: "\00ab";
        }

        .pagination #nextBtn[disabled]::before {
            content: "\00bb";
        }

        .pagination button::before {
            font-weight: bold;
        }

        .pagination #prevBtn::before {
            content: "\2039";
        }

        .pagination #nextBtn::before {
            content: "\203a";
        }

        .pagination .pageList {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 10px;
        }

        .pagination .pageList button {
            margin: 0 5px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .pagination .pageList button.active,
        .pagination .pageList button:hover {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="container mt-4">

        <div class="row">
            <div class="col-8">
                <?
                $patient_id = $_SESSION["user-id"];
                $link = DB::connect();
                if(isset($_POST["cancel-app"])) {
                    $sql = "UPDATE appointments SET status = 'Canceled' WHERE id = {$_POST['app-id']}";
                    $link->query($sql);     
                }
                ?>
                <form action="" method="post">
                    <input type="submit" style="display: none;" name="cancel-app" id=cancel-app>
                    <input type="text" style="display: none;" id="app-id" name="app-id" value="">
                </form>
                <?
                
                ?>
                <? 
                if(isset($_GET["page"])) {
                    $sql = "SELECT appointments.appointment_date, appointments.appointment_time, appointments.description, users.full_name, users.phone, users.email, appointments.status 
                    FROM appointments JOIN users ON appointments.doctor_id = users.user_id 
                    WHERE patient_id = $patient_id
                    ORDER BY appointments.appointment_date DESC, appointments.appointment_time DESC";
                    $apps = mysqli_fetch_all($link->query($sql));
                     ?>
                         <div class="d-flex align-items-center mb-5">

                    <button onclick="window.location.href = 'appointments.php'" type="button" class="btn btn-secondary ">
                        <i class="bi bi-arrow-left"></i> Назад
                    </button>
                    <h2 class="ms-3 mt-0">Історія записів</h2>
                    </div>


                    <div class="appointment-list">
                    <?       
                    if(count($apps) == 0) {
                        echo "<h2 class='mb-3'>Немає записів</h2>";
                    } 
                    $current_page = isset($_GET["page"]) ? $_GET["page"] : 1;
                    $s = 6;
                    $dcount = count($apps) ;
                    $total_pages = ceil($dcount/$s);
                    $f = $dcount == 0;
                    if(!$f) {
                        for($i = $s*($current_page-1); $i < $dcount && $i < $s*($current_page-1)+$s; $i++)
                        {         
                            $a = $apps[$i];
                            $st = $a[6];
                            if(strtotime($a[0].' '.$a[1]) <= time())
                                $st = "Завершено";
                            else if($st == "Confrimed")
                                $st = "Заплановано";
                            else if($st == "Not confrimed")
                                $st = "Не підтверджено";
                            else if($st == "Canceled")
                                $st = "Відмінено";
                            ?>

                        <div class="appointment-item">
                            <h4><?=crypt_str::de($a[3])?></h4>

                            <p class="small mb-1">
                                <i class="bi bi-telephone"></i> <?=crypt_str::de($a[4])?>
                            </p>
                            <p class="small mb-1">
                                <i class="bi bi-envelope"></i> <?=crypt_str::de($a[5])?>
                            </p>
                            <p class="small">
                                <i class="bi bi-info-circle-fill"></i> <?=(crypt_str::de($a[2]) ? crypt_str::de($a[2]) : '-')?>
                            </p>

                            <div class="mb-1">
                                <p type="button" class="float-right mr-3"><?=$st?></p>
                                <p><i class="bi bi-calendar-check mb-0"></i><b> <?=$a[0]?> <?=substr($a[1], 0 , -3)?></b></p>
                            </div>
                        </div>
                    <?}?>


                    <? if ($total_pages > 1) {?>
                    <div class="b-pager ">
                        <div data-bazooka="Paginator" data-pagination-pages-count="6" data-pagination-current-page="1" data-pagination-per-page="24" data-pagination-radius="1" data-pagination-link-class="b-pager__link" data-pagination-current-page-class="b-pager__link_type_current" data-pagination-first-item-class="b-pager__link_pos_first" data-pagination-last-item-class="b-pager__link_pos_last" data-pagination-dot-class="b-pager__dotted-link" data-pagination-symbol-previous="←" data-pagination-symbol-next="→" data-pagination-url-pattern="/g17035896-planshety/page_0" data-bazid="11">
                            <div class="pagination">
                                <button id="prevBtn">&laquo;</button>
                                <div class="pageList"></div>
                                <button id="nextBtn">&raquo;</button>
                            </div>
                        </div>
                        </div>

                        <?
                        }
                    }?>

                </div>
                <script>   
                          
                          const pagination = document.querySelector('.pagination');
                          const prevBtn = document.querySelector('#prevBtn');
                          const nextBtn = document.querySelector('#nextBtn');
                          const pageList = document.querySelector('.pageList');
                  
                          const currentPage = parseInt('<?=$current_page?>');
                          const totalPages = <?=$total_pages?>;
                          const maxVisiblePages = 5;
                  
                          let firstVisiblePage = currentPage - Math.floor(maxVisiblePages / 2);
                          if (firstVisiblePage < 1) {
                              firstVisiblePage = 1;
                          }
                          let lastVisiblePage = firstVisiblePage + maxVisiblePages - 1;
                          if (lastVisiblePage > totalPages) {
                              lastVisiblePage = totalPages;
                              firstVisiblePage = lastVisiblePage - maxVisiblePages + 1;
                              if (firstVisiblePage < 1) {
                                  firstVisiblePage = 1;
                              }
                          }
                       
                          function renderPageList() {
                              let pageListHtml = '';
                              if (firstVisiblePage > 1) {
                                  pageListHtml += '<button class="pageLink" data-page="' + (firstVisiblePage - 1) + '">...</button>';
                              }
                              for (let i = firstVisiblePage; i <= lastVisiblePage; i++) {
                                  pageListHtml += '<button class="pageLink' + (i === currentPage ? ' active' : '') + '" data-page="' + i + '">' + i + '</button>';
                              }
                              if (lastVisiblePage < totalPages) {
                                  pageListHtml += '<button class="pageLink" data-page="' + (lastVisiblePage + 1) + '">...</button>';
                              }
                              pageList.innerHTML = pageListHtml;
                          }
                  
                          renderPageList();
                  
                          prevBtn.disabled = currentPage === 1;
                          nextBtn.disabled = currentPage === totalPages;
                  
                          prevBtn.addEventListener('click', function() {
                              if (currentPage > 1) {
                                  location.href = '<?=replaceString("page=".strval($current_page),"page=".strval($current_page-1), basename($_SERVER['REQUEST_URI']))?>';
                              }
                          });
                  
                          nextBtn.addEventListener('click', function() {
                              if (currentPage < totalPages) {
                                  location.href = '<?=replaceString("page=".strval($current_page),"page=".strval($current_page+1), basename($_SERVER['REQUEST_URI']))?>';
                              }
                          });
                  
                          pageList.addEventListener('click', function(e) {
                              const pageLink = e.target.closest('.pageLink');
                              if (pageLink) {
                                  const page = parseInt(pageLink.dataset.page);
                                  if (page !== currentPage) {
                                      var s =
                                          location.href = replaceString('page=' + currentPage, 'page=' + page, '<?=basename($_SERVER['REQUEST_URI'])?>');
                                  }
                              }
                          });
                  
                          function replaceString(a, b, input) {
                              if (input.includes(a)) {
                                  return input.replace(a, b);
                              } else {                                    
                                  return input.replace('?', '') +'?'+ b;
                              }
                          }
                      </script>

                <?} else {
                    $sql = "SELECT appointments.appointment_date, appointments.appointment_time, appointments.description, users.full_name, users.phone, users.email,  appointments.id 
                    FROM appointments JOIN users ON appointments.doctor_id = users.user_id 
                    WHERE appointments.status = 'Not confrimed' AND patient_id = $patient_id AND CONCAT(appointment_date, ' ', appointment_time) >= NOW()
                    ORDER BY appointments.appointment_date, appointments.appointment_time ASC";
                    $apps = mysqli_fetch_all($link->query($sql));
                    if(count($apps) > 0) {
                        echo "<h2 class='mb-3'>Очікують підтвердження лікарем</h2>";
                    }    
                    foreach($apps as $a)
                    {         
                    ?>
                    <div class="appointment-item mb-2" style="background-color: #fcfce1;">
                        <h4><?=crypt_str::de($a[3])?></h4>
                        <p class="small mb-1">
                            <i class="bi bi-telephone"></i> <?=crypt_str::de($a[4])?>
                        </p>
                        <p class="small mb-1">
                            <i class="bi bi-envelope"></i> <?=crypt_str::de($a[5])?>
                        </p>
                        <p class="small">
                            <i class="bi bi-info-circle-fill"></i> <?=crypt_str::de($a[2]) ? crypt_str::de($a[2]) : '-'?>
                        </p>
                        <div class="mb-1">
                            <button type="button" onclick="cancel(<?=$a[6]?>)" class="btn btn-link text-secondary float-right">Скасувати</button>
                            <p><i class="bi bi-calendar-check mb-0"></i><b> <?=$a[0]?> <?=substr($a[1], 0 , -3)?></b></p>
                        </div>
                    </div>
                    <?}?>

                    <h2>Мої записи</h2>
                    <div class="appointment-list">
                        <?       
                        $sql = "SELECT appointments.appointment_date, appointments.appointment_time, appointments.description, users.full_name, users.phone, users.email,  appointments.id 
                        FROM appointments JOIN users ON appointments.doctor_id = users.user_id 
                        WHERE appointments.status = 'Confrimed' AND patient_id = $patient_id AND CONCAT(appointment_date, ' ', appointment_time) >= NOW()
                        ORDER BY appointments.appointment_date, appointments.appointment_time ASC";
                        $apps = mysqli_fetch_all($link->query($sql));
                        if(count($apps) == 0) {
                            echo "<p class='ml-5'>Поки немає записів..</p>";
                        }    
                        
                        foreach($apps as $a)
                        {         
                        ?>
                        <div class="appointment-item">
                            <h4><?=crypt_str::de($a[3])?></h4>
                            <p class="small mb-1">
                                <i class="bi bi-telephone"></i> <?=crypt_str::de($a[4])?>
                            </p>
                            <p class="small mb-1">
                                <i class="bi bi-envelope"></i> <?=crypt_str::de($a[5])?>
                            </p>
                            <p class="small">
                            <i class="bi bi-info-circle-fill"></i> <?=crypt_str::de($a[2]) ? crypt_str::de($a[2]) : '-'?>
                            </p>
                            <div class="mb-1">
                                <button type="button" onclick="cancel(<?=$a[6]?>)" class="btn btn-link text-secondary float-right">Скасувати</button>
                                <p><i class="bi bi-calendar-check mb-0"></i><b> <?=$a[0]?> <?=substr($a[1], 0 , -3)?></b></p>
                            </div>
                        </div>
                        <?}?>
                    <?}?>
                    

                </div>
            </div>                        
            <?if(!isset($_GET["page"])) {?>
            <div class="col-4">
                <div class="d-flex justify-content-center mt-5">
                    <button class="btn btn-outline-dark btn-lg mb-2" style="width: 100%;" onclick="window.location.href = '/patient/new_appointment.php'">Новий запис</button>
                </div>
                <div class="d-flex justify-content-center mt-1">
                        <button class="btn btn-outline-dark btn-lg mb-3" onclick="document.getElementById('history').click();" style="width: 100%;" onclick="">Історія записів</button>
                </div>
            </div>
            <?}?>

            <form action="" method="get">
                <input type="submit" id="history"  style="display: none;" name="page" value="1">
            </form>
        </div>
    </div>
    <script>
        function cancel(id) {
            if (confirm("Ви дійсно бажаєте скасувати цей запис?")) {
                document.getElementById('app-id').value = id;
                document.getElementById('cancel-app').click();
            }
        } 
    </script>
    
    <?
        function replaceString($a, $b, $str) {
            if (strpos($str, $a) !== false) {
                return str_replace($a, $b, $str);
            } else {                               
                return str_replace('?', '', $str) .'?'. $b;
            }
        }
                    ?>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>