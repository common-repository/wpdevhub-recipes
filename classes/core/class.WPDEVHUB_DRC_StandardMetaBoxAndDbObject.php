<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 9/7/17
 * Time: 11:48 PM
 * To change this template use File | Settings | File Templates.
 */
class WPDEVHUB_DRC_StandardMetaBoxAndDbObject extends WPDEVHUB_DRC_StandardMetaBox{

    public static $dataObjectClassname = "some-classname";

    /*
     * Return the classname holding the data object
     */
    public static function getDataObjectClassName(){
        if(class_exists(static::$dataObjectClassname)){
            return static::$dataObjectClassname;
        }
        WPDEVHUB_DRC_Utilities::logError("Class [".static::$dataObjectClassname."] does not exist.  Error.");
    }

    /*
     * Override this function get get the data object classname instead.
     * This funciton should not be called - but providing here as a precaution
     */
    public static function getEditorObjectClassName(){
        return static::getDataObjectClassName();
    }

    /*
     * Get the Data Object by ID
     */
    public static function getDataObject($postId){
        $object = call_user_func(array(static::getDataObjectClassName(), 'get'), $postId);
        WPDEVHUB_DRC_Utilities::logMessage("DataObject : ".print_r($object, true));
        return $object;
    }

    /*
     * Override this function to get the Data from the database instead of from the Post Meta
     */
    public static function getPostMeta($postId){
        return static::getDataObject($postId);
    }

    // This does not do anything - here for the editor handling
    public function save(){}

    public static function saveDataObject($postId, $editorObject){
        $dataObject = static::getDataObject($postId);

        if(!empty($dataObject)){
            foreach($editorObject as $k=>$v){
                $dataObject->$k = $v;
            }
            $dataObject->id = $postId;
            $dataObject->save();
        }
    }

    public static function savePostMeta($postId, $value){
        static::saveDataObject($postId, $value);
    }

    /*
    * If the object class exists then build the common editor to make editing fast and easy
    */
    public static function renderFromEditorOptions( $postId ){

        $object = null;
        if(!empty($postId)){
            $object = static::getDataObject( $postId );
        }

        $options = call_user_func(array(static::getDataObjectClassName(), 'editorBuildOptions'), $object);
        $options['showBottomSaveButton']=false;

        $html = WPDEVHUB_DRC_StandardEditor::buildEditor($options, '#');

        echo $html;

    }

    /*
    * If editor objects are defined then do the save automatically so child classes don't have to do it
    */
    public static function saveFromEditorOptions( $postId ){

        $object = static::getDataObject($postId);

        if(empty($object)){
            $classname = static::getDataObjectClassName();
            $object = new $classname();

            // Force the ID's to match the ID's of the Post
            $object->id = $postId;
            $object->save();
        }

        $options = call_user_func(array(static::getDataObjectClassName(), 'editorBuildOptions'), $object);

        $object = WPDEVHUB_DRC_StandardEditor::saveEditorChanges($object, $options, $_POST);

        // Save the object into the data class object
        static::saveDataObject( $postId, $object );


    }



}
