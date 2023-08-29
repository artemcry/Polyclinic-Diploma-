<?
  session_start();
  require_once 'dbconnect.php'; 
  require_once 'importcss.html';
  if(isset($_POST["exit"])) {
    unset($_SESSION["user-id"] );
    unset($_SESSION["user-role"] );
    unset($_SESSION["user-name"] );

  }
  $lg = isset($_SESSION["user-id"]);
?>
<link rel="stylesheet" type="text/css" href="/css/style.css">



<div class=hdr>
<div class="header">
    <div class="menu">

      <img onclick="window.location.href = '/'" src="/assets/images/logo.png" alt="" style="height: 50;">
      <span onclick="window.location.href = '/'" class="menu-item">Головна</span>
      <script>
        function gl(str, f) {
          return f ? str : '/login.php' ;
        }

      </script>

      <? if($_SESSION["user-role"] === "Doctor") {?>
        <span onclick="window.location.href = '/doctor/profile.php'" class="menu-item">Мій профіль</span>
        <span onclick="window.location.href = '/doctor/assigments.php'" class="menu-item">Пацієнти</span>
        <span onclick="window.location.href = '/doctor/new_assigment.php'" class="menu-item">Нове призначення</span>
        <span onclick="window.location.href = '/doctor/schedule.php'" class="menu-item">Графік роботи</span>
        <span onclick="window.location.href = '/doctor/appointments.php'" class="menu-item">Записи на прийом</span>
      <?} else  {?>
        <span onclick="window.location.href = gl('/patient/profile.php', <?=$lg?>)" class="menu-item">Мій кабінет</span>
        <span onclick="window.location.href = '/doctors.php'" class="menu-item">Лікарі</span>
        <span onclick="window.location.href = '/services.php'" class="menu-item">Послуги</span>
        <span onclick="window.location.href = gl('/patient/assigments.php', <?=$lg?>)" class="menu-item">Призначення</span>
        <span onclick="window.location.href = gl('/patient/appointments.php', <?=$lg?>)" class="menu-item">Запис на прийом</span>                  
      <?
     if($_SESSION["user-role"] === "Administrator") {?>
        <span onclick="window.location.href = '/administrator/main.php'" class="menu-item">Адміністратор</span>                  
     <?}
    
    } ?>
    </div>
    <?if(isset($_SESSION["user-id"])) {?>
      <form action="/" method="post">
          <input style="display: none;" id=exit type="submit" name="exit">
      </form>
      <span onclick="document.getElementById('exit').click();" class="menu-item">Вихід (<?=$_SESSION["user-name"]?>)</span>

    <?} else {?>
      <span onclick="window.location.href = '/login.php'" class="menu-item">Авторизація</span>
      <?}?>
  </div>
    </div>