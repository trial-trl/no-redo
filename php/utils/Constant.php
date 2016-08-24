<?php

/* 
 * Copyright 2015 TRIAL.
 * Author : Matheus Leonardo dos Santos Martins
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

/*
 * Commons
 */

// Database connection constants
const DB_DATABASE = 'mysql';
const DB_USER = 'root';
const DB_PASSWORD = '';
const DB_PREFIX = 'trial105_';

// Database names
const DATABASE_TRIAL = 'trial';
const DATABASE_SERVICES = 'services';
const DATABASE_CLICKER = 'clicker';
const DATABASE_USERS = 'users';
const DATABASE_GAMES = 'games';
const DATABASE_DOWNLOADS = 'downloads';
const DATABASE_NO_REDO = 'no_redo';
const DATABASE_SOLIDARITY_MAP = 'mapadasolidariedade';
const DATABASE_JOBS_NOW = 'jobs_now';
const DATABASE_ANNOTATION = 'annotation';
const DATABASE_VIRTUAL_HERITAGE_CONTROL = 'virtual_heritage_control';
const DATABASE_STREET_RACE = 'street_race';
const DATABASE_STORE = 'store';
const DATABASE_DIGITAL_BOOKLET = 'digital_booklet';

// Table names
const TABLE_USERS = 'users';
const TABLE_INSTITUTIONS = 'institutions';
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