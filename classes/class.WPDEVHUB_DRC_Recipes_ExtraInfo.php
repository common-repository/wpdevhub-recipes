<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 3/3/17
 * Time: 4:56 PM
 * To change this template use File | Settings | File Templates.
 */
class WPDEVHUB_DRC_Recipes_ExtraInfo {

    public $cooktimeValue=0;
    public $cooktimeMeasurement=self::UNIT_MINUTES;

    public $difficulty=self::DIFFICULTY_MEDIUM;

    const UNIT_SECONDS=1;
    const UNIT_MINUTES=2;
    const UNIT_HOURS=3;
    const UNIT_DAYS=4;
    const UNIT_DEFAULT = self::UNIT_MINUTES;

    const DIFFICULTY_EASY=1;
    const DIFFICULTY_MEDIUM=2;
    const DIFFICULTY_HARD=3;
    const DIFFICULTY_DEFAULT = self::DIFFICULTY_MEDIUM;

    public function __construct(){

    }

    public function getFinalString(){
        return $this->cooktimeValue.' '. self::getFormattedMeasurementString($this->cooktimeMeasurement);
    }

    /*
    * Returns the current Status flags as an array for Editors and such
    */
    public static function getAllMeasurementMarks(){
        $collection = array();
        $collection[self::UNIT_SECONDS]=self::getFormattedMeasurementString(self::UNIT_SECONDS);
        $collection[self::UNIT_MINUTES]=self::getFormattedMeasurementString(self::UNIT_MINUTES);
        $collection[self::UNIT_HOURS]=self::getFormattedMeasurementString(self::UNIT_HOURS);
        $collection[self::UNIT_DAYS]=self::getFormattedMeasurementString(self::UNIT_DAYS);
        return $collection;
    }

    /*
     * Returns a human readable version of the Status Flag
     */
    public static function getFormattedMeasurementString($status){
        $collection = array(
            self::UNIT_SECONDS=>'seconds',
            self::UNIT_MINUTES=>'minutes',
            self::UNIT_HOURS=>'hours',
            self::UNIT_DAYS=>'days',
        );
        return WPDEVHUB_DRC_Utilities::getFromArray($status, $collection, '');
    }

    /*
    * Returns the current Status flags as an array for Editors and such
    */
    public static function getAllDifficultyMarks(){
        $collection = array();
        $collection[self::DIFFICULTY_EASY]=self::getFormattedDifficultyString(self::DIFFICULTY_EASY);
        $collection[self::DIFFICULTY_MEDIUM]=self::getFormattedDifficultyString(self::DIFFICULTY_MEDIUM);
        $collection[self::DIFFICULTY_HARD]=self::getFormattedDifficultyString(self::DIFFICULTY_HARD);
        return $collection;
    }

    /*
     * Returns a human readable version of the Status Flag
     */
    public static function getFormattedDifficultyString($status){
        $collection = array(
            self::DIFFICULTY_EASY=>'Easy',
            self::DIFFICULTY_MEDIUM=>'Medium',
            self::DIFFICULTY_HARD=>'Hard',
        );
        return WPDEVHUB_DRC_Utilities::getFromArray($status, $collection, '');
    }

}
