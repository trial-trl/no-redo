<?php
/**
 * Description of FixItAccount
 * 
 * ConnectDB adapted to new version on 08/09/2016, ~20:47:28
 * 
 * Created on 05/09/2016, ~16:11:40
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.01NC
 * @package Account
 */

class FixItAccount {
    
    private $con;
    
    public function __construct() {
        $this->con = (new ConnectDB(DB_PREFIX . DATABASE_FIX_IT))->connect();
    }
    
    public function authenticateUser($id_trial, string $type = TRIALAccount::USER) {
        $profile = $this->getProfile($id_trial, $type);
        if ($profile['message'] === Message::NOT_EXIST) {
            $profile = ['level' => 1, 'experience' => 0, 'id' => Query::helper((new Insert($this->con))->table($type === TRIALAccount::USER ? 'users' : 'governments')->columns('user, register_date_time')->values([$id_trial, date('Y-m-d H:i:s')])->run(), function ($query) {
                return $query->getResult()['id'];
            })];
        }
        $domain = filter_input(INPUT_SERVER, 'HTTP_HOST') !== 'localhost' ? '.trialent.com' : 'localhost';
        setcookie(COOKIE_FIX_IT_ID, $profile['id'], time() + (60 * 60 * 24 * 365), '/', $domain);
        return $profile;
    }
    
    public function getProfile($user, string $type = TRIALAccount::USER) {
        return Query::helper((new Select($this->con))->table($type === TRIALAccount::USER ? 'users' : 'governments')->columns($type === TRIALAccount::USER ? 'id, user, level, experience' : 'id, user')->where('user = :user')->values([':user' => $user])->run(), function ($query) {
            if ($query->existRows()) {
                $result = $query->getResult()[0];
            }
            $result['message'] = $query->existRows() ? Message::EXIST : Message::NOT_EXIST;
            return $result;
        });
    }
    
}