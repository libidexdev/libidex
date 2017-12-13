<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
 /**
  * File provided by Matt Gifford.
  * Thanks Matt!
  */
class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Date_Picker extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
	/**
	 * Render the value
	 *
	 * @return $this
	 */
	protected function _render()
	{
        // Convert value to DateTime() object
        if ($value = $this->getValue()) {

            // Build a new DateTime object using the default timezone

            if (function_exists('date_create_from_format')) {
                // Use the PHP 5.3 'date_create_from_format' function

                // Convert the jQuery data format string to PHP format
                $dateFormat = $this->convertDataformatJqueryToPhp($this->field->date_format);

                // Parse the date string
                $date = DateTime::createFromFormat($dateFormat, $value);

                // Set the time to midnight (otherwise the time part of the DateTime object will be the current system time)
                $date->setTime(0, 0, 0);

                // Set the value to a new date time object
                $this->setValue($date);

            } else if (function_exists('strptime')) {
                // Use the PHP 5.1+ (unix only) 'strptime' function (however this function is not year 2038 safe)

                // Convert the jQuery data format string to PHP strptime format
                $dateFormat = $this->convertDataformatJqueryToStrptime($this->field->date_format);

                // Parse the date string
                if ($dateDetails = strptime($value, $dateFormat)) {
                    // Set the value to a new date time object
                    if (class_exists('DateTime')) {
                        // PHP 5.2+
                        $this->setValue(
                            new DateTime(sprintf('%04d-%02d-%02d',
                                    $dateDetails['tm_year'] + 1900,
                                    $dateDetails['tm_mon'] + 1,
                                    $dateDetails['tm_mday'])
                            )
                        );

                    } else {
                        // PHP 5.1 or older, return a timestamp
                        $this->setValue(strtotime(sprintf('%04d-%02d-%02d',
                                $dateDetails['tm_year'] + 1900,
                                $dateDetails['tm_mon'] + 1,
                                $dateDetails['tm_mday']
                                )
                            )
                        );
                    }
                } else {
                    // Invalid format, return false
                    $this->setValue(false);
                }
            } else {
                // Neither date parsing functions were available, try to parse as is
                if (class_exists('DateTime')) {
                    // PHP 5.2+
                    $this->setValue(new DateTime($value));
                } else {
                    // PHP 5.1 or older
                    $this->setValue(strtotime($value));
                }
            }
        }

		return parent::_render();
	}


    /**
     * Converts a jQuery data format string to PHP date()/DateTime() format
     *
     * @param   string     $dateFormatStr     The date format string to convert
     * @return $this
     */
    function convertDataformatJqueryToPhp($dateFormatStr)
    {
        // The before and after replacements, __1 & __2 are to stop the subsequent values matching the replacements (e.g. dd => d => j)
        $jquery = array('dd', 'd', 'DD', 'mm', 'MM', 'm',  'yy',  '__1', '__2');
        $php = array('__1', 'j', 'l', '__2', 'F', 'n', 'Y', 'd', 'm');

        // convert the string
        return str_replace($jquery, $php, $dateFormatStr);
    }


    /**
     * Converts a jQuery data format string to PHP strptime() format
     *
     * @param   string     $dateFormatStr     The date format string to convert
     * @return $this
     */
    function convertDataformatJqueryToStrptime($dateFormatStr)
    {
        // The before and after replacements, __1 & __2 are to stop the subsequent values matching the replacements (e.g. dd => d => j)
        $jquery = array('dd', 'd', 'DD', 'mm', 'MM', 'm',  'yy',  '__1', '__2');
        $php = array('__1', '   ', '', '__2', '', '', '%Y', '%d', '%m');

        // convert the string
        return str_replace($jquery, $php, $dateFormatStr);
    }
}
