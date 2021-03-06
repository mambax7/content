<?php

/**
 * @return array
 */
function site_block_horz_dhtml_nav()
{
    $myList = '';
    global $xoopsDB, $xoopsModule, $xoopsTpl, $_GET, $xoopsUser, $xoopsConfig;
    //-------------- Modules --------------
    $menuModule         = [];
    $moduleHandler     = xoops_getHandler('module');
    $criteria           = new CriteriaCompo(new Criteria('hasmain', 1));
    $criteria->add(new Criteria('weight', 0, '>'));
    $criteria->add(new Criteria('isactive', 1));
    $modules            = $moduleHandler->getObjects($criteria, true);
    $modulepermHandler = xoops_getHandler('groupperm');
    $groups             = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $read_allowed       = $modulepermHandler->getItemIds('module_read', $groups);
    foreach (array_keys($modules) as $i) {
        if (in_array($i, $read_allowed)) {
            $menuModule[$i]['title']    = $modules[$i]->getVar('name');
            $menuModule[$i]['url']      = XOOPS_URL . '/modules/' . $modules[$i]->getVar('dirname') . '/';
            $menuModule[$i]['priority'] = $modules[$i]->getVar('weight');
            $menuModule[$i]['id']       = $modules[$i]->getVar('id');
            $menuModule[$i]['type']     = 'module';
            $sublinks                   = $modules[$i]->subLink();
            if (count($sublinks) > 0) {
                foreach ($sublinks as $sublink) {
                    $menuModule[$i]['sublinks'][] = [
                                          'title' => $sublink['name'],
                                          'url'   => XOOPS_URL . '/modules/' . $modules[$i]->getVar('dirname') . '/' . $sublink['url']
                                          ];
                }
            } else {
                $menuModule[$i]['sublinks'] = [];
            }
        }
    }

    //-------------- Content --------------
    $result = $xoopsDB->query("SELECT *, blockid AS priority, 'content' AS type FROM "
                      . $xoopsDB->prefix('content')
                              . ' WHERE visible = 1 ORDER BY blockid');
    $contentItems          = [];
    $groupPermHandler      = xoops_getHandler('groupperm');
    $module                = $moduleHandler->getByDirname('content');
    $xoopsUser ? $groups = $xoopsUser->getGroups() : $groups = XOOPS_GROUP_ANONYMOUS;
    $allowedItems          = $groupPermHandler->getItemIds('content_page_view', $groups, $module->getVar('mid'));
    while ($tcontent        = $xoopsDB->fetchArray($result)) {
        if (in_array($tcontent['storyid'], $allowedItems)) {
            $contentItems[] = $tcontent;
        }
    }

    $menu = array_merge($menuModule, return_children($contentItems, 0));

    foreach ($menu as $key => $row) {
        $priority[$key]  = $row['priority'];
    }

    array_multisort($priority, SORT_ASC, $menu);
    $block             = [];
    $block['ct_depth'] = 0;
    $block['ct_menu']  = print_menu($menu, $contentItems, 0, $block['ct_depth']);
    return $block;
}

/**
 * @param $items
 * @param $parent_id
 * @return array
 */
function return_children($items, $parent_id)
{
    $myItems = [];
    foreach ($items as $item) {
        if ($item['parent_id'] == $parent_id) {
            $myItems[] = $item;
        }
    }
    return $myItems;
}

/**
 * @param $menuItems
 * @param $fullList
 * @param $level
 * @param $depth
 * @return string
 */
function print_menu($menuItems, $fullList, $level, $depth)
{
    $myList = '';
    if ($level + 1 > $depth) {
        $depth = $level + 1;
    }
    if (0 == $level) {
        $myList .= '<ul id="menu-h" class="horizontal">';
    }
    foreach ($menuItems as $menuItem) {
        $currentPosition = '';
        $currentPosition++;
        if ('content' === $menuItem['type']) {
            if ($menuItem['address'] && 1 != $menuItem['link']) {
                $contentURL = $menuItem['address'];
            } else {
                $contentURL = XOOPS_URL . '/modules/content/index.php?id=' . $menuItem['storyid'];
            }
        } else {
            $contentURL = $menuItem['url'];
        }
        
        $myList .= "\n\t<li";
        if (0 == $level) {
            $myList .= ' class="nav-item-' . $currentPosition . '"';
        }
        $myList .= '><a href="' . $contentURL . '">' . $menuItem['title'] . '</a>';
        if ('content' === $menuItem['type']) {
            if (return_children($fullList, $menuItem['storyid'])) {
                $myList .= '<ul>' . print_menu(return_children($fullList, $menuItem['storyid']), $fullList, $level + 1, $depth) . '</ul>';
            }
        } else {
            if ($menuItem['sublinks']) {
                $myList .= "<ul>\n";
                foreach ($menuItem['sublinks'] as $sublink) {
                    $myList .= '<li><a href="' . $sublink['url'] . '">' . $sublink['title'] . "</a></li>\n";
                }
                $myList .= "</ul>\n";
            }
        }
        $myList .= "</li>\n";
    }
    if (0 == $level) {
        $myList .= '</ul>';
    }
    return $myList;
}
