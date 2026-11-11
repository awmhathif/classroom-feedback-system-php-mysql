<?php
// Small functions used on many pages.

// ===== Showing things =====

// Makes text safe to put in HTML (stops people adding their own HTML or scripts)
function e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, "UTF-8");
}

// plural(1, "student") = "1 student", plural(2, "class", "classes") = "2 classes"
function plural($count, $word, $many = null)
{
    return $count . " " . ($count == 1 ? $word : ($many ?? $word . "s"));
}
