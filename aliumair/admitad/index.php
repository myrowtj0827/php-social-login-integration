<pre><?php
error_reporting(E_ALL);
function includeIfExists($file)
{
    if (file_exists($file)) {
        return include $file;
    }
}

if ((!$loader = includeIfExists(__DIR__.'/vendor/autoload.php'))) {
    die('You must set up the project dependencies, run the following commands:'.PHP_EOL.
        'curl -s http://getcomposer.org/installer | php'.PHP_EOL.
        'php composer.phar install'.PHP_EOL);
}

// $loader->add('Admitad\Api\Tests\\', __DIR__);

		$api = new Admitad\Api\Api();
		$response = $api->authorizeByPassword('BuX9X2gUmdXkHUkKFna10MxRBEFsHW', 'A5t3wHteMmIGJZUegIS94oM7HgziUz', 'statistics', 'halamiles', '{6baAHzF');
		$result = $response->getArrayResult();

		$api1 = new Admitad\Api\Api($result['access_token']);

		// $contents = $api1->getIterator('/statistics/actions/', array(
		// 	'date_start' => $this->startDate,
		// 	'date_end' => $this->endDate,
		// 	'limit' => 200,
		// 	'offset' => 0
		// )); 
		
		print_r($api1);
