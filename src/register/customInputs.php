<?php

namespace Zngly\ACFM\Register;

use Zngly\ACFM\Config;

// add_filter('graphql_input_fields', function ($fields, $type_name) {
//     if ($type_name === "CreateMyCustomInput")
//         $fields['customInput'] = [
//             'type' => 'String',
//         ];
//     return $fields;
// }, 10, 2);
/**
 * CustomInputs class.
 * Maps the above comment to acf fields
 */
class CustomInputs
{
    /**
     * @var Config <string> List of field groups and fields
     */
    protected $config;

    /**
     * @var array
     */
    private $input_fields;  // <array> List of mutation input fields

    public function __construct()
    {
        add_filter('graphql_input_fields', function ($input_fields, $type) {
            /**
             * Set the fields as class variables
             */
            $this->input_fields = $input_fields;

            $this->config = new Config();

            /**
             * Register input fields
             */
            $this->register_inputs($type);

            return $this->input_fields;
        }, 10, 2);
    }

    protected function register_inputs($type_name)
    {
        foreach ($this->config->field_groups as $field_group)
            foreach ($field_group['graphql_types'] as $graphql_type)
                if (in_array($type_name, ["Create{$graphql_type}Input", "Update{$graphql_type}Input"]))
                    foreach ($field_group['fields'] as $field) {

                        // check if there is a custom strict type set, otherwise we infer it
                        if (isset($field['strict_graphql_type']) && $field['strict_graphql_type'] != "")
                            $type = $field['strict_graphql_type'];
                        else $type = $this->config->get_acf_type($graphql_type, $field, $field_group);

                        if ($type == null)
                            continue;

                        $friendly_type_name = $type;
                        if (is_array($friendly_type_name))
                            $friendly_type_name = json_encode($friendly_type_name);

                        $this->input_fields[$field['graphql_name']] = [
                            'type' => $type,
                            'description' => $field['description'] . $friendly_type_name,
                        ];
                    }
    }
}
