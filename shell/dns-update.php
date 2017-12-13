<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_DNS_Update extends Mage_Shell_Abstract
{


    /**
     * Run script
     *
     */
    public function run()
    {

        /**
         * Due to multiple hacking attempts it was necessary to add .htaccess rules to the backend.
         * Because the broadband connection that Malaysia uses get a new IP quite often it is necessary
         * to update the .htaccess quite often with the new IP address.
         *
         * In Malaysia they have installed DynDNS to the computer in the office ot update the DNS name 'libidex.webhop.biz'
         * whenever the office IP updates.
         *
         * This script will pull this IP address from the DNS record for the domain name that is updated when the IP changes.
         *
         * If they cant get in simply go to http://libidex.com/update.php and it will cause the .htaccess to be re-written
         * with the new IP address
         */

        // DNS name IP to be pulled from
        $hostname = 'libidex.webhop.biz';

        // .htaccess file with marker
        $file = '../.htaccess';

        // Unique marker to look for in order to insert new IP address into file
        $marker = "####{{{IP_ADDRESS_PLACEHOLDER_DO_NOT_DELETE_SEE_SHELL/DNS-UPDATE.PHP_FOR_MORE_INFO}}}####";

        // this is what the IP needs to be appended to the end of
        $rewrite_prefix = 'RewriteCond %{REMOTE_ADDR} !^';

        // if available use an authoritive DNS server so we do not have to wait for DNS propogation
        // these are authoritative DNS servers for no-ip.com, libidex are using DynDNS, this was just for my testing
        // if you can find the correct addresses this would be ideal as it would mean querying the DynDNS servers
        // directly instead of a cached copy
        $authoritative_name_servers = array(    'ns1.dyndns.org',
                                                'ns2.dyndns.org',
                                                'ns3.dyndns.org',
                                                'ns4.dyndns.org');

        //Get DNS A record
        //This uses dns_get_record instead of gethostbyname as we can query the authoritative name server to get a quicker update
        //without having to wait for DNS propagation
        $record = dns_get_record($hostname, DNS_A, $authoritative_name_servers);

        // check if record was returned, dns_get_record() returns FALSE on fail
        if ($record !== false){

            // check if ip address was populated
            if (isset($record[0]['ip'])) {

                // verify it is a valid IP address
                $record = filter_var($record[0]['ip'], FILTER_VALIDATE_IP);

                // if so save to file, filter_var() returns FALSE on invalid input
                if ($record !== false) {

                    // Load .htaccess into var for parsing
                    $templateString = file_get_contents($file);

                    //file_get_contents returns false on fail
                    if ($templateString !== false) {

                        // extract string into array  using marker as separator
                        $chunks = explode($marker, $templateString);

                        // generate output
                        $outstring = $chunks[0].$marker.PHP_EOL.$rewrite_prefix.$record.PHP_EOL.$marker.$chunks[2];

                        // generate output
                        $fileWriteStatus = file_put_contents($file, $outstring);

                        //file_put_contents returns false on fail
                        if ($fileWriteStatus !== false) {
                            $this->updateMessage('UPDATE OK');
                        } else {
                            $this->updateMessage('UPDATE ERROR');
                        }
                    } else {
                        $this->updateMessage('Reading .htaccess file failed');
                    }
                } else {
                    $this->updateMessage('IP address did not pass validation');
                }
            } else {
                $this->updateMessage('IP address not populated');
            }
        } else {
            $this->updateMessage('DNS Lookup failed');
        }
    }


    public function updateMessage($messageString) {
        Mage::log($messageString, null, 'htaccess_updater.log', true);
        echo $messageString.PHP_EOL;
    }

}

$shell = new Mage_Shell_DNS_Update();
$shell->run();