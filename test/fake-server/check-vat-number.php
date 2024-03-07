<?php

$acceptFound = false;
$contentTypeFound = false;
foreach (getallheaders() as $name => $value) {
    switch (strtolower($name)) {
        case 'accept':
            if (!preg_match('/^application\/json(;\s*=[Uu][Tt][Ff]-8)?$/', $value)) {
                http_response_code(499);
                die("Invalid Accept request header: {$value}");
            }
            $acceptFound = true;
            break;
        case 'content-type':
            if (!preg_match('/^application\/json(;\s*=[Uu][Tt][Ff]-8)?$/', $value)) {
                http_response_code(499);
                die("Invalid Content-Type request header: {$value}");
            }
            $contentTypeFound = true;
            break;
    }
}
if (!$acceptFound) {
    http_response_code(499);
    die('Missing Accept request header');
}
if (!$contentTypeFound) {
    http_response_code(499);
    die('Missing Content-Type request header');
}
if (PHP_VERSION_ID < 70000) {
    /** @var string $HTTP_RAW_POST_DATA */
    $request = is_string($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
} else {
    $request = file_get_contents('php://input');
    if ($request === false) {
        http_response_code(499);
        die('Failed to read the request body');
    }
}
if ($request === '') {
    http_response_code(499);
    die('Empty request body');
}
if ($request === 'null') {
    $data = null;
} else {
    $data = json_decode($request, true);
    if ($data === null) {
        http_response_code(499);
        die('Invalid JSON received');
    }
}
if (!is_array($data)) {
    http_response_code(499);
    die('Wrong JSON received');
}
$now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
$requestDate = $now->format('Y-m-d\TH:i:s.') . substr('000000' . $now->format('u'), -6) . 'Z';

switch ((isset($data['countryCode']) ? $data['countryCode'] : '') . "\x01" . (isset($data['vatNumber']) ? $data['vatNumber'] : '')) {
    case "IT\x0100159560366":
        header('Content-Type: application/json;charset=UTF-8');
        die(json_encode([
            'countryCode' => 'IT',
            'vatNumber' => '00159560366',
            'requestDate' => $requestDate,
            'valid' => true,
            'requestIdentifier' => '',
            'name' => 'FERRARI S.P.A.',
            'address' => 'VIA EMILIA EST 1163 \n41122 MODENA MO\n',
            'traderName' => '---',
            'traderStreet' => '---',
            'traderPostalCode' => '---',
            'traderCity' => '---',
            'traderCompanyType' => '---',
            'traderNameMatch' => 'NOT_PROCESSED',
            'traderStreetMatch' => 'NOT_PROCESSED',
            'traderPostalCodeMatch' => 'NOT_PROCESSED',
            'traderCityMatch' => 'NOT_PROCESSED',
            'traderCompanyTypeMatch' => 'NOT_PROCESSED',
        ]));
        break;
}
http_response_code(499);
die('Unrecognized test case');
