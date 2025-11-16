# Environment Variables for Taxly Kubernetes Deployment

This document lists all environment variables found in `.env.docker` and how they're configured in Kubernetes.

## Application Configuration

| Variable | Value | Type | Location |
|----------|-------|------|----------|
| APP_NAME | Taxly | ConfigMap | `configmap.yaml` |
| APP_ENV | production | ConfigMap | `configmap.yaml` |
| APP_KEY | base64:SEia9Tv/QtdvwYPSS2+34lKCBynLndBmjBmyN8E5Ysk= | Secret | `secrets.yaml` |
| APP_DEBUG | false | ConfigMap | `configmap.yaml` |
| APP_URL | https://taxly.ng | ConfigMap | `configmap.yaml` |
| APP_LOCALE | en | ConfigMap | `configmap.yaml` |
| APP_FALLBACK_LOCALE | en | ConfigMap | `configmap.yaml` |
| APP_FAKER_LOCALE | en_US | ConfigMap | `configmap.yaml` |

## Database Configuration

| Variable | Value | Type | Location |
|----------|-------|------|----------|
| DB_CONNECTION | mysql | ConfigMap | `configmap.yaml` |
| DB_HOST | mysql-service | ConfigMap | `configmap.yaml` |
| DB_PORT | 3306 | ConfigMap | `configmap.yaml` |
| DB_DATABASE | taxly_db | ConfigMap | `configmap.yaml` |
| DB_USERNAME | taxly_user | ConfigMap | `configmap.yaml` |
| DB_PASSWORD | taxly_password | Secret | `secrets.yaml` |
| MYSQL_ROOT_PASSWORD | root_password | Secret | `secrets.yaml` |

## Redis Configuration

| Variable | Value | Type | Location |
|----------|-------|------|----------|
| REDIS_CLIENT | phpredis | ConfigMap | `configmap.yaml` |
| REDIS_HOST | redis-service | ConfigMap | `configmap.yaml` |
| REDIS_PORT | 6379 | ConfigMap | `configmap.yaml` |
| REDIS_PASSWORD | null | ConfigMap | `configmap.yaml` |
| SESSION_DRIVER | redis | ConfigMap | `configmap.yaml` |
| SESSION_LIFETIME | 120 | ConfigMap | `configmap.yaml` |
| CACHE_STORE | redis | ConfigMap | `configmap.yaml` |
| CACHE_PREFIX | taxly_cache | ConfigMap | `configmap.yaml` |
| QUEUE_CONNECTION | redis | ConfigMap | `configmap.yaml` |

## Mail Configuration

| Variable | Value | Type | Location |
|----------|-------|------|----------|
| MAIL_MAILER | smtp | ConfigMap | `configmap.yaml` |
| MAIL_HOST | smtppro.zoho.com | ConfigMap | `configmap.yaml` |
| MAIL_PORT | 465 | ConfigMap | `configmap.yaml` |
| MAIL_USERNAME | taxly@taxly.ng | Secret | `secrets.yaml` |
| MAIL_PASSWORD | QsmwuX4caU2T | Secret | `secrets.yaml` |
| MAIL_ENCRYPTION | tls | ConfigMap | `configmap.yaml` |
| MAIL_FROM_ADDRESS | noreply@taxly.ng | ConfigMap | `configmap.yaml` |
| MAIL_FROM_NAME | Taxly | ConfigMap | `configmap.yaml` |

## FIRS API Configuration

| Variable | Value | Type | Location |
|----------|-------|------|----------|
| FIRS_API_BASE | https://einvoice1.firs.gov.ng | ConfigMap | `configmap.yaml` |
| FIRS_API_KEY | 825e368e-f405-4e63-85a9-69ba1802f549 | Secret | `secrets.yaml` |
| FIRS_SECRET_KEY | tWAVe6TpASb2zyUK78v5PNeG9kq4GBKtT6JTDh8aONXkjg5enLPiqqcaQ1GVwLcZCstzAVFBhhtwNvVnvCtWaFFHenczy6Ozp7OO | Secret | `secrets.yaml` |
| FIRS_PUBLIC_KEY | LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0K... | Secret | `secrets.yaml` |
| FIRS_CERTIFICATE | NklBL25zYU9QOGc3TXpRWStobE9WdlBQZlovbFIwM1hkdjlkNGI4UkNzQT0= | Secret | `secrets.yaml` |
| FIRS_INTEGRATOR_SERVICE_ID | 804E0279 | ConfigMap | `configmap.yaml` |

