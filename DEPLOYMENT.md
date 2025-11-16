# Taxly Kubernetes Deployment Guide

Complete guide to deploy Taxly to Kubernetes cluster `0315a234-4213-4bf8-957d-801e651491d9`

## Quick Start

### 1. Prerequisites

Install required tools:
```bash
# macOS
brew install kubectl doctl

# Linux
snap install kubectl doctl
```

### 2. Connect to Cluster

```bash
# Authenticate with DigitalOcean
doctl auth init

# Connect to your cluster
doctl kubernetes cluster kubeconfig save 0315a234-4213-4bf8-957d-801e651491d9
```

### 3. Setup DigitalOcean Container Registry (DOCR)

Create and configure DOCR for storing Docker images:

```bash
# Create registry (if not already created)
doctl registry create taxly --region nyc3 --subscription-tier basic

# Setup DOCR credentials in Kubernetes
cd k8s
./setup-docr.sh
```

See [k8s/DOCR_SETUP.md](k8s/DOCR_SETUP.md) for detailed instructions.

### 4. Build and Push Docker Image

```bash
# Login to DOCR
doctl registry login

# Build the image
docker build -t registry.digitalocean.com/taxly/app:latest .

# Push to DOCR
docker push registry.digitalocean.com/taxly/app:latest
```

Or skip this step if using GitHub Actions (it will build and push automatically).

### 5. Deploy

**Option A: Using the deployment script (Recommended)**
```bash
cd k8s
./deploy.sh
```

**Option B: Manual deployment**
```bash
cd k8s
kubectl apply -k .
```

### 6. Configure DNS

Get the LoadBalancer IP:
```bash
# Web application LoadBalancer
kubectl get svc -n ingress-nginx ingress-nginx-controller

# MySQL LoadBalancer (for external database access)
kubectl get svc -n taxly mysql-external
```

Add these DNS records in your domain registrar:
- `taxly.ng` → A record → `<WEB_LOADBALANCER_IP>`
- `www.taxly.ng` → A record → `<WEB_LOADBALANCER_IP>`

### 7. Verify Deployment

```bash
# Check all resources
kubectl get all -n taxly

# Check certificate (wait for DNS propagation first)
kubectl get certificate -n taxly

# View application logs
kubectl logs -n taxly deployment/taxly-app -f
```

## GitHub Actions CI/CD Setup

### Required GitHub Secrets

Go to your repository → Settings → Secrets and variables → Actions → New repository secret:

| Secret Name | Description | Where to get it |
|-------------|-------------|-----------------|
| `DIGITALOCEAN_ACCESS_TOKEN` | DigitalOcean API token | https://cloud.digitalocean.com/account/api/tokens |

### How CI/CD Works

1. **Push to main branch** triggers the workflow
2. **Build**: Docker image is built and pushed to DigitalOcean Container Registry
3. **Deploy**: Image is deployed to Kubernetes cluster
4. **Migrate**: Database migrations run automatically
5. **Cache Clear**: Application caches are cleared

### Registry Details

Images are automatically pushed to:
- `registry.digitalocean.com/taxly/app:latest`
- `registry.digitalocean.com/taxly/app:<commit-sha>`

View workflow runs at: `https://github.com/YOUR_USERNAME/taxly/actions`

### Manual Deployment Trigger

You can manually trigger a deployment:
1. Go to Actions tab in GitHub
2. Select "Build and Deploy to Kubernetes"
3. Click "Run workflow"

## Project Structure

```
taxly2.0/
├── k8s/                          # Kubernetes manifests
│   ├── namespace.yaml            # Namespace definition
│   ├── secrets.yaml              # Sensitive data (⚠️ don't commit to git)
│   ├── configmap.yaml            # Non-sensitive config
│   ├── mysql-deployment.yaml     # MySQL database
│   ├── mysql-loadbalancer.yaml   # MySQL external access
│   ├── redis-deployment.yaml     # Redis cache/queue
│   ├── app-deployment.yaml       # Laravel application (with DOCR)
│   ├── cert-issuer.yaml          # Let's Encrypt certificate issuer
│   ├── ingress.yaml              # Ingress with SSL
│   ├── kustomization.yaml        # Kustomize config
│   ├── deploy.sh                 # Automated deployment script
│   ├── setup-docr.sh             # DOCR credentials setup
│   ├── README.md                 # Detailed K8s documentation
│   ├── DOCR_SETUP.md             # DOCR setup guide
│   └── ENV_VARIABLES.md          # Environment variables reference
├── .github/
│   └── workflows/
│       └── deploy.yml            # GitHub Actions workflow (DOCR)
└── Dockerfile                    # Docker image definition
```

