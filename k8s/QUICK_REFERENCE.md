# Quick Reference Guide

Quick commands and references for deploying and managing Taxly on Kubernetes.

## Initial Setup

```bash
# 1. Connect to cluster
doctl auth init
doctl kubernetes cluster kubeconfig save 0315a234-4213-4bf8-957d-801e651491d9

# 2. Create DOCR and setup credentials
doctl registry create taxly --region nyc3 --subscription-tier basic
cd k8s && ./setup-docr.sh

# 3. Build and push image
doctl registry login
docker build -t registry.digitalocean.com/taxly/app:latest .
docker push registry.digitalocean.com/taxly/app:latest

# 4. Deploy
./deploy.sh
```

## Container Registry (DOCR)

```bash
# Login to DOCR
doctl registry login

# Build image
docker build -t registry.digitalocean.com/taxly/app:latest .

# Tag version
docker tag registry.digitalocean.com/taxly/app:latest \
  registry.digitalocean.com/taxly/app:v1.0.0

# Push image
docker push registry.digitalocean.com/taxly/app:latest
docker push registry.digitalocean.com/taxly/app:v1.0.0

# List images
doctl registry repository list-tags taxly/app

# Delete old images
doctl registry repository delete-tag taxly/app v1.0.0
```

## Deployment Commands

```bash
# Full deployment
cd k8s && ./deploy.sh

# Or manual
kubectl apply -k k8s/

# Update only app deployment
kubectl apply -f k8s/app-deployment.yaml

# Force new image pull
kubectl rollout restart deployment/taxly-app -n taxly

# Rollback
kubectl rollout undo deployment/taxly-app -n taxly
```

## Get LoadBalancer IPs

```bash
# Web application (for DNS)
kubectl get svc -n ingress-nginx ingress-nginx-controller \
  -o jsonpath='{.status.loadBalancer.ingress[0].ip}'

# MySQL database (for external clients)
kubectl get svc -n taxly mysql-external \
  -o jsonpath='{.status.loadBalancer.ingress[0].ip}'
```

## Database Access

### From External Client

```bash
# Get MySQL IP
MYSQL_IP=$(kubectl get svc -n taxly mysql-external -o jsonpath='{.status.loadBalancer.ingress[0].ip}')

# Connect
mysql -h $MYSQL_IP -P 3306 -u taxly_user -p taxly_db
```

### Connection Info

| Parameter | Value |
|-----------|-------|
| Host | Run: `kubectl get svc -n taxly mysql-external` |
| Port | 3306 |
| Database | taxly_db |
| Username | taxly_user |
| Password | taxly_password (change in secrets.yaml!) |

### From Inside Cluster

```bash
# From app container
kubectl exec -it -n taxly deployment/taxly-app -- mysql -h mysql-service -u taxly_user -p taxly_db

# Laravel artisan
kubectl exec -n taxly deployment/taxly-app -- php artisan db:show
```

## Monitoring & Logs

```bash
# View app logs
kubectl logs -n taxly deployment/taxly-app -f

# View logs from specific pod
kubectl logs -n taxly <pod-name> -f

# View MySQL logs
kubectl logs -n taxly deployment/mysql -f

# Get pod status
kubectl get pods -n taxly

# Describe pod (for events)
kubectl describe pod -n taxly <pod-name>

# View all events
kubectl get events -n taxly --sort-by='.lastTimestamp'
```

## Laravel Artisan Commands

```bash
# Run migrations
kubectl exec -n taxly deployment/taxly-app -- php artisan migrate --force

# Clear caches
kubectl exec -n taxly deployment/taxly-app -- php artisan cache:clear
kubectl exec -n taxly deployment/taxly-app -- php artisan config:clear
kubectl exec -n taxly deployment/taxly-app -- php artisan view:clear

# Run queue worker (check if running)
kubectl exec -n taxly deployment/taxly-app -- php artisan queue:work --once

# Enter shell
kubectl exec -it -n taxly deployment/taxly-app -- bash
```

## Scaling

```bash
# Manual scaling
kubectl scale deployment/taxly-app --replicas=3 -n taxly

# Check replicas
kubectl get deployment -n taxly taxly-app

# Auto-scaling
kubectl autoscale deployment taxly-app \
  --min=2 --max=10 --cpu-percent=80 -n taxly

# Check HPA
kubectl get hpa -n taxly
```

## Environment Variables

```bash
# Edit ConfigMap
kubectl edit configmap taxly-config -n taxly

# Edit Secrets
kubectl edit secret taxly-secrets -n taxly

# View ConfigMap
kubectl get configmap taxly-config -n taxly -o yaml

# Restart pods to apply changes
kubectl rollout restart deployment/taxly-app -n taxly
```

