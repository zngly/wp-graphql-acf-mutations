<?php

namespace Zngly\ACFM\Register;

use Zngly\ACFM\Config;
use WPGraphQL\Registry\TypeRegistry;

class Register
{

    public function __construct()
    {
        // register custom types from acf config
        new CustomTypes();

        // add custom input to mutations
        new CustomInputs();
    }
}
