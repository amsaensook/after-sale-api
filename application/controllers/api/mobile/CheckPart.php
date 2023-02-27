<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class CheckPart extends REST_Controller
{

    protected $MenuId = 'CheckPartMobile';

    public function __construct()
    {

        parent::__construct();

        // Load CheckPart_Model
        $this->load->model('mobile/CheckPart_Model');

    }

    /**
     * Show CheckPart API
     * ---------------------------------
     * @method : GET
     * @link : check_part/index
     */
    public function index_get()
    {

        header("Access-Control-Allow-Origin: *");

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        // CheckPart Token Validation
        $is_valid_token = $this->authorization_token->validateToken();

        if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
            // Load CheckPart Function
            $output = $this->CheckPart_Model->select_check_part($this->input->get('QR_NO'));

            if (isset($output) && $output) {

                // Show CheckPart Success
                $message = [
                    'status' => true,
                    'data' => $output[0],
                    'message' => 'Show item successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

            } else {

                // Show CheckPart Error
                $message = [
                    'status' => false,
                    'message' => 'Item data was not found in the database',
                ];

                $this->response($message, REST_Controller::HTTP_OK);

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
