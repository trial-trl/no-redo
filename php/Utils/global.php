<?php

/* 
 * (c) 2018 TRIAL.
 * Created on 18/01/2018, 23:17:25.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

ini_set('default_charset', 'utf-8');
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'pt_BR.utf8');

use NoRedo\Utils\Header;

/**
 * Created on 18/01/2018 22:44:37 as global Header(string $header, string $value) function
 * Moved from Header class as getheader(string $n, string $d) method on 18/02/2018 22:44:37
 * 
 * @param string $n
 * @param string $d
 * @return \Header
 */
function get_header(string $n, string $d = ''): Header {
  return new Header($n, $d);
}

/**
 * 
 * @return string
 */
/* Created on 27/09/2018 13:44:13 */
function get_ip(): string {
  $client  = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '';
  $forward = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
  $remote  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
  return filter_var($client, FILTER_VALIDATE_IP) 
          ? $client
          : (filter_var($forward, FILTER_VALIDATE_IP)
             ? $forward
             : $remote);
}

/**
 * 
 * @param string $dir
 * @param bool $create
 * @return bool
 */
function path_exists(string $dir, bool $create = true): bool {
  if (file_exists($dir)) {
    return true;
  } else {
    if ($create) {
      mkdir($dir, 0777, true);
      return true;
    }
    return false;
  }
}

/**
 * 
 * @param string $url
 * @return bool
 */
function url_exists(string $url): bool {
    clearstatcache();
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code == 200;
}

/**
 * 
 * @return array
 */
