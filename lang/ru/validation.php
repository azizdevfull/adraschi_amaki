<?php

return [

    'accepted' => 'Вы должны принять :attribute.',
    'accepted_if' => 'Вы должны принять :attribute, когда :other равно :value.',
    'active_url' => 'Поле :attribute содержит недействительный URL.',
    'after' => 'Поле :attribute должно содержать дату после :date.',
    'after_or_equal' => 'Поле :attribute должно содержать дату после или равную :date.',
    'alpha' => 'Поле :attribute может содержать только буквы.',
    'alpha_dash' => 'Поле :attribute может содержать только буквы, цифры, дефисы и подчеркивания.',
    'alpha_num' => 'Поле :attribute может содержать только буквы и цифры.',
    'array' => 'Поле :attribute должно быть массивом.',
    'before' => 'Поле :attribute должно содержать дату до :date.',
    'before_or_equal' => 'Поле :attribute должно содержать дату до или равную :date.',
    'between' => [
        'array' => 'Поле :attribute должно содержать от :min до :max элементов.',
        'file' => 'Размер файла в поле :attribute должен быть от :min до :max килобайт.',
        'numeric' => 'Поле :attribute должно быть между :min и :max.',
        'string' => 'Количество символов в поле :attribute должно быть от :min до :max.',
    ],
    'boolean' => 'Поле :attribute должно иметь значение true или false.',
    'confirmed' => 'Поле :attribute не совпадает с подтверждением.',
    'current_password' => 'Неверный пароль.',
    'date' => 'Поле :attribute не является датой.',
    'date_equals' => 'Поле :attribute должно быть датой, равной :date.',
    'date_format' => 'Поле :attribute не соответствует формату :format.',
    'decimal' => 'Поле :attribute должно содержать :decimal десятичных знаков.',
    'declined' => 'Поле :attribute должно быть отклонено.',
    'declined_if' => 'Поле :attribute должно быть отклонено, когда :other равно :value.',
    'different' => 'Поля :attribute и :other должны различаться.',
    'digits' => 'Поле :attribute должно содержать :digits цифр.',
    'digits_between' => 'Поле :attribute должно содержать от :min до :max цифр.',
    'dimensions' => 'Поле :attribute имеет недопустимые размеры изображения.',
    'distinct' => 'Поле :attribute содержит повторяющееся значение.',
    'doesnt_end_with' => 'Поле :attribute не должно заканчиваться на одно из следующих значений: :values.',
    'doesnt_start_with' => 'Поле :attribute не должно начинаться c одного из следующих значений: :values.',
    'email' => 'Поле :attribute должно быть действительным адресом электронной почты.',
    'ends_with' => 'Поле :attribute должно заканчиваться одним из следующих значений: :values.',
    'enum' => 'Выбранное значение для :attribute недопустимо.',
    'exists' => 'Выбранное значение для :attribute недопустимо.',
    'file' => 'Поле :attribute должно быть файлом.',
    'filled' => 'Поле :attribute должно иметь значение.',
    'gt' => [
        'array' => 'Поле :attribute должно содержать более :value элементов.',
        'file' => 'Размер файла в поле :attribute должен быть больше :value килобайт.',
        'numeric' => 'Поле :attribute должно быть больше :value.',
        'string' => 'Количество символов в поле :attribute должно быть больше :value.',
],
'gte' => [
'array' => 'Поле :attribute должно содержать :value элементов или более.',
'file' => 'Размер файла в поле :attribute должен быть больше или равен :value килобайт.',
'numeric' => 'Поле :attribute должно быть больше или равно :value.',
'string' => 'Количество символов в поле :attribute должно быть больше или равно :value.',
],
'image' => 'Поле :attribute должно быть изображением.',
'in' => 'Выбранное значение для :attribute недопустимо.',
'in_array' => 'Поле :attribute не существует в :other.',
'integer' => 'Поле :attribute должно быть целым числом.',
'ip' => 'Поле :attribute должно быть действительным IP-адресом.',
'ipv4' => 'Поле :attribute должно быть действительным IPv4-адресом.',
'ipv6' => 'Поле :attribute должно быть действительным IPv6-адресом.',
'json' => 'Поле :attribute должно быть действительной JSON-строкой.',
'lowercase' => 'Поле :attribute должно быть в нижнем регистре.',
'lt' => [
'array' => 'Поле :attribute должно содержать менее :value элементов.',
'file' => 'Размер файла в поле :attribute должен быть меньше :value килобайт.',
'numeric' => 'Поле :attribute должно быть меньше :value.',
'string' => 'Количество символов в поле :attribute должно быть меньше :value.',
],
'lte' => [
'array' => 'Поле :attribute не должно содержать более :value элементов.',
'file' => 'Размер файла в поле :attribute должен быть меньше или равен :value килобайт.',
'numeric' => 'Поле :attribute должно быть меньше или равно :value.',
'string' => 'Количество символов в поле :attribute должно быть меньше или равно :value.',
],
'mac_address' => 'Поле :attribute должно быть действительным MAC-адресом.',
'max' => [
'array' => 'Поле :attribute не должно содержать более :max элементов.',
'file' => 'Размер файла в поле :attribute не должен превышать :max килобайт.',
'numeric' => 'Поле :attribute не должно быть больше :max.',
'string' => 'Количество символов в поле :attribute не должно превышать :max.',
],
'max_digits' => 'Поле :attribute не должно содержать более :max цифр.',
'mimes' => 'Поле :attribute должно быть файлом типа: :values.',
'mimetypes' => 'Поле :attribute должно быть файлом типа: :values.',
'min' => [
'array' => 'Поле :attribute должно содержать как минимум :min элементов.',
'file' => 'Размер файла в поле :attribute должен быть не менее :min килобайт.',
'numeric' => 'Поле :attribute должно быть не менее :min.',
'string' => 'Количество символов в поле :attribute должно быть не менее :min.',
],
'min_digits' => 'Поле :attribute должно содержать как минимум :min цифр.',
'missing' => 'Поле :attribute должно отсутствовать.',
'missing_if' => 'Поле :attribute должно отсутствовать, когда :other равно :value.',
'missing_unless' => 'Поле :attribute должно отсутствовать, если :other не равно :value.',
'missing_with' => 'Поле :attribute должно отсутствовать, когда присутствует :values.',
'missing_with_all' => 'Поле :attribute должно отсутствовать, когда присутствуют все значения :values.',
'multiple_of' => 'Поле :attribute должно быть кратным :value.',
'not_in' => 'Выбранное значение для :attribute недопустимо.',
'not_regex' => 'Формат поля :attribute недопустим.',
'numeric' => 'Поле :attribute должно быть числом.',
'password' => [
'letters' => 'Поле :attribute должно содержать хотя бы одну букву.',
'mixed' => 'Поле :attribute должно содержать хотя бы одну заглавную и одну строчную букву.',
'numbers' => 'Поле :attribute должно содержать хотя бы одну цифру.',
'symbols' => 'Поле :attribute должно содержать хотя бы один символ.',
'uncompromised' => 'Указанное значение :attribute появилось в утечке данных. Пожалуйста, выберите другое значение :attribute.',
],
'present' => 'Поле :attribute должно присутствовать.',
'prohibited' => 'Поле :attribute запрещено.',
'prohibited_if' => 'Поле :attribute запрещено, когда :other равно :value.',
'prohibited_unless' => 'Поле :attribute запрещено, если :other не находится в :values.',
'prohibits' => 'Поле :attribute запрещает наличие :other.',
'regex' => 'Формат поля :attribute недопустим.',
'required' => 'Поле :attribute обязательно для заполнения.',
'required_array_keys' => 'Поле :attribute должно содержать записи для: :values.',
'required_if' => 'Поле :attribute обязательно для заполнения, когда :other равно :value.',
'required_if_accepted' => 'Поле :attribute обязательно для заполнения, когда :other принято.',
'required_unless' => 'Поле :attribute обязательно для заполнения, если :other не находится в :values.',
'required_with' => 'Поле :attribute обязательно для заполнения, когда присутствует :values.',
'required_with_all' => 'Поле :attribute обязательно для заполнения, когда присутствуют все значения :values.',
'required_without' => 'Поле :attribute обязательно для заполнения, когда отсутствует :values.',
'required_without_all' => 'Поле :attribute обязательно для заполнения, когда отсутствуют все значения :values.',
'same' => 'Поля :attribute и :other должны совпадать.',
'size' => [
'array' => 'Поле :attribute должно содержать :size элементов.',
'file' => 'Размер файла в поле :attribute должен быть :size килобайт.',
'numeric' => 'Поле :attribute должно быть равно :size.',
'string' => 'Количество символов в поле :attribute должно быть равно :size.',
],
'starts_with' => 'Поле :attribute должно начинаться одним из следующих значений: :values.',
'string' => 'Поле :attribute должно быть строкой.',
'timezone' => 'Поле :attribute должно быть действительным часовым поясом.',
'unique' => 'Значение поля :attribute уже занято.',
'uploaded' => 'Не удалось загрузить файл в поле :attribute.',
'uppercase' => 'Поле :attribute должно быть в верхнем регистре.',
'url' => 'Поле :attribute должно быть действительным URL-адресом.',
'ulid' => 'Поле :attribute должно быть действительным ULID.',
'uuid' => 'Поле :attribute должно быть действительным UUID.',


    'custom' => [
        'attribute-name' => [
            'rule-name' => 'специальное сообщение',
        ],
    ],
    'attributes' => [],

];
