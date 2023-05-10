<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 4/25/2023
 * @time: 10:23 PM
 */

namespace app\components;

class DbManager extends \yii\rbac\DbManager
{
    /**
     * Initializes the application component.
     * This method overrides the parent implementation by prefixing the schema name on the table names.
     * Yii2 rbac uses the schema names from the db search path in that order. We don't want to mess with these values,
     * and so we provide our schema name.
     *
     * https://craftcms.stackexchange.com/questions/38405/why-do-postgres-schemas-different-from-public-sometimes-not-work-with-craft-d
     * https://dba.stackexchange.com/questions/56023/what-is-the-search-path-for-a-given-database-and-user
     * https://github.com/yiisoft/yii2/issues/12763
     */
    public function init()
    {
        parent::init();

        /**
         * @var string the name of the table storing authorization items. Defaults to "auth_item".
         */
        $this->itemTable = DB_SCHEMA . '.auth_item';
        /**
         * @var string the name of the table storing authorization item hierarchy. Defaults to "auth_item_child".
         */
        $this->itemChildTable = DB_SCHEMA . '.auth_item_child';
        /**
         * @var string the name of the table storing authorization item assignments. Defaults to "auth_assignment".
         */
        $this->assignmentTable = DB_SCHEMA . '.auth_assignment';
        /**
         * @var string the name of the table storing rules. Defaults to "auth_rule".
         */
        $this->ruleTable = DB_SCHEMA . '.auth_rule';
    }
}