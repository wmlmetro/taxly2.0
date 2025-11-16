# Deployment Summary - DigitalOcean Container Registry & External MySQL

This document summarizes the updates made to support DigitalOcean Container Registry (DOCR) and external MySQL database access.

## What's Been Updated

### 1. Container Registry Migration (GitHub CR → DOCR)

**Changed from**: GitHub Container Registry (ghcr.io)
**Changed to**: DigitalOcean Container Registry (registry.digitalocean.com)

#### Updated Files:
- ✅ [.github/workflows/deploy.yml](.github/workflows/deploy.yml) - CI/CD now uses DOCR
- ✅ [k8s/app-deployment.yaml](k8s/app-deployment.yaml) - Image reference updated
- ✅ [k8s/app-deployment.yaml](k8s/app-deployment.yaml) - Added `imagePullSecrets`

#### New Files:
- 📄 [k8s/setup-docr.sh](k8s/setup-docr.sh) - Automated DOCR credentials setup
- 📄 [k8s/DOCR_SETUP.md](k8s/DOCR_SETUP.md) - Complete DOCR setup guide

### 2. External MySQL Database Access

**New Feature**: MySQL database is now accessible from external clients via LoadBalancer

#### New Files:
- 📄 [k8s/mysql-loadbalancer.yaml](k8s/mysql-loadbalancer.yaml) - LoadBalancer service for MySQL

#### Updated Files:
- ✅ [k8s/kustomization.yaml](k8s/kustomization.yaml) - Added mysql-loadbalancer.yaml
- ✅ [k8s/deploy.sh](k8s/deploy.sh) - Deploy MySQL LoadBalancer
- ✅ [k8s/deploy.sh](k8s/deploy.sh) - Show both LoadBalancer IPs

### 3. Documentation Updates

All documentation has been updated to reflect DOCR and external database access:

- ✅ [DEPLOYMENT.md](DEPLOYMENT.md) - Main deployment guide
- ✅ [k8s/README.md](k8s/README.md) - Kubernetes documentation
- 📄 [k8s/QUICK_REFERENCE.md](k8s/QUICK_REFERENCE.md) - Quick command reference (NEW)

## Image Configuration

### Before (GitHub Container Registry):
```yaml
image: ghcr.io/YOUR_GITHUB_USERNAME/taxly:latest
```

### After (DigitalOcean Container Registry):
```yaml
imagePullSecrets:
  - name: docr-secret
containers:
  - name: taxly-app
    image: registry.digitalocean.com/taxly/app:latest
```

## GitHub Actions Workflow

### Before:
- Used GitHub Container Registry
- Required: `GITHUB_TOKEN` (automatic)

### After:
- Uses DigitalOcean Container Registry
- Required: `DIGITALOCEAN_ACCESS_TOKEN` (manual setup)
- Images tagged as:
  - `registry.digitalocean.com/taxly/app:latest`
  - `registry.digitalocean.com/taxly/app:<commit-sha>`

## MySQL Access

### Internal Access (from within cluster):
```
Host: mysql-service
Port: 3306
```

### External Access (new):
```
Host: <MYSQL_LOADBALANCER_IP>
Port: 3306
```

Get the IP with:
```bash
kubectl get svc -n taxly mysql-external -o jsonpath='{.status.loadBalancer.ingress[0].ip}'
```

## Setup Instructions

### First-Time Setup

1. **Create DOCR** (if not already created):
   ```bash
   doctl registry create taxly --region nyc3 --subscription-tier basic
   ```

2. **Setup DOCR credentials in Kubernetes**:
   ```bash
   cd k8s
   ./setup-docr.sh
   ```

3. **Build and push first image**:
   ```bash
   doctl registry login
   docker build -t registry.digitalocean.com/taxly/app:latest .
   docker push registry.digitalocean.com/taxly/app:latest
   ```

4. **Setup GitHub Actions secret**:
   - Go to: Repository → Settings → Secrets and variables → Actions
   - Add: `DIGITALOCEAN_ACCESS_TOKEN`
   - Get token from: https://cloud.digitalocean.com/account/api/tokens

5. **Deploy**:
   ```bash
   cd k8s
   ./deploy.sh
   ```

### Existing Deployment Update

If you've already deployed, update with:

```bash
# 1. Setup DOCR credentials
cd k8s
./setup-docr.sh

# 2. Build and push to DOCR
doctl registry login
docker build -t registry.digitalocean.com/taxly/app:latest .
docker push registry.digitalocean.com/taxly/app:latest

# 3. Apply updated manifests
kubectl apply -f app-deployment.yaml
kubectl apply -f mysql-loadbalancer.yaml

# 4. Verify deployment
kubectl rollout status deployment/taxly-app -n taxly
kubectl get svc -n taxly mysql-external
```

## LoadBalancer IPs

After deployment, you'll have two LoadBalancer IPs:

### 1. Web Application LoadBalancer
```bash
kubectl get svc -n ingress-nginx ingress-nginx-controller
```
- **Use for**: DNS records (taxly.ng, www.taxly.ng)
- **Purpose**: HTTPS traffic to your application

### 2. MySQL LoadBalancer
```bash
kubectl get svc -n taxly mysql-external
```
- **Use for**: Database client connections
- **Purpose**: External database access (MySQL Workbench, TablePlus, etc.)

## Security Recommendations

⚠️ **IMPORTANT**: The MySQL database is now publicly accessible. Secure it:

