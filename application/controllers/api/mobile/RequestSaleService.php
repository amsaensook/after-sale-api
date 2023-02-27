<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class RequestSaleService extends REST_Controller
{

  protected $MenuId = 'RequestSaleServiceMobile';

  public function __construct()
  {

    parent::__construct();

    // Load RequestSaleService_Model
    $this->load->model('mobile/RequestSaleService_Model');
  }

  /**
   * Show RequestSaleService All API
   * ---------------------------------
   * @method : GET
   * @link : request_sale_service/index
   */
  public function index_get()
  {

    header("Access-Control-Allow-Origin: *");

    // Load Authorization Token Library
    $this->load->library('Authorization_Token');

    // RequestSaleService Token Validation
    $is_valid_token = $this->authorization_token->validateToken();

    if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
      // Load RequestSaleService Function
      $output = $this->RequestSaleService_Model->select_request_sale_service();

      if (isset($output) && $output) {

        // Show RequestSaleService All Success
        $message = [
          'status' => true,
          'data' => $output,
          'message' => 'Show request sale service all successful',
        ];

        $this->response($message, REST_Controller::HTTP_OK);
      } else {

        // Show RequestSaleService All Error
        $message = [
          'status' => false,
          'message' => 'Request sale service data was not found in the database',
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

  /**
   * Update RequestSaleService API
   * ---------------------------------
   * @param: FormData
   * ---------------------------------
   * @method : POST
   * @link : request_sale_service/update
   */
  public function update_post()
  {

    header("Access-Control-Allow-Origin: *");

    # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
    $_POST = $this->security->xss_clean($_POST);

    # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
    $this->form_validation->set_rules('Withdraw_ID', 'Withdraw_ID', 'trim|required');

    if ($this->form_validation->run() == false) {
      // Form Validation Error
      $message = [
        'status' => false,
        'error' => $this->form_validation->error_array(),
        'message' => validation_errors(),
      ];

      $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
    } else {

      // Load Authorization Token Library
      $this->load->library('Authorization_Token');

      // RequestSaleService Token Validation
      $is_valid_token = $this->authorization_token->validateToken();

      if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

        $request_sale_service_token = json_decode(json_encode($this->authorization_token->userData()), true);
        $request_sale_service_permission = array_filter($request_sale_service_token['permission'], function ($permission) {
          return $permission['MenuId'] == $this->MenuId;
        });

        if ($request_sale_service_permission[array_keys($request_sale_service_permission)[0]]['Updated']) {

          $request_sale_service_data['where'] = [
            'Withdraw_ID' =>  $this->input->post('Withdraw_ID')
          ];

          $request_sale_service_data['data'] = [
            'status' => 9,
            'Update_By' => $request_sale_service_token['UserName'],
            'Update_Date' => date('Y-m-d H:i:s'),
          ];

          // Update RequestSaleService Function
          $request_sale_service_output = $this->RequestSaleService_Model->update_request_sale_service($request_sale_service_data);

          if (isset($request_sale_service_output) && $request_sale_service_output) {

            // Update RequestSaleService Success
            $message = [
              'status' => true,
              'message' => 'Update Request sale service Successful',
            ];

            $this->response($message, REST_Controller::HTTP_OK);
          } else {

            // Update RequestSaleService Error
            $message = [
              'status' => false,
              'message' => 'Update Request sale service Fail : [Update Data Fail]',
            ];

            $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
          }
        } else {
          // Permission Error
          $message = [
            'status' => false,
            'message' => 'You don’t currently have permission to Update',
          ];

          $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
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


  /**
   * Show RequestSaleService Item API
   * ---------------------------------
   * @method : GET
   * @link : request_sale_service/item
   */
  public function item_get()
  {

    header("Access-Control-Allow-Origin: *");

    // Load Authorization Token Library
    $this->load->library('Authorization_Token');

    // RequestSaleService Token Validation
    $is_valid_token = $this->authorization_token->validateToken();

    if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
      // Load RequestSaleService Function
      $output = $this->RequestSaleService_Model->select_request_sale_service_item($this->input->get('Withdraw_ID'));

      if (isset($output) && $output) {

        // Show RequestSaleService All Success
        $message = [
          'status' => true,
          'data' => $output,
          'message' => 'Show request sale service item successful',
        ];

        $this->response($message, REST_Controller::HTTP_OK);
      } else {

        // Show RequestSaleService All Error
        $message = [
          'status' => false,
          'message' => 'Request sale service Item data was not found in the database',
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

  /**
   * Exec RequestSaleService Transaction API
   * ---------------------------------
   * @param: FormData
   * ---------------------------------
   * @method : POST
   * @link : request_sale_service/exec_transaction
   */
  public function exec_transaction_post()
  {

    header("Access-Control-Allow-Origin: *");

    # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
    $_POST = $this->security->xss_clean($_POST);

    # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
    $this->form_validation->set_rules('Withdraw_ID', 'Withdraw_ID', 'trim|required');
    $this->form_validation->set_rules('QR_NO', 'QR_NO', 'trim|required');
    $this->form_validation->set_rules('Tag_ID', 'Tag_ID', 'trim|required');

    if ($this->form_validation->run() == false) {
      // Form Validation Error
      $message = [
        'status' => false,
        'error' => $this->form_validation->error_array(),
        'message' => validation_errors(),
      ];

      $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
    } else {

      // Load Authorization Token Library
      $this->load->library('Authorization_Token');

      // RequestSaleService Token Validation
      $is_valid_token = $this->authorization_token->validateToken();

      if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

        $request_sale_service_token = json_decode(json_encode($this->authorization_token->userData()), true);
        $request_sale_service_permission = array_filter($request_sale_service_token['permission'], function ($permission) {
          return $permission['MenuId'] == $this->MenuId;
        });

        if ($request_sale_service_permission[array_keys($request_sale_service_permission)[0]]['Created']) {

          $tag_data = [
            'Withdraw_ID' => intval($this->input->post('Withdraw_ID')),
            'QR_NO' => $this->input->post('QR_NO'),
            'Tag_ID' => intval($this->input->post('Tag_ID')),
            'Create_Date' => date('Y-m-d H:i:s'),
            'Create_By' => $request_sale_service_token['UserName'],
          ];

          // Exec RequestSaleService Item Function
          $request_sale_service_item = $this->RequestSaleService_Model->check_request_sale_service_item($tag_data);

          if (isset($request_sale_service_item) && $request_sale_service_item) {

            if (boolval($request_sale_service_item[0]['Result_status']) === true) {

              // Exec RequestSaleService Transaction Function
              $request_sale_service_output = $this->RequestSaleService_Model->exec_request_sale_service_transaction($tag_data);

              if (isset($request_sale_service_output) && $request_sale_service_output) {

                // Exec RequestSaleService Transaction Success
                $message = [
                  'status' => true,
                  'message' => 'Insert Request Sale Service Successful',
                ];

                $this->response($message, REST_Controller::HTTP_OK);
              } else {

                // Exec RequestSaleService Transaction Error
                $message = [
                  'status' => false,
                  'message' => 'Insert Request Sale Service Fail : [Insert Data Fail]',
                ];

                $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
              }
            } else {

              // Exec RequestSaleService Item Error Condition
              $message = [
                'status' => false,
                'message' => $request_sale_service_item[0]['Result_Desc'],
              ];

              $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
          } else {

            // Exec RequestSaleService Item Error
            $message = [
              'status' => false,
              'message' => 'Exec Transaction Fail : [Exec Data Fail]',
            ];

            $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
          }
        } else {
          // Permission Error
          $message = [
            'status' => false,
            'message' => 'You don’t currently have permission to Update',
          ];

          $this->response($message, REST_Controller::HTTP_UNAUTHORIZED);
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
}
