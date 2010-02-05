<?php

require_once 'Zend/Service/Twitter.php';
require_once 'Zend/Service/Twitter/Exception.php';

class Recordshelf_Service_Twitter_List extends Zend_Service_Twitter
{
    const LIST_MEMBER_LIMIT = 500;
    const MAX_LIST_NAME_LENGTH = 25;
    const MAX_LIST_DESCRIPTION_LENGTH = 100;
    
    /**
     * Initializes the service and adds the list to the method types 
     * of the parent service class.
     *
     * @param string $username The Twitter account name.
     * @param string $password The Twitter account password.
     * @see Zend_Service_Twitter::_methodTypes
     */
    public function __construct($username = null, $password = null)
    {
        parent::__construct($username, $password);
        $this->_methodTypes[] = 'list';
    }
    /**
     * Creates a list associated to the current user.
     *
     * @param string $listname The listname to create.
     * @param array $options The options to set whilst creating the list. 
     * Allows to set the list creation mode (public|private) 
     * and the list description.
     * @return Zend_Rest_Client_Result
     * @throws Zend_Service_Twitter_Exception
     */
    public function create($listname, array $options = array())
    {
        $this->_init();
        
        if ($this->_existsListAlready($listname)) {
            $exceptionMessage = 'List with name %s exists already';
            $exceptionMessage = sprintf($exceptionMessage, $listname);
            throw new Zend_Service_Twitter_Exception($exceptionMessage);
        }
        
        $_options = array('name' => $this->_validListname($listname));
        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'mode':
                    $_options['mode'] = $this->_validMode($value);
                    break;
                case 'description':
                    $_options['description'] = $this->_validDescription($value);
                    break;
                default:
                    break;
            }
        }
        $path = '/1/%s/lists.xml';
        $path = sprintf($path, $this->getUsername());
        
        $response = $this->_post($path, $_options);
        return new Zend_Rest_Client_Result($response->getBody());
    }
    /**
     * Deletes an owned list of the current user.
     *
     * @param string $listname The listname to delete.
     * @return Zend_Rest_Client_Result
     * @throws Zend_Service_Twitter_Exception
     */
    public function delete($listname)
    {
        $this->_init();
        
        if (!$this->_isListAssociatedWithUser($listname)) {
            $exceptionMessage = 'List %s is not associate with user %s ';
            $exceptionMessage = sprintf($exceptionMessage, 
                $listname, 
                $this->getUsername()
            );
            throw new Zend_Service_Twitter_Exception($exceptionMessage);
        }
        $_options['_method'] = 'DELETE';
        $path = '/1/%s/lists/%s.xml';
        $path = sprintf($path, 
            $this->getUsername(), 
            $this->_validListname($listname)
        );
        $response = $this->_post($path, $_options);
        return new Zend_Rest_Client_Result($response->getBody());
    }
    /**
     * Adds a member to a list of the current user.
     *
     * @param integer $userId The numeric user id of the member to add.
     * @param string $listname The listname to add the member to.
     * @return Zend_Rest_Client_Result
     * @throws Zend_Service_Twitter_Exception
     */
    public function addMember($userId, $listname)
    {
        $this->_init();
        
        if (!$this->_isListAssociatedWithUser($listname)) {
            $exceptionMessage = 'List %s is not associate with user %s ';
            $exceptionMessage = sprintf($exceptionMessage, 
                $listname, 
                $this->getUsername()
            );
            throw new Zend_Service_Twitter_Exception($exceptionMessage);
        }
        
        $_options['id'] = $this->_validInteger($userId); 
        $path = '/1/%s/%s/members.xml';
        $path = sprintf($path, 
            $this->getUsername(), 
            $this->_validListname($listname)
        );
        
        if ($this->_isListMemberLimitReached($listname)) {
            $exceptionMessage = 'List can contain no more than %d members';
            $exceptionMessage = sprintf($exceptionMessage, 
                self::LIST_MEMBER_LIMIT
            );
            throw new Zend_Service_Twitter_Exception($exceptionMessage);
        }
        
        $response = $this->_post($path, $_options);
        return new Zend_Rest_Client_Result($response->getBody());
    }
    /**
     * Removes a member from a list of the current user.
     *
     * @param integer $userId The numeric user id of the member to remove.
     * @param string $listname The listname to remove the member from.
     * @return Zend_Rest_Client_Result
     * @throws Zend_Service_Twitter_Exception
     */
    public function removeMember($userId, $listname)
    {
        $this->_init();
        
        if (!$this->_isListAssociatedWithUser($listname)) {
            $exceptionMessage = 'List %s is not associate with user %s ';
            $exceptionMessage = sprintf($exceptionMessage, 
                $listname, 
                $this->getUsername()
            );
            throw new Zend_Service_Twitter_Exception($exceptionMessage);
        }
        
        $_options['_method'] = 'DELETE';
        $_options['id'] = $this->_validInteger($userId);        
        $path = '/1/%s/%s/members.xml';
        $path = sprintf($path, 
            $this->getUsername(), 
            $this->_validListname($listname)
        );
        $response = $this->_post($path, $_options);
        return new Zend_Rest_Client_Result($response->getBody());
    }
    /**
     * Fetches the list members of the current user.
     *
     * @param string $listname The listname to fetch members from.
     * @return Zend_Rest_Client_Result
     * @throws Zend_Service_Twitter_Exception
     */
    public function getMembers($listname) {
        $this->_init();
        $path = '/1/%s/%s/members.xml';
        $path = sprintf($path, 
            $this->getUsername(), 
            $this->_validListname($listname)
        );
        $response = $this->_get($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }
    /**
     * Fetches the list of the current user or any given user.
     *
     * @param string $username The username of the list owner.
     * @return Zend_Rest_Client_Result
     */
    public function getLists($username = null)
    {
        $this->_init();
        $path = '/1/%s/lists.xml';
        if (is_null($username)) {
            $path = sprintf($path, $this->getUsername());
        } else {
            $path = sprintf($path, $username);
        }
        $response = $this->_get($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }
    /**
     * Checks if the list exists already to avoid number 
     * indexed recreations.
     *
     * @param string $listname The list name.
     * @return boolean
     * @throws Zend_Service_Twitter_Exception
     */
    private function _existsListAlready($listname)
    {
        $_listname = $this->_validListname($listname);
        $lists = $this->getLists();
        $_lists = $lists->lists;
        foreach ($_lists->list as $list) {
            if ($list->name == $_listname) {
                return true;
            }
        }
        return false;
    }
    /**
     * Checks if the list is associated with the current user.
     *
     * @param string $listname The list name.
     * @return boolean
     */
    private function _isListAssociatedWithUser($listname) 
    {
        return $this->_existsListAlready($listname);
    }
    /**
     * Checks if the list member limit is reached.
     *
     * @param string $listname The list name.
     * @return boolean
     */
    private function _isListMemberLimitReached($listname)
    {
        $members = $this->getMembers($listname);
        return self::LIST_MEMBER_LIMIT < count($members->users->user);
    }
    /**
     * Returns the list creation mode or returns the private mode when invalid.
     * Valid values are private or public.
     *
     * @param string $creationMode The list creation mode.
     * @return string
     */
    private function _validMode($creationMode)
    {
        if (in_array($creationMode, array('private', 'public'))) {
            return $creationMode;
        }
        return 'private';
    }
    /**
     * Returns the list name or throws an Exception when invalid.
     *
     * @param string $listname The list name.
     * @return string
     * @throws Zend_Service_Twitter_Exception
     */
    private function _validListname($listname)
    {
        $len = iconv_strlen(trim($listname), 'UTF-8');
        if (0 == $len) {
            $exceptionMessage = 'List name must contain at least one character';
            throw new Zend_Service_Twitter_Exception($exceptionMessage);
        } elseif (self::MAX_LIST_NAME_LENGTH < $len) {
            $exceptionMessage = 'List name must contain no more than %d characters';
            $exceptionMessage = sprintf($exceptionMessage, 
                self::MAX_LIST_NAME_LENGTH
            );
            throw new Zend_Service_Twitter_Exception($exceptionMessage);
        }
        return trim($listname);
    }
    /**
     * Returns the list description or throws an Exception when invalid.
     *
     * @param string $description The list description.
     * @return string
     * @throws Zend_Service_Twitter_Exception
     */
    private function _validDescription($description)
    {
        $len = iconv_strlen(trim($description), 'UTF-8');
        if (0 == $len) {
            return '';
        } elseif (self::MAX_LIST_DESCRIPTION_LENGTH < $len) {
            $exceptionMessage = 'List description must contain no more than %d characters';
            $exceptionMessage = sprintf($exceptionMessage, 
                self::MAX_LIST_DESCRIPTION_LENGTH
            );
            throw new Zend_Service_Twitter_Exception($exceptionMessage);
        }
        return trim(strip_tags($description));
    }
}