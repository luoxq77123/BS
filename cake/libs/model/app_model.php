<?php

/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Application model for Cake.
 *
 * This is a placeholder class.
 * Create the same file in app/app_model.php
 * Add your application-wide methods to the class, your models will inherit them.
 *
 * @package       cake
 * @subpackage    cake.cake.libs.model
 */
class AppModel extends Model {
    /**
     * 自定义操作
     */

    /**
     * 
     * 设置blob字段值
     */
    public function newSetBlob() {
        $params = func_get_args();
        $db = & ConnectionManager::getDataSource($this->useDbConfig);
        return call_user_func_array(array(&$db, 'saveBlob'), $params);
    }

    /**
     * 
     * 设置普通字段值
     */
    public function newSetData() {
        $params = func_get_args();
        $db = & ConnectionManager::getDataSource($this->useDbConfig);
        return call_user_func_array(array(&$db, 'saveData'), $params);
    }

    /**
     * 
     * 获取数据
     */
    public function newFind() {
        $params = func_get_args();
        $db = & ConnectionManager::getDataSource($this->useDbConfig);
        return call_user_func_array(array(&$db, 'getData'), $params);
    }

    /**
     * value分析
     * @access protected
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value) {
        if (is_string($value)) {
            $value = '\'' . $this->escapeString($value) . '\'';
        } elseif (isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp') {
            $value = $this->escapeString($value[1]);
        } elseif (is_array($value)) {
            $value = array_map(array($this, 'parseValue'), $value);
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }
    //sql安全过滤
    public function escapeString($str) {
        return addslashes($str);
    }

}
