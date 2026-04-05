# CloudFormation Templates

このディレクトリには、AWS リソースを CloudFormation で管理するためのテンプレートを配置します。

## ディレクトリ方針

AWS サービス単位でディレクトリを分けます。

```text
cloudformation/
  iam/
  lambda/
  apigateway/
  s3/
  route53/
```

テンプレート名は、デプロイ先の AWS サービスと用途が分かる名前にしてください。

例:

- `iam/github-actions-oidc-role.yaml`
- `s3/lambda-storage-bucket.yaml`
- `apigateway/http-api-custom-domain.yaml`

## 現在のテンプレート

### IAM

- `iam/github-actions-oidc-role.yaml`

GitHub Actions から OIDC で AssumeRole するための IAM Role を作成します。

このテンプレートは以下のブランチからの実行を許可します。

- `develop`
- `main`

## デプロイ例

### GitHub Actions 用 IAM Role

```bash
aws cloudformation deploy \
  --template-file cloudformation/iam/github-actions-oidc-role.yaml \
  --stack-name pmapp-github-actions-oidc-role \
  --capabilities CAPABILITY_NAMED_IAM
```

必要に応じてパラメータを上書きできます。

```bash
aws cloudformation deploy \
  --template-file cloudformation/iam/github-actions-oidc-role.yaml \
  --stack-name pmapp-github-actions-oidc-role \
  --capabilities CAPABILITY_NAMED_IAM \
  --parameter-overrides \
    RoleName=pmapp-github-actions-deploy-role \
    GitHubOrg=Yu-Mo-13 \
    GitHubRepo=pmapp-back-v2 \
    DevelopBranchName=develop \
    MainBranchName=main
```

## 運用メモ

- `serverless.yml` は Serverless Framework の設定ファイルであり、CloudFormation テンプレートそのものではありません。
- `serverless deploy` は内部で CloudFormation スタックを生成して AWS にデプロイします。
- 今後、CloudFormation に切り出せる共通リソースはこの配下へ寄せていきます。
