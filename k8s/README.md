# Kubernetes Deployment for Taxly

This directory contains all the Kubernetes manifests needed to deploy the Taxly application.

## Prerequisites

1. **Kubernetes cluster** - DigitalOcean Kubernetes cluster with ID: `0315a234-4213-4bf8-957d-801e651491d9`
2. **kubectl** - Kubernetes CLI tool installed
3. **doctl** - DigitalOcean CLI tool (for DigitalOcean clusters)
4. **DigitalOcean Container Registry** - DOCR with repository named `taxly`
5. **cert-manager** - For automatic SSL certificate management
6. **nginx-ingress-controller** - For ingress support

## Initial Setup

### 1. Connect to your Kubernetes cluster

```bash
# Install doctl if not already installed
brew install doctl  # macOS
# or
snap install doctl  # Linux

# Authenticate with DigitalOcean
doctl auth init

# Save cluster kubeconfig
doctl kubernetes cluster kubeconfig save 0315a234-4213-4bf8-957d-801e651491d9
```

### 2. Install required controllers

```bash
# Install nginx-ingress-controller
kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.8.1/deploy/static/provider/cloud/deploy.yaml

# Install cert-manager
kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.13.0/cert-manager.yaml

# Wait for cert-manager to be ready
kubectl wait --for=condition=Available --timeout=300s deployment/cert-manager -n cert-manager
kubectl wait --for=condition=Available --timeout=300s deployment/cert-manager-webhook -n cert-manager
```

### 3. Setup DigitalOcean Container Registry

Create a Container Registry and configure Kubernetes to pull from it:

```bash
# Create registry (if not already created)
doctl registry create taxly --region nyc3 --subscription-tier basic

# Setup DOCR secret in Kubernetes
./setup-docr.sh

# Or see DOCR_SETUP.md for detailed instructions
```

The image reference is already configured in `app-deployment.yaml`:

```yaml
image: registry.digitalocean.com/taxly/app:latest
```

### 4. Build and Push Docker Image

```bash
# Login to DOCR
doctl registry login

# Build image
docker build -t registry.digitalocean.com/taxly/app:latest .

# Push image
docker push registry.digitalocean.com/taxly/app:latest
```

Or use GitHub Actions for automated builds (see `.github/workflows/deploy.yml`).

### 5. Deploy the application

```bash
# Apply all manifests
kubectl apply -k .

# Or apply individually
kubectl apply -f namespace.yaml
kubectl apply -f secrets.yaml
kubectl apply -f configmap.yaml
kubectl apply -f mysql-deployment.yaml
kubectl apply -f redis-deployment.yaml
kubectl apply -f app-deployment.yaml
kubectl apply -f cert-issuer.yaml
kubectl apply -f ingress.yaml
```

### 6. Verify deployment

```bash
# Check all resources in the taxly namespace
kubectl get all -n taxly

# Check ingress
kubectl get ingress -n taxly

# Check certificate
kubectl get certificate -n taxly

# Check pods
kubectl get pods -n taxly

# View logs
kubectl logs -n taxly deployment/taxly-app -f
```

### 7. Run initial database migrations

```bash
kubectl exec -n taxly deployment/taxly-app -- php artisan migrate --force
```

## DNS Configuration

Point your domain `taxly.ng` and `www.taxly.ng` to the LoadBalancer IP address of the nginx-ingress-controller:

```bash
# Get the external IP
kubectl get svc -n ingress-nginx ingress-nginx-controller

# Add A records in your DNS provider:
# taxly.ng     A    <EXTERNAL-IP>
# www.taxly.ng A    <EXTERNAL-IP>
```

## External Database Access

The MySQL database is exposed via a LoadBalancer for external client access:

```bash
# Get MySQL LoadBalancer IP
kubectl get svc -n taxly mysql-external

# Connection details:
# Host:     <LOADBALANCER-IP>
# Port:     3306
# Database: taxly_db
# Username: taxly_user
# Password: (from secrets.yaml - default: taxly_password)
```

### Security Recommendations for External Database Access

1. **Restrict IP Access**: Edit `mysql-loadbalancer.yaml` to whitelist specific IPs:
   ```yaml
   annotations:
     service.beta.kubernetes.io/do-loadbalancer-allow-list: "YOUR_IP/32,OFFICE_IP/32"
   ```

