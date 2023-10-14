<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_WMS
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\WMS\Model;

use Webkul\WMS\Api\Data\StaffInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Staff warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Staff extends AbstractModel implements StaffInterface, IdentityInterface
{
    const NOROUTE_ID = "no-route";
    const CACHE_TAG = "wms_warehouse_staff";
    protected $_cacheTag = "wms_warehouse_staff";
    protected $_eventPrefix = "wms_warehouse_staff";

    /**
     * Contructor function
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init(\Webkul\WMS\Model\ResourceModel\Staff::class);
    }

    /**
     * Function load
     *
     * @param integer $id    id
     * @param string  $field field
     *
     * @return null
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteStaff();
        }
        return parent::load($id, $field);
    }

    /**
     * Function noRouteStaff
     *
     * @return Staff
     */
    public function noRouteStaff()
    {
        return $this->load(self::NOROUTE_ID, $this->getIdFieldName());
    }

    /**
     * Function getIdentities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . "_" . $this->getId()];
    }

    /**
     * Function getId
     *
     * @return integer
     */
    public function getId()
    {
        return parent::getData(self::ID);
    }

    /**
     * Function setId
     *
     * @param integer $id id
     *
     * @return boolean
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Function getOs
     *
     * @return string
     */
    public function getOs()
    {
        return parent::getData(self::OS);
    }

    /**
     * Function setOs
     *
     * @param string $os os
     *
     * @return null
     */
    public function setOs($os)
    {
        return $this->setData(self::OS, $os);
    }

    /**
     * Function getDob
     *
     * @return string
     */
    public function getDob()
    {
        return parent::getData(self::DOB);
    }

    /**
     * Function setDob
     *
     * @param string $dob dob
     *
     * @return boolean
     */
    public function setDob($dob)
    {
        return $this->setData(self::DOB, $dob);
    }

    /**
     * Function getAge
     *
     * @return integer
     */
    public function getAge()
    {
        return parent::getData(self::AGE);
    }

    /**
     * Function setAge
     *
     * @param integer $age age
     *
     * @return boolean
     */
    public function setAge($age)
    {
        return $this->setData(self::AGE, $age);
    }

    /**
     * Function getName
     *
     * @return string
     */
    public function getName()
    {
        return parent::getData(self::NAME);
    }

    /**
     * Function setName
     *
     * @param string $name name
     *
     * @return boolean
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Function getEmail
     *
     * @return string
     */
    public function getEmail()
    {
        return parent::getData(self::EMAIL);
    }

    /**
     * Function setEmail
     *
     * @param string $email email
     *
     * @return boolean
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Function getStatus
     *
     * @return integer
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);
    }

    /**
     * Function setStatus
     *
     * @param integer $status status
     *
     * @return boolean
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Function getGender
     *
     * @return string
     */
    public function getGender()
    {
        return parent::getData(self::GENDER);
    }

    /**
     * Function setGender
     *
     * @param string $gender gender
     *
     * @return boolean
     */
    public function setGender($gender)
    {
        return $this->setData(self::GENDER, $gender);
    }

    /**
     * Function getAddress
     *
     * @return string
     */
    public function getAddress()
    {
        return parent::getData(self::ADDRESS);
    }

    /**
     * Function setAddress
     *
     * @param string $address address
     *
     * @return boolean
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * Function getFilename
     *
     * @return string
     */
    public function getFilename()
    {
        return parent::getData(self::FILENAME);
    }

    /**
     * Function setFilename
     *
     * @param string $filename filename
     *
     * @return null
     */
    public function setFilename($filename)
    {
        return $this->setData(self::FILENAME, $filename);
    }

    /**
     * Function getContactNo
     *
     * @return string
     */
    public function getContactNo()
    {
        return parent::getData(self::CONTACT_NO);
    }

    /**
     * Function setContactNo
     *
     * @param string $contact_no contact_no
     *
     * @return boolean
     */
    public function setContactNo($contact_no)
    {
        return $this->setData(self::CONTACT_NO, $contact_no);
    }

    /**
     * Function getCreatedAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * Function setCreatedAt
     *
     * @param string $created_at created_at
     *
     * @return boolean
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * Function getUpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * Function setUpdatedAt
     *
     * @param string $updated_at updated_at
     *
     * @return boolean
     */
    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }

    /**
     * Function getStaffToken
     *
     * @return string
     */
    public function getStaffToken()
    {
        return parent::getData(self::STAFF_TOKEN);
    }

    /**
     * Function setStaffToken
     *
     * @param string $staff_token staff_token
     *
     * @return null
     */
    public function setStaffToken($staff_token)
    {
        return $this->setData(self::STAFF_TOKEN, $staff_token);
    }

    /**
     * Function getDeviceToken
     *
     * @return string
     */
    public function getDeviceToken()
    {
        return parent::getData(self::DEVICE_TOKEN);
    }

    /**
     * Function setDeviceToken
     *
     * @param string $device_token device_token
     *
     * @return null
     */
    public function setDeviceToken($device_token)
    {
        return $this->setData(self::DEVICE_TOKEN, $device_token);
    }

    /**
     * Function getWarehouseId
     *
     * @return string
     */
    public function getWarehouseId()
    {
        return parent::getData(self::WAREHOUSE_ID);
    }

    /**
     * Function setWarehouseId
     *
     * @param string $warehouse_id warehouse_id
     *
     * @return null
     */
    public function setWarehouseId($warehouse_id)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouse_id);
    }
}
