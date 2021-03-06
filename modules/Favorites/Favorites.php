<?PHP
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 * SuiteCRM is an extension to SugarCRM Community Edition developed by Salesagility Ltd.
 * Copyright (C) 2011 - 2014 Salesagility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for  technical reasons, the Appropriate Legal Notices must
 * display the words  "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 ********************************************************************************/

/**
 * THIS CLASS IS FOR DEVELOPERS TO MAKE CUSTOMIZATIONS IN
 */
require_once('modules/Favorites/Favorites_sugar.php');

class Favorites extends Favorites_sugar
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @deprecated deprecated since version 7.6, PHP4 Style Constructors are deprecated and will be remove in 7.8, please update your code, use __construct instead
     */
    public function Favorites(){
        $deprecatedMessage = 'PHP4 Style Constructors are deprecated and will be remove in 7.8, please update your code';
        if(isset($GLOBALS['log'])) {
            $GLOBALS['log']->deprecated($deprecatedMessage);
        }
        else {
            trigger_error($deprecatedMessage, E_USER_DEPRECATED);
        }
        self::__construct();
    }


    public function deleteFavorite($id){
        if($id){
            $favorite_record = BeanFactory::getBean('Favorites',$id);
            $favorite_record->deleted = 1;
            $favorite_record->save();
            return true;
        }else{
            return false;
        }

    }

    public function getFavoriteID($module, $record_id)
    {
        global $db, $current_user;
        $query = "SELECT id FROM favorites WHERE parent_id= '" . $record_id . "' AND parent_type = '" . $module . "' AND assigned_user_id = '" . $current_user->id . "' AND deleted = 0 ORDER BY date_entered desc";
        return $db->getOne($query);
    }

    public function getCurrentUserSidebarFavorites($id = null)
    {
        global $db, $current_user;

        $return_array = array();

        if($id){
            $query = "SELECT parent_id, parent_type FROM favorites WHERE assigned_user_id = '" . $current_user->id . "' AND parent_id = '" . $id . "' AND deleted = 0 ORDER BY date_entered desc";
        }else{
            $query = "SELECT parent_id, parent_type FROM favorites WHERE assigned_user_id = '" . $current_user->id . "' AND deleted = 0 ORDER BY date_entered desc";
        }

        $result = $db->query($query);

        $i = 0;
        while ($row = $db->fetchByAssoc($result)) {

            $bean = BeanFactory::getBean($row['parent_type'],$row['parent_id']);
            $return_array[$i]['item_summary'] = $bean->name;
            $return_array[$i]['item_summary_short'] = to_html(getTrackerSubstring($bean->name));
            $return_array[$i]['id'] = $row['parent_id'];
            $return_array[$i]['module_name'] = $row['parent_type'];
            $return_array[$i]['image'] = SugarThemeRegistry::current() ->getImage($row['parent_type'],'border="0" align="absmiddle"',null,null,'.gif',$bean->name);

            $i++;
        }

        return $return_array;
    }

}

?>
