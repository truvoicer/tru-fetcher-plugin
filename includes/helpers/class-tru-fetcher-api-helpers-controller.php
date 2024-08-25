<?php

namespace TruFetcher\Includes\Helpers;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Response;

class Tru_Fetcher_Api_Helpers_Controller
{

    public function sendErrorResponse(string $errorCode, string $message, Tru_Fetcher_Api_Response $responseObject ) {
        $responseObject->setStatus(Tru_Fetcher_Api_Response::STATUS_ERROR);
        return (rest_ensure_response(new \WP_Error(
            $errorCode,
            $message,
            $responseObject
        )));
    }
    public function sendSuccessResponse($message, Tru_Fetcher_Api_Response $responseObject ) {
        $responseObject->setStatus(Tru_Fetcher_Api_Response::STATUS_SUCCESS);
        $responseObject->setMessage($message);
        return rest_ensure_response( $responseObject );
    }

    public function sendWpErrorResponse(\WP_Error $WP_Error, Tru_Fetcher_Api_Response $api_Response) {
        return $this->sendErrorResponse(
            $WP_Error->get_error_code(),
            $WP_Error->get_error_message(),
            $api_Response
        );
    }

    public function handleErrors(Tru_Fetcher_Api_Response $responseObject, array $targetObjects) {
        foreach ($targetObjects as $targetObject) {
            if (!is_object($targetObject)) {
                continue;
            }
            if (!method_exists($targetObject, 'getErrors')) {
                continue;
            }
            if (count($targetObject->getErrors())) {
                $responseObject->setErrors(
                    array_merge(
                        $responseObject->getErrors(),
                        $targetObject->getErrors()
                    )
                );
            }
        }
        return $responseObject;
    }
}
