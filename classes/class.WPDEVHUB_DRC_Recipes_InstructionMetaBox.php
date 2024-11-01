<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 3/21/17
 * Time: 3:46 PM
 * To change this template use File | Settings | File Templates.
 */
class WPDEVHUB_DRC_Recipes_InstructionMetaBox extends WPDEVHUB_DRC_StandardMetaBox{

    const KEYNAME = "wpdevhub_drc_instructions";
    const TITLE = "Cooking Instructions";
    const SCREEN = WPDEVHUB_DRC_Recipes_Recipe::KEYNAME;

    public static function renderCustom( $post ){

        // Use get_post_meta to retrieve an existing value from the database.
        $instructions = self::getPostMeta($post->ID);
        $instructions = WPDEVHUB_DRC_Utilities::ensureIsArray($instructions);

        $html = '';

        // Display the form, using the current value.
        wp_enqueue_media();
        $key=0;

        $html .= '<ul class="WPDEVHUB_DRC_jquery-ui-sortable">';
        if(!empty($instructions)){
            foreach($instructions as $value){
                // The "$value" should be an Object representing an item

                // Vlaidate instance type
                if(is_a($value, 'WPDEVHUB_DRC_Recipes_Instruction')){

                    $rowId = 'drc_instruction_row_'.$key;
                    $rowKey = 'drc_instruction_'.$key;

                    $html .= '<li id="'.$rowKey.'_wrapper" class="drc-pbox5">';

                    // Hidden Element to identify this row
                    $html .= '<input type="hidden" name="'.$rowId.'" value="'.$key.'" />';

                    $html .= '<div style="text-align:right;">';
                    $html .= '<button onClick="javascript:WPDEVHUB_DRC_Admin.deleteGenericWrapperRow(\''.$rowKey.'\')">Delete Item</button>';
                    $html .= '</div>';

                    $html .= '<div><textarea id="'.$rowKey.'_contents" name="'.$rowKey.'_contents" style="width:100%;height:100px;">'.$value->contents.'</textarea></div>';

                    /*
                    ob_start();
                    wp_editor($value->contents, $rowKey.'_contents', array('textarea_rows'=>5));
                    $html .= '<div>'.ob_get_clean().'</div>';
                    */

                    $html .= '</li>';

                    $key++;
                }

            }
        }

        $html .= '</ul>';
        $html .= '<input type="hidden" id="drc_instruction_row_counter" value='.$key.' />';
        $html .= '<div id="addMoreDrcInstructionRows"><a href="javascript:WPDEVHUB_DRC_Admin.addDrcInstructionRow();">add cooking instruction step</a></div>';

        echo $html;

    }

    public static function saveCustom( $post_id ){

        error_log("POST VARS: ".print_r($_POST, true));

        $items = array();
        foreach($_POST as $name=>$value){
            $string = "drc_instruction_row_";
            if(strpos($name, $string) !== false){
                $rowKey = "drc_instruction_".$value;

                //WPDEVHUB_DRC_Utilities::logMessage("KEY FOUND: Key[$name] Value[$value]");

                // Get the values
                $contents = WPDEVHUB_DRC_Utilities::getFromArray($rowKey.'_contents', $_POST);

                if(!empty($contents)){
                    $instruction = new WPDEVHUB_DRC_Recipes_Instruction();

                    // Preserve text area new lines
                    $instruction->contents = $contents;

                    $items[]=$instruction;

                    //WPDEVHUB_DRC_Utilities::logMessage("Finished with a Step: ".print_r($instruction, true));
                }



            }
        }

        self::savePostMeta($post_id, $items);

    }

}
