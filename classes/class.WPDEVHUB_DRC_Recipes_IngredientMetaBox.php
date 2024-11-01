<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 3/21/17
 * Time: 3:46 PM
 * To change this template use File | Settings | File Templates.
 */
class WPDEVHUB_DRC_Recipes_IngredientMetaBox extends WPDEVHUB_DRC_StandardMetaBox{

    const KEYNAME = "wpdevhub_drc_ingredients";
    const TITLE = "Ingredients List";
    const SCREEN = WPDEVHUB_DRC_Recipes_Recipe::KEYNAME;

    public static function renderCustom( $post ){

        // Use get_post_meta to retrieve an existing value from the database.
        $ingredients = self::getPostMeta($post->ID);
        $ingredients = WPDEVHUB_DRC_Utilities::ensureIsArray($ingredients);

        $html = '';

        // Display the form, using the current value.
        wp_enqueue_media();
        $rowCounter=0;

        $html .= '<ul class="WPDEVHUB_DRC_jquery-ui-sortable">';
        if(!empty($ingredients)){
            foreach($ingredients as $ingredient){
                // The "$value" should be an Object representing an item

                // Validate instance type
                if(is_a($ingredient, 'WPDEVHUB_DRC_Recipes_Ingredient')){

                    $rowId = 'drc_ingredient_row_'.$rowCounter;
                    $rowKey = 'drc_ingredient_'.$rowCounter;

                    $html .= '<li id="'.$rowKey.'_wrapper" class="drc-pbox5">';

                    // Hidden Element to identify this row
                    $html .= '<input type="hidden" name="'.$rowId.'" value="'.$rowCounter.'" />';

                    $html .= '<div>';
                    $html .= '<input type="text" id="'.$rowKey.'_quantity" name="'.$rowKey.'_quantity" value="'.$ingredient->quantity.'" />';
                    $html .= '<select id="'.$rowKey.'_measurement" name="'.$rowKey.'_measurement">';
                    $measurements = WPDEVHUB_DRC_Recipes_Ingredient::getAllMeasurementMarks();
                    foreach($measurements as $measurementKey=>$measurementValue){
                        $selected = '';
                        if($ingredient->measurement == $measurementKey){
                            $selected = ' selected="selected"';
                        }
                        $html .= '<option value="'.$measurementKey.'"'.$selected.'>'.$measurementValue.'</option>';
                    }
                    $html .= '</select>';
                    $html .= '<input type="text" id="'.$rowKey.'_title" name="'.$rowKey.'_title" value="'.$ingredient->title.'" />';
                    $html .= '<button style="float:right" onClick="javascript:WPDEVHUB_DRC_Admin.deleteGenericWrapperRow(\''.$rowKey.'\')">Delete Item</button>';
                    $html .= '</div>';

                    $html .= '</li>';

                    // Increase row counter
                    $rowCounter++;
                }

            }
        }

        $html .= '</ul>';
        $html .= '<input type="hidden" id="drc_ingredient_row_counter" value='.$rowCounter.' />';
        $html .= '<div id="addMoreDrcIngredientRows"><a href="javascript:WPDEVHUB_DRC_Admin.addDrcIngredientRow();">add new ingredient</a></div>';

        echo $html;

    }

    public static function saveCustom( $post_id ){

        $items = array();
        foreach($_POST as $name=>$value){
            $string = "drc_ingredient_row_";
            if(strpos($name, $string) !== false){
                $rowKey = "drc_ingredient_".$value;

                $measurement = 0;
                $quantity = "";
                $title = "";

                if(array_key_exists($rowKey.'_title', $_POST)){
                    // Sanitize the incoming value - should only be basic text
                    $title = sanitize_text_field($_POST[$rowKey.'_title']);
                }

                if(array_key_exists($rowKey.'_quantity', $_POST)){
                    // Sanitize the incoming value -- intval will default to 0 if bad input
                    $quantity = intval($_POST[$rowKey.'_quantity']);
                }

                if(array_key_exists($rowKey.'_measurement', $_POST)){
                    // Sanitize the incoming value -- intval will default to 0 if bad input.  Menu optioins are numeric keys
                    $measurement = intval($_POST[$rowKey.'_measurement']);
                }

                if(!empty($title)){

                    $ingredient = new WPDEVHUB_DRC_Recipes_Ingredient();
                    $ingredient->title = $title;
                    $ingredient->quantity = $quantity;
                    $ingredient->measurement = $measurement;

                    $items[]=$ingredient;
                }



            }
        }

        self::savePostMeta($post_id, $items);

    }

}
