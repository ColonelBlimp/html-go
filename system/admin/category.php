<?php declare(strict_types=1);

use html_go\exceptions\InternalException;
use html_go\model\Config;

/**
 * Returns an admin content <b>view</b> object for <i>categories</i>.
 * @param array<mixed> $args
 * @return \stdClass
 */
function get_category_listview_content_object(array $args): \stdClass {
    return get_admin_content_object(
        'admin.dashboard.title',
        'admin-list.html',
        CATEGORY_SECTION,
        ADMIN_ACTION_VIEW,
        $args,
        get_categories(get_pagination_pagenumber(), get_config()->getInt(Config::KEY_ADMIN_ITEMS_PER_PAGE)));
}

/**
 * Returns an admin content <b>add</b> object for <i>categories</i>.
 * @param array<mixed> $args
 * @return \stdClass
 */
function get_category_add_content_object(array $args = []): \stdClass {
    return get_admin_content_object(
        'admin.dashboard.title',
        'admin-action.html',
        CATEGORY_SECTION,
        ADMIN_ACTION_ADD,
        $args);
}

/**
 * Edit wrapper for <code>get_category_editdelete_object</code> function.
 * @param array<mixed> $args
 * @return \stdClass
 */
function get_category_edit_object(array $args): \stdClass {
    return get_category_editdelete_object(ADMIN_ACTION_EDIT, $args);
}

/**
 * Delete wrapper for <code>get_category_editdelete_object</code> function.
 * @param array<mixed> $args
 * @return \stdClass
 */
function get_category_delete_object(array $args): \stdClass {
    return get_category_editdelete_object(ADMIN_ACTION_DELETE, $args);
}

/**
 * Return an admin content <b>edit/delete</b> object for <i>categories</i>.
 * @param string $action Must be either <code>edit</code> or <code>delete</code>
 * @param array<mixed> $args
 * @throws InvalidArgumentException
 * @throws InternalException
 * @return \stdClass
 */
function get_category_editdelete_object(string $action, array $args): \stdClass {
    if (\strpos($action, ADMIN_ACTION_EDIT) === false && \strpos($action, ADMIN_ACTION_DELETE) === false) {
        throw new InvalidArgumentException("Unknown action value [$action]");
    }
    if (empty($args[ID_STR])) {
        throw new InvalidArgumentException("The args array must contain an 'id' key.");
    }
    $manager = get_index_manager();
    if ($manager->elementExists($args[ID_STR]) === false) {
        $id = $args[ID_STR];
        throw new InternalException("Element does not exist [$id]");
    }
    $element = get_model_factory()->createContentObject($manager->getElementFromSlugIndex($args[ID_STR]));
    $params = [
        'title' => get_i18n()->getText('admin.dashboard.title'),
        'template' => 'admin-action.html',
        'context' => get_config()->getString(Config::KEY_ADMIN_CONTEXT),
        'section' => CATEGORY_SECTION,
        'list' => [$element],
        'action' => $action,
    ];
    return get_model_factory()->createAdminContentObject(\array_merge($args, $params));
}

/**
 * Persist a new category.
 * @param array<mixed> $formData Passed by reference, as the form data will be modified
 * and should maintain the modifications in case of validation failure.
 * @return bool <code>true</code> if the successful, otherwise <code>false</code>.
 */
function save_category(array &$formData): bool {
    $formData['errorKey'] = '';
    if (empty($formData['title'])) {
        $formData['errorfield'] = 'title';
        return false;
    }
    if (empty($formData['description'])) {
        $desc = \substr($formData['body'], 0, get_config()->getInt(Config::KEY_DESCRIPTION_LEN));
        $pos = \strpos($desc, '.', -1);
        if ($pos === false) {
            $pos = get_config()->getInt(Config::KEY_DESCRIPTION_LEN);
        }
        $formData['description'] = \substr($desc, 0, $pos + 1);
    }
    if (empty($formData[ADMIN_KEY_STR]) || $formData[ADMIN_KEY_STR] === 'category/') {
        $formData[ADMIN_KEY_STR] = 'category/'.URLify::slug($formData['title']);
    }
    if (get_index_manager()->elementExists( $formData[ADMIN_KEY_STR])) {
        $formData['fielderror'] = ADMIN_KEY_STR;
        return false;
    }

    save_content(CATEGORY_SECTION, $formData);
    return true;
}
