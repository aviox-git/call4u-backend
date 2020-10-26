<?php
defined('API_SUCCESS')                      OR define('API_SUCCESS', TRUE);
defined('API_ERROR')                        OR define('API_ERROR', FALSE);

// API Status Code
defined('API_STATUS_OK')                    OR define('API_STATUS_OK', 200);
defined('API_STATUS_CREATED')               OR define('API_STATUS_CREATED', 201);
defined('API_STATUS_UNAUTHORIZED')          OR define('API_STATUS_UNAUTHORIZED', 401);
defined('API_UNAUTHORIZED_MESSAGE')         OR define('API_UNAUTHORIZED_MESSAGE', 'Authentication Required');
defined('API_STATUS_FORBIDDEN')             OR define('API_STATUS_FORBIDDEN', 403);
defined('API_STATUS_BAD_REQUEST')           OR define('API_STATUS_BAD_REQUEST', 400);
defined('STATUS_SERVER_ERROR')              OR define('STATUS_SERVER_ERROR', 500);
defined('STATUS_NO_CONTENT')                OR define('STATUS_NO_CONTENT', 204);


defined('ERROR_MESSAGE')                    OR define('ERROR_MESSAGE', 'Oops! some error occured, please try again');
defined('SAVED_SUCCESS')                    OR define('SAVED_SUCCESS', ' saved successfully');
defined('UPDATED_SUCCESS')                  OR define('UPDATED_SUCCESS', ' updated successfully');
defined('DELETED_SUCCESS')                  OR define('DELETED_SUCCESS', ' deleted successfully');
