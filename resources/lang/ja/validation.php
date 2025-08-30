<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines (Japanese)
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attributeを承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url' => ':attributeには有効なURLを指定してください。',
    'after' => ':attributeには:dateより後の日付を指定してください。',
    'after_or_equal' => ':attributeには:date以降の日付を指定してください。',
    'alpha' => ':attributeには英字のみ使用できます。',
    'alpha_dash' => ':attributeには英数字・ハイフン・アンダースコアのみ使用できます。',
    'alpha_num' => ':attributeには英数字のみ使用できます。',
    'array' => ':attributeには配列を指定してください。',
    'before' => ':attributeには:dateより前の日付を指定してください。',
    'before_or_equal' => ':attributeには:date以前の日付を指定してください。',
    'between' => [
        'numeric' => ':attributeには:min〜:maxまでの数値を指定してください。',
        'file' => ':attributeには:min〜:maxキロバイトのファイルを指定してください。',
        'string' => ':attributeには:min〜:max文字で入力してください。',
        'array' => ':attributeには:min〜:max個の項目を指定してください。',
    ],
    'boolean' => ':attributeには真偽値を指定してください。',
    'confirmed' => ':attributeと確認用項目が一致しません。',
    'current_password' => 'パスワードが間違っています。',
    'date' => ':attributeには有効な日付を指定してください。',
    'date_equals' => ':attributeには:dateと同じ日付を指定してください。',
    'date_format' => ':attributeの形式が:formatと一致しません。',
    'declined' => ':attributeを拒否してください。',
    'declined_if' => ':otherが:valueの場合、:attributeを拒否してください。',
    'different' => ':attributeと:otherには異なる値を指定してください。',
    'digits' => ':attributeには:digits桁の数字を指定してください。',
    'digits_between' => ':attributeには:min〜:max桁の数字を指定してください。',
    'dimensions' => ':attributeの画像サイズが無効です。',
    'distinct' => ':attributeに重複した値があります。',
    'email' => ':attributeには有効なメールアドレスを指定してください。',
    'ends_with' => ':attributeには次のいずれかで終わる値を指定してください: :values',
    'enum' => '選択された:attributeは無効です。',
    'exists' => '選択された:attributeは無効です。',
    'file' => ':attributeにはファイルを指定してください。',
    'filled' => ':attributeには値を指定してください。',
    'gt' => [
        'numeric' => ':attributeには:valueより大きい数値を指定してください。',
        'file' => ':attributeには:valueキロバイトより大きいファイルを指定してください。',
        'string' => ':attributeには:value文字より多く入力してください。',
        'array' => ':attributeには:value個より多くの項目を指定してください。',
    ],
    'gte' => [
        'numeric' => ':attributeには:value以上の数値を指定してください。',
        'file' => ':attributeには:valueキロバイト以上のファイルを指定してください。',
        'string' => ':attributeには:value文字以上で入力してください。',
        'array' => ':attributeには:value個以上の項目を指定してください。',
    ],
    'image' => ':attributeには画像ファイルを指定してください。',
    'in' => '選択された:attributeは無効です。',
    'in_array' => ':attributeが:otherに存在しません。',
    'integer' => ':attributeには整数を指定してください。',
    'ip' => ':attributeには有効なIPアドレスを指定してください。',
    'ipv4' => ':attributeには有効なIPv4アドレスを指定してください。',
    'ipv6' => ':attributeには有効なIPv6アドレスを指定してください。',
    'json' => ':attributeには有効なJSON文字列を指定してください。',
    'lt' => [
        'numeric' => ':attributeには:valueより小さい数値を指定してください。',
        'file' => ':attributeには:valueキロバイトより小さいファイルを指定してください。',
        'string' => ':attributeには:value文字より少なく入力してください。',
        'array' => ':attributeには:value個より少ない項目を指定してください。',
    ],
    'lte' => [
        'numeric' => ':attributeには:value以下の数値を指定してください。',
        'file' => ':attributeには:valueキロバイト以下のファイルを指定してください。',
        'string' => ':attributeには:value文字以下で入力してください。',
        'array' => ':attributeには:value個以下の項目を指定してください。',
    ],
    'max' => [
        'numeric' => ':attributeには:max以下の数値を指定してください。',
        'file' => ':attributeには:maxキロバイト以下のファイルを指定してください。',
        'string' => ':attributeには:max文字以下で入力してください。',
        'array' => ':attributeには:max個以下の項目を指定してください。',
    ],
    'mimes' => ':attributeには:valuesタイプのファイルを指定してください。',
    'mimetypes' => ':attributeには:valuesタイプのファイルを指定してください。',
    'min' => [
        'numeric' => ':attributeには:min以上の数値を指定してください。',
        'file' => ':attributeには:minキロバイト以上のファイルを指定してください。',
        'string' => ':attributeには:min文字以上で入力してください。',
        'array' => ':attributeには:min個以上の項目を指定してください。',
    ],
    'multiple_of' => ':attributeは:valueの倍数である必要があります。',
    'not_in' => '選択された:attributeは無効です。',
    'not_regex' => ':attributeの形式が無効です。',
    'numeric' => ':attributeには数値を指定してください。',
    'password' => 'パスワードが間違っています。',
    'present' => ':attributeが存在していません。',
    'prohibited' => ':attributeフィールドは禁止されています。',
    'prohibited_if' => ':otherが:valueの場合、:attributeフィールドは禁止されています。',
    'prohibited_unless' => ':otherが:valuesでない限り、:attributeフィールドは禁止されています。',
    'prohibits' => ':attributeフィールドは:otherの存在を禁止しています。',
    'regex' => ':attributeの形式が無効です。',
    'required' => ':attributeは必須です。',
    'required_if' => ':otherが:valueの場合、:attributeは必須項目です。',
    'required_unless' => ':otherが:valuesでない限り、:attributeは必須項目です。',
    'required_with' => ':valuesが指定されている場合、:attributeは必須項目です。',
    'required_with_all' => ':valuesがすべて指定されている場合、:attributeは必須項目です。',
    'required_without' => ':valuesが指定されていない場合、:attributeは必須項目です。',
    'required_without_all' => ':valuesがすべて指定されていない場合、:attributeは必須項目です。',
    'same' => ':attributeと:otherが一致していません。',
    'size' => [
        'numeric' => ':attributeには:sizeを指定してください。',
        'file' => ':attributeには:sizeキロバイトのファイルを指定してください。',
        'string' => ':attributeには:size文字で入力してください。',
        'array' => ':attributeには:size個の項目を指定してください。',
    ],
    'starts_with' => ':attributeは次のいずれかで始まる必要があります: :values',
    'string' => ':attributeには文字列を指定してください。',
    'timezone' => ':attributeには有効なタイムゾーンを指定してください。',
    'unique' => ':attributeはすでに使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'url' => ':attributeには有効なURLを指定してください。',
    'uuid' => ':attributeには有効なUUIDを指定してください。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
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
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'application' => [
            'name' => 'アプリケーション名',
            'account_class' => 'アカウント区分',
            'notice_class' => '通知区分',
            'mark_class' => '記号区分',
            'pre_password_size' => '仮登録パスワード桁数',
        ]
    ],

];
