## Zngly - WpGraphql ACF Mutations

Wordpress plugin which add mutations to ACF Fields

### Usage

Install and Activate plugin

Acf fields should show up in mutations

### Specify Graphql Type

If an acf type needs a specific graphql type such as a custom enum then specify
`"strict_graphql_type" => "MyCustomEnum"` in your acf config.

### Limitations

Currently the mutations will not be set for objects with more than one child deep.

### Future Work

All mutations will be aggregated under a custom input type