2. **Use Strong Passwords**: Change the default password in `secrets.yaml`

3. **Use SSL/TLS**: Configure MySQL to require SSL connections

4. **Monitor Access**: Enable MySQL query logging and monitor connections

5. **Firewall Rules**: Use DigitalOcean Cloud Firewall for additional protection

### Connecting from Database Clients

**MySQL Workbench:**
```
Connection Name: Taxly Production
Hostname: <LOADBALANCER-IP>
Port: 3306
Username: taxly_user
Password: <from secrets.yaml>
```

**Command Line:**
```bash
mysql -h <LOADBALANCER-IP> -P 3306 -u taxly_user -p taxly_db
```

**PHP (Laravel .env):**
```env
DB_HOST=<LOADBALANCER-IP>
DB_PORT=3306
DB_DATABASE=taxly_db
DB_USERNAME=taxly_user
DB_PASSWORD=<from secrets.yaml>
```

## GitHub Actions Setup

### Required Secrets

Add the following secrets to your GitHub repository (Settings → Secrets and variables → Actions):

1. **DIGITALOCEAN_ACCESS_TOKEN** - Your DigitalOcean API token
   - Create at: https://cloud.digitalocean.com/account/api/tokens

### How it works

The GitHub Actions workflow (`.github/workflows/deploy.yml`) will:

1. Build the Docker image on every push to `main` branch
2. Push the image to DigitalOcean Container Registry (DOCR)
3. Deploy the new image to your Kubernetes cluster
4. Run database migrations
5. Clear application caches

### Registry Details

Images are pushed to:
- `registry.digitalocean.com/taxly/app:latest`
- `registry.digitalocean.com/taxly/app:<commit-sha>`

## Environment Variables

All environment variables are managed through:

- **secrets.yaml** - Sensitive data (API keys, passwords, etc.)
- **configmap.yaml** - Non-sensitive configuration

To update environment variables:

1. Edit the respective file
2. Apply the changes: `kubectl apply -f secrets.yaml` or `kubectl apply -f configmap.yaml`
3. Restart the deployment: `kubectl rollout restart deployment/taxly-app -n taxly`

## Monitoring and Troubleshooting

```bash
# Check pod status
kubectl get pods -n taxly

# View logs
kubectl logs -n taxly deployment/taxly-app -f

# Describe pod for events
kubectl describe pod -n taxly <pod-name>

# Check ingress events
kubectl describe ingress -n taxly taxly-ingress

# Check certificate status
kubectl describe certificate -n taxly taxly-tls-cert

# Execute commands in the container
kubectl exec -it -n taxly deployment/taxly-app -- bash

# Check database connectivity
kubectl exec -it -n taxly deployment/taxly-app -- php artisan db:show
```

## Scaling

```bash
# Scale the application
kubectl scale deployment/taxly-app --replicas=3 -n taxly

# Auto-scaling (HPA)
kubectl autoscale deployment taxly-app --min=2 --max=10 --cpu-percent=80 -n taxly
```

## Backup

```bash
# Backup MySQL database
kubectl exec -n taxly deployment/mysql -- mysqldump -u root -p<password> taxly_db > backup.sql

# Restore database
kubectl exec -i -n taxly deployment/mysql -- mysql -u root -p<password> taxly_db < backup.sql
```

## Cleanup

```bash
# Delete all resources
kubectl delete -k .

# Or delete namespace (removes everything)
kubectl delete namespace taxly
```

## SSL Certificate

The SSL certificate is automatically managed by cert-manager using Let's Encrypt. The certificate will be automatically renewed before expiration.

To check certificate status:

```bash
kubectl get certificate -n taxly taxly-tls-cert
kubectl describe certificate -n taxly taxly-tls-cert
```

## Security Notes

⚠️ **IMPORTANT**: The `secrets.yaml` file contains sensitive information. Make sure to:

1. Never commit actual secrets to version control
2. Use separate secrets for production
3. Rotate credentials regularly
4. Use Kubernetes secrets encryption at rest
5. Limit access to the cluster using RBAC

Consider using external secret management tools like:
- HashiCorp Vault
- AWS Secrets Manager
- Azure Key Vault
- Google Secret Manager
