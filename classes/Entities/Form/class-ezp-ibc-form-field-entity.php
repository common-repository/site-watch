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

require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-standard-entity-base.php');

if (!class_exists('EZP_IBC_Form_Field_Types')) {
    
    abstract class EZP_IBC_Form_Field_Types {
        const Contact_Field = 0;
        const Data_Field = 1;        
    }
}

if (!class_exists('EZP_IBC_Form_Field_Entity'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Form_Field_Entity extends EZP_IBC_JSON_Entity_Base
    {
        public $form_id = -1;
      //  public $type = EZP_IBC_Form_Field_Types::Contact_Field;
        public $form_field_type = EZP_IBC_Form_Field_Types::Contact_Field;
        public $field_order = -1;
        public $contact_field_id = -1;
        public $default_value = '';
        public $description = '';
        public $label = '';        
        public $post_field_name = ''; // auto generated - either will be based on contact_field_{form_field_id} or data_field_{form_field_id}
        public $required = false;

        function __construct()
        {
            parent::__construct();
        }

        public static function delete_all_by_form_id($form_id)
        {            
            self::delete_by_type_and_field(get_class(), 'form_id', $form_id);
        }
        
        
        public function get_all_by_form_id($form_id)
        {
            return self::get_by_type_and_field(get_class(), 'form_id', $form_id);
        }

        /**
         * 
         * @param type $id
         * @return EZP_IBC_Form_Entity
         */
        public static function get_by_id($id)
        {
            return self::get_by_id_and_type($id, $this->type, self::$TABLE_NAME);
        }
        
        public function child_save()
        {
            if($this->id == -1)
            {
                $this->save();
            }
            
            if($this->form_field_type == EZP_IBC_Form_Field_Types::Data_Field)
            {
                $this->post_field_name = "data_field_$this->id";
            }
            else
            {
                $this->post_field_name = EZP_IBC_Contact_Entity::get_field_name($this->contact_field_id);
            }                     
            
            $this->save();
        }
        
        public function save_from_post($post, $form_id, $post_index)
        {
            $post_contact_field_ids = $post['contact_field_id'];
            $post_labels = $post['label'];
            $post_descriptions = $post['description'];
            $post_default_values = $post['default_value'];
            $post_field_orders = $post['field_order'];
            $post_form_field_types = $post['form_field_type'];
                        
            if(isset($post['required']))
            {
                $post_requireds = $post['required'];
            }
                       
            // If new form save then add all new field values    
            $this->form_id = $form_id;
            $this->contact_field_id = $post_contact_field_ids[$post_index];
            $this->default_value = $post_default_values[$post_index];
            $this->field_order = $post_field_orders[$post_index];
            $this->description = $post_descriptions[$post_index];
            $this->label = $post_labels[$post_index];
            $this->form_field_type = intval($post_form_field_types[$post_index]);

            $this->required = isset($post_requireds[$post_index]);
            
            $this->child_save();        
        }
    }
}
?>