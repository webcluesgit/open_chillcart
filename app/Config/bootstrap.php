<?php
Cache::config('default', array('engine' => 'File'));

preg_match('/^(?:www\.)?(?:(.+)\.)?(.+\..+)$/i', env('HTTP_HOST'), $urlmatches);
Configure::write('SubdomainHTTP', array('subdomain' => empty($urlmatches[1]) ? false : $urlmatches[1], 'hostURL' => empty($urlmatches[2]) ? false : $urlmatches[2]));


Configure::write('Dispatcher.filters', array(
    'AssetDispatcher',
    'CacheDispatcher'
));
Configure::write('Stripe.TestSecret', 'sk_test_QcYAQLjuk0nH2VqIKlYcqLQI');
Configure::write('Stripe.LiveSecret', 'pk_test_4NJXMb2RzQRyJBIkAhaSBwZn');
Configure::write('Stripe.mode', 'Test');
Configure::write('Stripe.currency', 'eur');
/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
    'engine' => 'FileLog',
    'types' => array('notice', 'info', 'debug'),
    'file' => 'debug',
));
CakeLog::config('error', array(
    'engine' => 'FileLog',
    'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
    'file' => 'error',
));

//Add the CakeS3 plugin
Configure::write('CakeS3', array(
        's3Key' => 'AKIAJ7UUZ7Q22HDUQKMA',
        's3Secret' => 'DfYEkDUU17/asCOcDLqZ+T9A/2T8v9ILWIukqqAy',
        'bucket' => 's3test56b888c6be37d',
        'endpoint' => 's3.amazonaws.com' // [optional] Only required if your endpoint is not s3.amazonaws.com
    )
);
/** 
 * HybridAuth component
 *
 */
 Configure::write('Hybridauth', array(
    // openid providers
    "Google" => array(
        "enabled" => true,
        "keys" => array("id" => "633345069118-41bhfk6f0onim7haf9koaeibgsdvt0fn.apps.googleusercontent.com","secret" => "4XEpzRATmc44oze9jp1jjrDC"),
    ),
	"Twitter" => array(
        "enabled" => true,
        "keys" => array("key" => "Your-Twitter-Key", "secret" => "Your-Twitter-Secret")
    ),
	"Facebook" => array(
        "enabled" => true,
        "keys" => array("id" => "390488147813330", "secret" => "6938131093089cf147eaf86fdde037a9"),
    ),
));


/*Configure::write('Twilio.AccountSid', 'ACabd5c1ab4205beb8f7011d7eaa0c8230');
Configure::write('Twilio.AuthToken', 'bce4b1ff455bd4d479388b6488104529');
Configure::write('Twilio.from', '+353 899633038');*/

Configure::write('Twilio.AccountSid', 'ACa48eb8d38a4224f009a72ac9846a346f');
Configure::write('Twilio.AuthToken', '61ba8c940ee15d43b618f8bd1cc26308');
Configure::write('Twilio.from', '+1 315-217-6007');

App::import('Vendor', array('file' => 'autoload'));

CakePlugin::loadAll();
