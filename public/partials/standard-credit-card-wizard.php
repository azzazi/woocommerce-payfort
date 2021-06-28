<?php
echo '<div class="pf-iframe-background" id="div-pf-iframe" style="display:none">
<div class="pf-iframe-container">
    <span class="pf-close-container">
        <a href="' . esc_url( create_wc_api_url( 'aps_merchant_cancel' ) ) . '"><i class="fa fa-times-circle pf-iframe-close"></i></a>
    </span>
    <i class="fa fa-spinner fa-spin pf-iframe-spin"></i>
    <div class="pf-iframe" id="pf_iframe_content"></div>
</div>
</div><div class="form_box"></div>';

$tokens = array();
if ( 'yes' === $is_enabled_tokenization ) {
	$tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), APS_Constants::APS_PAYMENT_TYPE_CC );
	if ( 'yes' === $have_subscription ) {
		$tokens = array_filter(
			$tokens,
			function( $token_row ) {
				if ( in_array( $token_row->get_card_type(), array( 'visa', 'mastercard', 'amex' ), true ) ) {
					return true;
				} else {
					return false;
				}
			}
		);
	}
}
echo '<ul class="token-box">';
foreach ( $tokens as $token_row ) {
	$checked      = intval( $token_row->get_is_default() ) === 1 ? 'checked' : '';
	$masking_card = get_metadata( 'payment_token', $token_row->get_id(), 'masking_card', true );
	$maxlength    = 'amex' === $token_row->get_card_type() ? 4 : 3;
	$card_type = $token_row->get_card_type();
	if($card_type != 'mada'){
		$card_type = strtoupper($card_type);
	}
	echo '<li>';
		echo '<div class="aps-row ' . wp_kses_data( $checked ) . '">';
			echo '<div class="aps-col-sm-9">';
				echo '<input type="radio" class="aps_cc_token aps_token_radio" name="aps_payment_token_cc" data-masking-card="' . wp_kses_data( substr( $masking_card, 0, 6 ) ) . '" value="' . wp_kses_data( $token_row->get_token() ) . '" ' . $checked . '/>';
				echo '<img class="card-icon" src="' . $card_icons[ $token_row->get_card_type() ] . '"/>';
				echo '<strong>' . $card_type . ' ' . $token_row->get_last4() . '</strong> ';
				echo __( 'exp', 'amazon_payment_services' ) . ' ' . $token_row->get_expiry_month() . '/' . $token_row->get_expiry_year();
			echo '</div>';
			echo '<div class="aps-col-sm-3">';
				echo '<input type="text" value="" autocomplete="off" maxlength="' . $maxlength . '" class="input-text aps_saved_card_cvv onlynum" placeholder="' . __( 'CVV', 'amazon_payment_services' ) . '">';
			echo '</div>';
		echo '</div>';
	echo '</li>';
}
echo '<li>';
echo '<div class="aps-col-sm-12"> <input type="radio" name="aps_payment_token_cc" value="" class="aps_token_card aps_token_radio" required/> ' . __( 'Add a new card', 'amazon_payment_services' ) . '</div>';
echo '</li>';
echo '</ul>';
