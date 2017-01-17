<?php
/**
 * Description of OpportunityNowAccount
 * 
 * ConnectDB adapted to new version on 08/09/2016, ~20:47:28
 * 
 * Created on 21/10/2016, 17:40:52
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.0NC
 * @package Account
 */

class OpportunityNowAccount {
    
    private $con;
    
    public function __construct() {
        $this->con = (new ConnectDB(DB_PREFIX . DATABASE_JOBS_NOW))->connect();
    }
    
    private function buildResponse($query, $callback) : array {
        $query_response = $query instanceof QueryResponse ? $query : $query->run();
        if ($query_response->success()) {
            return $callback($query_response);
        } else {
            return ['error' => $query_response->getError(), 'message' => Message::ERROR];
        }
    }
    
    public function authenticateUser($id_trial) : array {
        $profile = $this->getProfile($id_trial);
        if ($profile['message'] === Message::EXIST) {
            $domain = filter_input(INPUT_SERVER, 'HTTP_HOST') !== 'localhost' ? '.trialent.com' : 'localhost';
            setcookie(COOKIE_OPPORTUNITY_NOW_ID, $profile['id'], time() + (60 * 60 * 24 * 365), '/', $domain);
            setcookie(COOKIE_OPPORTUNITY_NOW_FREELANCER, $profile['freelancer'], time() + (60 * 60 * 24 * 365), '/', $domain);
        }
        return $profile;
    }
    
    public function createAccount($user) : array {
        return $this->buildResponse((new Insert($this->con))->table('users')->columns('user, register_date_time')->values([$user, date('Y-m-d H:i:s')])->run(), function ($query) {
            $result = $query->getResult();
            $result['message'] = Message::SAVED_WITH_SUCCESS;
            return $result;
        });
    }
    
    public function getProfile($user) : array {
        return $this->buildResponse((new Select($this->con))->table('users')->columns('id, user, freelancer')->where('user = :user')->values([':user' => $user])->run(), function ($query) use ($user) {
            if ($query->existRows()) {
                $result = $query->getResult()[0];
            } else {
                $result['id'] = $this->createAccount($user)['id'];
                $result['freelancer'] = false;
            }
            $result['message'] = $query->existRows() ? Message::EXIST : Message::NOT_EXIST;
            return $result;
        });
    }
    
}
