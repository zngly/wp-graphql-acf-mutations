<?php

/**
 * Config for WPGraphQL ACF
 *
 * @package wp-graphql-acf
 */

namespace Zngly\ACFM;

use WPGraphQL\Registry\TypeRegistry;

/**
 * RegisterInputs class.
 */
class RegisterInputs
{
    /**
     * @var TypeRegistry
     */
    protected $type_registry;

    /**
     * @var Config <string> List of field groups and fields
     */
    protected $config;


    // constructor
    public function __construct()
    {
        add_action('graphql_register_types', function (TypeRegistry $type_registry) {
            /**
             * Set the type registry
             */
            $this->type_registry = $type_registry;

            /**
             * Set the config, this gives us access to the field groups
             */
            $this->config = new Config();

            /**
             * Register input fields
             */
            $this->register_input_types();
        }, 10, 1);
    }
    /**
     * Registers input types
     */
    protected function register_input_types()
    {
        foreach ($this->config->field_groups as $field_group)
            if (count($field_group['graphql_types']) > 0)
                foreach ($field_group['graphql_types'] as $graphql_type)
                    foreach ($field_group['fields'] as $field)
                        $this->register_input_type($graphql_type, $field, $field_group);
    }

    /**
     * Registers Group and Repeater Field Input Types
     * register a single sub field input. 
     * only need to register group and repeater fields types
     */
    protected function register_input_type(string $type_name, array $config, array $field_group)
    {
        // $acf_field = isset($config) ? $config : null;
        $acf_type  = isset($config['type']) ? $config['type'] : null;

        if (empty($acf_type))
            return;

        if (!($acf_type == 'group' || $acf_type == 'repeater'))
            return;

        $field_type_name = $type_name . '_' . $this->config::camel_case($field_group['graphql_field_name']) . '_' . ucfirst($this->config::camel_case($config['name']));


        if (!isset($config['sub_fields']) && !is_array($config['sub_fields']) && count($config['sub_fields']) < 1)
            return;

        $sub_fields = [];
        foreach ($config['sub_fields'] as $sub_field) {
            if ($sub_field['type'] == "group" || $sub_field['type'] == "repeater") {

                continue;
            }

            if (isset($sub_field['strict_graphql_type']) && $sub_field['strict_graphql_type'] != "")
                $sub_field_type = $sub_field['strict_graphql_type'];
            else
                $sub_field_type = $this->config->get_acf_type($type_name, $sub_field);

            if ($sub_field_type == null)
                continue;

            $graphql_name = $this->config->camel_case($sub_field['name']);

            $friendly_type_name = json_encode($sub_field_type);
            $description = isset($sub_field['instructions']) && $sub_field['instructions'] != "" ? $sub_field['instructions'] . " | " : "";
            $description .= "ACF Type: {$sub_field['type']} | Graphql Type: {$friendly_type_name}";

            $sub_fields[$graphql_name] = [
                'type' => $sub_field_type,
                'description' => $description,
            ];
        }


        if ($acf_type == 'group')
            $input_name = $field_type_name . "_GroupInput";
        else if ($acf_type == 'repeater')
            $input_name = $field_type_name . "_RepeaterInput";

        $input_config = [
            "description" => $config['description'],
            "fields" => $sub_fields,
        ];
        $this->type_registry->register_input_type($input_name, $input_config);
    }
}