## Logging Configuration

| Variable | Value | Type | Location |
|----------|-------|------|----------|
| LOG_CHANNEL | stack | ConfigMap | `configmap.yaml` |
| LOG_STACK | single | ConfigMap | `configmap.yaml` |
| LOG_LEVEL | info | ConfigMap | `configmap.yaml` |

## Other Configuration

| Variable | Value | Type | Location |
|----------|-------|------|----------|
| BROADCAST_CONNECTION | log | ConfigMap | `configmap.yaml` |
| FILESYSTEM_DISK | local | ConfigMap | `configmap.yaml` |
| SESSION_ENCRYPT | false | ConfigMap | `configmap.yaml` |
| SESSION_PATH | / | ConfigMap | `configmap.yaml` |
| AWS_DEFAULT_REGION | us-east-1 | ConfigMap | `configmap.yaml` |
| AWS_USE_PATH_STYLE_ENDPOINT | false | ConfigMap | `configmap.yaml` |

## Security Notes

⚠️ **IMPORTANT SECURITY CONSIDERATIONS:**

### Secrets Management

All sensitive values are stored in `secrets.yaml` as Kubernetes Secrets. However, these are only base64 encoded by default. For production:

1. **Enable Secrets Encryption at Rest**
   ```bash
   # Check if encryption is enabled on your cluster
   kubectl get pod -n kube-system kube-apiserver-* -o yaml | grep encryption-provider-config
   ```

2. **Use External Secret Management** (Recommended for production)
   - HashiCorp Vault
   - AWS Secrets Manager with External Secrets Operator
   - Azure Key Vault
   - Google Secret Manager
   - Sealed Secrets

3. **Rotate Credentials Regularly**
   - APP_KEY: Rotate Laravel application key
   - DB_PASSWORD: Update database passwords
   - MAIL_PASSWORD: Rotate email credentials
   - FIRS API keys: Rotate API credentials

4. **Never Commit Secrets to Git**
   - Add `k8s/secrets.yaml` to `.gitignore` for production
   - Use environment-specific secrets files
   - Use CI/CD secrets management (GitHub Secrets, GitLab CI/CD variables)

### Service-Specific Notes

1. **Database (MySQL)**
   - Current password: `taxly_password` (change for production!)
   - Root password: `root_password` (change for production!)
   - Consider using managed database services (DigitalOcean Managed MySQL, AWS RDS, etc.)

2. **Mail Service (Zoho)**
   - Username: `taxly@taxly.ng`
   - Password is stored in secrets
   - Consider using app-specific passwords instead of account passwords

3. **FIRS API**
   - Production API keys are configured
   - Public key and certificate are base64 encoded
   - Ensure these credentials are kept secure

### Updating Secrets

To update a secret value:

```bash
# Edit the secret
kubectl edit secret taxly-secrets -n taxly

# Or delete and recreate
kubectl delete secret taxly-secrets -n taxly
kubectl apply -f secrets.yaml

# Restart pods to pick up new secrets
kubectl rollout restart deployment/taxly-app -n taxly
```

### Using GitHub Secrets for CI/CD

For the GitHub Actions workflow, you need to set:

**Repository Secrets** (Settings → Secrets and variables → Actions):
- `DIGITALOCEAN_ACCESS_TOKEN`: Your DigitalOcean API token

The workflow automatically uses `GITHUB_TOKEN` for pushing Docker images to GitHub Container Registry.

## ConfigMap vs Secret Decision Matrix

| Data Type | Storage | Examples |
|-----------|---------|----------|
| Public configuration | ConfigMap | URLs, port numbers, feature flags |
| Credentials | Secret | Passwords, API keys, tokens |
| Encoded data | Secret | Certificates, private keys |
| Service discovery | ConfigMap | Service names, hosts |
| Application settings | ConfigMap | Locale, environment name |

## Environment-Specific Configuration

For multiple environments (staging, production), create separate files:

```
k8s/
├── base/
│   ├── deployment.yaml
│   └── service.yaml
├── staging/
│   ├── kustomization.yaml
│   ├── secrets.yaml
│   └── configmap.yaml
└── production/
    ├── kustomization.yaml
    ├── secrets.yaml
    └── configmap.yaml
```

Then deploy with:
```bash
kubectl apply -k k8s/production
```
