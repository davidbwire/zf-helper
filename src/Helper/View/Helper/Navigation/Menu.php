<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\View\Helper\Navigation;

use Zend\View\Helper\Navigation\Menu as MenuHelper;
use RecursiveIteratorIterator;
use Zend\Navigation\AbstractContainer;

/**
 * Description of Menu
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class Menu extends MenuHelper
{

    /**
     * Renders the deepest active menu within [$minDepth, $maxDepth], (called
     * from {@link renderMenu()})
     *
     * @param  AbstractContainer $container          container to render
     * @param  string            $ulClass            CSS class for first UL
     * @param  string            $indent             initial indentation
     * @param  int|null          $minDepth           minimum depth
     * @param  int|null          $maxDepth           maximum depth
     * @param  bool              $escapeLabels       Whether or not to escape the labels
     * @param  bool              $addClassToListItem Whether or not page class applied to <li> element
     * @param  string            $liActiveClass      CSS class for active LI
     * @return string
     */
    protected function renderDeepestMenu(
    AbstractContainer $container, $ulClass, $indent, $minDepth, $maxDepth,
            $escapeLabels, $addClassToListItem, $liActiveClass
    )
    {
        if (!$active = $this->findActive($container, $minDepth - 1, $maxDepth)) {
            return '';
        }

        // special case if active page is one below minDepth
        if ($active['depth'] < $minDepth) {
            if (!$active['page']->hasPages(!$this->renderInvisible)) {
                return '';
            }
        } elseif (!$active['page']->hasPages(!$this->renderInvisible)) {
            // found pages has no children; render siblings
            $active['page'] = $active['page']->getParent();
        } elseif (is_int($maxDepth) && $active['depth'] + 1 > $maxDepth) {
            // children are below max depth; render siblings
            $active['page'] = $active['page']->getParent();
        }

        /* @var $escaper \Zend\View\Helper\EscapeHtmlAttr */
        $escaper = $this->view->plugin('escapeHtmlAttr');
        $ulClass = $ulClass ? ' class="' . $escaper($ulClass) . '"' : '';
        $html = $indent . '<ul' . $ulClass . '>' . PHP_EOL;

        foreach ($active['page'] as $subPage) {
            if (!$this->accept($subPage)) {
                continue;
            }

            // render li tag and page
            $liClasses = array();
            // Is page active?
            if ($subPage->isActive(true)) {
                $liClasses[] = $liActiveClass;
            }
            // Add CSS class from page to <li>
            if ($addClassToListItem && $subPage->getClass()) {
                $liClasses[] = $subPage->getClass();
            }
            $liClass = empty($liClasses) ? '' : ' class="' . $escaper(implode(' ',
                                    $liClasses)) . '"';
            $ngClass = '';
            if ($subPage->get('ng-class')) {
                $ngClass = 'ng-class="' . $subPage->get('ng-class') . '"';
            }
            $html .= $indent . '    <li' . $liClass . ' ' . $ngClass . ' >' . PHP_EOL;
            $html .= $indent . '        ' . $this->htmlify($subPage,
                            $escapeLabels, $addClassToListItem) . PHP_EOL;
            $html .= $indent . '    </li>' . PHP_EOL;
        }

        $html .= $indent . '</ul>';

        return $html;
    }

    /**
     * Renders a normal menu (called from {@link renderMenu()})
     *
     * @param  AbstractContainer $container          container to render
     * @param  string            $ulClass            CSS class for first UL
     * @param  string            $indent             initial indentation
     * @param  int|null          $minDepth           minimum depth
     * @param  int|null          $maxDepth           maximum depth
     * @param  bool              $onlyActive         render only active branch?
     * @param  bool              $escapeLabels       Whether or not to escape the labels
     * @param  bool              $addClassToListItem Whether or not page class applied to <li> element
     * @param  string            $liActiveClass      CSS class for active LI
     * @return string
     */
    protected function renderNormalMenu(
    AbstractContainer $container, $ulClass, $indent, $minDepth, $maxDepth,
            $onlyActive, $escapeLabels, $addClassToListItem, $liActiveClass
    )
    {
        $html = '';

        // find deepest active
        $found = $this->findActive($container, $minDepth, $maxDepth);
        /* @var $escaper \Zend\View\Helper\EscapeHtmlAttr */
        $escaper = $this->view->plugin('escapeHtmlAttr');

        if ($found) {
            $foundPage = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator($container,
                RecursiveIteratorIterator::SELF_FIRST);
        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $isActive = $page->isActive(true);
            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibility
                continue;
            } elseif ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = false;
                if ($foundPage) {
                    if ($foundPage->hasPage($page)) {
                        // accept if page is a direct child of the active page
                        $accept = true;
                    } elseif ($foundPage->getParent()->hasPage($page)) {
                        // page is a sibling of the active page...
                        if (!$foundPage->hasPages(!$this->renderInvisible) ||
                                is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
                            // accept if active page has no children, or the
                            // children are too deep to be rendered
                            $accept = true;
                        }
                    }
                }

                if (!$accept) {
                    continue;
                }
            }

            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = $indent . str_repeat('        ', $depth);

            if ($depth > $prevDepth) {
                // start new ul tag
                if ($ulClass && $depth == 0) {
                    $ulClass = ' class="' . $escaper($ulClass) . '"';
                } else {
                    $ulClass = '';
                }
                $html .= $myIndent . '<ul' . $ulClass . '>' . PHP_EOL;
            } elseif ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . PHP_EOL;
                    $html .= $ind . '</ul>' . PHP_EOL;
                }
                // close previous li tag
                $html .= $myIndent . '    </li>' . PHP_EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . PHP_EOL;
            }

            // render li tag and page
            $liClasses = array();
            // Is page active?
            if ($isActive) {
                $liClasses[] = $liActiveClass;
            }
            // Add CSS class from page to <li>
            if ($addClassToListItem && $page->getClass()) {
                $liClasses[] = $page->getClass();
            }
            $liClass = empty($liClasses) ? '' : ' class="' . $escaper(implode(' ',
                                    $liClasses)) . '"';
            $ngClass = '';
            if ($page->get('ng-class')) {
                $ngClass = 'ng-class="' . $page->get('ng-class') . '"';
            }
            $html .= $myIndent . '    <li' . $liClass . ' ' . $ngClass . ' >' . PHP_EOL
                    . $myIndent . '        ' . $this->htmlify($page,
                            $escapeLabels, $addClassToListItem) . PHP_EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth + 1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i - 1);
                $html .= $myIndent . '    </li>' . PHP_EOL
                        . $myIndent . '</ul>' . PHP_EOL;
            }
            $html = rtrim($html, PHP_EOL);
        }

        return $html;
    }

}
