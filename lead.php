<?php
/*  КОНФИГ  */
const SUBDOMAIN        = 'company name';
const ACCESS_TOKEN     = 'token';
const REFRESH_TOKEN    = 'token';
const TIME_FIELD_ID    = 'lead_id'; /* убрать '' и вставить id */
/*  КОНФИГ  */

header('Content-Type: application/json; charset=UTF-8');

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$phone   = trim($_POST['phone']   ?? '');
$price   = trim($_POST['price']   ?? '');
$started = (int)($_POST['started'] ?? 0);

if (!$name || !$email || !$phone || !$price || !$started) {
    http_response_code(422);
    exit(json_encode(['ok'=>false,'msg'=>'Заполните все поля']));
}

$timerFlag = (time() - $started) > 30;

$payload = [[
    'name'  => 'Заявка с сайта ' . date('d.m.Y'),
    'price' => (int)$price,
    'custom_fields_values' => [[
        'field_id' => TIME_FIELD_ID,
        'values'   => [['value' => $timerFlag]]
    ]],
    '_embedded' => [
        'contacts' => [[
            'first_name' => $name,
            'custom_fields_values' => [
                ['field_code'=>'EMAIL','values'=>[['value'=>$email,'enum_code'=>'WORK']]],
                ['field_code'=>'PHONE','values'=>[['value'=>$phone,'enum_code'=>'WORK']]]
            ]
        ]]
    ]
]];

function amoRequest(array $body, bool $retry = true): array {
    $ch = curl_init('https://' . SUBDOMAIN . '.amocrm.ru/api/v4/leads/complex');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . ACCESS_TOKEN,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS     => json_encode($body, JSON_UNESCAPED_UNICODE),
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code === 401 && $retry && refreshToken()) {
        return amoRequest($body, false);
    }
    return [$code, $resp];
}

function refreshToken(): bool {
    $data = [
        'grant_type'    => 'refresh_token',
        'client_id'     => 0,
        'client_secret' => 0,
        'refresh_token' => REFRESH_TOKEN,
        'redirect_uri'  => 'https://example.com'
    ];
    $ch = curl_init('https://' . SUBDOMAIN . '.amocrm.ru/oauth2/access_token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($data, JSON_UNESCAPED_UNICODE),
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code === 200) {
        $new = json_decode($resp, true)['access_token'] ?? '';
        if ($new) {
            file_put_contents(__DIR__ . '/access_token.tmp', $new);
            define('ACCESS_TOKEN', $new);
            return true;
        }
    }
    return false;
}

[$status, $response] = amoRequest($payload);
$out = json_decode($response, true);

if (in_array($status, [200, 201])) {
    exit(json_encode(['ok'=>true,'lead_id'=>$out['_embedded']['leads'][0]['id'] ?? null]));
}

http_response_code(500);
exit(json_encode(['ok'=>false,'status'=>$status,'detail'=>$response]));
