<?php


namespace App\Services\Logging;


use Illuminate\Support\ServiceProvider;

// the required libs
use App\Services\Logging\Logger;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

use App\Services\Logging\Formatter;

class LogServiceProvider extends ServiceProvider {

    public function register() {

        $this->registerStreamHandlers();

        $this->app->bind('MonologLogger', function($app, $params) {
            $MonologLogger = new MonologLogger('main');

            if(isset($params) && !empty($params)) { 
                foreach($params as $name) {
                    $streamHandler = $app->make('Log\StreamHandler', [
                        'name' => $name
                    ]);
                    $MonologLogger->pushHandler($streamHandler);
                }
            } else {
                $streamHandler = $app->make('Log\StreamHandler');
                $MonologLogger->pushHandler($streamHandler);
            }

            $MonologLogger->pushProcessor(
                function (array $record) {
                    $trace = debug_backtrace();

                    // skip first since it's always the current method
                    array_shift($trace);
                    // the call_user_func call is also skipped
                    array_shift($trace);

                    $i = 0;
                    while (isset($trace[$i]['class']) && (false !== strpos($trace[$i]['class'], 'Monolog\\') || false !== strpos($trace[$i]['class'], 'Logger'))) {
                        $i++;
                    }

                    $file   = isset($trace[$i-1]['file']) ? $trace[$i-1]['file'] : '';

                    $projectPath = realpath(base_path());

                    if(($offset = strpos($file, $projectPath)) !== false) {
                        $file = substr($file, $offset + strlen($projectPath));
                    }

                    // we should have the call source now
                    $record['extra'] = array_merge(
                        $record['extra'],
                        array(
                            'base'      => $projectPath,
                            'file'      => $file,
                            'line'      => isset($trace[$i-1]['line']) ? $trace[$i-1]['line'] : null,
                            'class'     => isset($trace[$i]['class']) ? $trace[$i]['class'] : null,
                            'function'  => isset($trace[$i]['function']) ? $trace[$i]['function'] : null
                        )
                    );

                    return $record;
                }
            );


            return $MonologLogger;
        });

        $this->app->bind('Logger', function($app, $params) {
            $MonologLogger = $app->make('MonologLogger', $params);

            return new Logger($MonologLogger);
        });

        // $this->app->bind('log', function($app, $params) {
        //     $MonologLogger = $app->make('MonologLogger', $params);

        //     return new Logger($MonologLogger);
        // });
    }



    public function registerStreamHandlers() {

        $this->app->bind('Log\StreamHandler', function($app, $params) {
            $path = $app->config['log.path'];

            if(isset($params['name'])) {
                $logName = $params['name'].' -- '.date("Y-m-d").'.log';
            } else {
                $logName = $app->config['log.default'].' -- '.date("Y-m-d").'.log';
            }            

            $streamHandler = new StreamHandler($path.DIRECTORY_SEPARATOR.$logName);
            $streamHandler->setFormatter(new Formatter());
            return $streamHandler;
        });
    }

}
