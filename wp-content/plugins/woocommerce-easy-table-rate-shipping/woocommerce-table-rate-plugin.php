<?php
/*
  Plugin Name: Woocommerce Table Rate Shipping
  Plugin URI: http://www.jem-products.com/plugins.html
  Description: Provides shipping for Woocommerce based upon a table of rates. Unlimited countries.
  Version: 2.0.3
  Author: JEM Plugins
  Author URI: http://www.jem-products.com
  Requires at least: 4.0
  Tested up to: 4.9.8
  WC requires at least: 2.6.14
  WC tested up to: 3.5.1
 */


if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

    
//lets define some constants
define('JEM_DOMAIN', 'jem-table-rate-shipping-for-woocommerce');
define('JEM_URL', plugin_dir_url(__FILE__));  // Plugin URL
/**
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    function jem_table_rate_init() {
        if (!class_exists('JEM_Table_Rate_Shipping_Method')) {

            class JEM_Table_Rate_Shipping_Method extends WC_Shipping_Method {

                //Field declarations
                private $jem_shipping_method_order_option;
                private $zones_settings;
                private $rates_settings;
                private $option_key;
                private $jem_shipping_methods_option;
                private $condition_array;
                private $options;
                private $country_array;
                private $counter;

                /**
                 * Constructor for your shipping class

                 */
                public function __construct($instance_id = 0) {
                    $this->instance_id = absint($instance_id);
                    $this->id = 'jem_table_rate';      // Id for your shipping method. Should be uunique.
                    $this->method_title = __('Table Rate', 'JEM_DOMAIN');  // Title shown in admin
                    $this->method_description = __('Table Rate lets you define shipping based on a table of values', 'JEM_DOMAIN'); // Description shown in admin
                    $this->jem_shipping_method_order_option = 'jem_table_rate_shipping_method_order_' . $this->instance_id;
                    $this->supports = array(
                        'shipping-zones',
                        'instance-settings',
                    );
                    $this->zones_settings = $this->id . 'zones_settings';
                    $this->rates_settings = $this->id . 'rates_settings';
                    $this->enabled = "yes";         // This can be added as an setting but for this example its forced enabled
                    $this->title = "Table Rate";     // This can be added as an setting but for this example its forced.

                    $this->option_key = $this->id . '_table_rates';   //The key for wordpress options
                    $this->jem_shipping_methods_option = 'jem_table_rate_shipping_methods_' . $this->instance_id;
                    $this->options = array();         //the actual tabel rate options saved
                    $this->condition_array = array();    //holds an array of CONDITIONS for the select
                    $this->country_array = array();     //holds an array of COUNTRIES for the select
                    $this->counter = 0;         //we use this to keep unique names for the rows


                    $this->title = $this->get_option('title');

                    $this->init();
                    $this->enabled = $this->get_option('enabled');
                    $this->title = $this->get_option('title');

                    $this->get_options();           //load the options
                }

                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init() {
                    $this->instance_form_fields = array(
                        'enabled' => array(
                            'title' => __('Enable/Disable', 'JEM_DOMAIN'),
                            'type' => 'checkbox',
                            'label' => __('Enable this shipping method', 'JEM_DOMAIN'),
                            'default' => 'no'
                        ),
                        'title' => array(
                            'title' => __('Checkout Title', 'JEM_DOMAIN'),
                            'description' => __('This controls the title which the user sees during checkout.', 'JEM_DOMAIN'),
                            'type' => 'text',
                            'default' => 'Table Rate',
                            'desc_tip' => true
                        ),
                        'handling_fee' => array(
                            'title' => __('Handling Fee', 'JEM_DOMAIN'),
                            'description' => __('Enter an amount for the handling fee - leave BLANK to disable.', 'JEM_DOMAIN'),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'tax_status' => array(
                            'title' => __('Tax Status', 'JEM_DOMAIN'),
                            'type' => 'select',
                            'default' => 'taxable',
                            'options' => array(
                                'taxable' => __('Taxable', 'JEM_DOMAIN'),
                                'notax' => __('Not Taxable', 'JEM_DOMAIN'),
                            )
                        ),
                        'shipping_list' => array(
                            'type' => 'shipping_list'
                        )
                    );
                    // Load the settings API
                    $this->init_form_fields();  // This is part of the settings API. Override the method to add your own settings
                    $this->init_settings();  // This is part of the settings API. Loads settings you previously init.


                    //set up the select arrays
                    $this->create_select_arrays();

                    // Save settings in admin if you have any defined
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
                    //And save our options
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_custom_settings'));
                }

                /**
                 * * This initialises the form field
                 */
                function init_form_fields() {

                    $this->form_fields = array(
                        'shipping_list' => array(
                            'title' => __('Shipping Methods', 'JEM_DOMAIN'),
                            'type' => 'shipping_list',
                            'description' => '',
                        )
                    );
                }


                /**
                 * admin_options
                 * These generates the HTML for all the options
                 */
                public function generate_table_rates_table_html($key, $data) {
                    ob_start();
                    if (isset($_GET['action'])) {
                        $get_action_name = $_GET['action'];
                    }
                    ?>
                    <!--  begin email -->

                    <!-- Begin MailChimp Signup Form -->
                    <link href="//cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css">
                    <style type="text/css">
                        #mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
                        /* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
                           We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
                        #optin {
                            background: #dde2ec;
                            border: 2px solid #1c3b7e;
                            /* padding: 20px 15px; */
                            text-align: center;
                            width: 800px;
                        }
                        #optin input {
                            background: #fff;
                            border: 1px solid #ccc;
                            font-size: 15px;
                            margin-bottom: 10px;
                            padding: 8px 10px;
                            border-radius: 3px;
                            -moz-border-radius: 3px;
                            -webkit-border-radius: 3px;
                            box-shadow: 0 2px 2px #ddd;
                            -moz-box-shadow: 0 2px 2px #ddd;
                            -webkit-box-shadow: 0 2px 2px #ddd
                        }
                        #optin input.name { background: #fff url('<?php echo JEM_URL; ?>/images/name.png') no-repeat 10px center; padding-left: 35px }
                        #optin input.myemail { background: #fff url('<?php echo JEM_URL; ?>/images/email.png') no-repeat 10px center; padding-left: 35px }
                        #optin button {
                            background: #217b30 url('<?php echo JEM_URL; ?>/images/green.png') repeat-x top;
                            border: 1px solid #137725;
                            color: #fff;
                            cursor: pointer;
                            font-size: 14px;
                            font-weight: bold;
                            padding: 2px 0;
                            text-shadow: -1px -1px #1c5d28;
                            width: 120px;
                            height: 38px;
                        }
                        #optin button:hover { color: #c6ffd1 }
                        .optin-header{
                            font-size: 24px;
                            color: #ffffff;
                            background-color: #1c3b7e;
                            padding: 20px 15px;
                        }
                        #jem-submit-results{
                            padding: 10px 0px;
                            font-size: 24px;
                        }
                    </style>

                    <script>
						jQuery(document).ready(function(){
							//add shipping box on page load by default. // removes an ability to click on "Add New Shipping Zone" button	
							if( jQuery('.rate-row').length == 0 ){
								var zoneID = "#" + pluginID + "_settings";
								//ok lets add a row!
								var id = "#" + pluginID + "_settings table tbody tr:last";
								//create empty row
								var row = {};
								row.key = "";
								row.min = [];
								row.rates = [];
								row.condition = [];
								row.countries = [];
								jQuery(id).before(create_zone_row(row));
							}
						});
                        jQuery("#mc_button").click(function (e) {
                            e.preventDefault();
                            console.log('clicked');
                            data = {};

                            data["EMAIL"] = jQuery("#mce-EMAIL").val();
                            data["NAME"] = jQuery("#mce-FNAME").val();

                            jQuery.ajax({
                                url: '//jem-products.us12.list-manage.com/subscribe/post-json?u=6d531bf4acbb9df72cd2e718d&amp;id=de987ac678&c=?',
                                type: 'post',
                                data: data,
                                dataType: 'json',
                                contentType: "application/json; charset=utf-8",
                                success: function (data) {
                                    if (data['result'] != "success") {
                                        //ERROR
                                        console.log("error");
                                        console.log(data['msg']);
                                    } else {
                                        //SUCCESS - Do what you like here
                                        jQuery("#jem-submit-results").text("Please Check Your Email for your Code");
                                    }
                                }
                            });

                        });

                    </script>

                    <!--  end email -->
                    <tr>
                        <th scope="row" class="titledesc"><?php _e('Table Rates', 'JEM_DOMAIN'); ?></th>
                        <td id="<?php echo $this->id; ?>_settings">
                            <table class="shippingrows widefat">
                                <col style="width:0%">
                                <col style="width:0%">
                                <col style="width:0%">
                                <col style="width:100%;">
                                <!--<thead>
                                    <tr>
                                        <th class="check-column"></th>
                                        <th>Shipping Zone Name</th>
                                        <th>Condition</th>
                                        <th>Countries</th>
                                    </tr>
                                </thead> -->
                                <tbody style="border: 1px solid black;">
                                    <tr style="border: 1px solid black;">
                                        <!--<td colspan="5" class="add-zone-buttons">
                                            <a href="#" class="add button">Add New Shipping Zone</a>
                                            <a href="#" class="delete button">Delete Selected Zones</a>
                                        </td>-->
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>                                                      
                    <?php
                    $zone = WC_Shipping_Zones::get_zone_by('instance_id', $_GET['instance_id']);
                    $get_shipping_method_by_instance_id = WC_Shipping_Zones::get_shipping_method($_GET['instance_id']);
                    $link_content = '<a href="' . admin_url('admin.php?page=wc-settings&tab=shipping') . '">' . __('Shipping Zones', 'woocommerce') . '</a> &gt ';
                    $link_content .= '<a href="' . admin_url('admin.php?page=wc-settings&tab=shipping&zone_id=' . absint($zone->get_id())) . '">' . esc_html($zone->get_zone_name()) . '</a> &gt ';
                    $link_content .= '<a href="' . admin_url('admin.php?page=wc-settings&tab=shipping&instance_id=' . $_GET['instance_id']) . '">' . esc_html($get_shipping_method_by_instance_id->get_title()) . '</a>';
