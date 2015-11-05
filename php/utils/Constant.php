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
const DATABASE_CLICKER = 'clicker';
const DATABASE_USERS = 'users';
const DATABASE_DOWNLOADS = 'downloads';
const DATABASE_NO_REDO = 'no_redo';
const DATABASE_ANNOTATION = 'annotation';

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
const TABLE_STUDY_MATERIALS = 'study_materials';
const TABLE_MEMBERS = 'members';
const TABLE_ANNOTATIONS = 'annotations';

// Request response messages
const MESSAGE_EXIST = 'E';
const MESSAGE_NOT_EXIST = 'NE';
const MESSAGE_SAVED_WITH_SUCCESS = 'SWS';
const MESSAGE_EMAIL_OR_PASSWORD_INVALID = 'EPI';
const MESSAGE_NOT_ACTIVATED = 'NA';
const MESSAGE_ERROR = 'ERR';

// Paths
const PATH_IMAGES = '../../../clicker/images/';

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
const REQUEST_SIGN_OUT = 'sgout';
const REQUEST_GET_MEMBERS = 'gmembs';
const REQUEST_SIGN_PROJECT = 'sprojc';
const REQUEST_SIGN_ANNOTATION = 'sannot';
const REQUEST_DELETE_ANNOTATION = 'dannot';
const REQUEST_SIGN_STUDY_MATERIAL = 'ssymtl';
const REQUEST_CHANGE_STATUS = 'ustats';
const REQUEST_SIGN_GOAL = 'sgoal';
const REQUEST_SIGN_JOB_TITLE = 'sjobtt';

const IMAGE_PROFILE = 'img_profile';


/*
 * No Redo service
 */

// Status
const STATUS_CONCLUDED = 'CONCLUDED';
const STATUS_PENDING = 'PENDING';
const STATUS_FROZEN = 'FROZEN';
const STATUS_CANCELED = 'CANCELED';

// Cookies
const COOKIE_NO_REDO_COMPANY = 'NR_company';