<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        body {
            margin: 0;
            background:
                radial-gradient(circle at top left, rgba(15, 118, 110, 0.14), transparent 32%),
                linear-gradient(180deg, #f7faf9 0%, #eef4f2 100%);
            color: #1f2937;
        }

        .page-header {
            padding: 24px 24px 0;
        }

        .page-header h1 {
            margin: 0;
            font-size: 28px;
        }

        .page-header p {
            margin: 8px 0 0;
            color: #4b5563;
        }

        a {
            color: #0f766e;
        }

        #swagger-ui {
            margin: 16px 24px 24px;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.10);
        }

        .swagger-ui .topbar {
            display: none;
        }
    </style>
</head>
<body>
<header class="page-header">
    <h1>{{ $title }}</h1>
    <p>OpenAPI specification JSON: <a href="{{ $specUrl }}">{{ $specUrl }}</a></p>
</header>
<div id="swagger-ui"></div>
<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js" crossorigin="anonymous"></script>
<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js" crossorigin="anonymous"></script>
<script>
    window.onload = function () {
        const tagOrder = [
            '認証',
            'アカウント管理',
            'アプリケーション管理',
            'パスワード管理',
            'パスワード変更促進通知',
            '仮登録パスワード管理',
            '未登録パスワード管理',
            '共通'
        ];

        SwaggerUIBundle({
            url: @json($specUrl),
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            layout: 'BaseLayout',
            docExpansion: 'list',
            defaultModelsExpandDepth: 1,
            tagsSorter: function (a, b) {
                return tagOrder.indexOf(a) - tagOrder.indexOf(b);
            },
        });
    };
</script>
</body>
</html>
