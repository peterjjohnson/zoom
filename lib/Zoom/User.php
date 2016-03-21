<?
namespace Zoom;

/**
 * Class User
 *
 * For detailed documentation see https://support.zoom.us/hc/en-us/articles/201363033-REST-User-API
 *
 * @package Zoom
 */
class User extends Base
{
    const TYPE_BASIC = 1;
    const TYPE_PRO   = 2;
    const TYPE_CORP  = 3;

    var $id;
    var $email;
    var $type = self::TYPE_BASIC;
    var $first_name;
    var $last_name;
    var $disable_chat;
    var $enable_e2e_encryption;
    var $enable_silent_mode;
    var $disable_group_hd;
    var $disable_recording;
    var $enable_cmr;
    var $enable_auto_recording;
    var $enable_cloud_auto_recording;
    var $enable_webinar;
    var $webinar_capacity;
    var $enable_large;
    var $large_capacity;
    var $disable_feedback;
    var $disable_jbh_reminder;
    var $dept;

    /**
     * Create a custom user on Zoom
     *
     * This is used to create a user that can only access Zoom through our site. The user will be able to host meetings
     * through our site without the need for a Zoom login.
     *
     * @param array $user_data - An array of user data (must include at least an email and user type)
     * @param mixed $ZoomAPI   - (optional) A Zoom API instance
     *
     * @return bool|string
     */
    public static function custCreateUser( $user_data, $ZoomAPI = null )
    {
        $ZoomAPI = $ZoomAPI ?: API::getInstance();
        return $ZoomAPI->sendRequest( 'user/custcreate', $user_data );
    }

    /**
     * Delete a user on Zoom
     *
     * @param array $user_data - An array of user data (must include at least an id)
     * @param mixed $ZoomAPI   - (optional) A Zoom API instance
     *
     * @return bool|string
     */
    public static function deleteUser( $user_data, $ZoomAPI = null )
    {
        $ZoomAPI = $ZoomAPI ?: API::getInstance();
        return $ZoomAPI->sendRequest( 'user/delete', $user_data );
    }

    /**
     * Permanently remove a user from Zoom
     *
     * @param array $user_data - An array of user data (must include at least an email and id)
     * @param mixed $ZoomAPI   - (optional) A Zoom API instance
     *
     * @return bool|string
     */
    public static function permanentDeleteUser( $user_data, $ZoomAPI = null )
    {
        $ZoomAPI = $ZoomAPI ?: API::getInstance();
        return $ZoomAPI->sendRequest( 'user/permanentdelete', $user_data );
    }

    /**
     * Retrieve a list of Zoom users for our accoung
     *
     * @param int $page_size   - (optional) The number of users to get
     * @param int $page_number - (optional) The page offset
     * @param mixed $ZoomAPI   - (optional) A Zoom API instance
     *
     * @return mixed
     */
    public static function listUsers( $page_size = null, $page_number = null, $ZoomAPI = null )
    {
        $ZoomAPI   = $ZoomAPI ?: API::getInstance();
        $user_list = json_decode( $ZoomAPI->sendRequest( 'user/list', compact( 'page_size', 'page_number' ) ), true );
        $users     = array();

        foreach ( $user_list['users'] as $user_data ) {
            $users[$user_data['id']] = new User( $user_data );
        }

        $user_list['users'] = $users;

        return $user_list;
    }

    /**
     * Create a custom Zoom user based on the instantiated user
     *
     * @return bool|string
     */
    public function custCreate()
    {
        $this->update( json_decode( self::custCreateUser( $this->toArray() ), true ), $this->ZoomAPI );
    }

    /**
     * Delete the instantiated user from Zoom
     *
     * @return bool|string
     */
    public function delete()
    {
        return self::deleteUser( $this->toArray(), $this->ZoomAPI );
    }

    /**
     * Permanently remove the instantiated user from Zoom
     *
     * @return bool|string
     */
    public function permanentDelete()
    {
        return self::permanentDeleteUser( $this->toArray(), $this->ZoomAPI );
    }
}