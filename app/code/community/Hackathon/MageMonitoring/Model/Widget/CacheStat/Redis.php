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
 * @category    Hackathon
 * @package     Hackathon_MageMonitoring
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Hackathon_MageMonitoring_Model_Widget_CacheStat_Redis extends Hackathon_MageMonitoring_Model_Widget_CacheStat_Abstract
    implements Hackathon_MageMonitoring_Model_Widget_CacheStat
{
    private $_redisClient;
    private $_redisInfo;

    public function __construct()
    {
        try {
            $cacheConfig = Mage::getConfig()->getNode('global/cache')->asArray();
            if (array_key_exists('backend', $cacheConfig) && $cacheConfig['backend'] == 'Cm_Cache_Backend_Redis') {
                $server = $cacheConfig['backend_options']['server'];
                $port = $cacheConfig['backend_options']['port'];

                $this->_redisClient = new Credis_Client($server, $port);
                $this->_redisInfo = $this->_redisClient->__call('info', array());
            }

        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Redis';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        if (isset($this->_redisInfo['redis_version'])) {
            return $this->_redisInfo['redis_version'];
        }

        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        if (isset($this->_redisClient) && isset($this->_redisInfo)) {
            return true;
        }

        return false;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getMemoryMax()
     */
    public function getMemoryMax()
    {
        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getMemoryUsed()
     */
    public function getMemoryUsed()
    {
        if (isset($this->_redisInfo['used_memory'])) {
            return $this->_redisInfo['used_memory'];
        }

        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getCacheHits()
     */
    public function getCacheHits()
    {
        if (isset($this->_redisInfo['keyspace_hits'])) {
            return $this->_redisInfo['keyspace_hits'];
        }

        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getCacheMisses()
     */
    public function getCacheMisses()
    {
        if (isset($this->_redisInfo['keyspace_hits'])) {
            return $this->_redisInfo['keyspace_misses'];
        }

        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::flushCache()
     */
    public function flushCache()
    {
        $this->_redisClient->flushDb();

        return true;
    }

}
