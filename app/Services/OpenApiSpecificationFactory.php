<?php

namespace App\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

class OpenApiSpecificationFactory
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function make(): array
    {
        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => config('openapi.info.title'),
                'version' => config('openapi.info.version'),
                'description' => config('openapi.info.description'),
            ],
            'tags' => $this->buildTags(),
            'servers' => [
                [
                    'url' => config('openapi.server_url'),
                ],
            ],
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT',
                    ],
                ],
                'schemas' => $this->buildSchemas(),
            ],
            'paths' => $this->buildPaths(),
        ];
    }

    private function buildPaths(): array
    {
        $paths = [];

        /** @var Route $route */
        foreach ($this->router->getRoutes()->getRoutes() as $route) {
            if (!$this->isDocumentTarget($route)) {
                continue;
            }

            $uri = '/' . ltrim($route->uri(), '/');

            foreach ($this->extractMethods($route) as $method) {
                $paths[$uri][Str::lower($method)] = $this->buildOperation($route, $method);
            }
        }

        ksort($paths);

        return $paths;
    }

    private function isDocumentTarget(Route $route): bool
    {
        return Str::startsWith($route->uri(), 'api/v2');
    }

    private function extractMethods(Route $route): array
    {
        return array_values(array_filter(
            $route->methods(),
            static function (string $method): bool {
                return $method !== 'HEAD';
            }
        ));
    }

    private function buildOperation(Route $route, string $method): array
    {
        $operation = [
            'summary' => $this->makeSummary($route),
            'operationId' => $this->makeOperationId($route, $method),
            'responses' => $this->buildResponses($route),
            'tags' => [$this->resolveTag($route)],
        ];

        if ($this->requiresAuthentication($route)) {
            $operation['security'] = [
                [
                    'bearerAuth' => [],
                ],
            ];
        }

        $parameters = $this->buildParameters($route);
        if ($parameters !== []) {
            $operation['parameters'] = $parameters;
        }

        return $operation;
    }

    private function buildTags(): array
    {
        return [
            [
                'name' => '認証',
            ],
            [
                'name' => 'アカウント管理',
            ],
            [
                'name' => 'アプリケーション管理',
            ],
            [
                'name' => 'パスワード管理',
            ],
            [
                'name' => 'パスワード変更促進通知',
            ],
            [
                'name' => '仮登録パスワード管理',
            ],
            [
                'name' => '未登録パスワード管理',
            ],
            [
                'name' => '共通',
            ],
        ];
    }

    private function buildResponses(Route $route): array
    {
        $responses = [
            '200' => $this->buildJsonResponse(
                'Successful response',
                $this->inferSuccessSchemaName($route)
            ),
        ];

        if ($this->requiresAuthentication($route)) {
            $responses['401'] = $this->buildJsonResponse('Unauthorized', 'UnauthorizedResponse');
        }

        if ($this->usesFormRequest($route)) {
            $responses['422'] = $this->buildJsonResponse(
                'Validation error',
                $this->inferValidationSchemaName($route)
            );
        }

        if ($this->canReturnNotFound($route)) {
            $responses['404'] = $this->buildJsonResponse('Resource not found', 'NotFoundResponse');
        }

        if ($this->usesFormatterResponse($route, 'forbidden')) {
            $responses['403'] = $this->buildJsonResponse('Forbidden', 'ForbiddenResponse');
        }

        if ($this->usesFormatterResponse($route, 'internalServerError')) {
            $responses['500'] = $this->buildJsonResponse('Internal server error', 'InternalServerErrorResponse');
        }

        ksort($responses);

        return $responses;
    }

    private function buildJsonResponse(string $description, string $schemaName): array
    {
        return [
            'description' => $description,
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/' . $schemaName,
                    ],
                ],
            ],
        ];
    }

    private function buildParameters(Route $route): array
    {
        if (!in_array('GET', $this->extractMethods($route), true)) {
            return [];
        }

        $formRequestClass = $this->resolveFormRequestClass($route);
        if ($formRequestClass === null) {
            return [];
        }

        /** @var FormRequest $formRequest */
        $formRequest = new $formRequestClass();
        $formRequest->setContainer(app());
        $formRequest->setRedirector(app('redirect'));
        $formRequest->setRouteResolver(static function () use ($route) {
            return $route;
        });

        $parameters = [];
        foreach ($this->resolveFormRequestRules($formRequest) as $field => $rules) {
            if (Str::contains($field, '.')) {
                continue;
            }

            $parameterRules = $this->normalizeRules($rules);
            $parameters[] = [
                'name' => $field,
                'in' => 'query',
                'required' => in_array('required', $parameterRules, true),
                'schema' => $this->buildParameterSchema($parameterRules),
            ];
        }

        return $parameters;
    }

    private function buildParameterSchema(array $rules): array
    {
        $schema = [
            'type' => 'string',
        ];

        if (in_array('integer', $rules, true)) {
            $schema['type'] = 'integer';
        } elseif (in_array('boolean', $rules, true)) {
            $schema['type'] = 'boolean';
        }

        if (in_array('nullable', $rules, true)) {
            $schema['nullable'] = true;
        }

        return $schema;
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveFormRequestRules(FormRequest $formRequest): array
    {
        /** @var array<string, mixed> $rules */
        $rules = app()->call([$formRequest, 'rules']);

        return $rules;
    }

    private function normalizeRules($rules): array
    {
        if (is_string($rules)) {
            return explode('|', $rules);
        }

        return Collection::make($rules)
            ->map(static function ($rule): string {
                if (is_string($rule)) {
                    return $rule;
                }

                return class_basename($rule);
            })
            ->all();
    }

    private function buildSchemas(): array
    {
        return [
            'GenericSuccessResponse' => [
                'type' => 'object',
                'additionalProperties' => true,
            ],
            'EmptySuccessResponse' => [
                'type' => 'object',
                'additionalProperties' => false,
            ],
            'IdNameResource' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'name' => ['type' => 'string'],
                ],
                'required' => ['id', 'name'],
            ],
            'NullableIdNameResource' => [
                'allOf' => [
                    ['$ref' => '#/components/schemas/IdNameResource'],
                ],
                'nullable' => true,
            ],
            'CheckResponse' => [
                'type' => 'object',
                'properties' => [
                    'status' => ['type' => 'string'],
                ],
                'required' => ['status'],
            ],
            'HealthcheckStatusResponse' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'is_healthy' => ['type' => 'boolean'],
                    'message' => ['type' => 'string'],
                ],
                'required' => ['is_healthy', 'message'],
            ],
            'HealthcheckCreateResponse' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                    'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                ],
                'required' => ['id', 'created_at', 'updated_at'],
            ],
            'LoginResponse' => [
                'type' => 'object',
                'properties' => [
                    'access_token' => ['type' => 'string'],
                    'top_page_url' => ['type' => 'string'],
                ],
                'required' => ['access_token', 'top_page_url'],
            ],
            'LoginStatusResponse' => [
                'type' => 'object',
                'properties' => [
                    'name' => ['type' => 'string'],
                    'role' => [
                        'type' => 'object',
                        'nullable' => true,
                        'properties' => [
                            'code' => ['type' => 'string'],
                        ],
                        'required' => ['code'],
                    ],
                ],
                'required' => ['name', 'role'],
            ],
            'ApplicationResponse' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'name' => ['type' => 'string'],
                    'account_class' => ['type' => 'boolean'],
                    'notice_class' => ['type' => 'boolean'],
                    'mark_class' => ['type' => 'boolean'],
                    'pre_password_size' => ['type' => 'integer'],
                ],
                'required' => ['id', 'name', 'account_class', 'notice_class', 'mark_class', 'pre_password_size'],
            ],
            'AccountResponse' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'application_id' => ['type' => 'integer'],
                    'name' => ['type' => 'string'],
                ],
                'required' => ['id', 'application_id', 'name'],
            ],
            'AccountIndexItem' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'name' => ['type' => 'string'],
                    'application_id' => ['type' => 'integer'],
                    'application_name' => ['type' => 'string'],
                    'notice_class' => ['type' => 'boolean'],
                ],
                'required' => ['id', 'name', 'application_id', 'application_name', 'notice_class'],
            ],
            'AccountIndexResponse' => $this->arraySchema('AccountIndexItem'),
            'AccountApplicationIndexResponse' => $this->arraySchema('IdNameResource'),
            'ApplicationIndexItem' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'name' => ['type' => 'string'],
                    'account_class' => ['type' => 'boolean'],
                    'notice_class' => ['type' => 'boolean'],
                    'mark_class' => ['type' => 'boolean'],
                ],
                'required' => ['id', 'name', 'account_class', 'notice_class', 'mark_class'],
            ],
            'ApplicationIndexResponse' => $this->arraySchema('ApplicationIndexItem'),
            'ApplicationAccountIndexResponse' => $this->arraySchema('IdNameResource'),
            'MenuItem' => [
                'type' => 'object',
                'properties' => [
                    'name' => ['type' => 'string'],
                    'path' => ['type' => 'string'],
                ],
                'required' => ['name', 'path'],
            ],
            'MenuIndexResponse' => $this->arraySchema('MenuItem'),
            'PasswordIndexItem' => [
                'type' => 'object',
                'properties' => [
                    'latest_updated_at' => [
                        'type' => 'string',
                        'format' => 'date-time',
                        'nullable' => true,
                    ],
                    'application' => [
                        '$ref' => '#/components/schemas/IdNameResource',
                    ],
                    'account' => [
                        '$ref' => '#/components/schemas/NullableIdNameResource',
                    ],
                ],
                'required' => ['latest_updated_at', 'application', 'account'],
            ],
            'PasswordIndexResponse' => $this->arraySchema('PasswordIndexItem'),
            'PasswordLatestResponse' => [
                'type' => 'object',
                'properties' => [
                    'password' => ['type' => 'string'],
                ],
                'required' => ['password'],
            ],
            'ApplicationAccountPairItem' => [
                'type' => 'object',
                'properties' => [
                    'application' => [
                        '$ref' => '#/components/schemas/IdNameResource',
                    ],
                    'account' => [
                        '$ref' => '#/components/schemas/NullableIdNameResource',
                    ],
                ],
                'required' => ['application', 'account'],
            ],
            'PasswordUpdatePromoteIndexResponse' => $this->arraySchema('ApplicationAccountPairItem'),
            'PreregistedPasswordIndexItem' => [
                'type' => 'object',
                'properties' => [
                    'uuid' => ['type' => 'string'],
                    'application' => [
                        '$ref' => '#/components/schemas/NullableIdNameResource',
                    ],
                    'account' => [
                        '$ref' => '#/components/schemas/NullableIdNameResource',
                    ],
                    'created_at' => [
                        'type' => 'string',
                        'format' => 'date-time',
                        'nullable' => true,
                    ],
                ],
                'required' => ['uuid', 'application', 'account', 'created_at'],
            ],
            'PreregistedPasswordIndexResponse' => $this->arraySchema('PreregistedPasswordIndexItem'),
            'PreregistedPasswordResponse' => [
                'type' => 'object',
                'properties' => [
                    'uuid' => ['type' => 'string'],
                    'password' => ['type' => 'string'],
                    'application' => [
                        '$ref' => '#/components/schemas/NullableIdNameResource',
                    ],
                    'account' => [
                        '$ref' => '#/components/schemas/NullableIdNameResource',
                    ],
                    'created_at' => [
                        'type' => 'string',
                        'format' => 'date-time',
                        'nullable' => true,
                    ],
                ],
                'required' => ['uuid', 'password', 'application', 'account', 'created_at'],
            ],
            'UnregistedPasswordIndexResponse' => $this->arraySchema('PreregistedPasswordIndexItem'),
            'UnregistedPasswordResponse' => [
                '$ref' => '#/components/schemas/PreregistedPasswordResponse',
            ],
            'DeleteAllUnregistedPasswordsResponse' => [
                'type' => 'object',
                'properties' => [
                    'message' => ['type' => 'string'],
                ],
                'required' => ['message'],
            ],
            'UnauthorizedResponse' => $this->messageOnlySchema(),
            'ForbiddenResponse' => $this->messageOnlySchema(),
            'NotFoundResponse' => $this->messageOnlySchema(),
            'InternalServerErrorResponse' => $this->messageOnlySchema(),
            'ValidationErrorResponse' => [
                'type' => 'object',
                'properties' => [
                    'message' => ['type' => 'string'],
                    'errors' => [
                        'type' => 'object',
                        'additionalProperties' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                        ],
                    ],
                ],
                'required' => ['message', 'errors'],
            ],
            'LoginValidationErrorResponse' => $this->validationErrorSchema([
                'email',
                'password',
            ]),
            'AccountCreateValidationErrorResponse' => $this->validationErrorSchema([
                'account.name',
                'account.application_id',
                'account.notice_class',
            ]),
            'AccountUpdateValidationErrorResponse' => $this->validationErrorSchema([
                'account.name',
                'account.application_id',
                'account.notice_class',
            ]),
            'ApplicationCreateValidationErrorResponse' => $this->validationErrorSchema([
                'application.name',
                'application.account_class',
                'application.notice_class',
                'application.mark_class',
                'application.pre_password_size',
            ]),
            'ApplicationUpdateValidationErrorResponse' => $this->validationErrorSchema([
                'application.name',
                'application.account_class',
                'application.notice_class',
                'application.mark_class',
                'application.pre_password_size',
            ]),
            'PasswordCreateValidationErrorResponse' => $this->validationErrorSchema([
                'password.password',
                'password.application_id',
                'password.account_id',
            ]),
            'PasswordLatestShowValidationErrorResponse' => $this->validationErrorSchema([
                'application_id',
                'account_id',
            ]),
            'PasswordIndexValidationErrorResponse' => $this->validationErrorSchema([
                'application_id',
            ]),
        ];
    }

    private function arraySchema(string $itemSchemaName): array
    {
        return [
            'type' => 'array',
            'items' => [
                '$ref' => '#/components/schemas/' . $itemSchemaName,
            ],
        ];
    }

    private function validationErrorSchema(array $fields): array
    {
        $errorsProperties = [];

        foreach ($fields as $field) {
            $errorsProperties[$field] = [
                'type' => 'array',
                'items' => ['type' => 'string'],
            ];
        }

        return [
            'type' => 'object',
            'properties' => [
                'message' => ['type' => 'string'],
                'errors' => [
                    'type' => 'object',
                    'properties' => $errorsProperties,
                    'additionalProperties' => false,
                ],
            ],
            'required' => ['message', 'errors'],
        ];
    }

    private function messageOnlySchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'message' => ['type' => 'string'],
            ],
            'required' => ['message'],
        ];
    }

    private function makeSummary(Route $route): string
    {
        $action = $route->getActionName();

        if ($action === 'Closure') {
            return Str::headline($route->uri());
        }

        return Str::headline(Str::replaceLast('Controller', '', class_basename($action)));
    }

    private function makeOperationId(Route $route, string $method): string
    {
        $name = $route->getName();

        if ($name !== null) {
            return Str::camel($name . '.' . Str::lower($method));
        }

        return Str::camel($route->uri() . '.' . Str::lower($method));
    }

    private function requiresAuthentication(Route $route): bool
    {
        foreach ($route->gatherMiddleware() as $middleware) {
            if (Str::contains($middleware, 'Authenticate:api') || Str::startsWith($middleware, 'auth:api')) {
                return true;
            }
        }

        return false;
    }

    private function usesFormRequest(Route $route): bool
    {
        $method = $this->reflectInvokeMethod($route);

        if ($method === null) {
            return false;
        }

        foreach ($method->getParameters() as $parameter) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                continue;
            }

            if (is_a($type->getName(), FormRequest::class, true)) {
                return true;
            }
        }

        return false;
    }

    private function canReturnNotFound(Route $route): bool
    {
        return !empty($route->parameterNames()) || $this->usesFormatterResponse($route, 'notfound');
    }

    private function usesFormatterResponse(Route $route, string $formatterMethod): bool
    {
        $controllerClass = $this->resolveControllerClass($route);

        if ($controllerClass === null) {
            return false;
        }

        $reflection = new ReflectionClass($controllerClass);
        $fileName = $reflection->getFileName();

        if ($fileName === false) {
            return false;
        }

        $source = file_get_contents($fileName);

        if ($source === false) {
            return false;
        }

        return Str::contains($source, 'ApiResponseFormatter::' . $formatterMethod);
    }

    private function inferSuccessSchemaName(Route $route): string
    {
        if ($route->getActionName() === 'Closure' && $route->uri() === 'api/v2/check') {
            return 'CheckResponse';
        }

        $controllerClass = $this->resolveControllerClass($route);

        if ($controllerClass === null) {
            return 'GenericSuccessResponse';
        }

        switch (class_basename($controllerClass)) {
            case 'HealthcheckCreateController':
                return 'HealthcheckCreateResponse';
            case 'HealthcheckStatusShowController':
                return 'HealthcheckStatusResponse';
            case 'LoginController':
                return 'LoginResponse';
            case 'LoginStatusController':
                return 'LoginStatusResponse';
            case 'AccountIndexController':
                return 'AccountIndexResponse';
            case 'AccountApplicationIndexController':
                return 'AccountApplicationIndexResponse';
            case 'ApplicationShowController':
                return 'ApplicationResponse';
            case 'ApplicationIndexController':
                return 'ApplicationIndexResponse';
            case 'ApplicationAccountIndexController':
                return 'ApplicationAccountIndexResponse';
            case 'AccountShowController':
                return 'AccountResponse';
            case 'MenuIndexController':
                return 'MenuIndexResponse';
            case 'PasswordIndexController':
                return 'PasswordIndexResponse';
            case 'PasswordLatestShowController':
                return 'PasswordLatestResponse';
            case 'PasswordUpdatePromoteIndexController':
                return 'PasswordUpdatePromoteIndexResponse';
            case 'PreregistedPasswordIndexController':
                return 'PreregistedPasswordIndexResponse';
            case 'PreregistedPasswordShowController':
                return 'PreregistedPasswordResponse';
            case 'UnregistedPasswordIndexController':
                return 'UnregistedPasswordIndexResponse';
            case 'UnregistedPasswordShowController':
                return 'UnregistedPasswordResponse';
            case 'UnregistedPasswordDeleteAllController':
                return 'DeleteAllUnregistedPasswordsResponse';
            case 'ApplicationCreateController':
            case 'ApplicationUpdateController':
            case 'ApplicationDeleteController':
            case 'AccountCreateController':
            case 'AccountUpdateController':
            case 'AccountDeleteController':
            case 'PasswordCreateController':
            case 'PreregistedPasswordDeleteController':
            case 'UnregistedPasswordDeleteController':
                return 'EmptySuccessResponse';
            default:
                return 'GenericSuccessResponse';
        }
    }

    private function reflectInvokeMethod(Route $route): ?ReflectionMethod
    {
        $controllerClass = $this->resolveControllerClass($route);

        if ($controllerClass === null) {
            return null;
        }

        return new ReflectionMethod($controllerClass, '__invoke');
    }

    private function resolveFormRequestClass(Route $route): ?string
    {
        $method = $this->reflectInvokeMethod($route);

        if ($method === null) {
            return null;
        }

        foreach ($method->getParameters() as $parameter) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                continue;
            }

            if (is_a($type->getName(), FormRequest::class, true)) {
                return $type->getName();
            }
        }

        return null;
    }

    private function inferValidationSchemaName(Route $route): string
    {
        $controllerClass = $this->resolveControllerClass($route);

        if ($controllerClass === null) {
            return 'ValidationErrorResponse';
        }

        switch (class_basename($controllerClass)) {
            case 'LoginController':
                return 'LoginValidationErrorResponse';
            case 'AccountCreateController':
                return 'AccountCreateValidationErrorResponse';
            case 'AccountUpdateController':
                return 'AccountUpdateValidationErrorResponse';
            case 'ApplicationCreateController':
                return 'ApplicationCreateValidationErrorResponse';
            case 'ApplicationUpdateController':
                return 'ApplicationUpdateValidationErrorResponse';
            case 'PasswordCreateController':
                return 'PasswordCreateValidationErrorResponse';
            case 'PasswordLatestShowController':
                return 'PasswordLatestShowValidationErrorResponse';
            case 'PasswordIndexController':
                return 'PasswordIndexValidationErrorResponse';
            default:
                return 'ValidationErrorResponse';
        }
    }

    private function resolveControllerClass(Route $route): ?string
    {
        $action = $route->getActionName();

        if ($action === 'Closure') {
            return null;
        }

        return $action;
    }

    private function resolveTag(Route $route): string
    {
        if (Str::startsWith($route->uri(), 'api/v2/accounts')) {
            return 'アカウント管理';
        }

        if (Str::startsWith($route->uri(), 'api/v2/applications')) {
            return 'アプリケーション管理';
        }

        if (Str::startsWith($route->uri(), 'api/v2/login')) {
            return '認証';
        }

        if (Str::startsWith($route->uri(), 'api/v2/passwords')) {
            return 'パスワード管理';
        }

        if (Str::startsWith($route->uri(), 'api/v2/password-update-promote')) {
            return 'パスワード変更促進通知';
        }

        if (Str::startsWith($route->uri(), 'api/v2/preregisted-passwords')) {
            return '仮登録パスワード管理';
        }

        if (Str::startsWith($route->uri(), 'api/v2/unregisted-passwords')) {
            return '未登録パスワード管理';
        }

        return '共通';
    }
}
