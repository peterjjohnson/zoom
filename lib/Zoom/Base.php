<?
namespace Zoom;

/**
 * Class Base
 *
 * @package Zoom
 */
class Base
{
    /**
     * @var $ZoomAPI - A Zoom API instance
     */
    var $ZoomAPI;

    /**
     * Populate the object properties and get an instance of the API
     *
     * @param array $data - Key-value array of data used to populate properties
     *
     * @return Base
     */
    public function __construct( $data = array() )
    {
        $this->update( $data );
        $this->ZoomAPI = API::getInstance();
    }

    /**
     * Update object properties from supplied array values
     *
     * @param array $data - Key-value array used to update object property values
     *
     * @return void
     */
    protected function update( $data = array() )
    {
        foreach ( $data as $property => $value ) {
            if ( property_exists( get_called_class(), $property ) ) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Return the object's properties as a key-value array
     *
     * @return array
     */
    protected function toArray()
    {
        $properties = array();

        foreach ( $this as $property => $value ) {
            $properties[$property] = $value;
        }

        return $properties;
    }
}