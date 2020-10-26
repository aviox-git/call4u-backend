<?php
if (!function_exists('checkValidateRequest')) {
    function checkValidateRequest($validator)
    {
        $response = [];
        if ($validator->fails()) {
            $response['errors'] = implode(" ",$validator->messages()->all());
            $response['success'] = API_ERROR;
            $response['status'] = API_STATUS_BAD_REQUEST;
        } else {
            $response['success'] = API_SUCCESS;
        }
        return $response;
    }
}

if (!function_exists('dieA')) {
    function dieA($data)
    {
        echo "<pre>";
        print_r($data);die;
    }
}

if (!function_exists('apiResponseUnauthorized')) {
    function apiResponseUnauthorized()
    {
        $response = [];
        $response['message'] = API_UNAUTHORIZED_MESSAGE;
        $response['success'] = API_ERROR;
        $response['status'] = API_STATUS_UNAUTHORIZED;
        return $response;
    }
}

if (!function_exists('apiResponseCreateUpdate')) {
    function apiResponseCreateUpdate($message)
    {
        $response = [];
        $response['message'] = $message;
        $response['success'] = API_SUCCESS;
        $response['status'] = API_STATUS_OK;
        return $response;
    }
}

if (!function_exists('apiResponseServerError')) {
    function apiResponseServerError($e)
    {
        $response = [];
        $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
        $response['success'] = API_ERROR;
        $response['status'] = STATUS_SERVER_ERROR;
        return $response;
    }
}
?>
