# DigitalOcean Container Registry Setup

This guide explains how to set up and use DigitalOcean Container Registry (DOCR) with your Kubernetes cluster.

## Prerequisites

1. DigitalOcean account with Container Registry enabled
2. `doctl` CLI tool installed
3. Access to your Kubernetes cluster

## Create Container Registry

### 1. Create Registry via Web UI

1. Go to https://cloud.digitalocean.com/registry
2. Click "Create Registry"
3. Choose a name (suggested: `taxly`)
4. Select a subscription plan
5. Click "Create Registry"

### 2. Or Create via CLI

```bash
# List available plans
doctl registry options available-regions

# Create registry
doctl registry create taxly --region nyc3 --subscription-tier basic
```

## Setup Registry Secret in Kubernetes

### Option A: Automated Setup (Recommended)

Run the setup script:

```bash
cd k8s
./setup-docr.sh
```

This script will:
- Check if doctl is installed and authenticated
- Create the `taxly` namespace if it doesn't exist
- Create the `docr-secret` for pulling images from DOCR
- Verify the setup

### Option B: Manual Setup

```bash
# Login to DigitalOcean
doctl auth init

# Connect to your cluster
doctl kubernetes cluster kubeconfig save 0315a234-4213-4bf8-957d-801e651491d9

# Create namespace
kubectl create namespace taxly

# Create docker-registry secret
doctl registry login
kubectl create secret docker-registry docr-secret \
  --docker-server=registry.digitalocean.com \
  --docker-username=$(doctl registry docker-config | jq -r '.auths["registry.digitalocean.com"].username') \
  --docker-password=$(doctl registry docker-config | jq -r '.auths["registry.digitalocean.com"].password') \
  --docker-email=$(doctl account get --format Email --no-header) \
  -n taxly
```

## Build and Push Docker Images

### Manual Build and Push

```bash
# Login to DOCR
doctl registry login

# Build the image
docker build -t registry.digitalocean.com/taxly/app:latest .

# Push the image
docker push registry.digitalocean.com/taxly/app:latest

# Tag specific version
docker tag registry.digitalocean.com/taxly/app:latest registry.digitalocean.com/taxly/app:v1.0.0
docker push registry.digitalocean.com/taxly/app:v1.0.0
```

### Using GitHub Actions (Automated)

The GitHub Actions workflow (`.github/workflows/deploy.yml`) automatically:

1. Builds the Docker image on every push to `main`
2. Pushes to DOCR as:
   - `registry.digitalocean.com/taxly/app:latest`
   - `registry.digitalocean.com/taxly/app:${{ github.sha }}`
3. Updates the Kubernetes deployment

**Required GitHub Secret:**
- `DIGITALOCEAN_ACCESS_TOKEN` - Create at https://cloud.digitalocean.com/account/api/tokens

## Verify Setup

```bash
# Check if secret exists
kubectl get secret docr-secret -n taxly

# Describe the secret
kubectl describe secret docr-secret -n taxly

# Test pulling an image
kubectl run test-pod \
  --image=registry.digitalocean.com/taxly/app:latest \
  --image-pull-secrets=docr-secret \
  -n taxly \
  --rm -it --restart=Never \
  -- /bin/bash
```

## DOCR Integration in Kubernetes

The `app-deployment.yaml` includes the necessary configuration:

```yaml
spec:
  imagePullSecrets:
    - name: docr-secret
  containers:
    - name: taxly-app
      image: registry.digitalocean.com/taxly/app:latest
```

## Manage Registry

### List Images

```bash
# Via doctl
doctl registry repository list

# List tags for a repository
doctl registry repository list-tags taxly/app
```

### Delete Images

```bash
# Delete specific tag
doctl registry repository delete-tag taxly/app v1.0.0

# Delete entire repository
doctl registry repository delete taxly/app
```

### Garbage Collection

DOCR supports automatic garbage collection to remove untagged manifests:

```bash
# Start garbage collection
doctl registry garbage-collection start

# Check status
doctl registry garbage-collection get-active
```

## Cost Management

- **Basic Plan**: $5/month - 500MB storage, 500GB transfer
- **Starter Plan**: $20/month - 1TB storage, 1TB transfer
- **Professional Plan**: $40/month - 10TB storage, 5TB transfer

View current usage:

```bash
doctl registry get
```

## Troubleshooting

### ImagePullBackOff Error

If pods fail to pull images:

```bash
# Check pod events
kubectl describe pod <pod-name> -n taxly

# Common issues:
# 1. Secret doesn't exist or is in wrong namespace
kubectl get secret docr-secret -n taxly

# 2. Image doesn't exist in registry
doctl registry repository list-tags taxly/app

# 3. Secret expired (DOCR tokens expire after 24 hours)
# Recreate the secret:
kubectl delete secret docr-secret -n taxly
./setup-docr.sh
```

### Authentication Failed

```bash
# Re-authenticate with DigitalOcean
doctl auth init

# Login to registry
doctl registry login

# Verify authentication
docker pull registry.digitalocean.com/taxly/app:latest
```

### Registry Access from Local Machine

```bash
# Login
doctl registry login

# Pull image
docker pull registry.digitalocean.com/taxly/app:latest

# Run locally
docker run -p 8080:80 registry.digitalocean.com/taxly/app:latest
```

## Best Practices

1. **Use Specific Tags**: Instead of always using `:latest`, tag releases with versions
   ```bash
   docker tag registry.digitalocean.com/taxly/app:latest registry.digitalocean.com/taxly/app:v1.0.0
   ```

2. **Enable Garbage Collection**: Regularly clean up old images to save costs

3. **Scan Images**: Use DOCR's built-in vulnerability scanning
   ```bash
   doctl registry repository list-tags taxly/app --format Tag,VulnerabilityScanStatus
   ```

4. **Monitor Usage**: Keep track of storage and bandwidth usage
   ```bash
   doctl registry get
   ```

5. **Secure Tokens**: Never commit DOCR credentials to git. Use GitHub Secrets for CI/CD

## Integration with Kubernetes Cluster

DigitalOcean Kubernetes clusters can automatically integrate with DOCR:

```bash
# Enable integration (done automatically when using same DigitalOcean account)
doctl registry kubernetes-manifest | kubectl apply -f -
```

This creates a `registry-<name>` secret in all namespaces automatically.

## References

- [DOCR Documentation](https://docs.digitalocean.com/products/container-registry/)
- [DOCR API Reference](https://docs.digitalocean.com/reference/api/api-reference/#tag/Container-Registry)
- [Kubernetes ImagePullSecrets](https://kubernetes.io/docs/concepts/containers/images/#specifying-imagepullsecrets-on-a-pod)
