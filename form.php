<?php
  declare(strict_types=1);
  error_reporting(E_ERROR | E_WARNING | E_PARSE);
?>

<html>
<head>
<title>Registrační formulář</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" media="screen" href="./bootstrap/css/bootstrap.css" />
</head>
<body>

<div class="container">

<h1>Registrační formulář</h1>


<?php


//Pokud je formulář odeslán, zobrazím $_POST hodnoty
if (isset($_POST['submit'])) {
    echo '<div class="alert alert-success" role="alert">'."\n";
      echo '<p class="text-center">Jméno: '.$_POST['firstname'].'</p>'."\n";
      echo '<p class="text-center">Příjmení: '.$_POST['surname'].'</p>'."\n";
    echo '<p class="text-center">Kraj: '.$regions[$_POST['region']].'</p>'."\n";
    echo '</div>'."\n"; 
} else {
  ?>
  <form method="post" action="form.php">

    <div class="form-group row">
      <label for="firstname" class="col-md-2 col-form-label">Jméno</label>
      <div class="col-md-10">
        <input type="text" class="form-control" name="firstname" maxlength="50">
      </div>
    </div>
    <div class="form-group row">
      <label for="surname" class="col-md-2 col-form-label">Příjmení</label>
      <div class="col-md-10">
        <input type="text" class="form-control" name="surname" maxlength="50">
      </div>
    </div>
    <div class="form-group row">
      <label for="note" class="col-md-2 col-form-label">&nbsp;</label>
      <div class="col-md-10">
        <input type="submit" class="btn btn-success" name="submit" value="Odeslat">
      </div>
    </div>
  </form>

  <?php
}
  ?>


</div>
</body>
</html>