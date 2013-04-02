<?php
/**
 * Agent8_Configuration
 * 
 * @package easilydo
 * @author Jay Zhang <jay@easilydo.com>
 * @file Agent8/Configuration.php
 * @copyright Copyright 2012 Easilydo Inc. 
 * @version 1.0
 * @since 2012-10-23
 * 
 **/

/* $Id$ */

class Agent8_Configuration extends Agent8_Abstract {

    /* {{{ protected function _getUrl($suffix)  */
    protected function _getUrl($suffix) {
        return parent::_getUrl("/configuration/".ltrim($suffix, "/"));
    }
    /* }}} */

    /* {{{ public function loadDoConfigurations()  */
    public function loadDoConfigurations() {

        $strBody = $this->_get($this->_getUrl(__FUNCTION__), array());

        return $strBody;
        
    }
    /* }}} */

    /* {{{ public function getConnections() */
    public function getConnections() {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array());

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function getConnection($id)  */
    public function getConnection($id) {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("connectionId" => $id));

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function createConnection(array $data)  */
    public function createConnection(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        $arrTemp = json_decode($strRes, TRUE);

        if ($arrTemp && $arrTemp["status"] == 0) {
            return $arrTemp["id"];
        } else {
            Easy_Log::warning("Operation failed", $arrTemp);
            throw new Agent8_Exception("Operation failed");
        }
        
    }
    /* }}} */

    /* {{{ public function createConnectionEx($id, $name, $displayOrder)  */
    public function createConnectionEx($id, $name, $displayOrder) {

        return $this->createConnection(array(
            "id"            => $id,
            "name"          => $name,
            "displayOrder"  => $displayOrder,
        ));

    }
    /* }}} */

    /* {{{ public function importConnection(array $data)  */
    public function importConnection(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        $arrTemp = json_decode($strRes, TRUE);

        if ($arrTemp && $arrTemp["status"] == 0) {
            return $arrTemp["id"];
        } else {
            Easy_Log::warning("Operation failed", $arrTemp);
            throw new Agent8_Exception("Operation failed");
        }
        
    }
    /* }}} */

    /* {{{ public function importConnectionEx($id, $name, $displayOrder)  */
    public function importConnectionEx($id, $name, $displayOrder) {

        return $this->importConnection(array(
            "id"            => $id,
            "name"          => $name,
            "displayOrder"  => $displayOrder,
        ));

    }
    /* }}} */

    /* {{{ public function updateConnection(array $data)  */
    public function updateConnection(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        return json_encode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function updateConnectionEx($id, $name, $displayOrder)  */
    public function updateConnectionEx($id, $name, $displayOrder) {

        return $this->updateConnection(array(
            "id"            => $id,
            "name"          => $name,
            "displayOrder"  => $displayOrder,
        ));
        
    }
    /* }}} */

    /* {{{ public function deleteConnection($id)  */
    public function deleteConnection($id) {
        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("connectionId" => $id));
        return json_decode($strRes, TRUE);
    }
    /* }}} */

    /* {{{ public function getSourceGroups()  */
    public function getSourceGroups() {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array());

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function getSourceGroup($id)  */
    public function getSourceGroup($id) {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("sourceGroupId" => $id));

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function createSourceGroup(array $data)  */
    public function createSourceGroup(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        $arrTemp = json_decode($strRes, TRUE);

        if ($arrTemp && $arrTemp["status"] == 0) {
            return $arrTemp["id"];
        } else {
            Easy_Log::warning("Operation failed", $arrTemp);
            throw new Agent8_Exception("Operation failed");
        }

    }
    /* }}} */

    /* {{{ public function createSourceGroupEx($id, $name, array $sourceList, $groupType = 0, $required = TRUE)  */
    public function createSourceGroupEx($id, $name, array $sourceList, $groupType = 0, $required = TRUE) {

        $this->createSourceGroup(array(
            "id"            => $id,
            "name"          => $name,
            "groupType"     => $groupType,
            "sourceList"    => array_unique(array_map("intval", $sourceList)),
            "required"      => $required ? 1 : 0,
        ));

    }
    /* }}} */

    /* {{{ public function importSourceGroup(array $data)  */
    public function importSourceGroup(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        $arrTemp = json_decode($strRes, TRUE);

        if ($arrTemp && $arrTemp["status"] == 0) {
            return $arrTemp["id"];
        } else {
            Easy_Log::warning("Operation failed", $arrTemp);
            throw new Agent8_Exception("Operation failed");
        }

    }
    /* }}} */