//                                        <!--check action is new or edit-->  
                    if ($get_action_name == 'new') {
                        $link_content .= ' &gt ';
                        $link_content .= __('Add New', 'flexible-shipping');
                        ?>
                        <script>
                            jQuery("#mainform h2").first().replaceWith('<h2>' + '<?php echo $link_content; ?>' + '</h2>');
                            var options = <?php echo json_encode($this->create_dropdown_options()); ?>;

                            var country_array = <?php echo json_encode($this->country_array); ?>;
                            var condition_array = <?php echo json_encode($this->condition_array); ?>;
                            var pluginID = <?php echo json_encode($this->id); ?>;
console.log('test NISL 1');
                            var lastID = 0;

                        <?php
//                                                                        
                        foreach ($this->options as $key => $value) {
                            global $row;
                            //add the key back into the json object
                            $value['key'] = $key;
                            $row = json_encode($value);
                            echo "jQuery('#{$this->id}_settings table tbody tr:last').before(create_zone_row({$row}));\n";
                        }
                        ?>





                            /**
                             * This creates a new ZONE row
                             */
                            function create_zone_row(row) {

                                //lets get the ID of the last one

                                var el = '#' + pluginID + '_settings .jem-zone-row';
                                lastID = jQuery(el).last().attr('id');

                                //Handle no rows
                                if (typeof lastID == 'undefined' || lastID == "") {
                                    lastID = 1;
                                } else {
                                    lastID = Number(lastID) + 1;
                                }

                                var html = '\
                                                        <tr style="display:none;" id="' + lastID + '" class="jem-zone-row" >\
                                                                <input type="hidden" value="' + lastID + '" name="key[' + lastID + ']"></input>\
                                                                <td><input type="hidden" size="30" name="zone-name[' + lastID + ']"/></td>\
                                                        </tr>\
                                        ';

                                //This is the expandable/collapsable row for that holds the rates
                                html += '\
                                                <tr class="jem-rate-holder">\
                                                        <td colspan="3">\
                                                                <table class="jem-rate-table shippingrows widefat" id="' + lastID + '_rates">\
                                                                        <thead>\
                                                                                <tr>\
                                                                                        <th></th>\
																						<th style="width: 30%">Condition</th>\
                                                                                        <th style="width: 30%">Min Value</th>\
                                                                                        <th style="width: 30%">Max Value</th>\
                                                                                        <th style="width: 40%">Shipping Rate</th>\
                                                                                </tr>\
                                                                        </thead>\
                                                                        ' + create_rate_row(lastID, row) + '\
                                                                        <tr>\
                                                                                <td colspan="4" class="add-rate-buttons">\
                                                                                        <a href="#" class="add button" name="key_' + lastID + '">Add New Rate</a>\
                                                                                        <a href="#" class="delete button">Delete Selected Rates</a>\
                                                                                </td>\
                                                                        </tr>\
                                                                </table>\
                                                        </td>\
                                                </tr>\
                                        ';

                                return html;
                            }

                            /**
                             * This creates a new RATE row
                             * The container Table is passed in and this row is added to it
                             */
                            function create_rate_row(lastID, row) {


                                if (row == null || row.rates.length == 0) {
                                    //lets manufacture a rows
                                    //create dummy row
                                    var row = {};
                                    row.key = "";
                                    row.condition = [""];
                                    row.countries = [];
                                    row.rates = [];
                                    row.rates.push([]);
                                    row.rates[0].min = "";
                                    row.rates[0].max = "";
                                    row.rates[0].shipping = "";
                                }
                                //loop thru all the rate data and create rows

                                //handles if there are no rate rows yet
                                if (typeof (row.min) == 'undefined' || row.min == null) {
                                    row.min = [];
                                }

                                var html = '';
                                for (var i = 0; i < 1; i++) {
                                    html += '\
                                                        <tr>\
                                                                <td>\
                                                                        <input type="checkbox" class="jem-rate-checkbox" id="' + lastID + '"></input>\
                                                                </td>\
																<td>\
                                                                        <select name="conditions[' + lastID + '][]">\
                                                                        ' + generate_condition_html() + '\
                                                                        </select>\
                                                                </td>\
                                                                <td>\
                                                                        <input type="text" size="20" placeholder="" name="min[' + lastID + '][]"></input>\
                                                                </td>\
                                                                <td>\
                                                                        <input type="text" size="20" placeholder="" name="max[' + lastID + '][]"></input>\
                                                                </td>\
                                                                <td>\
                                                                        <input type="text" size="10" placeholder="" name="shipping[' + lastID + '][]"></input>\
                                                                </td>\
                                                        </tr>\
                                                ';



                                }


                                return html;
                            }

                            /**
                             * Handles the expansion contraction of the rate table for the zone
                             */
                            function expand_contract() {

                                var row = jQuery(this).parent('td').parent('tr').next();

                                if (jQuery(row).hasClass('jem-hidden-row')) {
                                    jQuery(row).removeClass('jem-hidden-row').addClass('jem-show-row');
                                    jQuery(this).removeClass('expand-icon').addClass('collapse-icon');
                                } else {
                                    jQuery(row).removeClass('jem-show-row').addClass('jem-hidden-row');
                                    jQuery(this).removeClass('collapse-icon').addClass('expand-icon');
                                }



                            }


                            //**************************************
                            // Generates the HTML for the country
                            // select. Uses an array of keys to
                            // determine which ones are selected
                            //**************************************
                            function generate_country_html(keys) {

                                html = "";

                                for (var key in country_array) {

                                    html += '<option value="' + key + '">' + country_array[key] + '</option>';

                                }

                                return html;
                            }


                            //**************************************
                            // Generates the HTML for the CONDITION
                            // select. Uses an array of keys to
                            // determine which ones are selected
                            //**************************************
                            function generate_condition_html(keys) {

                                html = "";

                                for (var key in condition_array) {

                                    html += '<option value="' + key + '">' + condition_array[key] + '</option>';
                                }

                                return html;
                            }

                            //***************************
                            // Handle add/delete clicks
                            //***************************

                            //ZONE TABLE


                            /*
                             * add new ZONE row
                             */
                            var zoneID = "#" + pluginID + "_settings";

                            jQuery(zoneID).on('click', '.add-zone-buttons a.add', function () {

                                //ok lets add a row!


                                var id = "#" + pluginID + "_settings table tbody tr:last";
                                //create empty row
                                var row = {};
                                row.key = "";
                                row.min = [];
                                row.rates = [];
                                row.condition = [];
                                row.countries = [];
                                jQuery(id).before(create_zone_row(row));

                                //turn on select2 for our row
                                if (jQuery().chosen) {
                                    jQuery("select.chosen_select").chosen({
                                        width: '350px',
                                        disable_search_threshold: 5
                                    });
                                } else {
                                    jQuery("select.chosen_select").select2();
                                }


                                return false;
                            });

                            /**
                             * Delete ZONE row
                             */
                            jQuery(zoneID).on('click', '.add-zone-buttons a.delete', function () {

                                //loop thru and see what is checked - if it is zap it!
                                var rowsToDelete = jQuery(this).closest('table').find('.jem-zone-checkbox:checked');

                                jQuery.each(rowsToDelete, function () {

                                    var thisRow = jQuery(this).closest('tr');
                                    //first lets get the next sibl;ing to this row
                                    var nextRow = jQuery(thisRow).next();

                                    //it should be a rate row
                                    if (jQuery(nextRow).hasClass('jem-rate-holder')) {
                                        //remove it!
                                        jQuery(nextRow).remove();
                                    } else {
                                        //trouble at mill
                                        return;
                                    }

                                    jQuery(thisRow).remove();
                                });

                                //TODO - need to delete associated RATES

                                return false;
                            });


                            //RATE TABLES

                            /**
                             * ADD RATE BUTTON
                             */
                            jQuery(zoneID).on('click', '.add-rate-buttons a.add', function () {

                                //we need to get the key of this zone - it's in the name of of the button
                                var name = jQuery(this).attr('name');
                                name = name.substring(4);

                                //remove key_ 
                                //ok lets add a row!


                                var row = create_rate_row(name, null);
                                jQuery(this).closest('tr').before(row);

                                return false;
                            });

                            /**
                             * Delete RATE roe
                             */
                            jQuery(zoneID).on('click', '.add-rate-buttons a.delete', function () {

                                //loop thru and see what is checked - if it is zap it!
                                var rowsToDelete = jQuery(this).closest('table').find('.jem-rate-checkbox:checked');

                                jQuery.each(rowsToDelete, function () {
                                    jQuery(this).closest('tr').remove();
                                });


                                return false;
                            });

                            //These handle building the select arras


                        <?php
                        echo "jQuery('#{$this->id}_settings').on('click', '.jem-expansion', expand_contract) ;\n";
                        ?>
                        </script>    
                        <?php
                    } else {
                        $method_id = $_GET['method_id'];
                        $get_shipping_methods_options = get_option($this->jem_shipping_methods_option, array());
                        $shipping_method_array = $get_shipping_methods_options[$method_id];
                        $get_selected_method_title = $shipping_method_array['method_title'];
                        if (isset($shipping_method_array['method_title']) && $shipping_method_array['method_title'] != '') {
                            $link_content .= ' &gt ';
                            $link_content .= esc_html($shipping_method_array['method_title']);
                        }
                        ?>
                        <script>
                            jQuery('#mainform h2').first().replaceWith('<h2>' + '<?php echo $link_content; ?>' + '</h2>');
                            var options = <?php echo json_encode($this->create_dropdown_options()); ?>;

                            var country_array = <?php echo json_encode($this->country_array); ?>;
                            var condition_array = <?php echo json_encode($this->condition_array); ?>;
                            var pluginID = <?php echo json_encode($this->id); ?>;
console.log('test NISL 2');
                            var lastID = 0;

                        <?php
                        $shipping_method_key = $this->option_key . '_' . $method_id;
                        if (isset($data['default'])) {
                            foreach ($data['default'] as $key => $value) {
                                global $row;
                                //add the key back into the json object
                                $value['key'] = $key;
                                $row = json_encode($value);
                                echo "jQuery('#{$this->id}_settings table tbody tr:last').before(create_zone_row({$row}));\n";
                            }
                        }
                        ?>





                            /**
                             * This creates a new ZONE row
                             */
                            function create_zone_row(row) {

                                //lets get the ID of the last one

                                var el = '#' + pluginID + '_settings .jem-zone-row';
                                lastID = jQuery(el).last().attr('id');

                                //Handle no rows
                                if (typeof lastID == 'undefined' || lastID == "") {
                                    lastID = 1;
                                } else {
                                    lastID = Number(lastID) + 1;
                                }

                                var html = '\
                                                        <tr style="display:none;" id="' + lastID + '" class="jem-zone-row" >\
                                                                <input type="hidden" value="' + lastID + '" name="key[' + lastID + ']"></input>\
                                                                <td><input type="hidden" size="30" value="zone-' + lastID + '"  name="zone-name[' + lastID + ']"/></td>\
                                                        </tr>\
                                        ';

                                //This is the expandable/collapsable row for that holds the rates
                                html += '\
                                                <tr class="jem-rate-holder">\
                                                        <td colspan="3">\
                                                                <table class="jem-rate-table shippingrows widefat" id="' + lastID + '_rates">\
                                                                        <thead>\
                                                                                <tr>\
                                                                                        <th></th>\
																						<th style="width: 25%">Condition</th>\
                                                                                        <th style="width: 25%">Min Value</th>\
                                                                                        <th style="width: 25%">Max Value</th>\
                                                                                        <th style="width: 25%">Shipping Rate</th>\
                                                                                </tr>\
                                                                        </thead>\
                                                                        ' + create_rate_row(lastID, row) + '\
                                                                        <tr>\
                                                                                <td colspan="5" class="add-rate-buttons">\
                                                                                        <a href="#" class="add button" name="key_' + lastID + '">Add New Rate</a>\
                                                                                        <a href="#" class="delete button">Delete Selected Rates</a>\
                                                                                </td>\
                                                                        </tr>\
                                                                </table>\
                                                        </td>\
                                                </tr>\
                                        ';

                                return html;
                            }

                            /**
                             * This creates a new RATE row
                             * The container Table is passed in and this row is added to it
                             */
                            function create_rate_row(lastID, row) {

                                if (row == null || row.rates.length == 0) {
                                    //lets manufacture a rows
                                    //create dummy row
                                    var row = {};
                                    row.key = "";
                                    row.condition = [""];
                                    // row.countries = [];
                                    row.rates = [];
                                    row.rates.push([]);
                                    row.rates[0].condition = "";
                                    row.rates[0].min = "";
                                    row.rates[0].max = "";
                                    row.rates[0].shipping = "";
                                }
                                //loop thru all the rate data and create rows

                                //handles if there are no rate rows yet
                                if (typeof (row.min) == 'undefined' || row.min == null) {
                                    row.min = [];
                                }

                                var html = '';
                                for (var i = 0; i < row.rates.length; i++) {
                                    html += '\
                                                        <tr class="rate-row">\
                                                                <td>\
                                                                        <input type="checkbox" class="jem-rate-checkbox" id="' + lastID + '"></input>\
                                                                </td>\
																<td>\
                                                                        <select class="'+ row.rates[i].condition +'" name="conditions[' + lastID + '][]">\
                                                                        ' + generate_condition_html(row.rates[i].condition) + '\
                                                                        </select>\
                                                                </td>\
                                                                <td>\
                                                                        <input type="text" size="20" placeholder="" name="min[' + lastID + '][]" value="' + row.rates[i].min + '"/>\
                                                                </td>\
                                                                <td>\
                                                                        <input type="text" size="20" placeholder="" name="max[' + lastID + '][]" value="' + row.rates[i].max + '"></input>\
                                                                </td>\
                                                                <td>\
                                                                        <input type="text" size="10" placeholder="" name="shipping[' + lastID + '][]" value="' + row.rates[i].shipping + '"></input>\
                                                                </td>\
                                                        </tr>\
                                                ';



                                }


                                return html;
                            }

                            /**
                             * Handles the expansion contraction of the rate table for the zone
                             */
                            function expand_contract() {

                                var row = jQuery(this).parent('td').parent('tr').next();

                                if (jQuery(row).hasClass('jem-hidden-row')) {
                                    jQuery(row).removeClass('jem-hidden-row').addClass('jem-show-row');
                                    jQuery(this).removeClass('expand-icon').addClass('collapse-icon');
                                } else {
                                    jQuery(row).removeClass('jem-show-row').addClass('jem-hidden-row');
                                    jQuery(this).removeClass('collapse-icon').addClass('expand-icon');
                                }



                            }


                            //TODO - these seem to be copies of the functions above - test commenting them out
                            //**************************************
                            // Generates the HTML for the country
                            // select. Uses an array of keys to
                            // determine which ones are selected
                            //**************************************
                            function generate_country_html(keys) {

                                html = "";

                                for (var key in country_array) {

                                    if (keys.indexOf(key) != -1) {
                                        //we have a match
                                        html += '<option value="' + key + '" selected="selected">' + country_array[key] + '</option>';
                                    } else {
                                        html += '<option value="' + key + '">' + country_array[key] + '</option>';

                                    }
                                }

                                return html;
                            }


                            //**************************************
                            // Generates the HTML for the CONDITION
                            // select. Uses an array of keys to
                            // determine which ones are selected
                            //**************************************
                            function generate_condition_html(keys) {

                                html = "";

                                for (var key in condition_array) {

                                    if (keys.indexOf(key) != -1) {
                                        //we have a match
                                        html += '<option value="' + key + '" selected="selected">' + condition_array[key] + '</option>';
                                    } else {
                                        html += '<option value="' + key + '">' + condition_array[key] + '</option>';

                                    }
                                }

                                return html;
                            }

                            //***************************
                            // Handle add/delete clicks
                            //***************************

                            //ZONE TABLE


                            /*
                             * add new ZONE row
                             */
                            var zoneID = "#" + pluginID + "_settings";

                            jQuery(zoneID).on('click', '.add-zone-buttons a.add', function () {

                                //ok lets add a row!


                                var id = "#" + pluginID + "_settings table tbody tr:last";
                                //create empty row
                                var row = {};
                                row.key = "";
                                row.min = [];
                                row.rates = [];
                                row.condition = [];
                                row.countries = [];
                                jQuery(id).before(create_zone_row(row));

                                //turn on select2 for our row
                                if (jQuery().chosen) {
                                    jQuery("select.chosen_select").chosen({
                                        width: '350px',
                                        disable_search_threshold: 5
                                    });
                                } else {
                                    jQuery("select.chosen_select").select2();
                                }


                                return false;
                            });

                            /**
                             * Delete ZONE row
                             */
                            jQuery(zoneID).on('click', '.add-zone-buttons a.delete', function () {

                                //loop thru and see what is checked - if it is zap it!
                                var rowsToDelete = jQuery(this).closest('table').find('.jem-zone-checkbox:checked');

                                jQuery.each(rowsToDelete, function () {

                                    var thisRow = jQuery(this).closest('tr');
                                    //first lets get the next sibl;ing to this row
                                    var nextRow = jQuery(thisRow).next();

                                    //it should be a rate row
                                    if (jQuery(nextRow).hasClass('jem-rate-holder')) {
                                        //remove it!
                                        jQuery(nextRow).remove();
                                    } else {
                                        //trouble at mill
                                        return;
                                    }

                                    jQuery(thisRow).remove();
                                });

                                //TODO - need to delete associated RATES

                                return false;
                            });


                            //RATE TABLES

                            /**
                             * ADD RATE BUTTON
                             */
                            jQuery(zoneID).on('click', '.add-rate-buttons a.add', function () {

                                //we need to get the key of this zone - it's in the name of of the button
                                var name = jQuery(this).attr('name');
                                name = name.substring(4);

                                //remove key_ 
                                //ok lets add a row!


                                var row = create_rate_row(name, null);
                                jQuery(this).closest('tr').before(row);

                                return false;
                            });

                            /**
                             * Delete RATE roe
                             */
                            jQuery(zoneID).on('click', '.add-rate-buttons a.delete', function () {

                                //loop thru and see what is checked - if it is zap it!
                                var rowsToDelete = jQuery(this).closest('table').find('.jem-rate-checkbox:checked');

                                jQuery.each(rowsToDelete, function () {
                                    jQuery(this).closest('tr').remove();
                                });


                                return false;
                            });

                            //These handle building the select arras


                        <?php
                        echo "jQuery('#{$this->id}_settings').on('click', '.jem-expansion', expand_contract) ;\n";
                        ?>
                        </script>					
                        <?php
                    }
                    //NIPL

                    return ob_get_clean();
                }

                public function generate_shipping_list_html() {
                    ob_start();
                    ?>
                    </table>

                    <!-- Begin MailChimp Signup Form -->
                    <link href="//cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css">
                    <style type="text/css">
                        #mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
                        /* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
                           We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
                        #optin {
                            background: #dde2ec;
                            border: 2px solid #1c3b7e;
                            /* padding: 20px 15px; */
                            text-align: center;
                            width: 800px;
                        }
                        #optin input {
                            background: #fff;
                            border: 1px solid #ccc;
                            font-size: 15px;
                            margin-bottom: 10px;
                            padding: 8px 10px;
                            border-radius: 3px;
                            -moz-border-radius: 3px;
                            -webkit-border-radius: 3px;
                            box-shadow: 0 2px 2px #ddd;
                            -moz-box-shadow: 0 2px 2px #ddd;
                            -webkit-box-shadow: 0 2px 2px #ddd
                        }
                        #optin input.name { background: #fff url('<?php echo JEM_URL; ?>/images/name.png') no-repeat 10px center; padding-left: 35px }
                        #optin input.myemail { background: #fff url('<?php echo JEM_URL; ?>/images/email.png') no-repeat 10px center; padding-left: 35px }
                        #optin button {
                            background: #217b30 url('<?php echo JEM_URL; ?>/images/green.png') repeat-x top;
                            border: 1px solid #137725;
                            color: #fff;
                            cursor: pointer;
                            font-size: 14px;
                            font-weight: bold;
                            padding: 2px 0;
                            text-shadow: -1px -1px #1c5d28;
                            width: 120px;
                            height: 38px;
                        }
                        #optin button:hover { color: #c6ffd1 }
                        .optin-header{
                            font-size: 24px;
                            color: #ffffff;
                            background-color: #1c3b7e;
                            padding: 20px 15px;
                        }
                        #jem-submit-results{
                            padding: 10px 0px;
                            font-size: 24px;
                        }
                    </style>

                    <div id="optin">

                        <div id="mc_embed_signup_scroll">
                            <div class="optin-header">Upgrade to Pro - get a 20% Discount Coupon</div>
                            <div class="mc-field-group" style="padding: 20px 15px;; text-align: left;">
                                <input type="text" value="Enter your email" size="30" name="EMAIL" class="myemail" id="mce-EMAIL" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;"
                                >
                                <input type="text" value="Enter your name" size="30" name="FNAME" class="name" id="mce-FNAME" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;"
                                >
                                <button id="mc_button" class="button" >Get Discount</button>
                            </div>
                            <div id="mce-responses" class="clear">
                                <div class="response" id="mce-error-response" style="display:none"></div>
                                <div class="response" id="mce-success-response" style="display:none"></div>
                            </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                            <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_6d531bf4acbb9df72cd2e718d_de987ac678" tabindex="-1" value=""></div>
                            <div class="clear"><img src="<?php echo JEM_URL ?>/images/lock.png">We respect your privacy and will never sell or rent your details</div>
                            <div id="jem-submit-results"></div>
                        </div>
                    </div>
                    <script>
                        jQuery("#mc_button").click(function(e){
                            e.preventDefault();
                            console.log('clicked');
                            data = {};

                            data["EMAIL"] = jQuery("#mce-EMAIL").val();
                            data["NAME"] = jQuery("#mce-FNAME").val();

                            jQuery.ajax({
                                url: '//jem-products.us12.list-manage.com/subscribe/post-json?u=6d531bf4acbb9df72cd2e718d&amp;id=de987ac678&c=?',
                                type: 'post',
                                data: data,
                                dataType: 'json',
                                contentType: "application/json; charset=utf-8",
                                success: function (data) {
                                    if (data['result'] != "success") {
                                        //ERROR
                                        console.log("error");
                                        console.log(data['msg']);
                                    } else {
                                        //SUCCESS - Do what you like here
                                        jQuery("#jem-submit-results").text("Please Check Your Email for your Code");
                                    }
                                }
                            });

                        });

                    </script>

                    <h3 class="add_shipping_method" id="shiping_methods_h3">List of shipping methods
                        <a href="<?php echo remove_query_arg('shipping_methods_id', add_query_arg('action', 'new')); ?>" class="child_shipping_method"><?php echo __('Add New', 'JEM_DOMAIN'); ?></a>
                    </h3>
                    <table class="form-table">
                        <tr valign="top">
                            <td>
                                <table class="jem_table_rate_shipping_methods_class widefat wc_shipping wp-list-table" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="sort" style="width: 1%;">&nbsp;</th>
                                            <th class="method_title" style="width: 30%;"><?php _e('Title', 'JEM_DOMAIN'); ?></th>
                                            <th class="method_status" style="width: 1%;text-align: center;"><?php _e('Enabled', 'JEM_DOMAIN'); ?></th>
                                            <th class="method_select" style="width: 0%;"><input type="checkbox" class="tips checkbox-select-all" data-tip="<?php _e('Select all', 'JEM_DOMAIN'); ?> " class="checkall-checkbox-class" id="checkall-checkbox-id" /></th>
                                        </tr>
                                    </thead>
                                    <!--get option for saved methods details-->
                    <?php
                    $get_shipping_methods_options = get_option($this->jem_shipping_methods_option, array());
                    $get_shipping_method_order = get_option( $this->jem_shipping_method_order_option, array() );
                    $shipping_methods_options_array = array();
                    if (is_array($get_shipping_method_order)) {
                        foreach ($get_shipping_method_order as $method_id) {
                            if (isset($get_shipping_methods_options[$method_id])){
                                $shipping_methods_options_array[$method_id] = $get_shipping_methods_options[$method_id];
                            }
                        }
                    }
                    ?>
                    <!--display shipping method data-->
                                    <tbody>
                    <?php foreach ($shipping_methods_options_array as $shipping_method_options) {
                        ?>
                                            <tr id="shipping_method_id_<?php echo $shipping_method_options['method_id']; ?>" class="<?php //echo $tr_class; ?>">
                                                <td class="sort">
                                                    <input type="hidden" name="method_order[<?php echo esc_attr( $shipping_method_options['method_id'] ); ?>]" value="<?php echo esc_attr( $shipping_method_options['method_id'] ); ?>" />
                                                </td>
                                                <td class="method-title">
                                                    <a href="<?php echo remove_query_arg('shipping_methods_id', add_query_arg('method_id', $shipping_method_options['method_id'], add_query_arg('action', 'edit'))); ?>">
                                                        <strong><?php echo esc_html($shipping_method_options['method_title']); ?></strong>
                                                    </a>
                                                </td>
                                                <td class="method-status" style="width: 524px;display: -moz-stack;">
                        <?php if (isset($shipping_method_options['method_enabled']) && 'yes' === $shipping_method_options['method_enabled']) : ?>
                                                        <span class="status-enabled tips" data-tip="<?php _e('yes', JEM_DOMAIN); ?>"><?php _e('yes', 'JEM_DOMAIN'); ?></span>
                        <?php else : ?>
                                                        <span class="na">-</span>
                        <?php endif; ?>
                                                </td>                                                                        
                                                <td class="method-select" style="width: 2% !important;text-align: center;" nowrap>
                                                    <input type="checkbox" class="tips checkbox-select chkItems" value="<?php echo esc_attr($shipping_method_options['method_id']); ?>" data-tip="<?php echo esc_html($shipping_method_options['method_title']); ?>" />
                                                </td>
                                            </tr>
                                                    <?php
                                                }