## Environment Variables

All environment variables from [.env.docker](.env.docker) have been configured:

- **Secrets** (sensitive data) → [k8s/secrets.yaml](k8s/secrets.yaml)
- **ConfigMap** (public config) → [k8s/configmap.yaml](k8s/configmap.yaml)

See [k8s/ENV_VARIABLES.md](k8s/ENV_VARIABLES.md) for complete reference.

### Key Variables

| Category | Variables | Location |
|----------|-----------|----------|
| App | APP_KEY, APP_URL | Secret, ConfigMap |
| Database | DB_HOST=mysql-service, DB_PASSWORD | ConfigMap, Secret |
| Redis | REDIS_HOST=redis-service | ConfigMap |
| Mail | MAIL_USERNAME, MAIL_PASSWORD | Secret |
| FIRS API | FIRS_API_KEY, FIRS_SECRET_KEY | Secret |

## External Database Access

The MySQL database is exposed via a LoadBalancer for external client connections:

```bash
# Get MySQL LoadBalancer IP
kubectl get svc -n taxly mysql-external
```

### Connection Details

| Parameter | Value |
|-----------|-------|
| Host | `<MYSQL_LOADBALANCER_IP>` |
| Port | 3306 |
| Database | taxly_db |
| Username | taxly_user |
| Password | taxly_password (from secrets.yaml) |

### Database Clients

**MySQL Workbench:**
```
Hostname: <MYSQL_LOADBALANCER_IP>
Port: 3306
Username: taxly_user
Password: <from secrets.yaml>
Database: taxly_db
```

**Command Line:**
```bash
mysql -h <MYSQL_LOADBALANCER_IP> -P 3306 -u taxly_user -p taxly_db
```

**TablePlus / DBeaver / HeidiSQL:**
- Same connection parameters as above

### Security Recommendations

⚠️ **IMPORTANT**: The database is publicly accessible. Secure it properly:

1. **Change default password** in [k8s/secrets.yaml](k8s/secrets.yaml)
2. **Whitelist IPs**: Edit [k8s/mysql-loadbalancer.yaml](k8s/mysql-loadbalancer.yaml):
   ```yaml
   annotations:
     service.beta.kubernetes.io/do-loadbalancer-allow-list: "YOUR_IP/32,OFFICE_IP/32"
   ```
3. **Enable SSL/TLS** for MySQL connections
4. **Monitor access** via MySQL logs
5. **Use DigitalOcean Cloud Firewall** for additional protection

See [k8s/README.md](k8s/README.md) for more security details.

## SSL Certificate

SSL certificates are automatically managed by cert-manager using Let's Encrypt:

- **Domain**: taxly.ng, www.taxly.ng
- **Issuer**: Let's Encrypt (production)
- **Renewal**: Automatic (60 days before expiration)

Check certificate status:
```bash
kubectl describe certificate -n taxly taxly-tls-cert
```

## Common Operations

### View Logs
```bash
kubectl logs -n taxly deployment/taxly-app -f
```

### Run Artisan Commands
```bash
# Run migrations
kubectl exec -n taxly deployment/taxly-app -- php artisan migrate

# Clear cache
kubectl exec -n taxly deployment/taxly-app -- php artisan cache:clear

# Enter container shell
kubectl exec -it -n taxly deployment/taxly-app -- bash
```

### Scale Application
```bash
# Manual scaling
kubectl scale deployment/taxly-app --replicas=3 -n taxly

# Auto-scaling
kubectl autoscale deployment taxly-app --min=2 --max=10 --cpu-percent=80 -n taxly
```

