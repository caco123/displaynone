<?php
require_once 'app.php';
$app = new parselogs();
/* $pos276 = $app->getlog(276);
echo '<pre>';
var_dump($pos276);
echo '</pre>';

$fecha = date_parse_from_format("d-H-i-s", '29:23:53:25');
var_dump($fecha['minute']);
$lista = [1, 2, 3]; */

/* echo '<pre>';
var_dump($app->showLogs());
echo '</pre>'; */
function random_color_part()
{
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
}

function random_color()
{
    return random_color_part() . random_color_part() . random_color_part();
}
echo random_color();