<?

require_once '../importcss.html';
require_once '../dbconnect.php';
$msg = null;

if(isset($_POST["delete"])) {
    $link = DB::connect();
    $sql = "DELETE FROM services WHERE `id` = '{$_POST["id"]}'";
    if(mysqli_query($link, $sql)) {
        $msg = "<h5 id=error-msg style=\"color: green;\">Послуга видалена</h5>";
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
                <h2 class="ms-3 mt-1">Перелік послуг</h2>
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
            if(confirm("Ви дійсно бажаєте видалити цю послугу?")){
                document.getElementById('delete-srvc_'+id).click();
            }
        }
    </script>
               
        </div>
            <style>
                .service-table {
                    margin-top: 30px;
                }

                .service-table th {
                    text-align: center;
                }

                .service-table td {
                    vertical-align: middle;
                    text-align: center;
                }
                .service-actions {
                    display: flex;
                    justify-content: center;
                    vertical-align: middle;
                }
            </style>

            <body>
                <div class="container">
                    <table class="table service-table">
                        <thead>
                            <tr>
                                <th>Зображення</th>
                                <th>Назва</th>
                                <th>Опис</th>
                                <th>Вартість</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                             $link = DB::connect();
                             $sql = "SELECT * FROM services";
                             $srvcs = mysqli_fetch_all(mysqli_query($link, $sql));
                            foreach($srvcs as $s) { ?>
                                <tr>                           
                                    <td><img src="<?=$s[4]?>" height="30"></td>
                                    <td>
                                        <script>
                                            var text = "<?=crypt_str::de($s[1])?>";
                                            if (text.length > 25) {
                                            text = text.slice(0, 22) + "<b> ...</b>";
                                            }
                                            document.write(text);
                                        </script>
                                    </td>
                                    <td>
                                        <script>
                                            var text = "<?=crypt_str::de($s[3])?>";
                                            if (text.length > 25) {
                                            text = text.slice(0, 22) + "<b> ...</b>";
                                            }
                                            document.write(text);
                                        </script>
                                    </td>
                                    <td><?=crypt_str::de($s[2])?> грн.</td>
                                    <td class="service-actions">
                                        <button onclick="window.location.href = 'edit_service.php?id=<?=$s[0]?>'" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Редагувати</button>
                                        <form action="" method="post">
                                            <input style="display: none;" type="text" name=id value="<?=$s[0]?>">
                                            <input style="display: none;" type="submit" name="delete" id="delete-srvc_<?=$s[0]?>">
                                        </form>
                                        <button style="margin-left: 3px;" class="btn btn-danger btn-sm" onclick="return confirmDelete(<?=$s[0]?>);"><i class="bi bi-trash"></i> Видалити</button>
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