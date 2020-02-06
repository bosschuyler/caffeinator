<?php 
namespace App\Services\Logging;

use Monolog\Formatter\FormatterInterface;

class Formatter implements FormatterInterface
{
    public function format(array $record)
    {
        // echo "<pre>";
        // print_r($record);
        // exit;
        $message = "[".$record['datetime']->format('Y-m-d H:i:s')."] ";
        $message .= $record['level_name'] . " :: ";
        $message .= $record['message'] . "\n\n";

        if($record['context']) {
            $message .= print_r($record['context'], true);
            $message .= "\n";
        }
        
        $message .= "\t##### Additional Information #####"."\n\n";
        foreach($record['extra'] as $key=>$value) {
            if($value) {    
                $message .= "\t".$key." - ". $value . "\n";
            }
            
        }

        $message .= "\n=================================================\n";

        return $message;
    }

    public function formatBatch(array $records)
    {
        print 'BATCH';
        exit;

        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }

    protected function normalizeException(Exception $e)
    {
        $data = array(
            'class' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile().':'.$e->getLine(),
        );

        $trace = $e->getTrace();
        array_shift($trace);
        foreach ($trace as $frame) {
            if (isset($frame['file'])) {
                $data['trace'][] = $frame['file'].':'.$frame['line'];
            } else {
                $data['trace'][] = json_encode($frame);
            }
        }

        if ($previous = $e->getPrevious()) {
            $data['previous'] = $this->normalizeException($previous);
        }

        return $data;
    }
}