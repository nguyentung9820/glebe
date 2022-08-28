<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

$formatted_destination    = isset( $formatted_destination ) ? $formatted_destination : WC()->countries->get_formatted_address( $package['destination'], ', ' );
$has_calculated_shipping  = ! empty( $has_calculated_shipping );
$show_shipping_calculator = ! empty( $show_shipping_calculator );
$calculator_text          = '';
?>
<tr class="woocommerce-shipping-totals shipping">
	<th><?php echo wp_kses_post( $package_name ); ?></th>
	<td data-title="<?php echo esc_attr( $package_name ); ?>">
		<?php if ( $available_methods ) : ?>
			<ul id="shipping_method" class="woocommerce-shipping-methods">
				<?php foreach ( $available_methods as $method ) : ?>
					<li>
						<?php
						if ( 1 < count( $available_methods ) ) {
							printf( '<input type="radio" onload="handleClick(this);" onchange="handleClick(this);" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ), checked( $method->id, $chosen_method, false ) ); // WPCS: XSS ok.
						} else {
							printf( '<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" />', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ) ); // WPCS: XSS ok.
						}
						printf( '<label for="shipping_method_%1$s_%2$s">%3$s</label>', $index, esc_attr( sanitize_title( $method->id ) ), wc_cart_totals_shipping_method_label( $method ) ); // WPCS: XSS ok.
            ?>
            <?php if (sanitize_title( wc_cart_totals_shipping_method_label( $method ) == "Local pickup") && checked( $method->id, $chosen_method, false )) : ?>
            <div>
              <p>Select a Pickup Date:</p>
              <?php
              $selected_time = (string)@WC()->session->get( 'chosen_prefer_pickup' )['prefer_time'];
              $selected_date = (string)@WC()->session->get( 'chosen_prefer_pickup' )['prefer_date'];

              if (empty($selected_time) || empty($selected_date)){
                $data = array(
                  'prefer_time' => $selected_time ?: date("H:i"),
                  'prefer_date' => $selected_date ?: date("Y-m-d"),
                );
                WC()->session->set( 'chosen_prefer_pickup',$data );
                $selected_time = (string)@WC()->session->get( 'chosen_prefer_pickup' )['prefer_time'];
                $selected_date = (string)@WC()->session->get( 'chosen_prefer_pickup' )['prefer_date'];

              }
              ?>
              <?php if (is_cart()) : ?>
                <input type="date" id="pickup_date" value="<?= $selected_date; ?>" min="<?php echo date("Y-m-d"); ?>"/>
              <?php else: ?>
              <p><?= $selected_date?></p>
              <?php endif; ?>
              <p>Prefer Pickup Time:</p>
              <?php if (is_cart()) : ?>
              <input type="time" name="pickup_time" id="pickup_time" value="<?= $selected_time; ?>">
              <?php else: ?>
                <p><?= strtoupper($selected_time)?></p>
              <?php endif; ?>
              <br/>
            </div>
            <?php endif; ?>
            <?php
						do_action( 'woocommerce_after_shipping_rate', $method, $index );
						?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( is_cart() ) : ?>
        <?php if ($chosen_method !="local_pickup:1") : ?>
				<p class="woocommerce-shipping-destination">
					<?php
					if ( $formatted_destination ) {
						// Translators: $s shipping destination.
						printf( esc_html__( 'Shipping to %s.', 'woocommerce' ) . ' ', '<strong>' . esc_html( $formatted_destination ) . '</strong>' );
						$calculator_text = esc_html__( 'Change address', 'woocommerce' );
					} else {
						echo wp_kses_post( apply_filters( 'woocommerce_shipping_estimate_html', __( 'Shipping options will be updated during checkout.', 'woocommerce' ) ) );
					}
					?>
				</p>
        <?php endif; ?>
      <?php endif; ?>
			<?php
		elseif ( ! $has_calculated_shipping || ! $formatted_destination ) :
			if ( is_cart() && 'no' === get_option( 'woocommerce_enable_shipping_calc' ) ) {
        if ($chosen_method !="local_pickup:1") {
          echo wp_kses_post(apply_filters('woocommerce_shipping_not_enabled_on_cart_html', __('Shipping costs are calculated during checkout.', 'woocommerce')));
        }
      } else {
				echo wp_kses_post( apply_filters( 'woocommerce_shipping_may_be_available_html', __( 'Enter your address to view shipping options.', 'woocommerce' ) ) );
			}
		elseif ( ! is_cart() ) :
			echo wp_kses_post( apply_filters( 'woocommerce_no_shipping_available_html', __( 'There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' ) ) );
		else :
			// Translators: $s shipping destination.
			echo wp_kses_post( apply_filters( 'woocommerce_cart_no_shipping_available_html', sprintf( esc_html__( 'No shipping options were found for %s.', 'woocommerce' ) . ' ', '<strong>' . esc_html( $formatted_destination ) . '</strong>' ) ) );
			$calculator_text = esc_html__( 'Enter a different address', 'woocommerce' );
		endif;
		?>

		<?php if ( $show_package_details ) : ?>
			<?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html( $package_details ) . '</small></p>'; ?>
		<?php endif; ?>

		<?php if ( $show_shipping_calculator ) : ?>
      <?php if ( $chosen_method !="local_pickup:1" ) : ?>
        <?php woocommerce_shipping_calculator( $calculator_text ); ?>
      <?php endif; ?>
    <?php endif; ?>
	</td>
</tr>
