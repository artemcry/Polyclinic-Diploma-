<? 
    require_once '../header.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Список записів на прийом до лікаря</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .appointment-list {
            margin-bottom: 20px;
        }

        .appointment-item {
            padding: 10px;
            border: 1px solid lightgray;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .appointment-item.completed {
            background-color: lightgray;
        }

        .confirmation-list {
            width: 250px;
        }

        .confirmation-item {
            padding: 10px;
            border: 1px solid lightgray;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .confirmation-item .small {
            font-size: 12px;
        }

        .section-divider {
            border-top: 1px solid #000000;
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
                $dctr_id = $_SESSION["user-id"];
                $link = DB::connect();
          
                $sql = "SELECT appointment_time FROM doctors WHERE user_id = $dctr_id";
                $apptm = mysqli_fetch_row($link->query($sql))[0];

                $sql = "SELECT appointments.appointment_date, appointments.appointment_time, appointments.description, users.full_name, users.phone, users.birth_date, appointments.id, users.email   
                FROM appointments JOIN users ON appointments.patient_id = users.user_id 
                WHERE appointments.status = 'Confrimed' AND doctor_id = $dctr_id 
                AND appointment_date = CURRENT_DATE() AND CURRENT_TIME() >= appointments.appointment_time AND CURRENT_TIME() <= DATE_ADD( appointments.appointment_time, INTERVAL $apptm MINUTE)";

                $curr_app = mysqli_fetch_row($link->query($sql));
                if(!empty($curr_app)) {
                ?>
                <h2>Поточний прийом</h2>
                <div class="appointment-item" style="background-color: #DCFFE5;">
                    <h4><?=crypt_str::de($curr_app[3])?></h4>
                    <p class="small mb-1">
                        <i class="bi bi-telephone"></i> <?=crypt_str::de($curr_app[4])?>
                    </p>
                    <p class="small mb-1">
                        <i class="bi bi-envelope"></i> <?=crypt_str::de($curr_app[7])?>
                    </p>
                    <p class="small">
                        <i class="bi bi-info-circle-fill"></i> <?=crypt_str::de($curr_app[2]) ? crypt_str::de($curr_app[2]) : '-'?>
                    </p>
                    <div class="mb-1">
                        <p><i class="bi bi-calendar-check mb-0"></i><b> <?=$curr_app[0]?> <?=substr($curr_app[1], 0 , -3)?></b></p>
                    </div>

                </div>
                <?}?>
                <h2>Прийняті записи</h2>
                <div class="appointment-list">
                    <?       
                    $sql = null;
                    if(isset($_POST["confrim-app"])) {
                        $sql = "UPDATE appointments SET status = 'Confrimed' WHERE id = {$_POST['app-id']}";
                    } else if(isset($_POST["not-confrim-app"]) || isset($_POST["cancel-app"])) {
                        $sql = "UPDATE appointments SET status = 'Canceled' WHERE id = {$_POST['app-id']}";
                    }
                    if($sql != null) {
                        $link->query($sql);         
                    }

                    $sql = "SELECT appointments.appointment_date, appointments.appointment_time, appointments.description, users.full_name, users.phone, users.birth_date, appointments.id, users.email  
                    FROM appointments JOIN users ON appointments.patient_id = users.user_id 
                    WHERE appointments.status = 'Confrimed' AND doctor_id = $dctr_id AND CONCAT(appointment_date, ' ', appointment_time) >= NOW()
                    ORDER BY appointments.appointment_date, appointments.appointment_time ASC";
                    $apps = mysqli_fetch_all($link->query($sql));
                    if(count($apps) == 0) {
                        echo "<p class='ml-5'>Поки немає записів..</p>";
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
                            ?>

                    <div class="appointment-item">                       
                        <h4><?=crypt_str::de($a[3])?></h4>
                        <p class="small mb-1">
                            <i class="bi bi-telephone"></i> <?=crypt_str::de($a[4])?>
                        </p>
                        <p class="small mb-1">
                            <i class="bi bi-envelope"></i> <?=crypt_str::de($a[7])?>
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

                    <script>
                        function cancel(id) {
                            if (confirm("Ви дійсно бажаєте скасувати цей запис?")) {
                                confrim(id);
                                document.getElementById('cancel-app').click();
                            }
                        }
                    </script>
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
                <?        
       
                    $sql = "SELECT appointments.appointment_date, appointments.appointment_time, appointments.description, users.full_name, users.phone, users.birth_date, appointments.id, users.email
                    FROM appointments JOIN users ON appointments.patient_id = users.user_id WHERE appointments.status = 'Not confrimed' AND doctor_id = $dctr_id 
                    ORDER BY appointments.appointment_date, appointments.appointment_time ASC";
                    $apps = mysqli_fetch_all($link->query($sql));
                ?>

            </div>
            <div class="col-4">
                <div class="d-flex justify-content-center mt-5">
                    <button class="btn btn-outline-dark btn-lg mb-3" style="width: 100%;" onclick="window.location.href = '/doctor/new_appointment.php'">Новий запис</button>
                </div>
                <div class="section-divider mb-2"></div>
                <h3>Підтвердження записів</h3>
                <div class="confirmation-list">

                    <form action="" method="post">
                        <input type="submit" style="display: none;" name="cancel-app" id=cancel-app>
                        <input type="text" style="display: none;" id="app-id" name="app-id" value="">
                        <? 
                        if(count($apps) == 0) {
                            echo "<p class='ml-5'>Поки немає записів..</p>";
                        }
                        foreach($apps as $a)  {?>
                        <div class="confirmation-item">
                            <h4><?=crypt_str::de($a[3])?></h4>
                            <p>
                                <i class="bi bi-calendar-check"></i><b> <?=$a[0]?> <?=substr($a[1], 0 , -3)?></b>
                            </p>

                            <p class="small mb-1">
                                <i class="bi bi-telephone"></i> <?=crypt_str::de($a[4])?>
                            </p>
                            <p class="small mb-1">
                                <i class="bi bi-envelope"></i> <?=crypt_str::de($a[7])?>
                            </p>
                            <p class="small">
                                <i class="bi bi-info-circle-fill"></i> <?=crypt_str::de($a[2]) ? crypt_str::de($a[2]) : '-'?>
                            </p>

                            <button onclick="confrim(<?=$a[6]?>);" name="confrim-app" class="btn btn-success">Прийняти</button>
                            <button onclick="if(confirm('Ви дійсно бажаєте відхилити цей запис?')) { confrim(<?=$a[6]?>);}" name="not-confrim-app" class="btn btn-danger">Відхилити</button>
                        </div>
                        <?}?>
                    </form>

                    <script>
                        function confrim(id) {
                            document.getElementById('app-id').value = id;
                        }

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
                    <?
                        function replaceString($a, $b, $str) {
                            if (strpos($str, $a) !== false) {
                                return str_replace($a, $b, $str);
                            } else {                               
                                return str_replace('?', '', $str) .'?'. $b;
                            }
                        }
                    ?>
                    <!-- Додайте аналогічні елементи для кожного запису -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>