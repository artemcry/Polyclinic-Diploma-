
<?
    require_once '../importcss.html';
    require_once '../functions.php';
    $link = DB::connect();
    if (isset($_POST["upd_srvc"])) {
        $name = crypt_str::en(sql_valid($_POST["name"]));
        $descr = crypt_str::en(sql_valid($_POST["descr"]));
        $price = crypt_str::en($_POST["price"]);
        $pimg = $_POST["service-image"];
        $id = $_GET["id"];

        $sqp = "";
        if($_FILES['service-image']['name'] != "") {
            $img = saveIMG($_FILES['service-image']['name'], $_FILES['service-image']['size'], $_FILES['service-image']['tmp_name']);
            $sqp .= ", photo = '".$img ."'";
        } 
        $sql = "UPDATE services SET name = '$name', price = '$price', description = '$descr'".$sqp." WHERE id = $id";

        if($link->query($sql)) {
            $msg = "<h5 id=error-msg style=\"color: green;\">Успішно змінено</h5>";
        } else {
            $msg = "<h5 id=error-msg style=\"color: red;\">Помилка :".$r[1]."</h5>";
            unlink($img);
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
    <h2 class="ms-3 mt-1">Додавання послуги</h2>
    </div>
    <?=$msg?>
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                var element = document.getElementById('error-msg');
                element.style.display = 'none';
            }, 3000);
        });
    </script>
    <?
    
    $sql = "SELECT * FROM services WHERE id = {$_GET["id"]}";
    $s = mysqli_fetch_row(mysqli_query($link, $sql));
    ?>
      <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label for="name">Назва</label>
          <input type="text" id="name" name="name" class="form-control mb-2" value="<?=crypt_str::de($s[1])?>">
        </div>
        <div class="form-group">
          <label for="descr">Опис</label>
          <input type="text" id="descr" name="descr"  class="form-control mb-2" value="<?=crypt_str::de($s[3])?>">
        </div> 
        <div class="form-group">
          <label for="price">Ціна</label>
          <input type="text" id="price" name="price"  class="form-control mb-2" value="<?=crypt_str::de($s[2])?>">
        </div>   

        <div class="form-group mt-1">
          <label for="service-image">Нове зображення (Не обов'язково)</label><br>
          <input type="file" id="service-image" name="service-image"  class="form-control mb-2-file mt-1 mb-2" accept="image/*">
        </div>
        <input type="submit" name="upd_srvc" id="upd_srvc" style="display: none;">
        </form>

        <button onclick="submitForm()" class="btn btn-primary mx-auto d-block w-50 mt-3 mb-3">Зберегти</button>
    </div>
  </div>
</div>


<script>
    function validateForm() {
        var name = document.getElementById("name").value;
        var descr = document.getElementById("descr").value;
        var price = document.getElementById("price").value;

        if (name === '' || descr === '' || price === '' ) {
            alert("Будь ласка, заповніть всі поля");
            return false; 
        }
        return true; 
    }

    function submitForm() {
        if (validateForm()) {
            document.getElementById("upd_srvc").click();
        }
    }
</script>