## SSL Certificate

```bash
# Check certificate status
kubectl get certificate -n taxly

# Describe certificate (for events)
kubectl describe certificate -n taxly taxly-tls-cert

# Check cert-manager logs
kubectl logs -n cert-manager deployment/cert-manager

# Force certificate renewal
kubectl delete certificate -n taxly taxly-tls-cert
kubectl apply -f k8s/ingress.yaml
```

## Database Backup & Restore

```bash
# Backup
kubectl exec -n taxly deployment/mysql -- \
  mysqldump -u root -proot_password taxly_db > backup-$(date +%Y%m%d-%H%M%S).sql

# Restore
kubectl exec -i -n taxly deployment/mysql -- \
  mysql -u root -proot_password taxly_db < backup-20250115-120000.sql

# Copy backup from pod
kubectl cp taxly/<pod-name>:/backup.sql ./local-backup.sql
```

## GitHub Actions

```bash
# Required secret: DIGITALOCEAN_ACCESS_TOKEN
# Get from: https://cloud.digitalocean.com/account/api/tokens

# Workflow runs on:
# - Push to main branch
# - Manual trigger (workflow_dispatch)

# View workflow runs
# https://github.com/YOUR_USERNAME/taxly/actions
```

## Troubleshooting

```bash
# ImagePullBackOff - Recreate DOCR secret
kubectl delete secret docr-secret -n taxly
cd k8s && ./setup-docr.sh

# Pod not starting - Check events
kubectl describe pod -n taxly <pod-name>

# Database connection failed
kubectl exec -n taxly deployment/taxly-app -- php artisan db:show

# SSL cert not issuing
kubectl describe certificate -n taxly taxly-tls-cert
kubectl logs -n cert-manager deployment/cert-manager

# Check ingress
kubectl describe ingress -n taxly taxly-ingress

# Resource usage
kubectl top pods -n taxly
kubectl top nodes
```

## Security Checklist

- [ ] Change all default passwords in secrets.yaml
- [ ] Whitelist IPs for MySQL LoadBalancer
- [ ] Enable MySQL SSL/TLS connections
- [ ] Configure DigitalOcean Cloud Firewall
- [ ] Enable Kubernetes secrets encryption at rest
- [ ] Set up automated database backups
- [ ] Configure monitoring and alerts
- [ ] Review and limit RBAC permissions
- [ ] Enable audit logging
- [ ] Regularly update container images

## Resource Limits

Current configuration:

| Component | CPU Request | CPU Limit | Memory Request | Memory Limit |
|-----------|-------------|-----------|----------------|--------------|
| App | 250m | 500m | 512Mi | 1Gi |
| MySQL | 250m | 500m | 512Mi | 1Gi |
| Redis | 100m | 200m | 256Mi | 512Mi |

Adjust in respective deployment YAML files.

## DNS Records

Add these A records in your DNS provider:

```
taxly.ng     A    <WEB_LOADBALANCER_IP>
www.taxly.ng A    <WEB_LOADBALANCER_IP>
```

Get IPs:
```bash
kubectl get svc -n ingress-nginx ingress-nginx-controller
```

## Cleanup

```bash
# Delete everything
kubectl delete namespace taxly

# Or use kustomize
kubectl delete -k k8s/

# Keep data, delete only app
kubectl delete -f k8s/app-deployment.yaml
```

## Important Links

- **DOCR Setup**: [k8s/DOCR_SETUP.md](DOCR_SETUP.md)
- **Full Documentation**: [k8s/README.md](README.md)
- **Environment Variables**: [k8s/ENV_VARIABLES.md](ENV_VARIABLES.md)
- **Deployment Guide**: [DEPLOYMENT.md](../DEPLOYMENT.md)
- **DigitalOcean Console**: https://cloud.digitalocean.com
- **Kubernetes Dashboard**: `doctl kubernetes cluster kubeconfig save <cluster-id>`

## Quick Health Check

```bash
#!/bin/bash
echo "=== Pods ==="
kubectl get pods -n taxly

echo -e "\n=== Services ==="
kubectl get svc -n taxly

echo -e "\n=== Ingress ==="
kubectl get ingress -n taxly

echo -e "\n=== Certificate ==="
kubectl get certificate -n taxly

echo -e "\n=== Recent Events ==="
kubectl get events -n taxly --sort-by='.lastTimestamp' | tail -5
```

Save as `health-check.sh` and run with: `bash health-check.sh`
