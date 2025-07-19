### 1.6.28 - 2025-07-18

Fix compatibility with the site editor (full-site editing)

### 1.6.27 - 2025-07-15

Improve performance for the block editor

### 1.6.26 - 2025-05-08

Fix performance issue with new Builder's Local JSON feature, and improve performance for the block editor.

### 1.6.25 - 2025-02-04

Improve codebase to work better with MB Builder.

### 1.6.24 - 2024-09-26

Fix issue with `clone_empty_start`

### 1.6.23 - 2024-09-07

Fix issue with `clone_empty_start`

### 1.6.22 - 2024-08-19

Fix running PHP Codesniffer when installing & autoload the plugin's main file via Composer

### 1.6.21 - 2024-01-22

- Fix not working with decimal number
- Fix: don't set required when the field is visible back

### 1.6.20 - 2023-09-06

Hide column if there is no fields or all fields are hidden #8

### 1.6.19 - 2023-05-10

Improve performance

### 1.6.18 - 2023-03-23

- Fix custom callback not working
- Fix not working if the field is visible but meta box is hidden

### 1.6.17 - 2022-03-01

- Export runConditionalLogic function to the global rwmb object to use in other scripts
- Fix not working in cloneable group due to arrow function

### 1.6.16 - 2022-01-21

Fix blank page in Oxygen

### 1.6.15 - 2022-01-17

- Fix conditional logic not running due to window.load event not firing
- Fix not show/hide meta box in the front end
- Improve code using ES6

### 1.6.14 - 2021-04-19

Fix checkbox in group

### 1.6.13 - 2020-05-27

Fix not working with Oxygen widget

### 1.6.12 - 2020-04-30

Fix JSON-encode conditions

### 1.6.11 - 2020-01-05

Make it work with fields inside dynamic blocks created with [MB Blocks](https://metabox.io/plugins/mb-blocks/).

### 1.6.10 - 2020-01-05

Fix not working in the Customizer (for Settings Page extension).

### 1.6.9 - 2019-12-22

Fix toggle type parameter not working.

### 1.6.8 - 2019-12-19

Fix conditional logic break with Frontend Submission. Now the JS is enqueued only if conditions exist.

### 1.6.7 - 2019-08-13

Fix conditional logic fails on switch

### 1.6.6 - 2019-06-13

Allow conditions to work for elements outside cloneable groups.

### 1.6.5 - 2019-03-05

Added full support for Gutenberg, now works with all rules, including `post_format`, `page_template`, `parent`, `post_ID`, categories, tags and custom taxonomies. Used Gutenberg API (not jQuery) to handle the logic.

### 1.6.4

Added support for Gutenberg and WordPress 5.0. However, Gutenberg has some limitation that we can't make some rules such as for categories or taxonomies work.

### 1.6.3

Fixed outside conditions not working.

### 1.6.2

Fixed for outside conditions and front-end forms (since the same meta box can be inserted in multiple forms in the front end).

### 1.6.1

Added minor improvements for conditional checks, especially for cloneable groups.

### 1.6.0

- Improved speed of conditional checks. Work faster for cloning groups with many conditions. In our tests, the speed increases up to 4 times than before.
- `required` fields no longer prevent from submitting when they're hidden. E.g., when a field is hidden, its `required` attribute takes no effect and users still can submit posts.

### 1.5.8

- Fixed getting selector incorrectly if there are another field that contain current field name
- Added trigger `cl_hide` for inputs when they're hid, allowing developers to clear their values if needed.

### 1.5.7

- Fixed empty selector that causes error in the console.
- Fixed docs for custom taxonomies.

### 1.5.6

Fixed not showing/hiding a meta box

### 1.5.5

Fixed when some select fields have the same parts that break logic for select tree.

### 1.5.4

Initialize the conditional checks only when page finishes loading to improve the performance.

### 1.5.3

Check for featured image is now supported via `_thumbnail_id` parameter. See docs for usage.

### 1.5.2

Fixed conditions don't work with "select_tree" for taxonomy/taxonomy_advanced field.

### 1.5.1

Fix: Apply the conditional logic to new cloned group.

### 1.5

- Support Frontend Submission
- Add `slide` and `fade` animation via toggle_type

### 1.4.1

Compatibility with Group 1.2+

### 1.4

- Use `display: none;` instead of `visibility: hidden` for field by default. You can configure that value by using `'toggle_type' => 'visibility'` or `'toggle_type' => 'display'`
- Remove .min.js
- Use `admin_enqueue_scripts` instead of `rwmb_enqueue_scripts` since it can work on any admin area.

### 1.3.4

Fix: Scope of dependence fields between groups

### 1.3.3

Improvement: Users don't have to define MBC_JS_URL to load JS file anymore

### 1.3.2

Fix: Conditional logic doesn't works if event source and field is located in different group levels

### 1.3.1

- New: Load uncompressed JS when `WP_DEBUG` is set to `TRUE`
- Improvement: Add prepare statement for `MB_Conditional_Logic::slug_to_id()` method
- Fix: Sub Sub Sub field's conditional logic doesn't works.

### 1.3

- New feature: Supports slug for post categories and custom taxonomies
- New feature: Supports function callback
- Improvement: Better supports Custom Taxonomy
- Improvement: Better `in` operator, now supports compare array to array. Thanks MikeTrebilcock.
- Improvement: Add minified script `conditional-logic.min.js` and use it by default.
- Bug fixes: Only hide content inside columns, not remove whole columns, issue [#766](https://github.com/rilwis/meta-box/issues/766). Thanks wgstjf.

### 1.2

- Improvement: Compatibility with MB Settings Page
- Improvement: Compatibitity with Group, Heading and Html
- Bug fixes: Conditional Logic doesn't works with two or more fields has similar name [Issue #708](https://github.com/rilwis/meta-box/issues/708)

### 1.1

New feature: Allows user put any selector instead of ID by default

### 1.0.8

- Bug fixes: Compatibility with latest Meta Box
- Improve performance
- Change operators: from `start_with` to `starts with`, from `end_with` to `ends with`

### 1.0.7

- Bug fixes: Cannot hide image_select, file_advanced fields
- Improve performance

### 1.0.6

Bug fixes: Remove PHP notices

### 1.0.5

Bug fixes: Support `image_select` field

### 1.0.4

- Improvements: Allows shorter syntax for fields outside Meta Box
- Bug fixes: No action with Select, Select Advanced

### 1.0.3

Bug fixes: Only works with Group when has interaction

### 1.0.2

Bug fixes: Cannot works Group extension

### 1.0.1

- Bug fixes: Cannot works with PHP < 5.4
- Bug fixes: Cannot hide dependent fields
- Bug fixes: Cannot hide cloned fields