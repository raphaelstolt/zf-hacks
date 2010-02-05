<?php
require_once 'Zend/Validate/Abstract.php';
require_once 'Zend/Service/Twitter.php';

class Recordshelf_Validate_TwitterScreenName extends Zend_Validate_Abstract
{
    const MSG_INVALID_SCREEN_NAME = 'msgInvalid';
    const MSG_NON_EXISTENT_SCREEN_NAME = 'msgNonExistent';

    protected $_messageTemplates = array(
        self::MSG_INVALID_SCREEN_NAME => "%value% is an invalid screen name",
        self::MSG_NON_EXISTENT_SCREEN_NAME => "%value% is an non existent screen name"
    );
    
    public function isValid($name)
    {
        $this->_setValue($name);
        
        if (!$this->_validateScreenName($name)) {
            $this->_error(self::MSG_INVALID_SCREEN_NAME);
            return false;
        }
        if (!$this->_validateScreenNameExistence($name)) {
            $this->_error(self::MSG_NON_EXISTENT_SCREEN_NAME);
            return false;
        }
        return true;
    }
    /**
     * @param string Twitter screen name
     * @return boolean
     * @uses Zend_Service_Twitter
     */
    private function _validateScreenNameExistence($name)
    {
        $twitter = new Zend_Service_Twitter();
        $response = $twitter->user->show($name);
        $userId = (string) $response->id;
        return !empty($userId);
    }
    /**
     * Validates a Twitter screen name against a pattern.
     *
     * @param string Twitter screen name
     * @return boolean
     */
    private function _validateScreenName($name)
    {
        if (!preg_match('/^[a-zA-Z0-9_]{0,15}$/', $name)) {
            return false;
        }
        return true;
    }
}