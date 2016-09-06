<?php
/**
 * Description of Location
 *
 * Created on: 06/09/2016, 16:00:55
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package TRIAL
 */
class Location {
    
    private $lat_lng;
    private $postal_code;
    private $street_number;
    private $route;
    private $sublocality;
    private $county;
    private $state;
    private $country;
    
    public function __construct($lat, $lng, $location) {
        $this->lat_lng = ['lat' => $lat, 'lng' => $lng];
        $this->postal_code = [
            'long' => isset($location->{'postal_code'}->{'long'}) ? $location->{'postal_code'}->{'long'} : null,
            'short' => isset($location->{'postal_code'}->{'short'}) ? $location->{'postal_code'}->{'short'} : null
        ];
        $this->street_number = [
            'long' => isset($location->{'street_number'}->{'long'}) ? $location->{'street_number'}->{'long'} : null,
            'short' => isset($location->{'street_number'}->{'short'}) ? $location->{'street_number'}->{'short'} : null
        ];
        $this->route = [
            'long' => isset($location->{'route'}->{'long'}) ? $location->{'route'}->{'long'} : null,
            'short' => isset($location->{'route'}->{'short'}) ? $location->{'route'}->{'short'} : null
        ];
        $this->sublocality = [
            'long' => isset($location->{'sublocality'}->{'long'}) ? $location->{'sublocality'}->{'long'} : null,
            'short' => isset($location->{'sublocality'}->{'short'}) ? $location->{'sublocality'}->{'short'} : null
        ];
        $this->county = [
            'long' => isset($location->{'county'}->{'long'}) ? $location->{'county'}->{'long'} : null,
            'short' => isset($location->{'county'}->{'short'}) ? $location->{'county'}->{'short'} : null
        ];
        $this->state = [
            'long' => isset($location->{'state'}->{'long'}) ? $location->{'state'}->{'long'} : null,
            'short' => isset($location->{'state'}->{'short'}) ? $location->{'state'}->{'short'} : null
        ];
        $this->country = [
            'long' => isset($location->{'country'}->{'long'}) ? $location->{'country'}->{'long'} : null,
            'short' => isset($location->{'country'}->{'short'}) ? $location->{'country'}->{'short'} : null
        ];
    }
    
    public function getLatLng() {
        return $this->lat_lng;
    }
    
    public function getLongPostalCode() {
        return $this->postal_code['long'];
    }
    
    public function getLongStreetNumber() {
        return $this->street_number['long'];
    }
    
    public function getLongRoute() {
        return $this->route['long'];
    }
    
    public function getLongSublocality() {
        return $this->sublocality['long'];
    }
    
    public function getLongCounty() {
        return $this->county['long'];
    }
    
    public function getLongState() {
        return $this->state['long'];
    }
    
    public function getLongCountry() {
        return $this->state['long'];
    }
    
    public function getShortPostalCode() {
        return $this->postal_code['short'];
    }
    
    public function getShortStreetNumber() {
        return $this->street_number['short'];
    }
    
    public function getShortRoute() {
        return $this->route['short'];
    }
    
    public function getShortSublocality() {
        return $this->sublocality['short'];
    }
    
    public function getShortCounty() {
        return $this->county['short'];
    }
    
    public function getShortState() {
        return $this->state['short'];
    }
    
    public function getShortCountry() {
        return $this->state['short'];
    }
    
}
