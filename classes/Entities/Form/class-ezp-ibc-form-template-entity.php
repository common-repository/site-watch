<?php //

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

//require_once('class-ezp-ibc-standard-entity-base.php');
//require_once('class-ezp-ibc-form-field-entity.php');
//
//if (!class_exists('EZP_IBC_Built_In_Form_Template_ID'))
//{
//
//    abstract class EZP_IBC_Built_In_Form_Template_ID
//    {
//        const OneBy = -1;
//        const TwoBy = -2;
//    }
//}
//
//if (!class_exists('EZP_IBC_Form_Template_Entity'))
//{
//    // RSR: For now this just supports built-in templates
//
//    /**
//     * @author Bob Riley <bob@easypiewp.com>
//     * @copyright 2015 Synthetic Thought LLC
//     */
//    class EZP_IBC_Form_Template_Entity extends EZP_IBC_JSON_Entity_Base
//    {
//        public $name = "";
//        public $template_string = "";
//
//        //public static $TABLE_NAME = "easy_pie_ibc_form_tempates";
//
//        function __construct()
//        {
//          //  parent::__construct(self::$TABLE_NAME);
//            parent::__construct();
//        }
//     
//        public static function render($template_id, $form)
//        {
//            switch ($template_id)
//            {
//                case EZP_IBC_Built_In_Form_Template_ID::OneBy:
//                    return self::render_one_by($form);
//                    break;
//
//                case EZP_IBC_Built_In_Form_Template_ID::TwoBy:
//                    return self::render_two_by($form);
//                    break;
//
//                default:
//                    // RSR TODO: Future saved
//                    return "";
//                    break;
//            }
//        }
//
//        // Simply spit all fields out in a single column
//        // TODO: Warning: only have each form on one page - no dupe forms on same page
//        // <form>
//        //     <label id="ezp-form-{form_id}-{field_name}-label" for="ezp-form-{form_id}-{field_name}>the label</label>
//        //     <input id="ezp-form-{form_id}-{field_name}" name="ezp-form-{form_id}-{field_name}" />
//        // </form>
//        private static function render_one_by($form)
//        {
//            $render_string = '';
//            $postback_status = EZP_IBC_Form_Entity::get_postback_status($form->id);
//            
//            if($postback_status == null)
//            {               
//                /* @var $form EZP_IBC_Form_Entity */
//
//                $form_fields = $form->get_form_fields();
//                $form_element_id = "ezp-form-$form->id";            
//
//                // see easy-pie-ibc-styles for at least temporary location of styles (eventually probably in settings and sepcific ones in each form settings
//
//                $render_string = "<form id='$form_element_id' class='ezp-form' method='post'>";
//
//                $render_string .= "<input type='hidden' name='ezp_form_id' value='$form->id'/>";
//
//                // Each item has a generic class used for all ezp forms as well as a specific class to this form
//                foreach ($form_fields as $form_field)
//                {
//                    /* @var $form_field EZP_IBC_Form_Field_Entity */
//
//                    $required_string = $form_field->required ? '<span style="ff0000">*</span>' : '';
//
//                    $render_string .= "post field name:$form_field->post_field_name";
//                    $render_string .=
//                            "<label class='ezp-form-label ezp-form-$form->id-label' for='$form_field->post_field_name'>$form_field->label$required_string</label>"
//                            . "<input class='ezp-form-input ezp-form-$form->id-input' name='$form_field->post_field_name'/>";
//                }
//
//                $render_string .= '<button style="display:block" type="submit">' . EZP_IBC_U::__('Submit') . '</button></form>';
//            }
//            
//            return $render_string;
//        }
//    }
//}
?>