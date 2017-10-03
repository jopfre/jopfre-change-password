<?php
/*
Plugin Name:  Jopfre Front End Change Password
Plugin URI:   
Description:  
Version:      
Author: jopfre      
Author URI:   
License:      
License URI:  
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
 
add_action( 'wp_enqueue_scripts', 'jopfre_change_pass_enqueue' );
function jopfre_change_pass_enqueue() {
  wp_enqueue_script( 'jopfre-change-pass', plugin_dir_url( __FILE__ ) . '/jopfre-change-pass.js', array( 'jquery', 'jquery-form' ) );
  wp_localize_script( 'jopfre-change-pass', 'jopfreChangePass', array(
    'ajaxUrl'=> admin_url( 'admin-ajax.php' ),
    'nonce' => wp_create_nonce( 'jopfre-change-pass-nonce' ),
   )
  );
}

// add_action( 'wp_ajax_nopriv_jopfre-change-pass', 'jopfre-change-pass' );
add_action( 'wp_ajax_jopfre-change-pass', 'jopfre_change_pass' );

function jopfre_change_pass() {
  if ( ! wp_verify_nonce( $_POST['nonce'], 'jopfre-change-pass-nonce' ) ) {
    // echo json_encode('nonce failed');
    exit;
  }

  $current_user = wp_get_current_user();
  
  $curr_pass = trim($_POST['curr_pass']);
  $new_pass   = trim($_POST['new_pass']);
  $conf_pass  = trim($_POST['conf_pass']);
  
  if (!empty($curr_pass) && !empty($new_pass) && !empty($conf_pass) ) {

    if ( !wp_check_password( $curr_pass, $current_user->user_pass, $current_user->ID) ) {
      echo json_encode(array('status'=>'error', 'statusField' => 'curr-pass-status', 'message'=>__('Your current password does not match. Please retry.')));
    } elseif ( $new_pass != $conf_pass ) {
      echo json_encode(array('status'=>'error', 'statusField' => 'conf-pass-status', 'message'=>__('The passwords do not match. Please retry.')));
    } elseif ( strlen($new_pass) < 4 ) {
      echo json_encode(array('status'=>'error', 'statusField' => 'new-pass-status', 'message'=>__('Password too short.')));
    } elseif ( false !== strpos( wp_unslash($new_pass), "\\" ) ) {
      echo json_encode(array('status'=>'error', 'statusField' => 'new-pass-status', 'message'=>__('Password may not contain the character "\\" (backslash).')));
    } else {
      wp_set_password( $new_pass, $current_user->ID );
      echo json_encode(array('status'=>'success', 'statusField' => 'submit-status', 'message'=>__('Password successfully changed!<br/><br/>You will be redirected soon. Please log in again with your new password!')));
    }
        
  } else {
    echo json_encode(array('status'=>'error',  'statusField' => 'submit-status', 'message'=>__('All fields must be completed.')));
  }
  exit;
}

function jopfre_change_pass_form() {
  return '<p>Change your password</p>
          <form method="post" id="jopfre-change-pass-form" action="">
            <p><input type="password" autocomplete="off" maxlength="20" class="form-control" id="curr_pass" name="curr_pass" placeholder="Current password" required/></p>
            <p class="curr-pass-status status"></p>
            <p><input type="password" autocomplete="off" maxlength="20" class="form-control" id="new_pass" name="new_pass" placeholder="New password" required></p>
            <p class="new-pass-status status"></p>
            <p><input type="password" autocomplete="off" maxlength="20" class="form-control" id="conf_pass" name="conf_pass" placeholder="Confirm password" required/></p>
            <p class="conf-pass-status status"></p> 
            <input type="submit" class="btn-submit" value="Send" />
            <span class="submit-status status"></span> 
          </form>';
}
add_shortcode( 'jopfre-change-pass-form', 'jopfre_change_pass_form' );
?>