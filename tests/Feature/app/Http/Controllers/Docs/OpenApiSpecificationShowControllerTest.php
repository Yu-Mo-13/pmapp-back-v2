<?php

namespace Tests\Feature\app\Http\Controllers\Docs;

use Illuminate\Routing\Router;
use Tests\TestCase;

class OpenApiSpecificationShowControllerTest extends TestCase
{
    public function test_OpenAPIドキュメント系ルートはセッションを開始しない(): void
    {
        $router = $this->app->make(Router::class);

        $docsPageRoute = $router->getRoutes()->getByName('docs.api');
        $docsJsonRoute = $router->getRoutes()->getByName('docs.openapi');

        $this->assertSame([], $docsPageRoute->gatherMiddleware());
        $this->assertSame([], $docsJsonRoute->gatherMiddleware());
    }

    public function test_OpenAPIドキュメント画面を返す(): void
    {
        $response = $this->get('/docs/api');

        $response->assertOk()
            ->assertSee(config('app.name') . ' API Docs')
            ->assertSee('swagger-ui')
            ->assertSee('/docs/openapi.json')
            ->assertSee('SwaggerUIBundle');
    }

    public function test_OpenAPI仕様のJSONを返す(): void
    {
        $response = $this->getJson('/docs/openapi.json');
        $specification = $response->json();

        $response->assertOk()
            ->assertJsonPath('openapi', '3.0.3')
            ->assertJsonPath('info.title', config('app.name') . ' API')
            ->assertJsonPath('components.securitySchemes.bearerAuth.type', 'http')
            ->assertJsonPath('components.securitySchemes.bearerAuth.scheme', 'bearer')
            ->assertJsonPath('tags.0.name', '認証')
            ->assertJsonPath('paths./api/v2/healthchecks/status.get.summary', 'Healthcheck Status Show')
            ->assertJsonPath('paths./api/v2/accounts.get.security.0.bearerAuth', []);

        $this->assertArrayNotHasKey('security', $response->json('paths./api/v2/login.post'));
        $this->assertSame(
            '#/components/schemas/HealthcheckStatusResponse',
            $specification['paths']['/api/v2/healthchecks/status']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/LoginValidationErrorResponse',
            $specification['paths']['/api/v2/login']['post']['responses']['422']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            'string',
            $specification['components']['schemas']['LoginResponse']['properties']['top_page_url']['type']
        );
        $this->assertSame(
            'string',
            $specification['components']['schemas']['LoginStatusResponse']['properties']['role']['properties']['code']['type']
        );
        $this->assertTrue(
            $specification['components']['schemas']['LoginStatusResponse']['properties']['role']['nullable']
        );
        $this->assertContains(
            'role',
            $specification['components']['schemas']['LoginStatusResponse']['required']
        );
        $this->assertContains(
            'top_page_url',
            $specification['components']['schemas']['LoginResponse']['required']
        );
        $this->assertSame(
            '#/components/schemas/UnauthorizedResponse',
            $specification['paths']['/api/v2/accounts']['get']['responses']['401']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/NotFoundResponse',
            $specification['paths']['/api/v2/accounts/{account}']['get']['responses']['404']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/EmptySuccessResponse',
            $specification['paths']['/api/v2/applications']['post']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/AccountIndexResponse',
            $specification['paths']['/api/v2/accounts']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/AccountApplicationIndexResponse',
            $specification['paths']['/api/v2/accounts/applications']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/ApplicationIndexResponse',
            $specification['paths']['/api/v2/applications']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/ApplicationAccountIndexResponse',
            $specification['paths']['/api/v2/applications/{application}/accounts']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/MenuIndexResponse',
            $specification['paths']['/api/v2/menus']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/PasswordIndexResponse',
            $specification['paths']['/api/v2/passwords']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/PasswordUpdatePromoteIndexResponse',
            $specification['paths']['/api/v2/password-update-promote']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/PreregistedPasswordIndexResponse',
            $specification['paths']['/api/v2/preregisted-passwords']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/UnregistedPasswordIndexResponse',
            $specification['paths']['/api/v2/unregisted-passwords']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/CheckResponse',
            $specification['paths']['/api/v2/check']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/HealthcheckCreateResponse',
            $specification['paths']['/api/v2/healthchecks']['post']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/EmptySuccessResponse',
            $specification['paths']['/api/v2/preregisted-passwords']['post']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/PreregistedPasswordResponse',
            $specification['paths']['/api/v2/preregisted-passwords/{preregistedPassword}']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/PreregistedPasswordTargetResponse',
            $specification['paths']['/api/v2/preregisted-passwords/target']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/PreregistedPasswordCreateValidationErrorResponse',
            $specification['paths']['/api/v2/preregisted-passwords']['post']['responses']['422']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '仮登録パスワード管理',
            $specification['paths']['/api/v2/preregisted-passwords']['post']['tags'][0]
        );
        $this->assertSame(
            '#/components/schemas/UnregistedPasswordResponse',
            $specification['paths']['/api/v2/unregisted-passwords/{unregistedPassword}']['get']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/DeleteAllUnregistedPasswordsResponse',
            $specification['paths']['/api/v2/unregisted-passwords']['delete']['responses']['200']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/AccountCreateValidationErrorResponse',
            $specification['paths']['/api/v2/accounts']['post']['responses']['422']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/AccountUpdateValidationErrorResponse',
            $specification['paths']['/api/v2/accounts/{account}']['put']['responses']['422']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/ApplicationCreateValidationErrorResponse',
            $specification['paths']['/api/v2/applications']['post']['responses']['422']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/ApplicationUpdateValidationErrorResponse',
            $specification['paths']['/api/v2/applications/{application}']['put']['responses']['422']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/PasswordCreateValidationErrorResponse',
            $specification['paths']['/api/v2/passwords']['post']['responses']['422']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/PasswordLatestShowValidationErrorResponse',
            $specification['paths']['/api/v2/passwords/latest']['get']['responses']['422']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            '#/components/schemas/PasswordIndexValidationErrorResponse',
            $specification['paths']['/api/v2/passwords']['get']['responses']['422']['content']['application/json']['schema']['$ref']
        );
        $this->assertSame(
            'application_id',
            $specification['paths']['/api/v2/passwords']['get']['parameters'][0]['name']
        );
        $this->assertFalse(
            $specification['paths']['/api/v2/passwords']['get']['parameters'][0]['required']
        );
        $this->assertSame(
            'query',
            $specification['paths']['/api/v2/passwords']['get']['parameters'][0]['in']
        );
        $this->assertSame(
            'integer',
            $specification['paths']['/api/v2/passwords']['get']['parameters'][0]['schema']['type']
        );
        $this->assertSame(
            'application_id',
            $specification['paths']['/api/v2/passwords/latest']['get']['parameters'][0]['name']
        );
        $this->assertTrue(
            $specification['paths']['/api/v2/passwords/latest']['get']['parameters'][0]['required']
        );
        $this->assertSame(
            'account_id',
            $specification['paths']['/api/v2/passwords/latest']['get']['parameters'][1]['name']
        );
        $this->assertFalse(
            $specification['paths']['/api/v2/passwords/latest']['get']['parameters'][1]['required']
        );
        $this->assertSame(
            'アカウント管理',
            $specification['paths']['/api/v2/accounts']['get']['tags'][0]
        );
        $this->assertSame(
            'アカウント管理',
            $specification['paths']['/api/v2/accounts/{account}']['put']['tags'][0]
        );
        $this->assertSame(
            'アプリケーション管理',
            $specification['paths']['/api/v2/applications']['get']['tags'][0]
        );
        $this->assertSame(
            'アプリケーション管理',
            $specification['paths']['/api/v2/applications/{application}/accounts']['get']['tags'][0]
        );
        $this->assertSame(
            '認証',
            $specification['paths']['/api/v2/login']['post']['tags'][0]
        );
        $this->assertSame(
            '認証',
            $specification['paths']['/api/v2/login/status']['get']['tags'][0]
        );
        $this->assertSame(
            'パスワード管理',
            $specification['paths']['/api/v2/passwords']['get']['tags'][0]
        );
        $this->assertSame(
            'パスワード管理',
            $specification['paths']['/api/v2/passwords/latest']['get']['tags'][0]
        );
        $this->assertSame(
            'パスワード変更促進通知',
            $specification['paths']['/api/v2/password-update-promote']['get']['tags'][0]
        );
        $this->assertSame(
            '仮登録パスワード管理',
            $specification['paths']['/api/v2/preregisted-passwords']['get']['tags'][0]
        );
        $this->assertSame(
            '仮登録パスワード管理',
            $specification['paths']['/api/v2/preregisted-passwords/{preregistedPassword}']['delete']['tags'][0]
        );
        $this->assertSame(
            '仮登録パスワード管理',
            $specification['paths']['/api/v2/preregisted-passwords/target']['get']['tags'][0]
        );
        $this->assertSame(
            '未登録パスワード管理',
            $specification['paths']['/api/v2/unregisted-passwords']['get']['tags'][0]
        );
        $this->assertSame(
            '未登録パスワード管理',
            $specification['paths']['/api/v2/unregisted-passwords/{unregistedPassword}']['get']['tags'][0]
        );
        $this->assertSame(
            '共通',
            $specification['paths']['/api/v2/healthchecks/status']['get']['tags'][0]
        );
        $this->assertSame(
            '共通',
            $specification['paths']['/api/v2/menus']['get']['tags'][0]
        );
        $this->assertSame(
            '共通',
            $specification['paths']['/api/v2/check']['get']['tags'][0]
        );
    }
}
