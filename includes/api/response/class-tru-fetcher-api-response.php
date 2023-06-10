<?php

namespace TruFetcher\Includes\Api\Response;

use TruFetcher\Includes\Api\Pagination\Tru_Fetcher_Api_Pagination;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;
use WP_Error;

class Tru_Fetcher_Api_Response
{
    use Tru_Fetcher_Traits_Errors;

    const STATUS_SUCCESS = "success";
    const STATUS_ERROR = "error";

    public const BASE_API_RESPONSE_ERROR_CODE_PREFIX = 'tr_news_app_';

    public $status;
    public $message;
    public $data;
    public ?Tru_Fetcher_Api_Pagination $pagination = null;

	/**
	 * @return mixed
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param mixed $message
	 */
	public function setMessage( $message ) {
		$this->message = $message;
	}


	/**
	 * @return mixed
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param mixed $status
	 */
	public function setStatus( $status ) {
        if ($status !== self::STATUS_SUCCESS && $status !== self::STATUS_ERROR) {
            wp_send_json_error([
                "message" => "Set status invalid"
            ]);
        }
		$this->status = $status;
	}

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return Tru_Fetcher_Api_Pagination|null
     */
    public function getPagination(): ?Tru_Fetcher_Api_Pagination
    {
        return $this->pagination;
    }

    /**
     * @param Tru_Fetcher_Api_Pagination|null $pagination
     */
    public function setPagination(?Tru_Fetcher_Api_Pagination $pagination): void
    {
        $this->pagination = $pagination;
    }

}