//                                                        }
                                                ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th colspan="8"><span class="description"><?php _e('Drag and drop the above shipment methods to control their display order. Confirm by clicking Save changes button below.', 'JEM_DOMAIN'); ?></span></th>
                                        </tr>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th colspan="8">
                                                <button id="jem_table_rate_remove_selected_method" class="button" disabled><?php _e('Remove selected Method', 'JEM_DOMAIN'); ?></button>

                                                <div style="clear:both;"></div>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </td>
                        </tr>
                    </table>                 
                    <script type="text/javascript">
                        jQuery('.jem_table_rate_shipping_methods_class input[type="checkbox"]').click(function () {
                            jQuery('#jem_table_rate_remove_selected_method').attr('disabled', !jQuery('.jem_table_rate_shipping_methods_class td input[type="checkbox"]').is(':checked'));
                        });

                        jQuery('#jem_table_rate_remove_selected_method').click(function () {
                            var url = '<?php echo add_query_arg('shipping_methods_id', '', add_query_arg('action', 'delete')); ?>';
                            var first = true;
                            jQuery('input.checkbox-select').each(function () {
                                if (jQuery(this).is(':checked')) {
                                    if (!first) {
                                        url = url + ',';
                                    } else {
                                        url = url + '=';
                                    }
                                    url = url + jQuery(this).val();
                                    first = false;
                                }
                            })
                            if (first) {
                                alert('<?php _e('Please select shipping methods to remove', 'JEM_DOMAIN'); ?>');
                                return false;
                            }
                            if (url != '<?php echo add_query_arg('method_id', '', add_query_arg('action', 'delete')); ?>') {
                                jQuery('#jem_table_rate_remove_selected_method').prop('disabled', true);
                                jQuery('.woocommerce-save-button').prop('disabled', true);
                                window.location.href = url;
                            }
                            return false;
                        })
                    </script>
                    <?php
                    return ob_get_clean();
                }

                public function get_add_new_shipping_method_form($shipping_method_array) {
                    $this->form_fields = array(
                        'method_enabled' => array(
                            'title' => __('Enable/Disable', 'JEM_DOMAIN'),
                            'type' => 'checkbox',
                            'label' => __('Enable this shipping method', 'JEM_DOMAIN'),
                            'default' => $shipping_method_array['method_enabled']
                        ),
                        'method_title' => array(
                            'title' => __('Method Title', 'JEM_DOMAIN'),
                            'description' => __('This controls the title which the user sees during checkout.', 'JEM_DOMAIN'),
                            'type' => 'text',
                            'default' => $shipping_method_array['method_title'],
                            'desc_tip' => true
                        ),
                        'method_handling_fee' => array(
                            'title' => __('Handling Fee', 'JEM_DOMAIN'),
                            'description' => __('Enter an amount for the handling fee - leave BLANK to disable.', 'JEM_DOMAIN'),
                            'type' => 'text',
                            'default' => $shipping_method_array['method_handling_fee']
                        ),
                        'method_tax_status' => array(
                            'title' => __('Tax Status', 'JEM_DOMAIN'),
                            'type' => 'select',
                            'default' => $shipping_method_array['method_tax_status'],
                            'options' => array(
                                'taxable' => __('Taxable', 'JEM_DOMAIN'),
                                'notax' => __('Not Taxable', 'JEM_DOMAIN'),
                            )
                        ),
                        'table_rates_table' => array(
                            'title' => __('Shipping Methods', 'JEM_DOMAIN'),
                            'type' => 'table_rates_table',
                            'default' => isset($shipping_method_array['method_table_rates']) ? $shipping_method_array['method_table_rates'] : array(),
                            'description' => '',
                        )
                    );
                }

                /**
                 * Generates HTML for table_rate settings table.
                 * this gets called automagically!
                 */
                function admin_options() {
                    ?>
                    <h2><?php _e('Table Rate Shipping Options', 'woocommerce'); ?></h2>				
                    <table class="form-table">
                    <?php
                    $shipping_method_action = false;
                    if (isset($_GET['action'])) {
                        $shipping_method_action = $_GET['action'];
                    }
                    if ($shipping_method_action == 'new' || $shipping_method_action == 'edit') {
                        $get_shipping_methods_options = get_option($this->jem_shipping_methods_option, array());

                        $shipping_method_array = array(
                            'method_title' => '',
                            'method_enabled' => 'no',
                            'method_handling_fee' => '',
                            'method_tax_status' => 'taxable',
                            'method_table_rates' => ''
                        );
                        $method_id = '';
                        if ($shipping_method_action == 'edit') {
                            $method_id = $_GET['method_id'];
                            $shipping_method_array = $get_shipping_methods_options[$method_id];
                            $method_id_for_shipping = $this->id . '_' . $this->instance_id . '_' . sanitize_title($shipping_method_array['method_title']);
                            if (isset($shipping_method_array['method_id_for_shipping']) && $shipping_method_array['method_id_for_shipping'] != '') {
                                $method_id_for_shipping = $shipping_method_array['method_id_for_shipping'];
                            }
                            $method_id_for_shipping = $method_id_for_shipping;
                        } else {
                            $method_id_for_shipping = '';
                        }
                        ?>
                            <input type="hidden" name="shipping_method_action" value="<?php echo $shipping_method_action; ?>" />
                            <input type="hidden" name="shipping_method_id" value="<?php echo $method_id; ?>" />
                            <input type="hidden" name="method_id_for_shipping" value="<?php echo $method_id_for_shipping; ?>" />
                            <?php
                            $shipping_method['woocommerce_method_instance_id'] = $this->instance_id;
                            $this->generate_settings_html($this->get_add_new_shipping_method_form($shipping_method_array));
                        } else if ($shipping_method_action == 'delete') {
                            $selected_shipping_methods_id = '';
                            // get selected methods id and explode it with ',' 
                            if (isset($_GET['shipping_methods_id'])) {
                                $selected_shipping_methods_id = explode(',', $_GET['shipping_methods_id']);
                            }
                            // get all shipping methods options for delete
                            $get_shipping_methods_options_for_delete = get_option($this->jem_shipping_methods_option, array()); //
                            // get all shipping methods order for delete
                            $get_shipping_methods_order_for_delete = get_option( $this->shipping_method_order_option, array() );
                            foreach ($selected_shipping_methods_id as $removed_method_id) {
                                if (isset($get_shipping_methods_options_for_delete[$removed_method_id])) {
                                    if (isset($get_shipping_methods_order_for_delete[$removed_method_id])) {
                                        unset($get_shipping_methods_order_for_delete[$removed_method_id]);
                                    }
                                    $shipping_method = $get_shipping_methods_options_for_delete[$removed_method_id];
                                    unset($get_shipping_methods_options_for_delete[$removed_method_id]);
                                    // Update all shipping methods options after delete
                                    update_option($this->jem_shipping_methods_option, $get_shipping_methods_options_for_delete);
                                    // Update all shipping methods order after delete
                                    update_option( $this->shipping_method_order_option, $get_shipping_methods_order_for_delete );
                                }
                            }
                            $this->generate_settings_html();
                        } else {
                            $this->generate_settings_html();
                        }
                        ?>
                    </table>
                        <?php
                    }

                    /**
                     * Returns the latest counter 
                     */
                    function get_counter() {
                        $this->counter = $this->counter + 1;
                        return $this->counter;
                    }

                    //*********************
                    // PHP functions
                    //***********************

                    function create_select_arrays() {

                        //first the CONDITION html
                        $this->condition_array = array();
                        $this->condition_array['weight'] = sprintf(__('Weight (%s)', 'MHTR_DOMAIN'), get_option('woocommerce_weight_unit'));
                        $this->condition_array['total'] = sprintf(__('Total Price (%s)', 'MHTR_DOMAIN'), get_woocommerce_currency_symbol());


                        //Now the countries
                        $this->country_array = array();

                        // Get the country list from Woo....
                        foreach (WC()->countries->get_shipping_countries() as $id => $value) :
                            $this->country_array[esc_attr($id)] = esc_js($value);
                        endforeach;
                    }

                    //TODO - do we need this function?
                    /** 				 
                     * This generates the select option HTML for teh zones & rates tables
                     */
                    function create_select_html() {
                        //first the CONDITION html
                        $arr = array();
                        $arr['weight'] = sprintf(__('Weight (%s)', 'MHTR_DOMAIN'), get_option('woocommerce_weight_unit'));
                        $arr['total'] = sprintf(__('Total Price (%s)', 'MHTR_DOMAIN'), get_woocommerce_currency_symbol());

                        //now create the html from the array
                        $html = '';
                        foreach ($arr as $key => $value) {
                            $html .= '<option value=">' . $key . '">' . $value . '</option>';
                        }

                        $this->condition_html = $html;

                        $html = '';
                        $arr = array();
                        //Now the countries
                        // Get the country list from Woo....
                        foreach (WC()->countries->get_shipping_countries() as $id => $value) :
                            $arr[esc_attr($id)] = esc_js($value);
                        endforeach;

                        //And create the HTML
                        foreach ($arr as $key => $value) {
                            $html .= '<option value=">' . $key . '">' . $value . '</option>';
                        }

                        $this->country_html = $html;
                    }

                    //Creates the HTML options for the selected

                    function create_dropdown_html($arr) {

                        $arr = array();



                        $this->condition_html = html;
                    }

                    /**
                     * Create dropdown options 
                     */
                    function create_dropdown_options() {

                        $options = array();


                        // Get the country list from Woo....
                        foreach (WC()->countries->get_shipping_countries() as $id => $value) :
                            $options['country'][esc_attr($id)] = esc_js($value);
                        endforeach;

                        // Now the conditions - cater for language & woo
                        $option['condition']['weight'] = sprintf(__('Weight (%s)', 'JEM_DOMAIN'), get_option('woocommerce_weight_unit'));
                        $option['condition']['price'] = sprintf(__('Total (%s)', 'JEM_DOMAIN'), get_woocommerce_currency_symbol());

                        return $options;
                    }

                    /**
                     * This saves all of our custom table settings
                     */
                    function process_admin_options() {

                        $shipping_method_action = false;
                        if (isset($_POST['shipping_method_action'])) {
                            $shipping_method_action = $_POST['shipping_method_action'];
                        }
                        if ($shipping_method_action == 'new' || $shipping_method_action == 'edit') {
                            //Arrays to hold the clean POST vars
                            $keys = array();
                            $zone_name = array();
                            $condition = array();
                            $countries = array();
                            $min = array();
                            $max = array();
                            $shipping = array();

                            //Take the POST vars, clean em up and put thme in nice arrays
							// echo "<pre>";
								// print_r($_POST);
							// echo "</pre>";
							// die;
                            if (isset($_POST['key']))
                                $keys = array_map('wc_clean', $_POST['key']);
                            if (isset($_POST['zone-name']))
                                $zone_name = array_map('wc_clean', $_POST['zone-name']);
                            // if (isset($_POST['condition']))
                                // $condition = array_map('wc_clean', $_POST['condition']);
                            //no wc_clean as multi-D arrays
                            if (isset($_POST['countries']))
                                $countries = $_POST['countries'];
                            if (isset($_POST['conditions']))
                                $conditions = $_POST['conditions']; 
							if (isset($_POST['min']))
                                $min = $_POST['min'];
                            if (isset($_POST['max']))
                                $max = $_POST['max'];
                            if (isset($_POST['shipping']))
                                $shipping = $_POST['shipping'];

                            //todo - need to add soem validation here and some error messages???
                            //Master var of options - we keep it in one big bad boy
                            $options = array();

                            //OK we need to loop thru all of them - the keys will help us here - process by key
                            foreach ($keys as $key => $value) {

                                //we only process it if all the fields are set
                               /*  if (
                                        empty($zone_name[$key]) ||
                                        // empty($condition[$key]) ||
                                        empty($countries[$key])
                                ) {
                                    //something is empty so don't save it
                                    continue;
                                } */

                                //Get the zone name - this is our main key
                                $name = $zone_name[$key];

                                //Going to add the rates now.
                                //before we do that check if we have any empty rows and delete them
                                $obj = array();
								if( !empty($min) ){
									foreach ($min[$key] as $k => $val) {
										if (
												empty($conditions[$key][$k]) &&
												empty($min[$key][$k]) &&
												empty($max[$key][$k]) &&
												empty($shipping[$key][$k])
										) {
											unset($conditions[$key][$k]);
											unset($min[$key][$k]);
											unset($max[$key][$k]);
											unset($shipping[$key][$k]);
										} else {
											//add it to the object array
											$obj[] = array("condition" => $conditions[$key][$k] , "min" => $min[$key][$k], "max" => $max[$key][$k], "shipping" => $shipping[$key][$k]);
										}
									}
								}
                                //OK now lets sort or array of objects!!
                                usort($obj, 'self::cmp');

                                //create the array to hold the data				
                                $options[$name] = array();
                                $options[$name]['method_handling_fee'] = $_POST['woocommerce_' . $this->id . '_method_handling_fee'];
                                // $options[$name]['condition'] = $condition[$key];
                                // $options[$name]['countries'] = $countries[$key];
                                $options[$name]['min'] = $min[$key];
                                $options[$name]['max'] = $max[$key];
                                $options[$name]['shipping'] = $shipping[$key];
                                $options[$name]['rates'] = $obj;   //This is the sorted rates object!	
                            }
                            $get_shipping_methods_options = get_option($this->jem_shipping_methods_option, array());
                            $get_shipping_method_order = get_option( $this->jem_shipping_method_order_option, array() );
                            $shipping_method_array = array();
                            if ($shipping_method_action == 'new') {
                                $get_shipping_methods_options = get_option($this->jem_shipping_methods_option, array());
                                $method_id = get_option('jem_table_rate_sub_shipping_method_id', 0);
                                foreach ($get_shipping_methods_options as $shipping_method_array) {
                                    if (intval($shipping_method_array['method_id']) > $method_id)
                                        $method_id = intval($shipping_method_array['method_id']);
                                }
                                $method_id++;
                                update_option('jem_table_rate_sub_shipping_method_id', $method_id);
                                $method_id_for_shipping = $this->id . '_' . $this->instance_id . '_' . $method_id;
                            }
                            else {
                                $method_id = $_POST['shipping_method_id'];
                                $method_id_for_shipping = $_POST['method_id_for_shipping'];
                            }

                            $shipping_method_array['method_id'] = $method_id;
                            $shipping_method['method_id_for_shipping'] = $method_id_for_shipping;
                            if (isset($_POST['woocommerce_' . $this->id . '_method_enabled']) && $_POST['woocommerce_' . $this->id . '_method_enabled'] == 1) {
                                $shipping_method_array['method_enabled'] = 'yes';
                            } else {
                                $shipping_method_array['method_enabled'] = 'no';
                            }
                            $shipping_method_array['method_title'] = $_POST['woocommerce_' . $this->id . '_method_title'];
                            $shipping_method_array['method_handling_fee'] = $_POST['woocommerce_' . $this->id . '_method_handling_fee'];
                            $shipping_method_array['method_tax_status'] = $_POST['woocommerce_' . $this->id . '_method_tax_status'];

                            //SAVE IT
                            $shipping_method_array['method_table_rates'] = $options;
                            $get_shipping_methods_options[$method_id] = $shipping_method_array;
                            update_option($this->jem_shipping_methods_option, $get_shipping_methods_options);
                            if (isset($_GET['action'])) {
                                $shipping_method_action = $_GET['action'];
                            }
                            
                            if ($shipping_method_action == 'new') {
                                $get_shipping_method_order[$method_id] = $method_id;
                                update_option($this->jem_shipping_method_order_option, $get_shipping_method_order);
                                $redirect = add_query_arg(array('action' => 'edit', 'method_id' => $method_id));
                                if (1 == 1 && headers_sent()) {
                                    ?>
                                <script>
                                    parent.location.replace('<?php echo $redirect; ?>');
                                </script>
                                <?php
                            } else {
                                wp_safe_redirect($redirect);
                            }
                            exit;
                        }
                    }
                    else{
                        if (isset($_POST['method_order'])) {
                            update_option($this->jem_shipping_method_order_option, $_POST['method_order']);
                        }
                    }
                }

                //Comparision function for usort of associative arrays
                function cmp($a, $b) {
                    return $a['min'] - $b['min'];
                }

                /**
                 * This RETIEVES  all of our custom table settings

                 */
                function get_options() {

                    //Retrieve the zones & rates
                    $this->options = array_filter((array) get_option($this->option_key));

                    $x = 5;
                }

                /**
                 * calculate_shipping function. Woo calls this automagically
                 *
                 */
                public function calculate_shipping($package = Array()) {

                    $get_shipping_methods_options = get_option($this->jem_shipping_methods_option, array());
					$get_shipping_method_order = get_option( $this->jem_shipping_method_order_option, array() );
					$method_rate_id = $this->id.':'.$this->instance_id;
					$zone_id = $this->get_shipping_zone_from_method_rate_id( $method_rate_id );
					$delivery_zones = WC_Shipping_Zones::get_zones();
					$zone_countries = array();


					foreach ((array) $delivery_zones[$zone_id]['zone_locations'] as $zlocation ) {
						$zone_countries[] = $zlocation->code;
					}

                    $shipping_methods_options_array = array();

                    //TODO - need to work out what this array is holding??
                    if ( is_array( $get_shipping_method_order ) ) {
						foreach ( $get_shipping_method_order as $method_id ) {
							if ( isset( $get_shipping_methods_options[$method_id] ) ) $shipping_methods_options_array[$method_id] = $get_shipping_methods_options[$method_id];
						}
					}

                    //And what is this
                    foreach ($get_shipping_methods_options as $shipping_method) {
                        if (!isset($shipping_methods_options_array[$shipping_method['method_id']]))
                            $shipping_methods_options_array[$shipping_method['method_id']] = $shipping_method;
                    }

                    //TODO = can we check for this earlier rather than do a seperate loop???
                    // Remove table rates if shipping method is disable
                    foreach ($shipping_methods_options_array as $key => $shipping_method) {
                        if (isset($shipping_method['method_enabled']) && 'yes' != $shipping_method['method_enabled'])
                            unset($shipping_methods_options_array[$key]);
                    }

                    $shipping_methods_options = $shipping_methods_options_array;

                    //@simon Nov 18 - can't see why this is here so getting rid of it'
                    //$loop_count = 0;
                    foreach ($shipping_methods_options as $shipping_method_option) {

                        foreach ($shipping_method_option['method_table_rates'] as $method_rule) {

                            //SE - Added in to stop the error
                            $cost = 0;

                            //@simon Nov '18
                            //Need a field to show we have or have not found a match
                            //It's always showing up as zero
                            $found = false;


                            //what is the tax status
                            if ($shipping_method_option['method_tax_status'] == 'notax') {
                                $taxes = false;
                            } else {
                                $taxes = '';
                            }

                            //ok first lets get the country that this order is for
                            // check destination country is available in rule
                            $dest_country = $package['destination']['country'];
                            if (!in_array($dest_country, $zone_countries)) {
                                $found = false;
                            }
							
							// NISL custom code based on rates and conditions set for each row set.
								foreach( $method_rule['rates'] as $rates ){
									if( $rates['condition'] == 'total' ){

                                        //@simon Nov 18 - need to include taxes IF the they are included in the product
                                        //Cater for taxes
                                        $tax_display = get_option( 'woocommerce_tax_display_cart' );

                                        if( "incl" == $tax_display ){
                                            $total =  WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax();

                                        } else {
                                            $total =  WC()->cart->get_cart_contents_total() ;

                                        }
										//$costs = $this->find_matching_rate_custom(WC()->cart->cart_contents_total, $rates);
                                        $costs = $this->find_matching_rate_custom($total, $rates);

                                        //@simon Nov '18
                                        if($costs == null){
                                            continue;
                                        }

										$cost = $cost + $costs;
                                        $found = true;
									}
									else if($rates['condition'] == 'weight' ){
										$costs = $this->find_matching_rate_custom(WC()->cart->cart_contents_weight, $rates);


                                        //@simon Nov '18
                                        if($costs == null){
                                            continue;
                                        }

										$cost = $cost + $costs;
                                        $found = true;
									}
								}
							// END NISL custom code
							


                            $method_id = $this->id . '_' . $this->instance_id . '_' . sanitize_title($shipping_method_option['method_title']);
                            if (isset($shipping_method_option['method_id_for_shipping']) && $shipping_method_option['method_id_for_shipping'] != '') {
                                $method_id = $shipping_method_option['method_id_for_shipping'];
                            }

                            //$method_id = $method_id;

                            //If it's free shipping append the Woo value)
                            if($cost === 0){
                                $shipping_method_option['method_title'] =$shipping_method_option['method_title'] . " (" . __("Free Shipping", 'woocommerce') . ")";
                            }

                            if ( $found ) {
                                    $rate = array(
                                        'id' => $method_id,
                                        'label' => $shipping_method_option['method_title'],
                                        'cost' => $cost,
                                        'taxes' => $taxes,
                                        'calc_tax' => 'per_order'
                                    );


                                // Register the rate
                                $this->add_rate($rate);

                            }


                            //$loop_count = $loop_count + 1;
                        }

                    }
                }

                function get_rates_for_country($country) {

//                                    //Loop thru and see if we can find one
                    $get_shipping_methods_options = get_option($this->jem_shipping_methods_option, array());

                    $shipping_methods_options_array = array();
                    foreach ($get_shipping_methods_options as $shipping_method) {
                        if (!isset($shipping_methods_options_array[$shipping_method['method_id']]))
                            $shipping_methods_options_array[$shipping_method['method_id']] = $shipping_method;
                    }
                    //                                pr($shipping_method);
                    // Remove table rates if shipping method is disable
                    foreach ($shipping_methods_options_array as $key => $shipping_method) {
                        if (isset($shipping_method['method_enabled']) && 'yes' != $shipping_method['method_enabled'])
                            unset($shipping_methods_options_array[$key]);
                    }
                    $shipping_methods_options = $shipping_methods_options_array;
                    $ret = array();
//					$get_shipping_methods_options = get_option( $this->jem_shipping_methods_option, array() );
                    foreach ($shipping_methods_options as $shipping_methods_option) {

                        foreach ($shipping_methods_option['method_table_rates'] as $rate) {
                            if (in_array($country, $rate['countries'])) {
                                $ret[] = $rate;
                            }
                        }
                    }

                    //if we found something return it, otherwise a null.
                    if (count($ret) > 0) {
                        return $ret;
                    } else {
                        return null;
                    }
                }

                //Here we find the matching rate
                function find_matching_rate($value, $zones) {
//                                    echo "cart price:".$value;
//                                    die();
//                                    pr($zones['min']);
//                                    pr($zones);
//                                    pr($zones['max']);
//                                    echo "total". count($zones['max']);
//                                    die();
//                                    echo"hello";
//                                    die();
                    $zone = $zones;
                    foreach ($zone as $zones_array) {
                        //inside each zone will be the arrays of min max & shipping
                        //TODO - should probably make this a better data structure - array of objects, next version
//						pr($zone['max']);
                        // remember * means infinity!
                        for ($i = 0; $i < 1; $i++) {
                            if ($zone['max'][$i] == '*') {
                                if ($value >= $zone['min'][$i]) {
                                    $handling_fee = $zone['method_handling_fee'];
                                    $total_fee = $zone['shipping'][$i] + $handling_fee;
                                    return $total_fee;
                                }
                            } else {
                                if ($value >= $zone['min'][$i] && $value <= $zone['max'][$i]) {
                                    $handling_fee = $zone['method_handling_fee'];
                                    $total_fee = $zone['shipping'][$i] + $handling_fee;
                                    return $total_fee;
                                }
                            }
                        }

                        //OK if we got all the way to here, then we have NO match
                        return null;
                    }
                }

                //This finds which one of the rules matches the value
                //It uses an asterisk for infinite
				function find_matching_rate_custom($value, $rates) {
					$rate = $rates;
					if ($rate['max'] == '*') {
						if ($value >= $rate['min']) {
							$total_fee = $rate['shipping'];
							return $total_fee;
						}
					} else {
						if ($value >= $rate['min'] && $value <= $rate['max']) {
							$total_fee = $rate['shipping'];
							return $total_fee;
						}
					}
					//OK if we got all the way to here, then we have NO match
					return null;
				}
				function get_shipping_zone_from_method_rate_id( $method_rate_id ){
					global $wpdb;

					$data = explode( ':', $method_rate_id );
					$method_id = $data[0];
					$instance_id = $data[1];

					// The first SQL query
					$zone_id = $wpdb->get_col( "
						SELECT wszm.zone_id
						FROM {$wpdb->prefix}woocommerce_shipping_zone_methods as wszm
						WHERE wszm.instance_id = '$instance_id'
						AND wszm.method_id LIKE '$method_id'
					" );
					$zone_id = reset($zone_id); // converting to string

					// 1. Wrong Shipping method rate id
					if( empty($zone_id) )   
					{
						return __("Error! doesn't exist…");
					} 
					// 2. Default WC Zone name 
					elseif( $zone_id == 0 ) 
					{
						return __("All Other countries");
					}
					// 3. Created Zone name  
					else                       
					{
						/* // The 2nd SQL query
						$zone_name = $wpdb->get_col( "
							SELECT wsz.zone_name
							FROM {$wpdb->prefix}woocommerce_shipping_zones as wsz
							WHERE wsz.zone_id = '$zone_id'
						" );
						return reset($zone_name); // converting to string and returning the value */
						return $zone_id;
					}
				}

            } // END of class definition

        } // END of if class exists
    }
    add_action('woocommerce_shipping_init', 'jem_table_rate_init');

    function add_jem_table_rate($methods) {
        $methods['jem_table_rate'] = 'JEM_Table_Rate_Shipping_Method';
        return $methods;
    }
    add_filter('woocommerce_shipping_methods', 'add_jem_table_rate');


    //TODO - I don't think I want this
