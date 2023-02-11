<meta http-equiv="refresh" content="1; URL=index.php">

<?php

session_start();

require 'function.php';

$config = parse_ini_file('config.ini');

$ball_position_x = $_SESSION['ball_position_x'] ?? 0;
$ball_position_y = $_SESSION['ball_position_y'] ?? (int)($config['total_rows'] / 2) - 1;
$ball_degree = $_SESSION['ball_degree'] ?? 0;
$ball_to_right = $_SESSION['ball_to_right'] ?? true;

$left_pallet = $_SESSION['left_pallet'] ?? (int)($config['total_rows'] / 2 - 1);
$right_pallet = $_SESSION['right_pallet'] ?? (int)($config['total_rows'] / 2 - 1);

$is_left_pallet_bot = $_SESSION['is_left_pallet_bot'] ?? true;

[$next_ball_position_x, $next_ball_position_y] = detect_next_ball_position($ball_position_x, $ball_position_y, $ball_degree, $ball_to_right);

$pallet_start = $_SESSION['pallet_start'] ?? (int)($_SESSION['left_pallet_position'] / 2);
$pallet_end = $pallet_start + $config['pallet_size'];

if ($is_left_pallet_bot) {
  $left_pallet = $left_pallet + rand(-1, 1);
  $right_pallet = $right_pallet + ($_GET['pallet_position'] ?? 0);
  if ($right_pallet < 0) {
    $right_pallet = 0;
  } elseif ($right_pallet >= $config['total_rows'] - $config['pallet_size']) {
    $right_pallet = $config['total_rows'] - $config['pallet_size'];
  }
} else {
  $left_pallet = $left_pallet + ($_GET['pallet_position'] ?? 0);
  $right_pallet = $right_pallet + rand(-1, 1);
  if ($left_pallet < 0) {
    $left_pallet = 0;
  } elseif ($left_pallet >= $config['total_rows'] - $config['pallet_size']) {
    $left_pallet = $config['total_rows'] - $config['pallet_size'];
  }
}

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

<?php

print "<table class='diagram'>";

for ($y = 0; $y < $config['total_rows']; $y++) {

  print "<tr>";

  for ($x = 0; $x < $config['total_columns']; $x++) {

    print match ($x) {
      0 => render_pallet($y, $left_pallet, $left_pallet + $config['pallet_size'], $is_left_pallet_bot),
      ($config['total_columns'] - 1) => render_pallet($y, $right_pallet, $right_pallet + $config['pallet_size'], !$is_left_pallet_bot),
      default => render_cell($x, $y, $next_ball_position_x, $next_ball_position_y),
    };

  }

  print "</tr>";

}

print "</table>";

?>

<div class="detail">

    <div>
        <form>
            <label>
                <input type="hidden" name="pallet_position" value="<?php print $pallet_start - 1; ?>">
            </label>
            <button type="submit">TOP</button>
        </form>
    </div>

    <div>
        <form>
            <label>
                <input type="hidden" name="pallet_position" value="<?php print $pallet_start + 1; ?>">
            </label>
            <button type="submit">BOTTOM</button>
        </form>
    </div>

    <br>
    <hr>
    <br>

    <div>
        <form action="/create.php">
            <button type="submit">START NEW GAME</button>
        </form>
    </div>

</div>

</body>

</html>

<?php

// limit left pallet position, can't leave the table section
if ($left_pallet < 0) {
  $_SESSION['left_pallet'] = 0;
} elseif ($left_pallet >= $config['total_rows'] - $config['pallet_size']) {
  $_SESSION['left_pallet'] = $config['total_rows'] - $config['pallet_size'];
} else {
  $_SESSION['left_pallet'] = $left_pallet;
}

// limit right pallet position, can't leave the table section
if ($right_pallet < 0) {
  $_SESSION['right_pallet'] = 0;
} elseif ($right_pallet >= $config['total_rows'] - $config['pallet_size']) {
  $_SESSION['right_pallet'] = $config['total_rows'] - $config['pallet_size'];
} else {
  $_SESSION['right_pallet'] = $right_pallet;
}

if ($ball_to_right) {
  $_SESSION['ball_position_x']++;
} else {
  $_SESSION['ball_position_x']--;
}

$_SESSION['ball_position_y'] = $ball_position_y;

switch ($_SESSION['ball_degree']) {
  case 45:
    $_SESSION['ball_position_y']--;
    break;
  case -45:
    $_SESSION['ball_position_y']++;
    break;
  default:
}

if ($next_ball_position_x === $config['total_columns'] - 2 && $ball_to_right) {

  if ($_SESSION['ball_position_y'] < $right_pallet || $_SESSION['ball_position_y'] > $right_pallet + $config['pallet_size']) {
    header("Location: create.php?lose=1");
    exit();
  }

  $_SESSION['ball_position_y'] = $next_ball_position_y;
  $_SESSION['ball_to_right'] = false;
} elseif ($next_ball_position_x === 1 && !$ball_to_right) {

  if ($next_ball_position_y < $left_pallet || $next_ball_position_y > $left_pallet + $config['pallet_size']) {
    header("Location: create.php?win=1");
    exit();
  }

  $_SESSION['ball_position_y'] = $next_ball_position_y;
  $_SESSION['ball_to_right'] = true;
} else {
  $_SESSION['ball_to_right'] = $ball_to_right;
}

// the ball hit top/bottom line
if ($next_ball_position_y === 0 || $next_ball_position_y === $config['total_rows'] - 1) {
  $_SESSION['ball_degree'] *= -1;
}

// the right pallet hit the ball
if ($_SESSION['ball_position_x'] === $config['total_columns'] - 2) {
  if ($_SESSION['right_pallet'] == $_SESSION['ball_position_y']) {
    $_SESSION['ball_degree'] = -45;
  } elseif ($_SESSION['right_pallet'] == $_SESSION['ball_position_y'] - $config['pallet_size'] + 1) {
    $_SESSION['ball_degree'] = 45;
  } else {
    $_SESSION['ball_degree'] = 0;
  }

  $_SESSION['ball_position_x']--;
}

// the left pallet hit the ball
if ($_SESSION['ball_position_x'] === 0 && !$ball_to_right) {
  if ($_SESSION['left_pallet'] == $_SESSION['ball_position_y']) {
    $_SESSION['ball_degree'] = -45;
  } elseif ($_SESSION['left_pallet'] == $_SESSION['ball_position_y'] - $config['pallet_size'] + 1) {
    $_SESSION['ball_degree'] = 45;
  } else {
    $_SESSION['ball_degree'] = 0;
  }

  $_SESSION['ball_to_right'] = true;
  $_SESSION['ball_position_x']++;
}

//print '<pre>';
//print_r($_SESSION);
//print '</pre>';
