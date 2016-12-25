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
 * @version 1.02NC
 * @package Account
 */

/*
 * Implementation of RG, CPF, Escolaridade, Ocupação principal started on 05/09/2016, ~16:32:00
 * 
 * 22/11/2016, 18:28:20 - 18:44:53 => updated all codes to new codes, fixed authenticateUser() not making login right
 * 
 * 24/12/2016, 14:51:52 => update ConnectDB code to DB code; added use Query, Select statement
 */

use SQL\Query, SQL\Select;

class FixItAccount {
    
    private $con;
    
    public function __construct() {
        $this->con = DB::connect(DATABASE_FIX_IT);
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