    /* {{{ public function importSourceGroupEx($id, $name, array $sourceList, $groupType = 0, $required = TRUE)  */
    public function importSourceGroupEx($id, $name, array $sourceList, $groupType = 0, $required = TRUE) {

        $this->importSourceGroup(array(
            "id"            => $id,
            "name"          => $name,
            "groupType"     => $groupType,
            "sourceList"    => array_unique(array_map("intval", $sourceList)),
            "required"      => $required ? 1 : 0,
        ));

    }
    /* }}} */

    /* {{{ public function updateSourceGroup(array $data)  */
    public function updateSourceGroup(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        return json_encode($strRes, TRUE);

    }
    /* }}} */

    /* {{{ public function updateSourceGroupEx($id, $name, array $sourceList, $groupType = 0, $required = TRUE)  */
    public function updateSourceGroupEx($id, $name, array $sourceList, $groupType = 0, $required = TRUE) {

        $this->updateSourceGroupEx(array(
            "id"            => $id,
            "name"          => $name,
            "groupType"     => $groupType,
            "sourceList"    => array_unique(array_map("intval", $sourceList)),
            "required"      => $required ? 1 : 0,
        ));

    }
    /* }}} */

    /* {{{ public function deleteSourceGroup($id)  */
    public function deleteSourceGroup($id) {
        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("sourceGroupId" => $id));
        return json_decode($strRes, TRUE);
    }
    /* }}} */

    /* {{{ public function getTriggers()  */
    public function getTriggers() {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array());

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function getTrigger($id)  */
    public function getTrigger($id) {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("triggerId" => $id));

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function createTrigger(array $data)  */
    public function createTrigger(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        $arrTemp = json_decode($strRes, TRUE);

        if ($arrTemp && $arrTemp["status"] == 0) {
            return $arrTemp["id"];
        } else {
            Easy_Log::warning("Operation failed", $arrTemp);
            throw new Agent8_Exception("Operation failed");
        }

    }
    /* }}} */

    /* {{{ public function createTriggerEx($id, $name, array $sourceGroupIds, array $variables = array())  */
    public function createTriggerEx($id, $name, array $sourceGroupIds, array $variables = array()) {

        $this->createTrigger(array(
            "id"            => $id,
            "name"          => $name,
            "sourceGroupIds"=> array_unique(array_map("intval", $sourceGroupIds)),
            "variables"     => array_unique($variables),
        ));

    }
    /* }}} */

    /* {{{ public function importTrigger(array $data)  */
    public function importTrigger(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        $arrTemp = json_decode($strRes, TRUE);

        if ($arrTemp && $arrTemp["status"] == 0) {
            return $arrTemp["id"];
        } else {
            Easy_Log::warning("Operation failed", $arrTemp);
            throw new Agent8_Exception("Operation failed");
        }

    }
    /* }}} */

    /* {{{ public function importTriggerEx($id, $name, array $sourceGroupIds, array $variables = array())  */
    public function importTriggerEx($id, $name, array $sourceGroupIds, array $variables = array()) {

        $this->importTrigger(array(
            "id"            => $id,
            "name"          => $name,
            "sourceGroupIds"=> array_unique(array_map("intval", $sourceGroupIds)),
            "variables"     => array_unique($variables),
        ));

    }
    /* }}} */

    /* {{{ public function updateTrigger(array $data)  */
    public function updateTrigger(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        return json_encode($strRes, TRUE);

    }
    /* }}} */

    /* {{{ public function updateTriggerEx($id, $name, array $sourceGroupIds, array $variables = array())  */
    public function updateTriggerEx($id, $name, array $sourceGroupIds, array $variables = array()) {

        $this->updateTrigger(array(
            "id"            => $id,
            "name"          => $name,
            "sourceGroupIds"=> array_unique(array_map("intval", $sourceGroupIds)),
            "variables"     => array_unique($variables),
        ));

    }
    /* }}} */

    /* {{{ public function deleteTrigger($id)  */
    public function deleteTrigger($id) {
        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("triggerId" => $id));
        return json_decode($strRes, TRUE);
    }
    /* }}} */

    /* {{{ public function getActions()  */
    public function getActions() {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array());

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function getAction($id)  */
    public function getAction($id) {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("actionId" => $id));

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function createAction(array $data)  */
    public function createAction(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        $arrTemp = json_decode($strRes, TRUE);

        if ($arrTemp && $arrTemp["status"] == 0) {
            return $arrTemp["id"];
        } else {
            Easy_Log::warning("Operation failed", $arrTemp);
            throw new Agent8_Exception("Operation failed");
        }

    }
    /* }}} */

    /* {{{ public function createActionEx($id, $displayName, $methodName, $sourceGroupId, $executionType, array $parameters)  */
    public function createActionEx($id, $displayName, $methodName, $sourceGroupId, $executionType, array $parameters) {

        $this->createAction(array(
            "id"            => $id,
            "displayName"   => $displayName,
            "methodName"    => $methodName,
            "sourceGroupId" => $sourceGroupId,
            "executionType" => $executionType,
            "parameters"    => $parameters,
        ));

    }
    /* }}} */

    /* {{{ public function importAction(array $data)  */
    public function importAction(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        $arrTemp = json_decode($strRes, TRUE);

        if ($arrTemp && $arrTemp["status"] == 0) {
            return $arrTemp["id"];
        } else {
            Easy_Log::warning("Operation failed", $arrTemp);
            throw new Agent8_Exception("Operation failed");
        }

    }
    /* }}} */

    /* {{{ public function importActionEx($id, $displayName, $methodName, $sourceGroupId, $executionType, array $parameters)  */
    public function importActionEx($id, $displayName, $methodName, $sourceGroupId, $executionType, array $parameters) {

        $this->importAction(array(
            "id"            => $id,
            "displayName"   => $displayName,
            "methodName"    => $methodName,
            "sourceGroupId" => $sourceGroupId,
            "executionType" => $executionType,
            "parameters"    => $parameters,
        ));

    }
    /* }}} */

    /* {{{ public function updateAction(array $data)  */
    public function updateAction(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        return json_encode($strRes, TRUE);

    }
    /* }}} */

    /* {{{ public function updateActionEx($id, $displayName, $methodName, $sourceGroupId, $executionType, array $parameters)  */
    public function updateActionEx($id, $displayName, $methodName, $sourceGroupId, $executionType, array $parameters) {

        $this->updateAction(array(
            "id"            => $id,
            "displayName"   => $displayName,
            "methodName"    => $methodName,
            "sourceGroupId" => $sourceGroupId,
            "executionType" => $executionType,
            "parameters"    => $parameters,
        ));

    }
    /* }}} */

    /* {{{ public function deleteAction($id)  */
    public function deleteAction($id) {
        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("actionId" => $id));
        return json_decode($strRes, TRUE);
    }
    /* }}} */

    /* {{{ public function getDoDefinitions($userName=NULL)  */
    public function getDoDefinitions($userName=NULL) {

        $arrParam = array(
            "latest" => "true",
        );

        if ($userName) {
            $arrParam["userName"] = $userName;
        }

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), $arrParam);

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function getDoDefinition($id)  */
    public function getDoDefinition($id) {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("doId" => $id));

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function createDoDefinition(array $data)  */
    public function createDoDefinition(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        $arrTemp = json_decode($strRes, TRUE);

        if ($arrTemp && $arrTemp["status"] == 0) {
            return $arrTemp["id"];
        } else {
            Easy_Log::warning("Operation failed", $arrTemp);
            throw new Agent8_Exception("Operation failed");
        }

    }
    /* }}} */

    /* {{{ public function createDoDefinitionEx($doId, $triggerId, array $actionIds, ... */
    public function createDoDefinitionEx($doId, $triggerId, array $actionIds, $lastUpdateTime,
            $appVersion, array $titles, $subTitle, $deviceSensitive, $pushNtfMessage, $pushNtfEventName,
            $owner, $creator, $groupId, $displayName, $maxVisibleCount, $onDisplay, $displayOrder,
            $connections, $description, $weight, $defaultEnable, $needsPreferences, $screenTitle,
            $reportName, $sourceList, $fbShareText, $twitterShareText, $timeSaved, $state, $markup //,
//            $completeStateSubTitle, $scheduleStateSubTitle, $variableMap, $parameters
            ) {


        return $this->createDoDefinition(array(
            "doId"              => $doId,
            "triggerId"         => $triggerId,
            "actionIds"         => $actionIds,
            "lastUpdateTime"    => $lastUpdateTime,
            "appVersion"        => $appVersion,
            "titles"            => $titles,
            "subTitle"          => $subTitle,
            "deviceSensitive"   => $deviceSensitive,
            "pushNtfMessage"    => $pushNtfMessage,
            "pushNtfEventName"  => $pushNtfEventName,
            "owner"             => $owner,
            "creator"           => $creator,
            "groupId"           => $groupId,
            "displayName"       => $displayName,
            "maxVisibleCount"   => $maxVisibleCount,
            "onDisplay"         => $onDisplay,
            "displayOrder"      => $displayOrder,
            "connections"       => $connections,
            "description"       => $description,
            "weight"            => $weight,
            "defaultEnable"     => $defaultEnable,
            "needsPreferences"  => $needsPreferences,
            "screenTitle"       => $screenTitle,
            "reportName"        => $reportName,
            "sourceList"        => $sourceList,
            "fbShareText"       => $fbShareText,
            "twitterShareText"  => $twitterShareText,
            "timeSaved"         => $timeSaved,
            "state"             => $state,
            "markup"            => $markup,
            // "completeStateSubTitle" => $completeStateSubTitle,
            // "scheduleStateSubTitle" => $scheduleStateSubTitle,
        ));

    }
    /* }}} */

    /* {{{ public function importDoDefinition(array $data)  */
    public function importDoDefinition(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        $arrTemp = json_decode($strRes, TRUE);

        if ($arrTemp && $arrTemp["status"] == 0) {
            return $arrTemp["id"];
        } else {
            Easy_Log::warning("Operation failed", $arrTemp);
            throw new Agent8_Exception("Operation failed");
        }

    }
    /* }}} */

    /* {{{ public function importDoDefinitionEx($doId, $triggerId, array $actionIds, ... */
    public function importDoDefinitionEx($doId, $triggerId, array $actionIds, $lastUpdateTime,
            $appVersion, array $titles, $subTitle, $deviceSensitive, $pushNtfMessage, $pushNtfEventName,
            $owner, $creator, $groupId, $displayName, $maxVisibleCount, $onDisplay, $displayOrder,
            $connections, $description, $weight, $defaultEnable, $needsPreferences, $screenTitle,
            $reportName, $sourceList, $fbShareText, $twitterShareText, $timeSaved, $state, $markup,
            $completeStateSubTitle, $scheduleStateSubTitle, $variableMap, $permissions
            ) {

        return $this->importDoDefinition(array(
            "doId"              => $doId,
            "triggerId"         => $triggerId,
            "actionIds"         => $actionIds,
            "lastUpdateTime"    => $lastUpdateTime,
            "appVersion"        => $appVersion,
            "titles"            => $titles,
            "subTitle"          => $subTitle,
            "deviceSensitive"   => $deviceSensitive,
            "pushNtfMessage"    => $pushNtfMessage,
            "pushNtfEventName"  => $pushNtfEventName,
            "owner"             => $owner,
            "creator"           => $creator,
            "groupId"           => $groupId,
            "displayName"       => $displayName,
            "maxVisibleCount"   => $maxVisibleCount,
            "onDisplay"         => $onDisplay,
            "displayOrder"      => $displayOrder,
            "connections"       => $connections,
            "description"       => $description,
            "weight"            => $weight,
            "defaultEnable"     => $defaultEnable,
            "needsPreferences"  => $needsPreferences,
            "screenTitle"       => $screenTitle,
            "reportName"        => $reportName,
            "sourceList"        => $sourceList,
            "fbShareText"       => $fbShareText,
            "twitterShareText"  => $twitterShareText,
            "timeSaved"         => $timeSaved,
            "state"             => $state,
            "markup"            => $markup,
            "completeStateSubTitle" => $completeStateSubTitle,
            "scheduleStateSubTitle" => $scheduleStateSubTitle,
            "variableMap"       => $variableMap,
            "permissions"       => $permissions,
        ));

    }
    /* }}} */

    /* {{{ public function updateDoDefinition(array $data)  */
    public function updateDoDefinition(array $data) {

        $strBody = json_encode($data);
        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);
        return json_encode($strRes, TRUE);

    }
    /* }}} */

    /* {{{ public function updateDoDefinitionEx($doId, $triggerId, array $actionIds, ... */
    public function updateDoDefinitionEx($doId, $triggerId, array $actionIds, $lastUpdateTime,
            $appVersion, array $titles, $subTitle, $deviceSensitive, $pushNtfMessage, $pushNtfEventName,
            $owner, $creator, $groupId, $displayName, $maxVisibleCount, $onDisplay, $displayOrder,
            $connections, $description, $weight, $defaultEnable, $needsPreferences, $screenTitle,
            $reportName, $sourceList, $fbShareText, $twitterShareText, $timeSaved, $state, $markup,
            $completeStateSubTitle, $scheduleStateSubTitle, $variableMap, $permissions
            ) {

        return $this->updateDoDefinition(array(
            "doId"              => $doId,
            "triggerId"         => $triggerId,
            "actionIds"         => $actionIds,
            "lastUpdateTime"    => $lastUpdateTime,
            "appVersion"        => $appVersion,
            "titles"            => $titles,
            "subTitle"          => $subTitle,
            "deviceSensitive"   => $deviceSensitive,
            "pushNtfMessage"    => $pushNtfMessage,
            "pushNtfEventName"  => $pushNtfEventName,
            "owner"             => $owner,
            "creator"           => $creator,
            "groupId"           => $groupId,
            "displayName"       => $displayName,
            "maxVisibleCount"   => $maxVisibleCount,
            "onDisplay"         => $onDisplay,
            "displayOrder"      => $displayOrder,
            "connections"       => $connections,
            "description"       => $description,
            "weight"            => $weight,
            "defaultEnable"     => $defaultEnable,
            "needsPreferences"  => $needsPreferences,
            "screenTitle"       => $screenTitle,
            "reportName"        => $reportName,
            "sourceList"        => $sourceList,
            "fbShareText"       => $fbShareText,
            "twitterShareText"  => $twitterShareText,
            "timeSaved"         => $timeSaved,
            "state"             => $state,
            "markup"            => $markup,
            "completeStateSubTitle" => $completeStateSubTitle,
            "scheduleStateSubTitle" => $scheduleStateSubTitle,
            "variableMap"       => $variableMap,
            "permissions"       => $permissions,
        ));

    }
    /* }}} */

    /* {{{ public function deleteDoDefinition($id)  */
    public function deleteDoDefinition($id) {
        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("doId" => $id));
        return json_decode($strRes, TRUE);
    }
    /* }}} */

    /* {{{ public function getConfig()  */
    public function getConfig() {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array());

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function getEDConfiguration()  */
    public function getEDConfiguration() {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array());

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function printConfig()  */
    public function printConfig() {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array());

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function getDoIdsForTrigger($triggerId)  */
    public function getDoIdsForTrigger($triggerId) {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("triggerId"=>$triggerId));

        return json_decode($strRes, TRUE);
        
    }
    /* }}} */

    /* {{{ public function getAllDoLikes()  */
    public function getAllDoLikes() {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__));
        $arrTemp = json_decode($strRes, TRUE);
        return $arrTemp;

    }
    /* }}} */

    /* {{{ public function getDoLikesByUser($userName)  */
    public function getDoLikesByUser($userName) {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("userName" => $userName));
        $arrTemp = json_decode($strRes, TRUE);
        $arrRet = array();
        foreach ($arrTemp as $item) {
            $arrRet [$item["doId"]] = 1;
        }
        return $arrRet;

    }
    /* }}} */

    /* {{{ public function getDoLikesByDoId($doId)  */
    public function getDoLikesByDoId($doId) {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("doId" => intval($doId)));
        $arrTemp = json_decode($strRes, TRUE);
        return $arrTemp;

    }
    /* }}} */

    /* {{{ public function getDoDislikesByUser($userName)  */
    public function getDoDislikesByUser($userName) {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("userName" => $userName));
        $arrTemp = json_decode($strRes, TRUE);
        $arrRet = array();
        foreach ($arrTemp as $item) {
            $arrRet [$item["doId"]] = -1;
        }
        return $arrRet;

    }
    /* }}} */

    /* {{{ public function getDoDislikesByDoId($doId)  */
    public function getDoDislikesByDoId($doId) {

        $strRes = $this->_get($this->_getUrl(__FUNCTION__), array("doId" => intval($doId)));
        $arrTemp = json_decode($strRes, TRUE);
        return $arrTemp;

    }
    /* }}} */

    /* {{{ public function likesDo($userName, $doId, $likes=TRUE)  */
    public function likesDo($userName, $doId, $likes=TRUE) {

        $strBody = json_encode(array(
            "userName"  => $userName,
            "doId"      => intval($doId),
            "likes"     => $likes,
        ));

        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);

        return TRUE;
    }
    /* }}} */

    /* {{{ public function dislikesDo($userName, $doId, $likes=FALSE)  */
    public function dislikesDo($userName, $doId, $likes=FALSE) {

        $strBody = json_encode(array(
            "userName"  => $userName,
            "doId"      => intval($doId),
            "likes"     => $likes,
        ));

        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);

        return TRUE;
    }
    /* }}} */

    /* {{{ public function updateLikesDo($userName, $doId, $likes)  */
    public function updateLikesDo($userName, $doId, $likes) {

        $strBody = json_encode(array(
            "userName"  => $userName,
            "doId"      => intval($doId),
            "likes"     => $likes,
        ));

        $strRes = $this->_post($this->_getUrl(__FUNCTION__), $strBody);

        return TRUE;
    }
    /* }}} */

}
