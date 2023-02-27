<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class ReceiveSaleService extends REST_Controller
{

  protected $MenuId = 'ReceiveSaleServiceMobile';

  public function __construct()
  {

    parent::__construct();

    // Load ReceiveSaleService_Model
    $this->load->model('mobile/ReceiveSaleService_Model');
  }

  /**
   * Show ReceiveSaleService All API
   * ---------------------------------
   * @method : GET
   * @link : receive_sale_service/index
   */
  public function index_get()
  {

    header("Access-Control-Allow-Origin: *");

    // Load Authorization Token Library
    $this->load->library('Authorization_Token');

    // ReceiveSaleService Token Validation
    $is_valid_token = $this->authorization_token->validateToken();

    if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {


      $receive_sale_service_token = json_decode(json_encode($this->authorization_token->userData()), true);

      // Load ReceiveSaleService Function
      $output = $this->ReceiveSaleService_Model->select_receive_sale_service($receive_sale_service_token['GroupName']);

      if (isset($output) && $output) {

        // Show ReceiveSaleService All Success
        $message = [
          'status' => true,
          'data' => $output,
          'message' => 'Show receive sale service all successful',
        ];

        $this->response($message, REST_Controller::HTTP_OK);
      } else {

        // Show ReceiveSaleService All Error
        $message = [
          'status' => false,
          'message' => 'Receive sale service data was not found in the database',
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
   * Update ReceiveSaleService API
   * ---------------------------------
   * @param: FormData
   * ---------------------------------
   * @method : POST
   * @link : receive_sale_service/update
   */
  public function update_post()
  {

    header("Access-Control-Allow-Origin: *");

    # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
    $_POST = $this->security->xss_clean($_POST);

    # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
    $this->form_validation->set_rules('Rec_ID', 'Rec_ID', 'trim|required');

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

      // ReceiveSaleService Token Validation
      $is_valid_token = $this->authorization_token->validateToken();

      if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

        $receive_sale_service_token = json_decode(json_encode($this->authorization_token->userData()), true);
        $receive_sale_service_permission = array_filter($receive_sale_service_token['permission'], function ($permission) {
          return $permission['MenuId'] == $this->MenuId;
        });

        if ($receive_sale_service_permission[array_keys($receive_sale_service_permission)[0]]['Updated']) {

          $receive_sale_service_data['where'] = [
            'Rec_ID' =>  $this->input->post('Rec_ID')
          ];

          $receive_sale_service_data['data'] = [
            'status' => 9,
            'Update_By' => $receive_sale_service_token['UserName'],
            'Update_Date' => date('Y-m-d H:i:s'),
          ];

          // Update ReceiveSaleService Function
          $receive_sale_service_output = $this->ReceiveSaleService_Model->update_receive_sale_service($receive_sale_service_data);

          if (isset($receive_sale_service_output) && $receive_sale_service_output) {

            // Update ReceiveSaleService Success
            $message = [
              'status' => true,
              'message' => 'Update Receive sale service Successful',
            ];

            $this->response($message, REST_Controller::HTTP_OK);
          } else {

            // Update ReceiveSaleService Error
            $message = [
              'status' => false,
              'message' => 'Update Receive sale service Fail : [Update Data Fail]',
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
   * Show ReceiveSaleService Item API
   * ---------------------------------
   * @method : GET
   * @link : receive_sale_service/item
   */
  public function item_get()
  {

    header("Access-Control-Allow-Origin: *");

    // Load Authorization Token Library
    $this->load->library('Authorization_Token');

    // ReceiveSaleService Token Validation
    $is_valid_token = $this->authorization_token->validateToken();

    if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {
      // Load ReceiveSaleService Function
      $output = $this->ReceiveSaleService_Model->select_receive_sale_service_item($this->input->get('Rec_ID'));

      if (isset($output) && $output) {

        // Show ReceiveSaleService All Success
        $message = [
          'status' => true,
          'data' => $output,
          'message' => 'Show receive sale service item successful',
        ];

        $this->response($message, REST_Controller::HTTP_OK);
      } else {

        // Show ReceiveSaleService All Error
        $message = [
          'status' => false,
          'message' => 'Receive sale service Item data was not found in the database',
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
   * Exec ReceiveSaleService Transaction API
   * ---------------------------------
   * @param: FormData
   * ---------------------------------
   * @method : POST
   * @link : receive_sale_service/exec_transaction
   */
  public function exec_transaction_post()
  {

    header("Access-Control-Allow-Origin: *");

    # XSS Filtering  (https://codeigniter.com/userguide3/libraries/security.html)
    $_POST = $this->security->xss_clean($_POST);

    # Form Validation (https://codeigniter.com/userguide3/libraries/form_validation.html)
    $this->form_validation->set_rules('Rec_ID', 'Rec_ID', 'trim|required');
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

      // ReceiveSaleService Token Validation
      $is_valid_token = $this->authorization_token->validateToken();

      if (isset($is_valid_token) && boolval($is_valid_token['status']) === true) {

        $receive_sale_service_token = json_decode(json_encode($this->authorization_token->userData()), true);
        $receive_sale_service_permission = array_filter($receive_sale_service_token['permission'], function ($permission) {
          return $permission['MenuId'] == $this->MenuId;
        });

        if ($receive_sale_service_permission[array_keys($receive_sale_service_permission)[0]]['Created']) {

          $tag_data = [
            'Rec_ID' => intval($this->input->post('Rec_ID')),
            'QR_NO' => $this->input->post('QR_NO'),
            'Tag_ID' => intval($this->input->post('Tag_ID')),
            'Username' => $receive_sale_service_token['UserName'],
          ];

          // Exec ReceiveSaleService Transaction Function
          $receive_sale_service_output = $this->ReceiveSaleService_Model->exec_receive_sale_service_transaction($tag_data);

          if (isset($receive_sale_service_output) && $receive_sale_service_output) {

            if (boolval($receive_sale_service_output[0]['Result_status']) === true) {

              // Exec ReceiveSaleService Transaction Success
              $message = [
                'status' => true,
                'message' => $receive_sale_service_output[0]['Result_Desc'],
              ];

              $this->response($message, REST_Controller::HTTP_OK);
            } else {
              // Exec ReceiveSaleService Transaction Error Condition
              $message = [
                'status' => false,
                'message' => $receive_sale_service_output[0]['Result_Desc'],
              ];

              $this->response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
          } else {

            // Exec ReceiveSaleService Transaction Error
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
