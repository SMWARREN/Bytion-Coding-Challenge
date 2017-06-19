<?php
/**
 *
 * Wrtie [BCCFORM] in your post editor to render this shortcode.
 *
 * @package	 SWBCC
 * @since    1.0.0
 */

if ( ! function_exists( 'SW_BCC_shortcode' ) ) {
    add_action( 'plugins_loaded', function() {
        add_shortcode( 'BCCFORM', 'SW_BCC_shortcode' );
    });

    /**
     * Bytion Coding Challenge Form ShortCode
     *
     * A shortcode which Authors can use to insert a HTML form into their content.
     * The form shouldinclude a name and email field, along with a submitbutton.
     *
     *
     * @return  A form to be displayed.
     **/

    function SW_BCC_shortcode() {
      SW_BCC_FORM();
      return
    '<div class="form">
      <form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">
      <div class="field">
        <label for="BCC_NAME">Name:</label>
        <input id="BCC_NAME" name="BCC_NAME" type="text">
      </div>
      <div class="field">
        <label for="BCC_EMAIL">Email Address:</label>
        <input id="BCC_EMAIL" name="BCC_EMAIL" type="email" >
      </div>
      <button name="BCC_FORM">Submit</button>
    </form>
  </div>';

    }

  function SW_BCC_FORM() {
    if ( isset( $_POST['BCC_FORM'] ) ) {

        $name    = sanitize_text_field( $_POST["BCC_NAME"] );
        $email   = sanitize_email( $_POST["BCC_EMAIL"] );
    }
  }
}
