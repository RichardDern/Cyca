<?php

return PhpCsFixer\Config::create()
    ->setRules([
        '@PhpCsFixer'                               => true,
        'array_indentation'                         => true,
        'array_syntax'                              => ['syntax' => 'short'],
        'combine_consecutive_unsets'                => true,
        'method_separation'                         => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'single_quote'                              => true,
        'binary_operator_spaces'                    => [
            'align_double_arrow' => true,
            'align_equals'       => true,
        ],
        'declare_equal_normalize' => [
            'space' => 'single',
        ],
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false]
    ]);
