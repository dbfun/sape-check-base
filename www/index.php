<?php
$config = json_decode(file_get_contents('../etc/config.json'));
?><!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel='stylesheet' href='styles.css' type='text/css' />
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="script.js"></script>
  <?php if($config->recaptcha->active == 1) { ?>
    <meta name="recaptcha" content="<?=$config->recaptcha->public;?>">
    <script src='https://www.google.com/recaptcha/api.js?onload=initRecaptcha&render=explicit'></script>
  <?php } ?>
</head>

<body>

  <a href="https://github.com/dbfun/sape-check-base"><img style="position: absolute; top: 0; left: 0; border: 0;" src="https://camo.githubusercontent.com/8b6b8ccc6da3aa5722903da7b58eb5ab1081adee/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f6c6566745f6f72616e67655f6666373630302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_left_orange_ff7600.png"></a>

  <header>
  	<h1>В какой базе SAPE?</h1>
  </header>

  <div id="form">

    <div class="fish" id="fish"></div>
    <div class="fish" id="fish2"></div>

    <form id="waterform">

      <div class="formgroup" id="message-form">
        <label for="message">ID сайтов через запятую, пробел или с новой строки</label>
        <textarea id="message" name="message"></textarea>
      </div>

      <?php if($config->recaptcha->active == 1) { ?>
        <div id="g-recaptcha"></div>
      <?php } ?>
      <input type="submit" value="Проверить!" />

      <a class="github-link" href="https://github.com/dbfun/sape-check-base/archive/master.zip" target="_blank">
        <svg class="github-logo" aria-hidden="true" height="32" version="1.1" viewBox="0 0 16 16" width="32"><path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z"/></svg>
      Скачать этот сайт</a>
    </form>
  </div>
  <?php include('../etc/counters.html'); ?>
  </body>
</html>