### 1. Change Default Password
Edit [k8s/secrets.yaml](k8s/secrets.yaml):
```yaml
stringData:
  DB_PASSWORD: "YOUR_STRONG_PASSWORD_HERE"
  MYSQL_ROOT_PASSWORD: "YOUR_STRONG_ROOT_PASSWORD_HERE"
```

Apply changes:
```bash
kubectl apply -f k8s/secrets.yaml
kubectl rollout restart deployment/mysql -n taxly
kubectl rollout restart deployment/taxly-app -n taxly
```

### 2. Whitelist IPs
Edit [k8s/mysql-loadbalancer.yaml](k8s/mysql-loadbalancer.yaml):
```yaml
metadata:
  annotations:
    service.beta.kubernetes.io/do-loadbalancer-allow-list: "1.2.3.4/32,5.6.7.8/32"
```

Apply changes:
```bash
kubectl apply -f k8s/mysql-loadbalancer.yaml
```

### 3. Additional Security
- Enable MySQL SSL/TLS connections
- Use DigitalOcean Cloud Firewall
- Enable query logging
- Monitor access regularly
- Use strong, unique passwords

## Testing External Database Access

### From Command Line:
```bash
# Get MySQL IP
MYSQL_IP=$(kubectl get svc -n taxly mysql-external -o jsonpath='{.status.loadBalancer.ingress[0].ip}')

# Test connection
mysql -h $MYSQL_IP -P 3306 -u taxly_user -p taxly_db
```

### From MySQL Workbench:
1. Create new connection
2. Connection Name: `Taxly Production`
3. Hostname: `<MYSQL_LOADBALANCER_IP>`
4. Port: `3306`
5. Username: `taxly_user`
6. Password: `taxly_password` (or your updated password)
7. Default Schema: `taxly_db`
8. Test Connection

## CI/CD Workflow

### Automatic Deployment:
1. Push to `main` branch
2. GitHub Actions builds Docker image
3. Image pushed to DOCR
4. Deployed to Kubernetes
5. Migrations run automatically

### Manual Deployment:
1. Go to GitHub Actions
2. Select "Build and Deploy to Kubernetes"
3. Click "Run workflow"

## Quick Commands Reference

```bash
# View deployment status
kubectl get all -n taxly

# View LoadBalancer IPs
kubectl get svc -n ingress-nginx ingress-nginx-controller
kubectl get svc -n taxly mysql-external

# View logs
kubectl logs -n taxly deployment/taxly-app -f

# Connect to database externally
mysql -h $(kubectl get svc -n taxly mysql-external -o jsonpath='{.status.loadBalancer.ingress[0].ip}') -P 3306 -u taxly_user -p taxly_db

# Run migrations
kubectl exec -n taxly deployment/taxly-app -- php artisan migrate --force

# Scale application
kubectl scale deployment/taxly-app --replicas=3 -n taxly
```

## Documentation Files

| File | Purpose |
|------|---------|
| [DEPLOYMENT.md](DEPLOYMENT.md) | Main deployment guide |
| [k8s/README.md](k8s/README.md) | Detailed Kubernetes documentation |
| [k8s/DOCR_SETUP.md](k8s/DOCR_SETUP.md) | DOCR setup and usage guide |
| [k8s/ENV_VARIABLES.md](k8s/ENV_VARIABLES.md) | Environment variables reference |
| [k8s/QUICK_REFERENCE.md](k8s/QUICK_REFERENCE.md) | Quick command reference |

## Troubleshooting

### ImagePullBackOff Error
```bash
# Recreate DOCR secret
kubectl delete secret docr-secret -n taxly
cd k8s && ./setup-docr.sh
kubectl rollout restart deployment/taxly-app -n taxly
```

### Can't Connect to MySQL Externally
```bash
# Check LoadBalancer status
kubectl get svc -n taxly mysql-external

# Check if IP is assigned (may take 2-3 minutes)
# Check security groups/firewall
# Verify password
```

### SSL Certificate Not Issuing
```bash
# Check certificate status
kubectl describe certificate -n taxly taxly-tls-cert

# Verify DNS records are pointing to correct LoadBalancer
# May take up to 24 hours for DNS propagation
```

## Next Steps

1. ✅ Setup DOCR credentials: `cd k8s && ./setup-docr.sh`
2. ✅ Add `DIGITALOCEAN_ACCESS_TOKEN` to GitHub Secrets
3. ✅ Build and push first image to DOCR
4. ✅ Deploy: `cd k8s && ./deploy.sh`
5. ✅ Configure DNS records
6. ⚠️ **IMPORTANT**: Change default passwords in secrets.yaml
7. ⚠️ **IMPORTANT**: Whitelist IPs for MySQL LoadBalancer
8. ✅ Test external database access
9. ✅ Verify SSL certificate after DNS propagation
10. ✅ Test application at https://taxly.ng

## Support & Resources

- **DOCR Documentation**: https://docs.digitalocean.com/products/container-registry/
- **DigitalOcean Console**: https://cloud.digitalocean.com
- **Kubernetes Dashboard**: Access via doctl
- **Project Issues**: Check deployment logs and events

## Cost Implications

### DigitalOcean Container Registry:
- **Basic Plan**: $5/month (500MB storage, 500GB transfer)
- Recommended for production

### MySQL LoadBalancer:
- **DigitalOcean LoadBalancer**: $12/month
- Always-on cost for external database access

**Total Additional Cost**: ~$17/month

Consider using managed database (DigitalOcean Managed MySQL) for production.

---

**Deployment Date**: 2025-01-15
**Cluster ID**: 0315a234-4213-4bf8-957d-801e651491d9
**Registry**: registry.digitalocean.com/taxly
**Domain**: taxly.ng
