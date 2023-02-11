<?php

// Start the session
session_start();

session_destroy();
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="style.css">
    <title>Ping Pong PHP Game | 2D Dimension</title>
</head>

<body>

<div class="detail">

    <h1>Ping <span class="bot">Pong</span> PHP Game | <span class="bot">2D</span> Dimension</h1>

</div>

<div class="detail">

  <?php

  if ($_GET['win']) {
    print '<h2 class="user">Congratulations, you win!!!</h2>';
  } elseif ($_GET['lose']) {
    print  '<h2 class="bot">Sorry, you lose!!!</h2>';
  }

  ?>

    <div>
        <form action="/index.php">
            <button type="submit">START NEW GAME</button>
        </form>
    </div>

</div>

</body>

</html>
