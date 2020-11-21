<?php

require_once 'app.php';
$app = new parselogs();

switch ($_POST['accion']) {
    case 'getRequestMethod':
        echo json_encode($app->getRequestMethod());
        break;
    case 'requesPerMinutes':
        $promedio = $app->requesPerMinutes();
        echo $promedio->promedio;
        break;
    case 'responseCode':
        echo json_encode($app->responseCode());
        break;

    default:
        # code...
        break;
}
