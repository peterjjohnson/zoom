<?
namespace Zoom;

/**
 * Class Meeting
 *
 * For detailed documentation see https://support.zoom.us/hc/en-us/articles/201363053-REST-Meeting-API
 *
 * @package Zoom
 */
class Meeting extends Base
{
    const TYPE_INSTANT   = 1;
    const TYPE_SCHEDULED = 2;
    const TYPE_RECURRING = 3;

    var $id;
    var $uuid;
    var $host_id;
    var $topic;
    var $type = self::TYPE_RECURRING;
    var $start_time;
    var $duration;
    var $timezone;
    var $password;
    var $option_jbh = false;
    var $option_host_video = true;
    var $option_participants_video = false;
    var $option_audio = 'both';
    var $join_url;
    var $start_url;

    /**
     * Create a Zoom meeting
     *
     * @param array $meeting_data - Array of meeting data (must at least contain host_id and topic)
     * @param mixed $ZoomAPI      - (optional) Zoom API instance
     *
     * @return mixed
     */
    public static function createMeeting( $meeting_data, $ZoomAPI = null )
    {
        $ZoomAPI = $ZoomAPI ?: API::getInstance();
        return $ZoomAPI->sendRequest( 'meeting/create', $meeting_data );
    }

    /**
     * Get details for a specific meeting
     *
     * @param array $meeting_data - Array of meeting data (must at least contain id and host_id)
     * @param mixed $ZoomAPI      - (optional) Zoom API instance
     *
     * @return Meeting
     */
    public static function getMeeting( $meeting_data, $ZoomAPI = null )
    {
        $ZoomAPI = $ZoomAPI ?: API::getInstance();
        return new Meeting( json_decode( $ZoomAPI->sendRequest( 'meeting/get', $meeting_data ), true ) );
    }

    /**
     * Delete a meeting
     *
     * @param array $meeting_data - Array of meeting data (must at least contain id and host_id)
     * @param mixed $ZoomAPI      - (optional) Zoom API instance
     *
     * @return mixed
     */
    public static function deleteMeeting( $meeting_data, $ZoomAPI = null )
    {
        $ZoomAPI = $ZoomAPI ?: API::getInstance();
        return $ZoomAPI->sendRequest( 'meeting/delete', $meeting_data );
    }

    /**
     * Create a Zoom meeting based on this meeting object
     *
     * @return mixed
     */
    public function create()
    {
        $json_meeting = self::createMeeting( $this->toArray(), $this->ZoomAPI );
        $this->update( json_decode( $json_meeting, true ) );
        return $json_meeting;
    }

    /**
     * Delete this meeting
     *
     * @return mixed
     */
    public function delete()
    {
        return self::deleteMeeting( $this->toArray(), $this->ZoomAPI );
    }
}