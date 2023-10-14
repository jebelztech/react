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

namespace Webkul\WMS\Api\Data;

/**
 * Class StaffInterface api
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
interface StaffInterface
{
    const ID = "id";
    const OS = "os";
    const DOB = "dob";
    const AGE = "age";
    const NAME = "name";
    const EMAIL = "email";
    const STATUS = "status";
    const GENDER = "gender";
    const ADDRESS = "address";
    const FILENAME = "filename";
    const CONTACT_NO = "contact_no";
    const CREATED_AT = "created_at";
    const UPDATED_AT = "updated_at";
    const STAFF_TOKEN = "staff_token";
    const DEVICE_TOKEN = "device_token";
    const WAREHOUSE_ID = "warehouse_id";

    /**
     * Function getId
     *
     * @return integer
     */
    public function getId();

    /**
     * Function setId
     *
     * @param integer $id id
     *
     * @return null
     */
    public function setId($id);

    /**
     * Function getOs
     *
     * @return string
     */
    public function getOs();

    /**
     * Function setOs
     *
     * @param string $os os
     *
     * @return null
     */
    public function setOs($os);

    /**
     * Function getDob
     *
     * @return string
     */
    public function getDob();

    /**
     * Function setDob
     *
     * @param string $dob dob
     *
     * @return null
     */
    public function setDob($dob);

    /**
     * Function getAge
     *
     * @return integer
     */
    public function getAge();

    /**
     * Function setAge
     *
     * @param integer $age age
     *
     * @return null
     */
    public function setAge($age);

    /**
     * Function getName
     *
     * @return string
     */
    public function getName();

    /**
     * Function setName
     *
     * @param string $name name
     *
     * @return null
     */
    public function setName($name);

    /**
     * Function getEmail
     *
     * @return string
     */
    public function getEmail();

    /**
     * Function setEmail
     *
     * @param string $email email
     *
     * @return null
     */
    public function setEmail($email);

    /**
     * Function getStatus
     *
     * @return integer
     */
    public function getStatus();

    /**
     * Function setStatus
     *
     * @param integer $status status
     *
     * @return null
     */
    public function setStatus($status);

    /**
     * Function getGender
     *
     * @return string
     */
    public function getGender();

    /**
     * Function setGender
     *
     * @param string $gender gender
     *
     * @return null
     */
    public function setGender($gender);

    /**
     * Function getAddress
     *
     * @return string
     */
    public function getAddress();

    /**
     * Function setAddress
     *
     * @param string $address address
     *
     * @return null
     */
    public function setAddress($address);

    /**
     * Function getFilename
     *
     * @return string
     */
    public function getFilename();

    /**
     * Function setFilename
     *
     * @param string $filename filename
     *
     * @return null
     */
    public function setFilename($filename);

    /**
     * Function getContactNo
     *
     * @return string
     */
    public function getContactNo();

    /**
     * Function setContactNo
     *
     * @param string $contact_no contact_no
     *
     * @return null
     */
    public function setContactNo($contact_no);

    /**
     * Function getCreatedAt
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Function setCreatedAt
     *
     * @param string $created_at created_at
     *
     * @return null
     */
    public function setCreatedAt($created_at);

    /**
     * Function getUpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Function setUpdatedAt
     *
     * @param string $updated_at updated_at
     *
     * @return null
     */
    public function setUpdatedAt($updated_at);

    /**
     * Function getStaffToken
     *
     * @return string
     */
    public function getStaffToken();

    /**
     * Function setStaffToken
     *
     * @param string $staff_token staff_token
     *
     * @return null
     */
    public function setStaffToken($staff_token);

    /**
     * Function getDeviceToken
     *
     * @return string
     */
    public function getDeviceToken();

    /**
     * Function setDeviceToken
     *
     * @param string $device_token device_token
     *
     * @return null
     */
    public function setDeviceToken($device_token);

    /**
     * Function getWarehouseId
     *
     * @return string
     */
    public function getWarehouseId();

    /**
     * Function setWarehouseId
     *
     * @param string $warehouse_id warehouse_id
     *
     * @return null
     */
    public function setWarehouseId($warehouse_id);
}
