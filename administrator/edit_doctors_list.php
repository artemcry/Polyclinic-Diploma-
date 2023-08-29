<?

require_once '../importcss.html';
require_once '../dbconnect.php';
$msg = null;
if(isset($_POST["delete"])) {
    $link = DB::connect();
    $usr = "DELETE FROM `users` WHERE `user_id` = '{$_POST["id"]}'";
    $dct = "DELETE FROM `doctors` WHERE `user_id` = '{$_POST["id"]}'";
    if(mysqli_query($link, $dct) && mysqli_query($link, $usr)) {
        $msg = "<h5 id=error-msg style=\"color: green;\">Користувач видалений</h5>";
    } else {
        $msg = "<h5 id=error-msg style=\"color: red;\">Помилка видалення</h5>";
    }    
}
?>


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-5">
                <button onclick="window.location.href = 'main.php'" type="button" class="btn btn-secondary ">
                    <i class="bi bi-arrow-left"></i> Назад
                </button>
                <h2 class="ms-3 mt-1">Список лікарів</h2>
                </div>     
                <?=$msg?>
                <script>
                window.addEventListener('load', function() {
                setTimeout(function() {
                    var element = document.getElementById('error-msg');
                    element.style.display = 'none';
                 }, 3000);
        });
        function confirmDelete(id) {
            if(confirm("Ви дійсно бажаєте видалити цього лікаря?")){
                document.getElementById('delete-dctr_'+id).click();
            }
        }
    </script>
               
        </div>
            <style>
                .doctor-table {
                    margin-top: 30px;
                }

                .doctor-table th {
                    text-align: center;
                }

                .doctor-table td {
                    vertical-align: middle;
                    text-align: center;
                }
                .doctor-actions {
                    display: flex;
                    justify-content: center;
                    vertical-align: middle;
                }
            </style>

            <body>
                <div class="container">
                    <table class="table doctor-table">
                        <thead>
                            <tr>
                                <th>Фото профілю</th>
                                <th>Повне ім'я</th>
                                <th>Дата народження</th>
                                <th>Телефон</th>
                                <th>Email</th>
                                <th>Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                             $link = DB::connect();
                             $sql = "SELECT * FROM `doctors`";
                             $dctr = mysqli_fetch_all(mysqli_query($link, $sql));
                             $sql = "SELECT * FROM `users` WHERE `role`='Doctor'";
                             $users = mysqli_fetch_all(mysqli_query($link, $sql));
                            foreach($users as $u) { ?>
                                <tr>                           
                                    <td><img src="<?=$u[8]?>" alt="Фото профілю" height="30"></td>
                                    <td><?=crypt_str::de($u[6])?></td>
                                    <td><?=crypt_str::de($u[7])?></td>
                                    <td><?=crypt_str::de($u[4])?></td>
                                    <td><?=crypt_str::de($u[5])?></td>
                                    <td class="doctor-actions">
                                        <button onclick="window.location.href = 'edit_doctor.php?id=<?=$u[0]?>'" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Редагувати</button>
                                        <form action="" method="post">
                                            <input style="display: none;" type="text" name=id value="<?=$u[0]?>">
                                            <input style="display: none;" type="submit" name="delete" id="delete-dctr_<?=$u[0]?>">
                                        </form>
                                        <button style="margin-left: 3px;" class="btn btn-danger btn-sm" onclick="return confirmDelete(<?=$u[0]?>);"><i class="bi bi-trash"></i> Видалити</button>
                                    </td>
                                </tr> 
                            <? }
                            $link->close();?>                       
                        </tbody>
                    </table>
                </div>
            </body>

    </div>
</div>