<?php

/**
 * MailChimp For Magento
 *
 * @category  Ebizmarts_MailChimp
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     7/11/19 11:24 AM
 * @file:     Date.php
 */
class Ebizmarts_MailChimp_Helper_Date extends Mage_Core_Helper_Abstract
{

    /**
     * get Date with Microtime.
     *
     * @return string
     */
    public function getDateMicrotime()
    {
        $microtime = explode(' ', microtime());
        $msec = $microtime[0];
        $msecArray = explode('.', $msec);
        $time = $this->formatDate(null, 'Y-m-d-H-i-s');
        $date = $time . '-' . $msecArray[1];
        return $date;
    }

    /**
     * Check if more than 270 seconds passed since the migration started to prevent the job to take too long.
     *
     * @param  $initialTime
     * @return bool
     */
    public function timePassed($initialTime)
    {
        $storeCount = count($this->getMageApp()->getStores());
        $timePassed = false;
        $finalTime = time();
        $difference = $finalTime - $initialTime;
        //Set minimum of 30 seconds per store view.
        $timeForAllStores = (30 * $storeCount);
        //Set total time in 4:30 minutes if it is lower.
        $timeAmount = ($timeForAllStores < 270) ? 270 : $timeForAllStores;
        if ($difference > $timeAmount) {
            $timePassed = true;
        }

        return $timePassed;
    }

    /**
     * @return string
     */
    protected function getCurrentaDateTime()
    {

        return $this->formatDate(null, 'd-m-Y H:i:s');
    }

    /**
     * Return date in given format and avoid timezone offset when avoidOffset(true)
     *
     * @param string $format
     * @param $date
     * @param bool $avoidOffset
     * @return mixed
     * @throws Mage_Core_Model_Store_Exception
     */
    public function formatDate($date = null, $format = 'Y-m-d', $avoidOffset = 1)
    {
        $gmtTimestamp = Mage::getModel('core/date')->gmtTimestamp($date);
        $currentTimestamp = $this->getTimestamp($gmtTimestamp);
        if ($avoidOffset) {
            $currentTimestamp = $this->avoidTimeZoneOffset($currentTimestamp);
        }

        $newDate = Mage::getModel('core/date')->gmtDate($format, $currentTimestamp);
        return $newDate;
    }

    /**
     * @param $timestamp
     * @return mixed
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function avoidTimeZoneOffset($timestamp)
    {
        $timeZone = Mage::app()->getStore()->getConfig('general/locale/timezone');
        $offSet   = Mage::getModel('core/date')->calculateOffset($timeZone);
        return ($timestamp + $offSet);
    }

    /**
     * @param null $time
     * @return string
     */
    protected function getTimestamp($time = null)
    {
        return Mage::getModel('core/date')->timestamp($time);
    }
}
