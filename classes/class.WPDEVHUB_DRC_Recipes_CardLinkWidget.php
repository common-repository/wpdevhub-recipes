<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ben
 * Date: 3/12/15
 * Time: 11:23 PM
 *
 * This file controls the Widget Options for the Dimbal Software Banners Widgets
 *
 */
class WPDEVHUB_DRC_Recipes_CardLinkWidget extends WP_Widget {

    public static $displayOptions = array(
        1=>'Recipe',
        2=>'Zone'
    );

    public function __construct() {
        // widget actual processes
        parent::__construct(
            WPDEVHUB_CONST_DRC_SLUG, // Base ID
            WPDEVHUB_CONST_DRC_PLUGIN_TITLE, // Name
            array( 'description' => __( 'Add this widget to display Banners from the '.WPDEVHUB_CONST_DRC_PLUGIN_TITLE.'.  Select either a single banner or a group of banners.', 'text_domain' ), ) // Args
        );
    }

    public function form( $instance ) {
        // outputs the options form on admin
        $banners = WPDEVHUB_DRC_Banners_Banner::getAll();
        $zones = WPDEVHUB_DRC_Zone::getAllByTypeId(WPDEVHUB_DRC_Zone::TYPE_DBM);

        $bannerId = false;
        $zoneId = false;
        $displayId = false;
        if ( $instance ) {
            $title = esc_attr( $instance['title'] );
            $displayId = $instance['display_id'];
            $bannerId = $instance['banner_id'];
            $zoneId = $instance['zone_id'];
        }
        else {
            $title = __( 'Banner Ad' );
        }
        if(empty($displayId)){
            $displayId = 1;
        }

        $displayIdFieldOriginal = $this->get_field_name( 'display_id' );
        $displayIdFieldName = str_replace("[","_",$displayIdFieldOriginal);
        $displayIdFieldName = str_replace("]","_",$displayIdFieldName);
        $displayIdFieldName = str_replace("-","_",$displayIdFieldName);

        ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        <br />
        <br />
    <p><?php _e( 'Choose a Display Type:' ); ?></p>
    <br />
    <?php
        foreach(self::$displayOptions as $displayOptionKey=>$displayOptionName){
            $selected='';
            if($displayId == $displayOptionKey){
                $selected=' checked="checked"';
            }
            echo '<input type="radio" name="'.$displayIdFieldOriginal.'" value="'.$displayOptionKey.'"'.$selected.' onclick="WPDEVHUB_DRC_Banner_Admin.widgetChangeType(\''.$displayIdFieldName.'\','.$displayOptionKey.')" /> '.$displayOptionName.'&nbsp;&nbsp;&nbsp;';
        }
        ?>
    <br />
    <hr />
    <br />
    <?php

        ?>
    <div id="<?=($displayIdFieldName)?>_1" class="dimbal_dbm_widget_wrapper_<?=($displayIdFieldName)?> <?=($displayIdFieldName)?>_1">
        <label for="<?php echo $this->get_field_id( 'banner_id' ); ?>"><?php _e( 'Banner:' ); ?></label>
        <select id="<?php echo $this->get_field_id( 'banner_id' ); ?>" name="<?php echo $this->get_field_name( 'banner_id' ); ?>">
            <?php
            foreach($banners as $banner){
                $selected='';
                if($bannerId == $banner->id){
                    $selected=' selected="selected"';
                }
                echo '<option value="'.$banner->id.'"'.$selected.'>'.$banner->title.'</option>';
            }
            ?>
        </select>
    </div>
    <div id="<?=($displayIdFieldName)?>_2" class="dimbal_dbm_widget_wrapper_<?=($displayIdFieldName)?> <?=($displayIdFieldName)?>_2">
        <label for="<?php echo $this->get_field_id( 'zone_id' ); ?>"><?php _e( 'Zone:' ); ?></label>
        <select id="<?php echo $this->get_field_id( 'zone_id' ); ?>" name="<?php echo $this->get_field_name( 'zone_id' ); ?>">
            <?php
            foreach($zones as $zone){
                $selected='';
                if($zoneId == $zone->id){
                    $selected=' selected="selected"';
                }
                echo '<option value="'.$zone->id.'"'.$selected.'>'.$zone->text.'</option>';
            }
            ?>
        </select>
    </div>
    <script>
        WPDEVHUB_DRC_Banner_Admin.widgetChangeType('<?=($displayIdFieldName)?>',<?=($displayId)?>);
    </script>
    </p>
    <?php
    }

    public function update( $new_instance, $old_instance ) {
        // processes widget options to be saved
        $title = (array_key_exists('title',$new_instance))?$new_instance['title']:"";
        $instance['title'] = strip_tags($title);

        $bannerId = (array_key_exists('banner_id',$new_instance))?$new_instance['banner_id']:0;
        $instance['banner_id'] = strip_tags($bannerId);

        $zoneId = (array_key_exists('zone_id',$new_instance))?$new_instance['zone_id']:0;
        $instance['zone_id'] = strip_tags($zoneId);

        $displayId = (array_key_exists('display_id',$new_instance))?$new_instance['display_id']:1;
        $instance['display_id'] = strip_tags($displayId);
        return $instance;
    }

    public function widget( $args, $instance ) {
        // outputs the content of the widget

        //error_log("Inside Widget Display: ARGS: ".print_r($args, true));
        //error_log("Inside Widget Display: INSTANCE: ".print_r($instance, true));

        // Make sure the framework is enabled (this will prevent the widget from display entirely)
        if(!WPDEVHUB_DRC_Banners_Main::isPluginEnabled()){
            return;
        }

        /*
        if(defined("DOING_CRON")){
            error_log("DOING_CRON is defined: Value:".DOING_CRON);
        }
        if(defined("DOING_AJAX")){
            error_log("DOING_AJAX is defined: Value:".DOING_AJAX);
        }
        */

        // Make sure the Dimbal Scripts are enqueued
        // WPDEVHUB_DRC_Banners_Main::enqueuePublicResources();

        $html = "";

        // Now display the result based upon display id
        // Grabbing the html first -- so that we can just skip the display of the widget as a whole if there are problems
        $displayId = $instance['display_id'];
        switch($displayId){
            case 1:
                // Banner
                $bannerId = $instance['banner_id'];
                //error_log("Inside Widget Display: BANNER FOUND: ID:".$bannerId);
                $html = WPDEVHUB_DRC_Banners_Banner::shortcodeHandlerDisplayBanner(array('banner_id'=>$bannerId));
                break;
            case 2:
                // Zone
                $zoneId = $instance['zone_id'];
                //error_log("Inside Widget Display: ZONE FOUND: ID:".$zoneId);
                $html = WPDEVHUB_DRC_Banners_Banner::shortcodeHandlerDisplayZoneBanner(array('zone_id'=>$zoneId));
                break;
        }

        //error_log("Inside Widget Display: Instance: ".print_r($instance, true));
        //error_log("Inside Widget Display: HTML: ".$html);

        // Now display everything if the HTML variable has proper data in it.
        if(!empty($html)){

            // Before the widget
            echo $args['before_widget'];

            // Title information
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'];
                echo esc_html( $instance['title'] );
                echo $args['after_title'];
            }

            // Html for Banner Display
            echo $html;

            // After the widget
            echo $args['after_widget'];

        }

    }
}
