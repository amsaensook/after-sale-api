<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class Platform extends REST_Controller
{

    protected $PlatformId = 'Platform';

    public function __construct()
    {

        parent::__construct();

        // Load Platform_Model
        $this->load->model('Platform_Model');

    }

    /**
     * Show Platform All API
     * ---------------------------------
     * @method : GET
     * @link : Platform/index
     */
    public function index_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // Platform Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load Platform Function
            $output = $this->Platform_Model->select_platform();

            if (isset($output) && $output) {

                // Show Platform All Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show menu type all successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show Platform All Error
                $message = [
                    'status' => false,
                    'message' => 'Platform data was not found in the database',
                ];

                $this->response($message, REST_Controller::HTTP_NOT_FOUND);

            }

        } else {
            // Validate Error
            $message = [
                'status' => false,
                'message' => $is_valid_token['message'],
            ];

            $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
        }

    }

}