//	function no_shipping_text(){
//		echo wpautop( __( 'Free Shipping', 'woocommerce' ));
//	}
//	add_filter('woocommerce_cart_no_shipping_available_html', 'no_shipping_text');
//	add_filter('woocommerce_no_shipping_available_html', 'no_shipping_text');


}

/**
 * Load admin scripts
 */
function jem_table_rate_admin_scripts($hook) {
    global $wptr_settings_page, $post_type;

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';



    //Load the styles & scripts we need 
    if ($hook == 'woocommerce_page_wc-settings') {

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
    }
    wp_enqueue_style('jem-table-rate-css', plugin_dir_url(__FILE__) . 'assets/css/custom.css');
}
add_action('admin_enqueue_scripts', 'jem_table_rate_admin_scripts', 100);

function pr($pr_data) {
    ?>
    <script>
        jQuery(document).ready(function () {
            jQuery("#checkall-checkbox-id").change(function () {
                var checked = jQuery(this).is(':checked'); // Checkbox state
                // Select all
                if (checked) {
                    jQuery('.chkItems').each(function () {
                        jQuery(this).prop('checked', 'checked');
                        jQuery('#jem_table_rate_remove_selected_method').prop('disabled', false);
                    });
                } else {
                    // Deselect All
                    jQuery('.chkItems').each(function () {
                        jQuery(this).prop('checked', false);
                        jQuery('#jem_table_rate_remove_selected_method').prop('disabled', true);
                    });
                }

            });
        });
		
    //        jQuery(document).on("click",".wc-shipping-zone-method-settings",function(){
    //            var a_href = jQuery('.wc-shipping-zone-method-settings').attr('href');
    //            var templateUrl = '<?= admin_url(); ?>'+ a_href;
    //            jQuery(".wc-shipping-zone-method-settings").attr("target","_blank"); 
    //            alert(templateUrl);
    //});
    </script><?php
    echo "<pre>";
    print_r($pr_data);
    echo "</pre>";
}
//add_action('admin_head', 'pr');
add_action('admin_footer', 'pr');
