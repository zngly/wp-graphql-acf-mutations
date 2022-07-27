### Mutations Folder

This directory hosts the logic where mutations happen and the binding to acf.

#### PostObject

When a postObject is mutated, we hook into `graphql_post_object_mutation_update_additional_data`.
From there we loop through the acf groups which relate to the postObject
-> foreach group we loop through the acf fields and manually update the database using the acf api update_field/delete_field.

#### MediaItem

The same logic as postObject is used for mediaItem except we hook into `graphql_media_item_mutation_update_additional_data`.

#### taxonomy

Since taxonomy updates dont include any extra input args we may pass into the hook `graphql_term_object_insert_term_args` and there is also no other hook to hook into. We must first allow acf field inputs to be added to the term_args.
We then use the same logic as before.
