<?php

/*
  Easy Pie Site Spy Plugin
  Copyright (C) 2015, Synthetic Thought LLC
  website: easypiewp.com contact: bob@easypiewp.com

  Easy Pie Site Spy Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA
  
  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

require_once(dirname(__FILE__) . '/Entities/class-ezp-ibc-global-entity.php');


if (!class_exists('EZP_IBC_Product_Detail'))
{
    class EZP_IBC_Product_Detail
    {
        public $id = -1;
        public $name = '';
        
        public function __construct()
        {
            $this->name = EZP_IBC_U::__('Unknown');
        }    
    }       
}

if (!class_exists('EZP_IBC_Order_Line_Item'))
{
    class EZP_IBC_Order_Line_Item
    {
        public $order_id = -1;
        public $quantity = 0;
        public $total = 0;
        public $ecommerce_system_type = -1;
        
        /*
         * @var $product_detail EZP_IBC_Product_Detail
         */
        public $product_detail = null;
        
        public function __construct($ecommerce_system_type = EZP_IBC_ECommerce_Modes::WOO_COMMERCE)
        {
            $this->product_detail = new EZP_IBC_Product_Detail();
            $this->ecommerce_system_type = $ecommerce_system_type;
        }
        
        public static function get_instance_from_event_data($event_data)
        {
            $line_item_data = json_decode($event_data);
            
            $line_item = new EZP_IBC_Order_Line_Item();
            
            $line_item->set($line_item_data);
            
            return $line_item;
        }
        
        private function set($data)
        {
            foreach ($data as $key => $value)
            {
                $this->{$key} = $value;
            }
        }
    }       
}

if (!class_exists('EZP_IBC_ECommerce_Callback'))
{
    class EZP_IBC_ECommerce_Callback
    {
        public $purchase = null;
    }
}
    
if(!interface_exists('EZP_IBC_IECommerce'))
{
    interface EZP_IBC_IECommerce
    {
        public function get_products($filter);
        public function register_callback($callback);                
        public function get_product_detail($product_id);
    }
}

if (!class_exists('EZP_IBC_ECommerce_Woo_Commerce'))
{
    class EZP_IBC_ECommerce_Woo_Commerce implements EZP_IBC_IECommerce
    {
        private $callback = null;
               
        /*
         * returns array of EZP_IBC_Product_Detail
         */
        public function get_products($filter)
        {
            $args = array('post_type' => 'product', 'post_status' => 'publish', 'nopaging' => true, 'orderby' => 'title');
            
            $loop = new WP_Query($args);
            
            $product_details = array();
            
            while($loop->have_posts())
            {
                $post = $loop->the_post();
                
                $ezp_product_detail = new EZP_IBC_Product_Detail();
                
                $ezp_product_detail->id = $post->ID;
                $ezp_product_detail->name = $post->post_title;
            }
            
            return $product_details;            
        }
        
        public function get_product_detail($product_id)
        {
            $product = get_post($product_id);
            
            $ezp_product_detail = new EZP_IBC_Product_Detail();
            
            $ezp_product_detail->id = $product_id;
            
            if($product != null)
            {                                
                $ezp_product_detail->name = $post->post_title;
            }
            
            return $ezp_product_detail;
        }
        
        public function register_callback($callback)
        {            
            $this->callback = $callback;
            
            add_action('woocommerce_checkout_order_processed', array($this, 'order_processed'));
            
            // Processing = payment received and stock has been reduced
         //    add_action('woocommerce_order_status_processing', array($this, 'order_processed'));
        }
        
        public function order_processed($order_id /* posted also supposed to be passed but getting arg error */)
        {
            EZP_IBC_U::debug("Woocommerce order processed callback for $order_id");
            
            if($this->callback != null)
            {
                EZP_IBC_U::debug("callback not null");
                
                $order = new WC_Order($order_id);
                $checkout_items = $order->get_items();

                $ezp_line_items = array();

                foreach( $checkout_items as $item )
                {
                    $product = $order->get_product_from_item($item);

                    $ezp_product_detail = new EZP_IBC_Product_Detail();
                    $ezp_line_item = new EZP_IBC_Order_Line_Item();

                    $ezp_product_detail->name = $product->get_title();
                    $ezp_product_detail->id = $product->id;

                    $ezp_line_item->order_id = $order->id;
                    $ezp_line_item->total = $order->get_line_total($item);
                    $ezp_line_item->quantity = $item['qty'];
                    $ezp_line_item->product_detail = $ezp_product_detail;

                    array_push($ezp_line_items, $ezp_line_item);                
                }

                if(count($ezp_line_items) > 0)
                {
                    /* Assuming will always be a class method */

                //    $object = $this->callback->purchase[0];
                 //   $method = $this->callback->purchase[1];

                    EZP_IBC_U::debug("Calling purchase callback");
                    
                    call_user_func_array($this->callback->purchase, array($ezp_line_items));
                  //  $object->$method($ezp_order);
                }
            }
        }
    }
}

if (!class_exists('EZP_IBC_ECommerce_Factory'))
{

    class EZP_IBC_ECommerce_Factory
    {
        /*
         * @returns EZP_IBC_IECommerce
         */
        public static function get_ecommerce()
        {
            $global = EZP_IBC_Global_Entity::get_instance();
            
            return self::get_commerce_by_id($global->ecommerce_mode);           
        }
        
        /*
         * @returns EZP_IBC_IECommerce
         */
        public static function get_commerce_by_id($ecommerce_id)
        {
            $ecommerce = null;
                          
            $global = EZP_IBC_Global_Entity::get_instance();
            
            if($global->ecommerce_mode == EZP_IBC_ECommerce_Modes::WOO_COMMERCE)
            {
                $ecommerce = new EZP_IBC_ECommerce_Woo_Commerce();
            }
            
            return $ecommerce;
        }
    }       
}
?>