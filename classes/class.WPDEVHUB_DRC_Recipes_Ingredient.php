<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 3/3/17
 * Time: 4:56 PM
 * To change this template use File | Settings | File Templates.
 */
class WPDEVHUB_DRC_Recipes_Ingredient {

    public $title='';
    public $quantity=0;
    public $measurement=0;

    const M_NULL = 0;
    const M_TEASPOON = 1;
    const M_TABLESPOON = 2;
    const M_FLUID_OUNCE = 3;
    const M_CUP = 4;
    const M_PINT = 5;
    const M_QUART = 6;
    const M_GALLON = 7;
    const M_MILLILITER = 8;
    const M_LITER = 9;
    const M_POUND = 10;
    const M_OUNCE = 11;
    const M_MILLIGRAM = 12;
    const M_GRAM = 13;
    const M_KILOGRAM = 14;
    const M_STALK = 15;
    const M_BUSHEL = 16;
    const M_BOX = 17;
    const M_WHOLE = 18;
    const M_CAN = 19;
    const M_CONTAINER = 20;

    public function __construct(){

    }



    /*
    * Returns the current Status flags as an array for Editors and such
    */
    public static function getAllMeasurementMarks(){
        $collection = array();
        $collection[self::M_NULL]=self::getFormattedMeasurementString(self::M_NULL);
        $collection[self::M_TEASPOON]=self::getFormattedMeasurementString(self::M_TEASPOON);
        $collection[self::M_TABLESPOON]=self::getFormattedMeasurementString(self::M_TABLESPOON);
        $collection[self::M_FLUID_OUNCE]=self::getFormattedMeasurementString(self::M_FLUID_OUNCE);
        $collection[self::M_CUP]=self::getFormattedMeasurementString(self::M_CUP);
        $collection[self::M_PINT]=self::getFormattedMeasurementString(self::M_PINT);
        $collection[self::M_QUART]=self::getFormattedMeasurementString(self::M_QUART);
        $collection[self::M_GALLON]=self::getFormattedMeasurementString(self::M_GALLON);
        $collection[self::M_MILLILITER]=self::getFormattedMeasurementString(self::M_MILLILITER);
        $collection[self::M_LITER]=self::getFormattedMeasurementString(self::M_LITER);
        $collection[self::M_POUND]=self::getFormattedMeasurementString(self::M_POUND);
        $collection[self::M_OUNCE]=self::getFormattedMeasurementString(self::M_OUNCE);
        $collection[self::M_MILLIGRAM]=self::getFormattedMeasurementString(self::M_MILLIGRAM);
        $collection[self::M_GRAM]=self::getFormattedMeasurementString(self::M_GRAM);
        $collection[self::M_KILOGRAM]=self::getFormattedMeasurementString(self::M_KILOGRAM);
        $collection[self::M_STALK]=self::getFormattedMeasurementString(self::M_STALK);
        $collection[self::M_BUSHEL]=self::getFormattedMeasurementString(self::M_BUSHEL);
        $collection[self::M_BOX]=self::getFormattedMeasurementString(self::M_BOX);
        $collection[self::M_CAN]=self::getFormattedMeasurementString(self::M_CAN);
        $collection[self::M_CONTAINER]=self::getFormattedMeasurementString(self::M_CONTAINER);
        $collection[self::M_WHOLE]=self::getFormattedMeasurementString(self::M_WHOLE);
        return $collection;
    }

    /*
     * Returns a human readable version of the Status Flag
     */
    public static function getFormattedMeasurementString($status){
        $collection = array(
            self::M_NULL=>'',
            self::M_TEASPOON=>'Teaspoon',
            self::M_TABLESPOON=>'Tablespoon',
            self::M_FLUID_OUNCE=>'Fluid Ounce',
            self::M_CUP=>'Cup',
            self::M_PINT=>'Pint',
            self::M_QUART=>'Quart',
            self::M_GALLON=>'Gallon',
            self::M_MILLILITER=>'Milliliter',
            self::M_LITER=>'Liter',
            self::M_POUND=>'Pound',
            self::M_OUNCE=>'Ounce',
            self::M_MILLIGRAM=>'Milligram',
            self::M_GRAM=>'Gram',
            self::M_KILOGRAM=>'Kilogram',
            self::M_STALK=>'Stalk',
            self::M_BUSHEL=>'Bushel',
            self::M_BOX=>'Box',
            self::M_CAN=>'Can',
            self::M_CONTAINER=>'Container',
            self::M_WHOLE=>'Whole'
        );
        return WPDEVHUB_DRC_Utilities::getFromArray($status, $collection, '');
    }

}