function get_user_agent(): array {
  $ch = curl_init('https://helloacm.com/api/parse-user-agent?s=' . urlencode($_SERVER['HTTP_USER_AGENT']));
  curl_setopt($ch, CURLOPT_POST, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $agent = json_decode(curl_exec($ch), true);
  curl_close($ch);
  return is_array($agent) ? $agent : [];
}

/**
 * 
 * @param string $image
 * @param string $dir
 * @param string $filename
 */
function upload_base_64_img(string $image, string $dir, string $filename) {
  $decodedImage = base64_decode($image);
  path_exists($dir);
  $file = fopen($dir . DIRECTORY_SEPARATOR . $filename, 'wb');
  fwrite($file, $decodedImage);
  fclose($file);
}

/*
 * Commons
 */

// Database connection constants
const DB_DATABASE = 'mysql';
const DB_USER = 'root';
const DB_PASSWORD = '';
const DB_PREFIX = 'trial105_';

// Database names
const DATABASE_TRIAL = DB_PREFIX . 'trial';
const DATABASE_SERVICES = DB_PREFIX . 'services';
const DATABASE_CLICKER = DB_PREFIX . 'clicker';
const DATABASE_USERS = DB_PREFIX . 'users';
const DATABASE_GAMES = DB_PREFIX . 'games';
const DATABASE_DOWNLOADS = DB_PREFIX . 'downloads';
const DATABASE_NO_REDO = DB_PREFIX . 'no_redo';
const DATABASE_SOLIDARITY_MAP = DB_PREFIX . 'mapadasolidariedade';
const DATABASE_JOBS_NOW = DB_PREFIX . 'jobs_now';
const DATABASE_ANNOTATION = DB_PREFIX . 'annotation';
const DATABASE_VIRTUAL_HERITAGE_CONTROL = DB_PREFIX . 'virtual_heritage_control';
const DATABASE_STREET_RACE = DB_PREFIX . 'street_race';
const DATABASE_STORE = DB_PREFIX . 'store';
const DATABASE_DIGITAL_BOOKLET = DB_PREFIX . 'digital_booklet';
const DATABASE_FIX_IT = DB_PREFIX . 'fix_it';

// Table names
const TABLE_USERS = 'users';
const TABLE_INSTITUTIONS = 'institutions';
const TABLE_GOVERNMENTS = 'governments';
const TABLE_DIVISIONS = 'divisions';
const TABLE_GUIDES = 'guides';
const TABLE_FUNCTIONS = 'functions';
const TABLE_JOB_TITLES = 'job_titles';
const TABLE_GOALS = 'goals';
const TABLE_TYPE_PROJECTS = 'type_projects';
const TABLE_PROJECTS = 'projects';
const TABLE_IMAGES_PROJECTS = 'images_projects';
const TABLE_STUDY_MATERIALS = 'study_materials';
const TABLE_MEMBERS = 'members';
const TABLE_ANNOTATIONS = 'annotations';
const TABLE_SET_OF_QUICK_ACTIONS = 'set_quick_actions';

// Request response messages
const MESSAGE_EXIST = 'E';
const MESSAGE_NOT_EXIST = 'NE';
const MESSAGE_SAVED_WITH_SUCCESS = 'SWS';
const MESSAGE_EMAIL_OR_PASSWORD_INVALID = 'EPI';
const MESSAGE_NOT_ACTIVATED = 'NA';
const MESSAGE_ERROR = 'ERR';
const MESSAGE_ERROR_PASSWORD_INCORRECT = 'ERR_PI';

const MESSAGE_MEMBER_WITHOUT_INSTITUTION = 'MWI';

const TRIAL_ACCOUNT_TYPE_INSTITUTION = 'institution';
const TRIAL_ACCOUNT_TYPE_USER = 'user';

const TRIAL_ACCOUNT_TYPE_INSTITUTION_MEMBER = 'institution_member';

const TRIAL_ACCOUNT_TYPE_GOVERNMENT = 'government';

// Paths
const PATH_CLICKER_QUESTIONS = '../images/questions';
const PATH_CLICKER_BASE_TEXTS = '../base_texts/';

// Emails
const EMAIL_CONTACT_TRIAL = 'contato@trialent.com';

const SUBJECT_ACTIVATION_ACCOUNT = 'Link de ativação da conta TRIAL';

const DURATION_INDEFINED = 0;
const DURATION_FULL_DAY = 86400;
const DURATION_HALF_DAY = 43200;

// Permissions
const PERMISSION_ROOT = "ROOT";
const PERMISSION_ADMIN = "ADMIN";
const PERMISSION_USER = "USER";

// Server requests
const REQUEST_AUTHENTICATION = 'auth';
const REQUEST_CREATE_TRIAL_ACCOUNT = 'sign';
const REQUEST_LOGOUT = 'lgout';
const REQUEST_SIGN_OUT = 'sgout';
const REQUEST_GET_MEMBERS = 'gmembs';
const REQUEST_SIGN_PROJECT = 'sprojc';
const REQUEST_SIGN_NEW_PROJECT_IMAGE = 'snprig';
const REQUEST_DELETE_PROJECT = 'dprojc';
const REQUEST_SIGN_ANNOTATION = 'sannot';
const REQUEST_DELETE_ANNOTATION = 'dannot';
const REQUEST_SIGN_STUDY_MATERIAL = 'ssymtl';
const REQUEST_CHANGE_STATUS = 'ustats';
const REQUEST_SIGN_GOAL = 'sgoal';
const REQUEST_DELETE_GOAL = 'dgoal';
const REQUEST_SIGN_JOB_TITLE = 'sjobtt';

const IMAGE_PROFILE = 'img_profile';

// User cookies
const COOKIE_ID_TRIAL = 'TRL_TU_idTRIAL';
const COOKIE_NAME = 'TRL_TU_name';
const COOKIE_EMAIL = 'TRL_TU_email';
const COOKIE_PERMISSION = 'TRL_TU_permission';

// Institution cookies
const COOKIE_TI_ID_TRIAL = 'TRL_TI_idTRIAL';
const COOKIE_TI_NAME = 'TRL_TI_name';
const COOKIE_TI_EMAIL = 'TRL_TI_email';

// Institution cookies
const COOKIE_TG_ID_TRIAL = 'TRL_TG_idTRIAL';
const COOKIE_TG_NAME = 'TRL_TG_name';
const COOKIE_TG_EMAIL = 'TRL_TG_email';

// Department cookies
const COOKIE_TGD_ID_TRIAL = 'TRL_TGD_idTRIAL';
const COOKIE_TGD_NAME = 'TRL_TGD_name';
const COOKIE_TGD_EMAIL = 'TRL_TGD_email';

// Common cookie's
const COOKIE_TYPE = 'TRL_type';

/*
 * Url's
 */
const URL_ACCOUNT = 'http://account.trialent.com/';

/*
 * No Redo service
 */

// Status
const STATUS_CONCLUDED = 'CONCLUDED';
const STATUS_NOT_CONCLUDED = 'NOT_CONCLUDED';
const STATUS_PENDING = 'PENDING';
const STATUS_FROZEN = 'FROZEN';
const STATUS_CANCELED = 'CANCELED';

// Cookies
const COOKIE_NO_REDO_COMPANY = 'TRL_NR__company';
const COOKIE_NO_REDO_MEMBER_COMPANIES = 'TRL_NR__member_companies';
const COOKIE_NO_REDO_ID_MEMBER = 'TRL_NR__id_member';

/*
 * Clicker service
 */

const CLICKER_TYPE_TEACHER = 'TEACHER';
const CLICKER_TYPE_STUDENT = 'STUDENT';
 
// Cookies
const COOKIE_CLICKER_ID = 'TRL_CL__id';
const COOKIE_CLICKER_INSTITUTION = 'TRL_CL__institution';
const COOKIE_CLICKER_TYPE = 'TRL_CL__type';
// 18/02/2017, 18:37:27
const COOKIE_CLICKER_CREATE = 'TRL_CL__create';
 
/*
 * Fix It service
 */

// Cookies
const COOKIE_FIX_IT_ID = 'TRL_FI__id';
const COOKIE_FIX_IT_LOCALIZATION = 'TRL_FI__localization';
const COOKIE_FIX_IT_AVATAR = 'TRL_FI__avatar';

/*
 * Street Race service
 */
const COOKIE_STREET_RACE_ID = 'TRL_SR__id';
const COOKIE_STREET_RACE_TYPE = 'TRL_SR__type';

/*
 * Opportunity Now! service
 */
const COOKIE_OPPORTUNITY_NOW_ID = 'TRL_ON__id';

const COOKIE_OPPORTUNITY_NOW_FREELANCER = 'TRL_ON__freelancer';
    
/**
 * @author Rokas Šleinius <raveren+gh@gmail.com>
 * @link https://gist.github.com/raveren/5555297 Original GitHub Gist
 */
function random_text( $type = 'alnum', $length = 8 ) {
  switch ( $type ) {
    case 'alnum':
      $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      break;
    case 'alpha':
      $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      break;
    case 'hexdec':
      $pool = '0123456789abcdef';
      break;
    case 'numeric':
      $pool = '0123456789';
      break;
    case 'nozero':
      $pool = '123456789';
      break;
    case 'distinct':
      $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
      break;
    default:
      $pool = (string) $type;
      break;
  }
  $crypto_rand_secure = function ( $min, $max ) {
    $range = $max - $min;
    if ( $range < 0 ) return $min; // not so random...
    $log    = log( $range, 2 );
    $bytes  = (int) ( $log / 8 ) + 1; // length in bytes
    $bits   = (int) $log + 1; // length in bits
    $filter = (int) ( 1 << $bits ) - 1; // set all lower bits to 1
    do {
      $rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
      $rnd = $rnd & $filter; // discard irrelevant bits
    } while ( $rnd >= $range );
    return $min + $rnd;
  };
  $token = "";
  $max   = strlen( $pool );
  for ( $i = 0; $i < $length; $i++ ) {
    $token .= $pool[$crypto_rand_secure( 0, $max )];
  }
  return $token;
}

function getContrastYIQ($hexcolor, $greater_than = 128) {
  $r = hexdec(substr($hexcolor, 1, 2));
  $g = hexdec(substr($hexcolor, 3, 2));
  $b = hexdec(substr($hexcolor, 5, 2));
  $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
  return $yiq >= $greater_than ? '#000000' : '#ffffff';
}