<?php

if (! function_exists('tournamentClassNameByType')) {
    function tournamentClassNameByType(string $type) {
        return "App\\Models\\Tournament\\".ucfirst(strtolower($type))."Tournament";
    }
}