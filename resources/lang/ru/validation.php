<?php declare(strict_types=1);

return [
    'accepted'             => 'Поле «:attribute» должно быть принято.',
    'accepted_if'          => 'Поле «:attribute» должно быть принято when :other is :value.',
    'active_url'           => 'Поле «:attribute» должно быть a valid URL.',
    'after'                => 'Поле «:attribute» должно быть a date after :date.',
    'after_or_equal'       => 'Поле «:attribute» должно быть a date after or equal to :date.',
    'alpha'                => 'Поле «:attribute» must only contain letters.',
    'alpha_dash'           => 'Поле «:attribute» must only contain letters, numbers, dashes, and underscores.',
    'alpha_num'            => 'Поле «:attribute» must only contain letters and numbers.',
    'array'                => 'Поле «:attribute» должно быть an array.',
    'ascii'                => 'Поле «:attribute» must only contain single-byte alphanumeric characters and symbols.',
    'before'               => 'Поле «:attribute» должно быть a date before :date.',
    'before_or_equal'      => 'Поле «:attribute» должно быть a date before or equal to :date.',
    'between'              => [
        'array'   => 'Поле «:attribute» должно быть между :min и :max.',
        'file'    => 'Поле «:attribute» должно быть больше :min и меньше :max Килобайт.',
        'numeric' => 'Поле «:attribute» должно быть между :min and :max.',
        'string'  => 'Поле «:attribute» должно содержать от :min до :max символов.',
    ],
    'boolean'              => 'Поле «:attribute» должно быть true or false.',
    'can'                  => 'Поле «:attribute» contains an unauthorized value.',
    'confirmed'            => 'Поле «:attribute» не совпадает с полем подтверждения.',
    'current_password'     => 'Поле «password» указано неверно.',
    'date'                 => 'Поле «:attribute» должно быть датой.',
    'date_equals'          => 'Поле «:attribute» должно с a date equal to :date.',
    'date_format'          => 'Поле «:attribute» must match Поле «format :format.',
    'decimal'              => 'Поле «:attribute» must have :decimal decimal places.',
    'declined'             => 'Поле «:attribute» должно быть declined.',
    'declined_if'          => 'Поле «:attribute» должно быть declined when :other is :value.',
    'different'            => 'Поле «:attribute» and :other должно быть different.',
    'digits'               => 'Поле «:attribute» должно быть :digits digits.',
    'digits_between'       => 'Поле «:attribute» должно быть between :min and :max digits.',
    'dimensions'           => 'Поле «:attribute» has invalid image dimensions.',
    'distinct'             => 'Поле «:attribute» has a duplicate value.',
    'doesnt_end_with'      => 'Поле «:attribute» must not end with one of Поле «following: :values.',
    'doesnt_start_with'    => 'Поле «:attribute» must not start with one of Поле «following: :values.',
    'email'                => 'Поле «:attribute» должно быть a valid email address.',
    'ends_with'            => 'Поле «:attribute» must end with one of Поле «following: :values.',
    'enum'                 => 'Поле «selected :attribute is invalid.',
    'exists'               => 'Поле «selected :attribute is invalid.',
    'extensions'           => 'Поле «:attribute» must have one of Поле «following extensions: :values.',
    'file'                 => 'Поле «:attribute» должно быть a file.',
    'filled'               => 'Поле «:attribute» must have a value.',
    'gt'                   => [
        'array'   => 'Поле «:attribute» must have more than :value items.',
        'file'    => 'Поле «:attribute» должно быть greater than :value kilobytes.',
        'numeric' => 'Поле «:attribute» должно быть greater than :value.',
        'string'  => 'Поле «:attribute» должно быть greater than :value characters.',
    ],
    'gte'                  => [
        'array'   => 'Поле «:attribute» must have :value items or more.',
        'file'    => 'Поле «:attribute» должно быть greater than or equal to :value kilobytes.',
        'numeric' => 'Поле «:attribute» должно быть greater than or equal to :value.',
        'string'  => 'Поле «:attribute» должно быть greater than or equal to :value characters.',
    ],
    'hex_color'            => 'Поле «:attribute» должно быть a valid hexadecimal color.',
    'image'                => 'Поле «:attribute» должно быть an image.',
    'in'                   => 'Поле «selected :attribute is invalid.',
    'in_array'             => 'Поле «:attribute» must exist in :other.',
    'integer'              => 'Поле «:attribute» должно быть an integer.',
    'ip'                   => 'Поле «:attribute» должно быть a valid IP address.',
    'ipv4'                 => 'Поле «:attribute» должно быть a valid IPv4 address.',
    'ipv6'                 => 'Поле «:attribute» должно быть a valid IPv6 address.',
    'json'                 => 'Поле «:attribute» должно быть a valid JSON string.',
    'lowercase'            => 'Поле «:attribute» должно быть lowercase.',
    'lt'                   => [
        'array'   => 'Поле «:attribute» must have less than :value items.',
        'file'    => 'Поле «:attribute» должно быть less than :value kilobytes.',
        'numeric' => 'Поле «:attribute» должно быть less than :value.',
        'string'  => 'Поле «:attribute» должно быть less than :value characters.',
    ],
    'lte'                  => [
        'array'   => 'Поле «:attribute» must not have more than :value items.',
        'file'    => 'Поле «:attribute» должно быть less than or equal to :value kilobytes.',
        'numeric' => 'Поле «:attribute» должно быть less than or equal to :value.',
        'string'  => 'Поле «:attribute» должно быть less than or equal to :value characters.',
    ],
    'mac_address'          => 'Поле «:attribute» должно быть a valid MAC address.',
    'max'                  => [
        'array'   => 'Поле «:attribute» must not have more than :max items.',
        'file'    => 'Поле «:attribute» must not be greater than :max kilobytes.',
        'numeric' => 'Поле «:attribute» must not be greater than :max.',
        'string'  => 'Поле «:attribute» не должно содержать больше :max символов.',
    ],
    'max_digits'           => 'Поле «:attribute» must not have more than :max digits.',
    'mimes'                => 'Поле «:attribute» должно быть a file of type: :values.',
    'mimetypes'            => 'Поле «:attribute» должно быть a file of type: :values.',
    'min'                  => [
        'array'   => 'Поле «:attribute» must have at least :min items.',
        'file'    => 'Поле «:attribute» должно быть at least :min kilobytes.',
        'numeric' => 'Поле «:attribute» должно быть не меньше :min.',
        'string'  => 'Поле «:attribute» должно содержать не менее :min символов.',
    ],
    'min_digits'           => 'Поле «:attribute» must have at least :min digits.',
    'missing'              => 'Поле «:attribute» должно быть missing.',
    'missing_if'           => 'Поле «:attribute» должно быть missing when :other is :value.',
    'missing_unless'       => 'Поле «:attribute» должно быть missing unless :other is :value.',
    'missing_with'         => 'Поле «:attribute» должно быть missing when :values is present.',
    'missing_with_all'     => 'Поле «:attribute» должно быть missing when :values are present.',
    'multiple_of'          => 'Поле «:attribute» должно быть a multiple of :value.',
    'not_in'               => 'Поле «selected :attribute is invalid.',
    'not_regex'            => 'Поле «:attribute» format is invalid.',
    'numeric'              => 'Поле «:attribute» должно быть a number.',
    'password'             => [
        'letters'       => 'Поле «:attribute» must contain at least one letter.',
        'mixed'         => 'Поле «:attribute» must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'Поле «:attribute» must contain at least one number.',
        'symbols'       => 'Поле «:attribute» must contain at least one symbol.',
        'uncompromised' => 'Поле «given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'present'              => 'Поле «:attribute» должно быть present.',
    'present_if'           => 'Поле «:attribute» должно быть present when :other is :value.',
    'present_unless'       => 'Поле «:attribute» должно быть present unless :other is :value.',
    'present_with'         => 'Поле «:attribute» должно быть present when :values is present.',
    'present_with_all'     => 'Поле «:attribute» должно быть present when :values are present.',
    'prohibited'           => 'Поле «:attribute» is prohibited.',
    'prohibited_if'        => 'Поле «:attribute» is prohibited when :other is :value.',
    'prohibited_unless'    => 'Поле «:attribute» is prohibited unless :other is in :values.',
    'prohibits'            => 'Поле «:attribute» prohibits :other from being present.',
    'regex'                => 'Поле «:attribute» неверного формата.',
    'required'             => 'Поле «:attribute» является обязательным.',
    'required_array_keys'  => 'Поле «:attribute» must contain entries for: :values.',
    'required_if'          => 'Поле «:attribute» является обязательным when :other is :value.',
    'required_if_accepted' => 'Поле «:attribute» является обязательным when :other is принято.',
    'required_unless'      => 'Поле «:attribute» является обязательным unless :other is in :values.',
    'required_with'        => 'Поле «:attribute» является обязательным when :values is present.',
    'required_with_all'    => 'Поле «:attribute» является обязательным when :values are present.',
    'required_without'     => 'Поле «:attribute» является обязательным when :values is not present.',
    'required_without_all' => 'Поле «:attribute» является обязательным when none of :values are present.',
    'same'                 => 'Поле «:attribute» must match :other.',
    'size'                 => [
        'array'   => 'Поле «:attribute» must contain :size items.',
        'file'    => 'Поле «:attribute» должно быть :size kilobytes.',
        'numeric' => 'Поле «:attribute» должно быть :size.',
        'string'  => 'Поле «:attribute» должно быть :size characters.',
    ],
    'starts_with'          => 'Поле «:attribute» must start with one of Поле «following: :values.',
    'string'               => 'Поле «:attribute» должно быть a string.',
    'timezone'             => 'Поле «:attribute» должно быть a valid timezone.',
    'unique'               => 'Поле «:attribute has already been taken.',
    'uploaded'             => 'Поле «:attribute failed to upload.',
    'uppercase'            => 'Поле «:attribute» должно быть uppercase.',
    'url'                  => 'Поле «:attribute» должно быть a valid URL.',
    'ulid'                 => 'Поле «:attribute» должно быть a valid ULID.',
    'uuid'                 => 'Поле «:attribute» должно быть a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name Поле «lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Поле «following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'login'    => 'Эл.почта',
        'password' => 'Пароль',
    ],
];
