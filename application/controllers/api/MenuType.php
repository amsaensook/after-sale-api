<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class MenuType extends REST_Controller
{

    protected $MenuTypeId = 'MenuType';

    public function __construct()
    {

        parent::__construct();

        // Load MenuType_Model
        $this->load->model('MenuType_Model');

    }

    /**
     * Show MenuType All API
     * ---------------------------------
     * @method : GET
     * @link : MenuType/index
     */
    public function index_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // MenuType Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load MenuType Function
            $output = $this->MenuType_Model->select_menu_type();

            if (isset($output) && $output) {

                // Show MenuType All Success
                $message = [
                    'status' => true,
                    'data' => $output,
                    'message' => 'Show menu type all successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show MenuType All Error
                $message = [
                    'status' => false,
                    'message' => 'MenuType data was not found in the database',
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
