<?php

function detect_next_ball_position(int $ball_position_x, int $ball_position_y, int $ball_degree, bool $ball_to_right): array
{

  if ($ball_to_right) {
    $ball_position_x++;
  }

  switch ($ball_degree) {
    case 45:
      $ball_position_y--;
      break;
    case -45:
      $ball_position_y++;
      break;
    case 0:
      break;
  }

  return [$ball_position_x, $ball_position_y];
}

function render_pallet(int $y, int $pallet_start, int $pallet_end, bool $is_bot): string
{

  if ($y >= $pallet_start && $y < $pallet_end) {
    if ($is_bot) {
      return "<td class='pallet bot'></td>";
    }

    return "<td class='pallet user'></td>";
  }

  return "<td class='pallet'></td>";
}

function render_cell(int $x, int $y, int $ball_position_x, int $ball_position_y): string
{
  if ($x === $ball_position_x && $y === $ball_position_y) {
    return "<td class='ball'></td>";
  }

  return "<td></td>";
}
