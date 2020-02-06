<?php
namespace App\Helpers;


class Exception {
	public static function normalize($e, $local = false) {  
        $data = array(
            'class' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile().':'.$e->getLine(),
        );

        $trace = $e->getTrace();
        array_shift($trace);
        foreach ($trace as $frame) {
            if (isset($frame['file'])) {
                $line = $frame['file'].':'.$frame['line'];
            } else {
                $line = json_encode($frame);
            }

            if(stristr($line, 'Illuminate') && $local)
                continue;
            $data['trace'][] = $line;
        }

        if ($previous = $e->getPrevious()) {
            $data['previous'] = self::normalize($previous, $local);
        }

        return $data;
	}
}

