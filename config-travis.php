<?php

namespace LTI;

const DB_NAME     = '%DATABASE%';
const DB_USER     = 'travis';
const DB_PASSWORD = '';
const DB_HOST     = '127.0.0.1';
const DB_PORT     = '3306';
const DB_DRIVER   = 'mysql'; // Should be one of PDO::getAvailableDrivers()
const DB_PREFIX   = 'lti_';


const ROOT_PATH     = __DIR__;
const TEMPLATE_PATH = ROOT_PATH . '/templates';
const CLASS_PATH    = ROOT_PATH . '/classes';
