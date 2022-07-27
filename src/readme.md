## wp-graphql-acf-mutations

#### config.php

-   Gets the acf information and formats it in a usable format.
-   Decides what graphql type an acf field type is. e.g. postObject -> ID

#### registerInputs.php

-   Registers the acf field inputs to the graphql type registry. This is how the acf field inputs show up in the graphql schema.

#### registerTypes.php

-   Creates types from the acf field information.
-   Group & Repeater types for example

#### utils.php

-   utility function file
