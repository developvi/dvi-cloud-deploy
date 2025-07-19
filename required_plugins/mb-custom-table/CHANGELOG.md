### 2.2.4 - 2025-06-11

Add `mbct_{$action}_bulk_action` action for handling bulk actions

### 2.2.3 - 2025-04-16

Fix REST API

### 2.2.2 - 2025-03-24

Fix wrong object id for mbct_after_add hook when using API::add()

### 2.2.1 - 2025-02-04

Fix: fatal error when set parent

### 2.2.0 - 2024-11-20

This version supports for custom models, allowing to submit data for custom models on the front end. For usage, please see [the docs](https://docs.metabox.io/extensions/mb-frontend-submission/).

### 2.1.13 - 2024-09-26

Add filter for list table class object, which allows developers to use their own list table object:

`$list_table = apply_filters( 'mbct_list_table_object', $list_table, $args );`

### 2.1.12 - 2024-08-19

Fix running PHP Codesniffer when installing & autoload the plugin's main file via Composer

### 2.1.11 - 2024-07-05

Default sort custom models by ID desc

### 2.1.10 - 2024-03-26

- Fix PHP error when adding a new custom model item
- Fix delete single custom model item

### 2.1.9 - 2024-03-20

- Improve bulk actions for custom models with ability to register custom action & callback. Support redirect after doing bulk action. See [the docs](https://docs.metabox.io/extensions/mb-custom-table/#bulk-actions-handling) for using.
- Add a `$force` param to `API::get()` method to force getting raw data from the database (true: default) or cache (false).
- Improve performance for bulk deleting models

### 2.1.8 - 2023-11-27

- Allow developers to set message class via $\_GET\['message-status'\] param
- Add "mbct\_add\_data" and "mbct\_update\_data" filters to let developers change data before inserting/updating into the DBAdd filter
- "mbct\_{$this->model->name}\_total\_items" to get total items for models
- Fix PHP notice when accessing non-model pages

### 2.1.7 - 2023-08-16

- Fix cannot update user meta in a custom table via REST API

### 2.1.6 - 2023-05-10

- Add filter "mbct\_{$model}\_prepare\_items" for prepare items SQL
- Fix get only updated field value after running the API::update() and API::get()
- Fix not working with REST API
- Fix not working with revision

### 2.1.5 - 2023-04-04

- Fix `rwmb_set_meta()` not saving data

### 2.1.4 - 2022-12-05

- Fix PHP notice for `wp_cache_get` in WP 6.1
- Fix term meta is not deleted after deleting term
- Fix not saving for custom blocks with custom post types

### 2.1.3 - 2022-09-20

- Fix data not saving properly

### 2.1.2 - 2022-07-14

- Fix calling hooks (like `mbct_after_update`) multiple times if there are more than one meta box that use the same custom table
- Fix missing `$object_id` for `mbct_after_add hook`
- Add CSS classes to the body for model pages
- Add more params for `row_actions` hook
- Add `bulk_actions` filter

### 2.1.1 - 2022-02-16

- Add confirmation before delete a model
- Add hooks to all places

### 2.1.0 - 2022-01-24

- Fix bug custom model pagination
- Add hooks before/after firing public API
- Add hooks to the submit box - custom model
- Add hooks for custom model table list

### 2.0.0 - 2021-09-07

- Rewrite the plugin with namespaces
- Add support for custom models, e.g. creating models with custom tables with no relations to WordPress posts, users or tems.

### 1.1.11 - 2021-01-13

- Fix not saving empty value

### 1.1.11 - 2020-12-01

- Fix saving fields with "multiple" setting

### 1.1.10 - 2020-01-28

- Make Storage::table property public
- Change ID column to bigint(20) unsigned to compatible with posts table

### 1.1.9 - 2020-01-14

- Fix not saving for term meta

### 1.1.8 - 2019-04-01

- Fixed not working for user meta.

### 1.1.7

- Removed debug code.

### 1.1.6

- Fixed output wrong value for checkbox field when unchecked.
- Do not create row when no data is submitted.

### 1.1.5

- Fixed: Checkboxes saved in custom table but shown in UI (checkbox\_list)

### 1.1.4

- Fixed not working with [MB Revisions](https://mbio.test/plugins/mb-revision/)

### 1.1.3

- Fixed inserting empty row into the custom table when creating a new post of another post type.

### 1.1.2

- Save empty values as NULL in the database, so empty arrays won't be serialized.

### 1.1.1

- Fixed data not saving correctly if there's field of type heading or divider.

### 1.1.0

- Removed rows in the database when deleting objects (posts, terms, users)
- Fixed saving values for field with "multiple=true".

### 1.0.1

- Fixed meta boxes are ignored to update to the DB

### 1.0

- Initial release