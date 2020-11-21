<?php
class parselogs
{
    private $logs_texto, $objeto_log;
    public function __construct()
    {
        $this->logs_texto = fopen('epa-http.txt', 'r');

        $this->objeto_log = new stdClass();
        $this->objeto_log->logs = array();
        $this->parselogs();
    }
    public function parselogs()
    {
        while (!feof($this->logs_texto)) {
            $obj = $this->estructuralog(fgets($this->logs_texto));
            array_push($this->objeto_log->logs, $obj);
        }
        fclose($this->logs_texto);
    }

    public function estructuralog($cadena)
    {

        $log = explode(" ", $cadena);

        $host = array_splice($log, 0, 1);
        $bytes = array_pop($log);
        $response = array_pop($log);

        $request_object = $this->getResponse($cadena);

        $objeto = new stdClass();
        $objeto->host = $host[0];
        $objeto->date =  $this->getDate($log);
        $objeto->method =  $request_object->method;
        $objeto->url = $request_object->url;
        $objeto->protocol = $request_object->protocol;
        $objeto->response = $response;
        $objeto->bytes = $bytes;
        return $objeto;
    }


    public function getDate($cadena)
    {
        $log = implode(" ", $cadena);
        $arreglo = explode("]", $log);
        $arreglo = $arreglo[0];
        $arreglo = explode("[", $arreglo);

        return array_pop($arreglo);
    }

    public function getResponse($cadena)
    {
        $arreglo = explode('"', $cadena);
        $request = array_splice($arreglo, 1, 1);
        $request_array = explode(" ", array_pop($request));
        $method_array = array_splice($request_array, 0, 1);
        $method = array_pop($method_array);

        $protocol = array_pop($request_array);

        $url = implode(" ", $request_array);

        $objeto = new stdClass();
        $objeto->method = $method;
        $objeto->url = $url;
        $objeto->protocol = $protocol;
        return $objeto;
    }
    public function getlog($pos)
    {
        return $this->objeto_log->logs[$pos];
    }
    public function showLogs()
    {
        return $this->objeto_log->logs;
    }
    public function cantidadLogs()
    {
        return count($this->objeto_log->logs);
    }

    public function getRequestMethod()
    {
        $objeto = new stdClass();

        $post_method = new stdClass();
        //$post_method->name = 'POST';
        $post_method->cuenta = 0;

        $get_method = new stdClass();
        //$get_method->name = 'GET';
        $get_method->cuenta = 0;

        $head_method = new stdClass();
        //$head_method->name = 'HEAD';
        $head_method->cuenta = 0;

        $invalid_method = new stdClass();
        //$invalid_method->name = 'INVALID';
        $invalid_method->cuenta = 0;

        foreach ($this->objeto_log->logs as $item) {

            switch ($item->method) {
                case 'POST':
                    $post_method->cuenta += 1;
                    break;
                case 'GET':
                    $get_method->cuenta += 1;
                    break;
                case 'HEAD':
                    //hay 106 peticiones HEAD pero en el log 22125 hay un head en el la URL sin embargo la peticion es GET
                    $head_method->cuenta += 1;
                    break;

                default:
                    $invalid_method->cuenta += 1;
                    break;
            }
        }
        $objeto->post = $post_method;
        $objeto->get = $get_method;
        $objeto->head = $head_method;
        $objeto->invalid = $invalid_method;

        $objeto->total_request = $post_method->cuenta + $get_method->cuenta + $head_method->cuenta + $invalid_method->cuenta;
        return $objeto;
    }

    public function requesPerMinutes()
    {
        $fecha_inicial = date_parse_from_format("d-H-i-s", $this->objeto_log->logs[0]->date);
        $arreglo = array();

        $objeto_fecha = new stdClass();
        $objeto_fecha->fecha = $this->objeto_log->logs[0]->date;
        $objeto_fecha->cantidad_logs = 0;

        array_push($arreglo, $objeto_fecha);
        $cuenta = 0;
        foreach ($this->objeto_log->logs as $item) {
            $fecha_cur = date_parse_from_format("d-H-i-s", $item->date);

            if ($fecha_inicial['minute'] == $fecha_cur['minute']) {
                $arreglo[$cuenta]->cantidad_logs += 1;
            } else {
                $objeto_fecha = new stdClass();
                $objeto_fecha->fecha = $item->date;
                $objeto_fecha->cantidad_logs = 1;

                array_push($arreglo, $objeto_fecha);
                $fecha_inicial =  $fecha_cur;
                $cuenta++;
            }
        }
        $objeto = new stdClass();
        $objeto->arreglo = $arreglo;
        $objeto->promedio = count($this->objeto_log->logs) / count($arreglo);
        return $objeto;
    }

    public function responseCode()
    {
        $arreglo = array();
        $arreglo_codigos = array();
        $arreglo_cantidades = array();
        $arreglo_colores = array();

        $objeto_response = new stdClass();
        $objeto_response->responseCode = $this->objeto_log->logs[0]->response;
        $objeto_response->cantidad_respuesta = 1;

        array_push($arreglo, $objeto_response);

        for ($i = 1; $i < count($this->objeto_log->logs); $i++) {
            $pos = $this->searchResponseCode($this->objeto_log->logs[$i]->response, $arreglo);
            if ($pos !== -1) {
                $arreglo[$pos]->cantidad_respuesta += 1;
            } else {
                $objeto_response = new stdClass();
                $objeto_response->responseCode = $this->objeto_log->logs[$i]->response;
                $objeto_response->cantidad_respuesta = 1;
                array_push($arreglo, $objeto_response);
            }
        }
        foreach ($arreglo as $key) {
            array_push($arreglo_cantidades, $key->cantidad_respuesta);
            array_push($arreglo_codigos, $key->responseCode);
            array_push($arreglo_colores, $this->random_color());
        }


        $objeto = new stdClass();
        $objeto->arreglo_tipo_codigos = $arreglo_codigos;
        $objeto->description = $arreglo;
        $objeto->arreglo_cantidades = $arreglo_cantidades;
        $objeto->colores = $arreglo_colores;

        return $objeto;
    }

    public function searchResponseCode($code, $arreglo)
    {
        $pos = -1;
        for ($i = 0; $i < count($arreglo); $i++) {
            if ($arreglo[$i]->responseCode == $code) {
                $pos = $i;
            }
        }
        return $pos;
    }
    private function random_color_part()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }

    private  function random_color()
    {
        return "#" . $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
    }
}