### Update Environment Variables
```bash
# Edit ConfigMap
kubectl edit configmap taxly-config -n taxly

# Edit Secrets
kubectl edit secret taxly-secrets -n taxly

# Restart to apply changes
kubectl rollout restart deployment/taxly-app -n taxly
```

### Rollback Deployment
```bash
# View rollout history
kubectl rollout history deployment/taxly-app -n taxly

# Rollback to previous version
kubectl rollout undo deployment/taxly-app -n taxly

# Rollback to specific revision
kubectl rollout undo deployment/taxly-app --to-revision=2 -n taxly
```

### Database Backup
```bash
# Create backup
kubectl exec -n taxly deployment/mysql -- mysqldump -u root -proot_password taxly_db > backup-$(date +%Y%m%d).sql

# Restore backup
kubectl exec -i -n taxly deployment/mysql -- mysql -u root -proot_password taxly_db < backup-20250115.sql
```

## Monitoring

### Check Resource Usage
```bash
# Pod resource usage
kubectl top pods -n taxly

# Node resource usage
kubectl top nodes
```

### View Events
```bash
# All events in namespace
kubectl get events -n taxly --sort-by='.lastTimestamp'

# Specific resource events
kubectl describe pod -n taxly <pod-name>
```

### Application Health
```bash
# Check pod status
kubectl get pods -n taxly

# Check endpoints
kubectl get endpoints -n taxly

# Check ingress
kubectl get ingress -n taxly
```

## Troubleshooting

### Pods not starting
```bash
# Check pod status
kubectl get pods -n taxly

# View pod events
kubectl describe pod -n taxly <pod-name>

# Check logs
kubectl logs -n taxly <pod-name>
```

### Database connection issues
```bash
# Test database connectivity
kubectl exec -it -n taxly deployment/taxly-app -- php artisan db:show

# Check MySQL service
kubectl get svc -n taxly mysql-service

# Check MySQL logs
kubectl logs -n taxly deployment/mysql
```

### SSL Certificate issues
```bash
# Check certificate status
kubectl get certificate -n taxly

# Describe certificate for events
kubectl describe certificate -n taxly taxly-tls-cert

# Check cert-manager logs
kubectl logs -n cert-manager deployment/cert-manager
```

### Application errors
```bash
# View real-time logs
kubectl logs -n taxly deployment/taxly-app -f

# Check Laravel logs in pod
kubectl exec -n taxly deployment/taxly-app -- tail -f storage/logs/laravel.log
```

## Security Best Practices

⚠️ **IMPORTANT**: Before going to production:

1. **Change all default passwords** in `secrets.yaml`
2. **Enable Kubernetes secrets encryption at rest**
3. **Use external secret management** (Vault, AWS Secrets Manager, etc.)
4. **Set up RBAC** for cluster access control
5. **Enable network policies** for pod-to-pod communication
6. **Regular security updates** for all container images
7. **Add `.gitignore` entry** for `k8s/secrets.yaml`

## Resource Allocation

Current resource limits:

| Component | CPU Request | CPU Limit | Memory Request | Memory Limit |
|-----------|-------------|-----------|----------------|--------------|
| App | 250m | 500m | 512Mi | 1Gi |
| MySQL | 250m | 500m | 512Mi | 1Gi |
| Redis | 100m | 200m | 256Mi | 512Mi |

Adjust in respective deployment YAML files based on actual usage.

## Cleanup

To remove the entire deployment:

```bash
# Delete all resources
kubectl delete namespace taxly

# Or use kustomize
cd k8s
kubectl delete -k .
```

## Support

For detailed Kubernetes documentation, see [k8s/README.md](k8s/README.md)

For environment variables reference, see [k8s/ENV_VARIABLES.md](k8s/ENV_VARIABLES.md)

## Next Steps

1. ✅ Deploy to Kubernetes
2. ✅ Configure DNS records
3. ✅ Set up GitHub Actions
4. ⏳ Wait for SSL certificate (after DNS propagation)
5. ⏳ Test application at https://taxly.ng
6. 🔒 Implement additional security measures
7. 📊 Set up monitoring and alerting (Prometheus, Grafana)
8. 💾 Configure automated database backups
9. 🔄 Set up staging environment
10. 📝 Document disaster recovery procedures
