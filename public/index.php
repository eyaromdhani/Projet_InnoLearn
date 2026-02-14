<?php

use App\Kernel;

<<<<<<< HEAD
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
=======
require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

if (($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'dev') === 'dev') {
    set_time_limit(0);
}
>>>>>>> user

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
