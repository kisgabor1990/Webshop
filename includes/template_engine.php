<?php

define('TEMPLATES_DIR', './templates/');

/**
 * @param string $template_name
 * @param array $data
 */
function show_template(string $template_name, array $data = []): void {
    // tartalom megjelenítése
    echo parse_template($template_name, $data);
}

/**
 * @param string $template_name
 * @param array $data
 * @return string
 */
function parse_template(string $template_name, array $data = []): string {
    // template_name betöltése
    $template = file_get_contents(TEMPLATES_DIR . $template_name . '.html');

    // esetleges változók kinyerése
    preg_match_all('/\{\$([^}]*)\}/', $template, $matches);

    // változók behelyettesítése
    foreach ($matches[0] as $key => $match) {
        if (str_contains($matches[1][$key], '.')) {
            $indexes = explode('.', $matches[1][$key]);

            if ($indexes[1] == 'price') {
                $variable_data = number_format($data[$indexes[0]][$indexes[1]], 0, ',', '.');
            } else {
                $variable_data = $data[$indexes[0]][$indexes[1]];
            }
        } else {
            $variable_data = $data[$matches[1][$key]];
        }
        $template = str_replace($match, $variable_data, $template);
    }
    return $template;
}
