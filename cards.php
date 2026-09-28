<?php
// Cards extension, https://github.com/pfadfinder26/yellow-cards
// Based on Datenstrom Yellow, https://datenstrom.se/yellow/

class YellowCards {
    const VERSION = "0.2.3";
    public $yellow;         // access to API
    public $depth;          // nesting level, a card within a card

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->depth = 0;
    }

    // Handle page content element
    public function onParseContentElement($page, $name, $text, $attributes, $type) {
        $output = null;
        if (($name=="card" || $name=="cards") && ($type=="block" || $type=="inline")) {
            $arguments = $this->yellow->toolbox->getTextArguments($text);
            list($location, $template) = $arguments;
            $options = array_values(array_filter(array_slice($arguments, 2)));
            $showUnlisted = in_array("unlisted", $options);
            $filters = array_filter($options, function ($option) { return strposu($option, ":")!==false; });
            if (is_string_empty($template)) $template = "default";
            $location = $this->getLocation($page, $location);
            if ($name=="card") {
                $pageCard = $this->yellow->content->find($location);
                if (is_null($pageCard)) return $this->getErrorHtml($location);
                $output = $this->getCardHtml($pageCard, $template, $options);
            } else {
                $pages = $this->yellow->content->getChildren($location, $showUnlisted);
                if ($this->yellow->content->find($location)===null) return $this->getErrorHtml($location);
                $pages = $this->getPagesSelected($pages, $filters, in_array("scheduled", $options));
                $class = in_array("scroll", $options) ? "cards cards-scroll" : "cards";
                $output = "<div class=\"".$class."\">\n";
                foreach ($pages as $pageCard) {
                    $output .= $this->getCardHtml($pageCard, $template, $options);
                }
                $output .= "</div>\n";
            }
        }
        return $output;
    }

    // Return the pages of a card row, filtered, sorted and limited
    // a page with a publication date in the future waits for that date
    public function getPagesSelected($pages, $filters, $showScheduled = false) {
        $selected = new YellowPageCollection($this->yellow);
        foreach ($pages as $pageCard) {
            if (!$showScheduled && $this->isScheduled($pageCard)) continue;
            if ($this->isMatchingAll($pageCard, $filters)) $selected->append($pageCard);
        }
        foreach ($filters as $filter) {
            list($key, $value) = $this->yellow->toolbox->getTextList($filter, ":", 2);
            if ($key=="sort" && !is_string_empty($value)) $selected->sort($value, false);
        }
        if ($showScheduled) $selected = $this->getPagesAhead($selected);
        foreach ($filters as $filter) {
            list($key, $value) = $this->yellow->toolbox->getTextList($filter, ":", 2);
            if ($key=="limit" && is_numeric($value)) $selected->limit(intval($value));
        }
        return $selected;
    }

    // Return the pages with the ones that are not published yet in front, the nearest date last,
    // so a row reads from what is furthest ahead through today into the past
    public function getPagesAhead($pages) {
        $selected = new YellowPageCollection($this->yellow);
        $ahead = $rest = array();
        foreach ($pages as $pageCard) {
            if ($this->isScheduled($pageCard)) {
                $ahead[] = $pageCard;
            } else {
                $rest[] = $pageCard;
            }
        }
        usort($ahead, function ($a, $b) {
            return strtotime($b->get("published"))<=>strtotime($a->get("published"));
        });
        foreach (array_merge($ahead, $rest) as $pageCard) $selected->append($pageCard);
        return $selected;
    }

    // Check if a page is not published yet
    public function isScheduled($pageCard) {
        $published = $pageCard->get("published");
        return !is_string_empty($published) && strtotime($published)>time();
    }

    // Check if a page matches all filters, written as setting:value
    public function isMatchingAll($pageCard, $filters) {
        foreach ($filters as $filter) {
            list($key, $value) = $this->yellow->toolbox->getTextList($filter, ":", 2);
            if (is_string_empty($key) || $key=="sort" || $key=="limit") continue;
            if (!$this->isMatching($pageCard->get($key), $value)) return false;
        }
        return true;
    }

    // Check if a page setting matches the filter, a setting can hold a list
    public function isMatching($setting, $value) {
        if ($value=="*") return !is_string_empty($setting);
        $tokens = array_map("trim", explode(",", strtoloweru($setting)));
        return in_array(strtoloweru($value), $tokens);
    }

    // Return card HTML, from a layout named card-<template>
    public function getCardHtml($pageCard, $template, $options = array()) {
        if ($this->depth>=2) return "";
        ++$this->depth;
        $pageCard->parseContent();
        ob_start();
        $this->yellow->layout("card-".$this->yellow->lookup->normaliseName($template), $pageCard, $template, $options);
        --$this->depth;
        return ob_get_clean();
    }

    // Return location of the requested page, relative to the current page
    public function getLocation($page, $location) {
        if (is_string_empty($location)) $location = $page->location;
        if (substru($location, 0, 1)!="/") $location = $page->location.$location;
        return $location;
    }

    // Return error message for authors
    public function getErrorHtml($location) {
        return "<p class=\"error\">Cards: page '".htmlspecialchars($location)."' does not exist!</p>\n";
    }
}
