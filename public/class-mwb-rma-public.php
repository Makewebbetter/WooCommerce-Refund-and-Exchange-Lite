<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       www.makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Rma
 * @subpackage Mwb_Rma/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mwb_Rma
 * @subpackage Mwb_Rma/public
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Mwb_Rma_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mwb_Rma_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mwb_Rma_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mwb-rma-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mwb_Rma_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mwb_Rma_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mwb-rma-public.js', array( 'jquery' ), $this->version, false );
		$ajax_nonce = wp_create_nonce( "mwb-rma-ajax-security-string" );
		$user_id = get_current_user_id();

		if($user_id > 0)
		{
			$myaccount_page = get_option( 'woocommerce_myaccount_page_id' );
			$myaccount_page_url = get_permalink( $myaccount_page );
		}
		else
		{
			$myaccount_page_url='';
			$myaccount_page_url=apply_filters('myaccount_page_url',$myaccount_page_url);
		}
		$translation_array = array(
			'attachment_msg'		=> __( 'File should be of .png , .jpg, or .jpeg extension' , 'mwb-rma'),
			'return_subject_msg' 	=> __( 'Please enter refund subject.', 'mwb-rma' ),
			'return_reason_msg'		=> __( 'Please enter refund reason.', 'mwb-rma' ),
			'mwb_rma_nonce'			=>	$ajax_nonce,
			'ajaxurl' 				=> admin_url('admin-ajax.php'),
			'myaccount_url' 		=> $myaccount_page_url,
		);
		wp_localize_script(  $this->plugin_name, 'global_mwb_rma', $translation_array );

	}

	/**
	 *  Add template for refund request form.
	 * @param $template
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */

	public function mwb_rma_product_return_template($template){
		
		$mwb_rma_return_request_form_page_id = get_option('mwb_rma_return_request_form_page_id');
		if(is_page($mwb_rma_return_request_form_page_id))
		{

			$located = locate_template('mwb-rma/public/partials/mwb-rma-refund-request-form.php');
			if ( !empty( $located ) ) {

				$new_template =wc_get_template('mwb-rma/public/partials/mwb-rma-refund-request-form.php');
			}
			else
			{
				$new_template = MWB_RMA_DIR_PATH. 'public/partials/mwb-rma-refund-request-form.php';
			}
			$template =  $new_template;
		}
		return $template;
	}

	/**
	 * This function is to add Return button on thankyou page after order details and show Return Product details
	 * 
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */
	public function mwb_rma_typ_order_return_button($order){

		$mwb_rma_refund_settings = get_option( 'mwb_rma_refund_settings' ,array());
		$mwb_rma_return_request_form_page_id =  get_option('mwb_rma_return_request_form_page_id',true);

		if(isset($mwb_rma_refund_settings) && !empty($mwb_rma_refund_settings) && is_array($mwb_rma_refund_settings)){

			$mwb_rma_refund_enable = isset($mwb_rma_refund_settings['mwb_rma_return_enable'])?$mwb_rma_refund_settings['mwb_rma_return_enable']:'';
			$mwb_rma_refund_max_days=isset($mwb_rma_refund_settings['mwb_rma_return_days'])?$mwb_rma_refund_settings['mwb_rma_return_days']:'';

			if($mwb_rma_refund_enable == 'on'){

				$order_id=$order->get_id();
				$order_date = date_i18n( 'd-m-Y', strtotime( $order->get_date_created()  ) );
				$statuses = isset($mwb_rma_refund_settings['mwb_rma_return_order_status'])?$mwb_rma_refund_settings['mwb_rma_return_order_status']:array();
				$order_status ="wc-".$order->get_status();

				if(in_array($order_status, $statuses))
				{
					$today_date = date_i18n( 'd-m-Y' );
					$order_date = strtotime($order_date);
					$today_date = strtotime($today_date);
					$days = $today_date - $order_date;
					$day_diff = floor($days/(60*60*24));
					$page_id=$mwb_rma_return_request_form_page_id;
					$return_url = get_permalink($page_id);
					if($mwb_rma_refund_max_days >= $day_diff && $mwb_rma_refund_max_days != 0){
						$return_url = add_query_arg('order_id',$order_id,$return_url);
						$return_url = wp_nonce_url($return_url,'mwb_rma_return_form_nonce','mwb_rma_return_form_nonce');
						?>
						<form action="<?php echo $return_url ?>" method="post">
							<input type="hidden" value="<?php echo $order_id?>" name="order_id">
							<p><input type="submit" class="btn button" value="<?php _e('Refund Request','mwb-rma');?>" name="mwb_rma_new_return_request"></p>
						</form>
						<?php 
					}
				}
			}
		}
	}

	/**
	 * Add refund button on my-account order section.
	 *
	 * @since    1.0.0
	 */
	public function mwb_rma_refund_exchange_button($actions, $order)
	{
		$mwb_rma_refund_settings = get_option( 'mwb_rma_refund_settings' ,array());
		$order = new WC_Order($order);		
		$mwb_rma_next_return = true;
		$order_id = $order->get_id();
		$mwb_rma_made = get_post_meta($order_id, "mwb_rma_request_made", true);
		if(isset($mwb_rma_made) && !empty($mwb_rma_made))
		{
			$mwb_rma_next_return = false;
		}

		if($mwb_rma_next_return)
		{				
			//Return Request at order detail page
			$mwb_rma_refund_enable = isset($mwb_rma_refund_settings['mwb_rma_return_enable'])?$mwb_rma_refund_settings['mwb_rma_return_enable']:'';
			$mwb_rma_refund_max_days=isset($mwb_rma_refund_settings['mwb_rma_return_days'])?$mwb_rma_refund_settings['mwb_rma_return_days']:'';

			if($mwb_rma_refund_enable == 'on')
			{

				$statuses = isset($mwb_rma_refund_settings['mwb_rma_return_order_status'])?$mwb_rma_refund_settings['mwb_rma_return_order_status']:array();
				$order_status ="wc-".$order->get_status();

				if(in_array($order_status, $statuses))
				{
					
					$order_date = date_i18n( 'd-m-Y', strtotime( $order->get_date_created() ) );

					$today_date = date_i18n( 'd-m-Y' );
					$order_date = strtotime($order_date);
					$today_date = strtotime($today_date);
					$days = $today_date - $order_date;
					$day_diff = floor($days/(60*60*24));

					$day_allowed = $mwb_rma_refund_max_days;

					$return_button_text = __('Refund','mwb-rma');
					
					if($day_allowed >= $day_diff && $day_allowed != 0)
					{

						$mwb_rma_return_request_form_page_id = get_option('mwb_rma_return_request_form_page_id');
						$return_url = get_permalink($mwb_rma_return_request_form_page_id);
						$order_id = $order->get_id();
						$return_url = add_query_arg('order_id',$order_id,$return_url);
						$return_url = wp_nonce_url($return_url,'mwb_rma_return_form_nonce','mwb_rma_return_form_nonce');
						$actions['return']['url'] = $return_url;
						$actions['return']['name'] = $return_button_text;

					}	

				}
			}
		}
		return $actions;
	}

	public function test_func_callback($order_id,$message){
				$order = new WC_Order($order_id);
				$fname = get_post_meta($order_id, '_billing_first_name', true);
				$lname = get_post_meta($order_id, '_billing_last_name', true);
				$billing_company = get_post_meta($order_id, '_billing_company', true);
				$billing_email = get_post_meta($order_id, '_billing_email', true);
				$billing_phone = get_post_meta($order_id, '_billing_phone', true);
				$billing_country = get_post_meta($order_id, '_billing_country', true);
				$billing_address_1 = get_post_meta($order_id, '_billing_address_1', true);
				$billing_address_2 = get_post_meta($order_id, '_billing_address_2', true);
				$billing_state = get_post_meta($order_id, '_billing_state', true);
				$billing_postcode = get_post_meta($order_id, '_billing_postcode', true);
				$shipping_first_name = get_post_meta($order_id, '_shipping_first_name', true);
				$shipping_last_name = get_post_meta($order_id, '_shipping_last_name', true);
				$shipping_company = get_post_meta($order_id, '_shipping_company', true);
				$shipping_country = get_post_meta($order_id, '_shipping_country', true);
				$shipping_address_1 = get_post_meta($order_id, '_shipping_address_1', true);
				$shipping_address_2 = get_post_meta($order_id, '_shipping_address_2', true);
				$shipping_city = get_post_meta($order_id, '_shipping_city', true);
				$shipping_state = get_post_meta($order_id, '_shipping_state', true);
				$shipping_postcode = get_post_meta($order_id, '_shipping_postcode', true);
				$payment_method_tittle = get_post_meta($order_id, '_payment_method_title', true);
				$order_shipping = get_post_meta($order_id, '_order_shipping', true);
				$order_total = get_post_meta($order_id, '_order_total', true);
				$refundable_amount = get_post_meta($order_id, 'refundable_amount', true);

				$message = str_replace('[_billing_company]', $billing_company, $message);
				$message = str_replace('[_billing_email]', $billing_email, $message);
				$message = str_replace('[_billing_phone]', $billing_phone, $message);
				$message = str_replace('[_billing_country]', $billing_country, $message);
				$message = str_replace('[_billing_address_1]', $billing_address_1, $message);
				$message = str_replace('[_billing_address_2]', $billing_address_2, $message);
				$message = str_replace('[_billing_state]', $billing_state, $message);
				$message = str_replace('[_billing_postcode]', $billing_postcode, $message);
				$message = str_replace('[_shipping_first_name]', $shipping_first_name, $message);
				$message = str_replace('[_shipping_last_name]', $shipping_last_name, $message);
				$message = str_replace('[_shipping_company]', $shipping_company, $message);
				$message = str_replace('[_shipping_country]', $shipping_country, $message);
				$message = str_replace('[_shipping_address_1]', $shipping_address_1, $message);
				$message = str_replace('[_shipping_address_2]', $shipping_address_2, $message);
				$message = str_replace('[_shipping_city]', $shipping_city, $message);
				$message = str_replace('[_shipping_state]', $shipping_state, $message);
				$message = str_replace('[_shipping_postcode]', $shipping_postcode, $message);
				$message = str_replace('[_payment_method_title]', $payment_method_tittle, $message);
				$message = str_replace('[_order_shipping]', $order_shipping, $message);
				$message = str_replace('[_order_total]', $order_total, $message);
				//$message = str_replace('[_refundable_amount]', $refundable_amount, $message);
				$message = str_replace('[formatted_shipping_address]', $order->get_formatted_shipping_address(), $message);
				$message = str_replace('[formatted_billing_address]', $order->get_formatted_billing_address(), $message);
				// print_r($order_id);
				return $message;
		
	}

	/**
	 * This function is to save return request Attachment
	 * 
	 * @author makewebbetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */
	public function mwb_rma_order_return_attach_files()
	{
		$check_ajax = check_ajax_referer( 'mwb-rma-ajax-security-string', 'security_check' );
		
		if ( $check_ajax ) 
		{
			if(current_user_can('mwb-rma-refund-request'))
			{
	
				if(isset($_FILES['mwb_rma_return_request_files']))
				{
					if(isset($_FILES['mwb_rma_return_request_files']['tmp_name']))
					{
						$filename = array();
						$order_id = sanitize_text_field($_POST['mwb_rma_return_request_order']);
						$count = sizeof($_FILES['mwb_rma_return_request_files']['tmp_name']);
						for($i=0;$i<$count;$i++)
						{
							if(isset($_FILES['mwb_rma_return_request_files']['tmp_name'][$i]))
							{	
								$directory = ABSPATH.'wp-content/attachment';
								if (!file_exists($directory)) 
								{
									mkdir($directory, 0755, true);
								}

								$sourcePath = sanitize_text_field($_FILES['mwb_rma_return_request_files']['tmp_name'][$i]);
								$targetPath = $directory.'/'.$order_id.'-'.sanitize_text_field($_FILES['mwb_rma_return_request_files']['name'][$i]);

								$filename[] = $order_id.'-'.sanitize_text_field($_FILES['mwb_rma_return_request_files']['name'][$i]);
								move_uploaded_file($sourcePath,$targetPath) ;
							}
						}

						$request_files = get_post_meta($order_id, 'mwb_rma_return_attachment', true);

						$pending = true;
						if(isset($request_files) && !empty($request_files))
						{
							foreach($request_files as $date=>$request_file)
							{
								if($request_file['status'] == 'pending')
								{
									unset($request_files[$date][0]);
									$request_files[$date]['files'] = $filename;
									$request_files[$date]['status'] = 'pending';
									$pending = false;
									break;
								}
							}
						}

						if($pending)
						{	
							$request_files = array();
							$date = date("d-m-Y");
							$request_files[$date]['files'] = $filename;
							$request_files[$date]['status'] = 'pending';
						}

						update_post_meta($order_id, 'mwb_rma_return_attachment', $request_files);
						echo 'success';
					}
				}


			}
		wp_die();
		}
	}

	public function mwb_rma_return_product_info_callback(){
		$check_ajax = check_ajax_referer( 'mwb-rma-ajax-security-string', 'security_check' );
		if ( $check_ajax ) 
		{
			if(current_user_can('mwb-rma-refund-request'))
			{
				$user_email = $current_user->user_email;
				$user_name = $current_user->display_name;
				$order_id = sanitize_text_field($_POST['orderid']);
				$subject = sanitize_text_field($_POST['subject']);
				$reason = sanitize_text_field($_POST['reason']);
				// $mwb_rnx_products = array();
				$pending = true;
				
				// $mwb_rnx_products = apply_filters('mwb_rnx_get_product_details', $order_id, $mwb_rnx_products);
				// if(is_array($mwb_rnx_products) && !empty($mwb_rnx_products)){
				// 	$products = $mwb_rnx_products['products'];
				// 	$pending = $mwb_rnx_products['pending'];
				// }
				$products = get_post_meta($order_id, 'mwb_rma_return_request_product', true);
				if(isset($products) && !empty($products))
				{
					foreach($products as $date=>$product)
					{
						if($product['status'] == 'pending')
						{
							$products[$date] = $_POST;
								$products[$date]['status'] = 'pending'; //update requested products
								$pending = false;
								break;
						}	
					}
				}
				if($pending)
				{
					if(!is_array($products))
					{
						$products = array();
					}
					$products = array();
					$date = date("d-m-Y");
					$products[$date] = $_POST;
					$products[$date]['status'] = 'pending';
				}	
				
				update_post_meta($order_id, "mwb_rma_return_request_made", true);
				
				update_post_meta($order_id, 'mwb_rma_return_request_product', $products);


					//Send mail to merchant
				$mwb_rma_mail_basic_settings = get_option('mwb_rma_mail_basic_settings',array());
				$mwb_rma_mail_refund_settings = get_option('mwb_rma_mail_refund_settings',array());

				$reason_subject = $subject;
					
				$mail_header = stripslashes(isset($mwb_rma_mail_basic_settings['mwb_rma_mail_header'])? $mwb_rma_mail_basic_settings['mwb_rma_mail_header']:'');
				$mail_footer = stripslashes(isset($mwb_rma_mail_basic_settings['mwb_rma_mail_footer'])? $mwb_rma_mail_basic_settings['mwb_rma_mail_footer']:'');

				$message = '<html>
					<body>
						'.do_action('wrnx_return_request_before_mail_content', $order_id).'
						<style>
							body {
								box-shadow: 2px 2px 10px #ccc;
								color: #767676;
								font-family: Arial,sans-serif;
								margin: 80px auto;
								max-width: 700px;
								padding-bottom: 30px;
								width: 100%;
							}

							h2 {
								font-size: 30px;
								margin-top: 0;
								color: #fff;
								padding: 40px;
								background-color: #557da1;
							}

							h4 {
								color: #557da1;
								font-size: 20px;
								margin-bottom: 10px;
							}

							.content {
								padding: 0 40px;
							}

							.Customer-detail ul li p {
								margin: 0;
							}

							.details .Shipping-detail {
								width: 40%;
								float: right;
							}

							.details .Billing-detail {
								width: 60%;
								float: left;
							}

							.details .Shipping-detail ul li,.details .Billing-detail ul li {
								list-style-type: none;
								margin: 0;
							}

							.details .Billing-detail ul,.details .Shipping-detail ul {
								margin: 0;
								padding: 0;
							}

							.clear {
								clear: both;
							}

							table,td,th {
								border: 2px solid #ccc;
								padding: 15px;
								text-align: left;
							}

							table {
								border-collapse: collapse;
								width: 100%;
							}

							.info {
								display: inline-block;
							}

							.bold {
								font-weight: bold;
							}

							.footer {
								margin-top: 30px;
								text-align: center;
								color: #99B1D8;
								font-size: 12px;
							}
							dl.variation dd {
								font-size: 12px;
								margin: 0;
							}
						</style>
						<div class="header" style="text-align:center;padding: 10px;">
							'.$mail_header.'
						</div>	
						<div class="header">
							<h2>'.$reason_subject.'</h2>
						</div>
						<div class="content">

							<div class="reason">
								<h4>'.__('Reason of Refund', 'mwb-rma').'</h4>
								<p>'.$reason.'</p>
							</div>
							<div class="Order">
								<h4>Order #'.$order_id.'</h4>
								<table>
									<tbody>
										<tr>
											<th>'.__('Product', 'mwb-rma').'</th>
											<th>'.__('Quantity', 'mwb-rma').'</th>
											<th>'.__('Price', 'mwb-rma').'</th>
										</tr>';
						
										$order = new WC_Order($order_id);
										$requested_products = $products[$date]['products'];

										if(isset($requested_products) && !empty($requested_products))
										{
											$total = 0;
											foreach( $order->get_items() as $item_id => $item )
											{
												//$product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
												foreach($requested_products as $requested_product)
												{
													if(isset($requested_product['item_id']))
													{	
														if($item_id == $requested_product['item_id'])
														{
															if(isset($requested_product['variation_id']) && $requested_product['variation_id'] > 0)
															{
																$prod = wc_get_product($requested_product['variation_id']);

															}
															else
															{
																$prod = wc_get_product($requested_product['product_id']);
															}

															$subtotal = $requested_product['price']*$item['qty'];
															
															$total += $subtotal;
															//print_r($_product);
															// $item_meta      = new WC_Order_Item_Product( $item, $_product );
															// //print_r($item_meta);
															// $item_meta_html = wc_display_item_meta($item_meta,array('echo'=> false));
															//print_r()
															$message .= '<tr>
															<td>'.$item['name'].'<br>';
																$message .= '<small>'.$item_meta_html.'</small>
																<td>'.$item['qty'].'</td>
																<td>'.wc_price($requested_product['price']*$item['qty']).'</td>
															</tr>';
														}
													}
												}	
											}	
										}

										$message .= '<tr>
										<th colspan="2">'.__('Refund Total', 'mwb-rma').':</th>
										<td>'.wc_price($total).'</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="Customer-detail">
							<h4>'.__('Customer details', 'mwb-rma').'</h4>
							<ul>
								<li><p class="info">
									<span class="bold">'.__('Email', 'mwb-rma').': </span>'.get_post_meta($order_id, '_billing_email', true).'
								</p></li>
								<li><p class="info">
									<span class="bold">'.__('Tel', 'mwb-rma').': </span>'.get_post_meta($order_id, '_billing_phone', true).'
								</p></li>
							</ul>
						</div>
						<div class="details">
							<div class="Shipping-detail">
								<h4>'.__('Shipping Address', 'mwb-rma').'</h4>
								'.$order->get_formatted_shipping_address().'
							</div>
							<div class="Billing-detail">
								<h4>'.__('Billing Address', 'mwb-rma').'</h4>
								'.$order->get_formatted_billing_address().'
							</div>
							<div class="clear"></div>
						</div>
						
					</div>
					<div class="footer" style="text-align:center;padding: 10px;">
						'.$mail_footer.'
					</div>

				</body>
				</html>';
				

				$headers = array();
				$headers[] = "Content-Type: text/html; charset=UTF-8";
				$to = isset($mwb_rma_mail_basic_settings['mwb_rma_mail_from_email'])? $mwb_rma_mail_basic_settings['mwb_rma_mail_from_email']:'';
				$subject = isset($mwb_rma_mail_refund_settings['mwb_rma_mail_merchant_return_subject'])? $mwb_rma_mail_refund_settings['mwb_rma_mail_merchant_return_subject']:'';
				$subject = str_replace('[order]', "#".$order_id, $subject);	
				
				wc_mail( $to, $subject, $message, $headers );
					
				//Send mail to User that we recieved your request

				$fname =  isset($mwb_rma_mail_basic_settings['mwb_rma_mail_from_email'])? $mwb_rma_mail_basic_settings['mwb_rma_mail_from_email']:'';
				$fmail =  isset($mwb_rma_mail_basic_settings['mwb_rma_mail_from_name'])? $mwb_rma_mail_basic_settings['mwb_rma_mail_from_name']:'';

				$to = get_post_meta($order_id, '_billing_email', true);
				$headers = array();
				$headers[] = "From: $fname <$fmail>";
				$headers[] = "Content-Type: text/html; charset=UTF-8";
				$subject = isset($mwb_rma_mail_refund_settings['mwb_rma_mail_return_subject'])? $mwb_rma_mail_refund_settings['mwb_rma_mail_return_subject']:'';
				$subject = str_replace('[order]', "#".$order_id, $subject);
				$message = stripslashes(isset($mwb_rma_mail_refund_settings['mwb_rma_mail_return_message'])? $mwb_rma_mail_refund_settings['mwb_rma_mail_return_message']:'');

				$message = str_replace('[order]', "#".$order_id, $message);
				$message = str_replace('[siteurl]', home_url(), $message);
				$firstname = get_post_meta($order_id, '_billing_first_name', true);
				$lname = get_post_meta($order_id, '_billing_last_name', true);
				
				$fullname = $firstname." ".$lname;
				$message = str_replace('[username]', $fullname, $message);

				$mwb_rma_shortcode='';
				$mwb_rma_shortcode = $message;

				$mwb_rma_shortcode = apply_filters( 'mwb_rma_add_shortcode_refund_mail' , $order_id,$mwb_rma_shortcode);
				
				$message = $mwb_rma_shortcode;
				
				$mwb_rma_refund_template = false;
				$mwb_rma_refund_template = apply_filters('mwb_rma_refund_template',$mwb_rma_refund_template );

				if($mwb_rma_refund_template){
					$html_content = $message;
				}else{
					$html_content = '<html>
										<head>
											<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
											<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
										</head>
										<body style="margin: 1% 0 0; padding: 0;">
											<table cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td style="text-align: center; margin-top: 30px; padding: 10px; color: #99B1D8; font-size: 12px;">
														'.$mail_header.'
													</td>
												</tr>
												<tr>
													<td>
														<table align="center" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family:Open Sans; max-width: 600px; width: 100%;">
															<tr>
																<td style="padding: 36px 48px; width: 100%; background-color:#557DA1;color: #fff; font-size: 30px; font-weight: 300; font-family:helvetica;">'.$subject.'</td>
															</tr>
															<tr>
																<td style="width:100%; padding: 36px 48px 10px; background-color:#fdfdfd; font-size: 14px; color: #737373;">'.$message.'</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td style="text-align: center; margin-top: 30px; color: #99B1D8; font-size: 12px;">
														'.$mail_footer.'
													</td>
												</tr>				
											</table>

										</body>
										</html>';
				}

				wc_mail($to, $subject, $html_content, $headers );

				$order->update_status('wc-return-requested', 'User Request to Refund Product');
				$response['msg'] = __('Message send successfully.You have received a notification mail regarding this, Please check your mail. Soon You redirect to My Account Page. Thanks', 'mwb-rma');
				$auto_accept_day_allowed = false;
				$auto_accept_day_allowed = apply_filters( 'auto_accept_day_allowed',$auto_accept_day_allowed,$order);
			
				if($auto_accept_day_allowed){
					$response['auto_accept'] = true;
				}

				echo json_encode($response);
				wp_die();
			}
		}
	}


}
