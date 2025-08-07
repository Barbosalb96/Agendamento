<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    dispatch(new \App\Jobs\MailDispatchDefault(
        'teste sqs',
        [1, 2, 3],
        'sqs',
        'barbosalucaslbs96@gmail.com'
    ));

    return 'ok';
});
