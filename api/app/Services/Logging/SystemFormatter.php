<?php 
namespace App\Services\Logging;

use Monolog\Formatter\NormalizerFormatter;
use Monolog\Formatter\FormatterInterface;

use Exception;

class SystemFormatter extends NormalizerFormatter implements FormatterInterface 
{
    const SIMPLE_DATE = "Y-m-d H:i:s";

    protected $dateFormat;
    protected $includeStacktraces = true;

    /**
     * @param string $dateFormat The format of the timestamp: one supported by DateTime::format
     */

    public function format(array $record)
    {

        $record = $this->normalize($record);

        $message = "\n[".$record['datetime']."] ";
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

    protected function normalizeException($e)
    {   
        if (!$e instanceof \Exception && !$e instanceof \Throwable) {
            throw new \InvalidArgumentException('Exception/Throwable expected, got '.gettype($e).' / '.get_class($e));
        }

        $previousText = '';
        if ($previous = $e->getPrevious()) {
            do {
                $previousText .= ', '.get_class($previous).'(code: '.$previous->getCode().'): '.$previous->getMessage().' at '.$previous->getFile().':'.$previous->getLine();
            } while ($previous = $previous->getPrevious());
        }

        $str = '[object] ('.get_class($e).'(code: '.$e->getCode().'): '.$e->getMessage().' at '.$e->getFile().':'.$e->getLine().$previousText.')';
        if ($this->includeStacktraces) {
            $str .= "\n[stacktrace]\n".$e->getTraceAsString();
        }

        return $str;
    }
}