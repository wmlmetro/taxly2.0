#!/bin/bash

# Ensure we are in the directory of the script
cd "$(dirname "$0")"

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}   Taxly Kubernetes Deployment Script${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""

# Check if kubectl is installed
if ! command -v kubectl &> /dev/null; then
    echo -e "${RED}Error: kubectl is not installed${NC}"
    exit 1
fi

build_and_push() {
    REGISTRY_IMAGE="registry.digitalocean.com/vendra-registry/taxly-app:latest"
    echo -e "\n${YELLOW}Building and pushing Docker image...${NC}"
    
    # Login to registry
    echo "Logging into DigitalOcean Container Registry..."
    if ! doctl registry login; then
        echo -e "${RED}Failed to login to DOCR. Make sure doctl is configured.${NC}"
        exit 1
    fi

    # Build image
    echo "Building image: $REGISTRY_IMAGE"
    # Go to root for build context
    cd ..
    docker build --platform linux/amd64 -t $REGISTRY_IMAGE .
    
    # Push image
    echo "Pushing image..."
    docker push $REGISTRY_IMAGE
    
    # Return to k8s dir
    cd k8s
    echo -e "${GREEN}Image built and pushed successfully!${NC}"
}

# Prompt for build
read -p "Do you want to build and push the Docker image first? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    build_and_push
fi

# Check if connected to cluster
if ! kubectl cluster-info &> /dev/null; then
    echo -e "${RED}Error: Not connected to a Kubernetes cluster${NC}"
    echo -e "${YELLOW}Please connect to your cluster using:${NC}"
    echo "  doctl kubernetes cluster kubeconfig save 0315a234-4213-4bf8-957d-801e651491d9"
    exit 1
fi

echo -e "${YELLOW}Current cluster:${NC}"
kubectl config current-context
echo ""

read -p "Is this the correct cluster? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    exit 1
fi

# Check if cert-manager is installed
echo -e "\n${YELLOW}Checking cert-manager installation...${NC}"
if ! kubectl get namespace cert-manager &> /dev/null; then
    echo -e "${YELLOW}cert-manager not found. Installing...${NC}"
    kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.13.0/cert-manager.yaml
    echo -e "${GREEN}Waiting for cert-manager to be ready...${NC}"
    kubectl wait --for=condition=Available --timeout=300s deployment/cert-manager -n cert-manager
    kubectl wait --for=condition=Available --timeout=300s deployment/cert-manager-webhook -n cert-manager
else
    echo -e "${GREEN}cert-manager is already installed${NC}"
fi

# Check if nginx-ingress is installed
echo -e "\n${YELLOW}Checking nginx-ingress-controller installation...${NC}"
if ! kubectl get namespace ingress-nginx &> /dev/null; then
    echo -e "${YELLOW}nginx-ingress-controller not found. Installing...${NC}"
    kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.8.1/deploy/static/provider/cloud/deploy.yaml
    echo -e "${GREEN}Waiting for nginx-ingress-controller to be ready...${NC}"
    kubectl wait --for=condition=Available --timeout=300s deployment/ingress-nginx-controller -n ingress-nginx
else
    echo -e "${GREEN}nginx-ingress-controller is already installed${NC}"
fi

# Check if DOCR secret exists
echo -e "\n${YELLOW}Checking DigitalOcean Container Registry secret...${NC}"
if ! kubectl get secret docr-secret -n taxly &> /dev/null 2>&1; then
    echo -e "${YELLOW}DOCR secret not found.${NC}"
    echo -e "${YELLOW}You need to create the DOCR secret to pull images.${NC}"
    echo -e "${YELLOW}Run: ./setup-docr.sh${NC}"
    echo ""
    read -p "Do you want to continue without DOCR secret? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
else
    echo -e "${GREEN}DOCR secret exists${NC}"
fi

# Apply Kubernetes manifests
echo -e "\n${GREEN}Deploying Taxly application...${NC}"

echo -e "${YELLOW}Creating namespace...${NC}"
kubectl apply -f namespace.yaml

echo -e "${YELLOW}Creating secrets...${NC}"
kubectl apply -f secrets.yaml

echo -e "${YELLOW}Creating configmap...${NC}"
kubectl apply -f configmap.yaml

echo -e "${YELLOW}Deploying MySQL...${NC}"
kubectl apply -f mysql-deployment.yaml

echo -e "${YELLOW}Creating MySQL LoadBalancer for external access...${NC}"
kubectl apply -f mysql-loadbalancer.yaml

echo -e "${YELLOW}Deploying Redis...${NC}"
kubectl apply -f redis-deployment.yaml

echo -e "${YELLOW}Deploying application...${NC}"
kubectl apply -f app-deployment.yaml

echo -e "${YELLOW}Creating cert-manager issuer...${NC}"
kubectl apply -f cert-issuer.yaml

echo -e "${YELLOW}Creating ingress...${NC}"
kubectl apply -f ingress.yaml

# Wait for deployments to be ready
echo -e "\n${GREEN}Waiting for deployments to be ready...${NC}"

echo -e "${YELLOW}Waiting for MySQL...${NC}"
kubectl wait --for=condition=Available --timeout=300s deployment/mysql -n taxly

echo -e "${YELLOW}Waiting for Redis...${NC}"
kubectl wait --for=condition=Available --timeout=300s deployment/redis -n taxly

echo -e "${YELLOW}Waiting for Taxly app...${NC}"
kubectl wait --for=condition=Available --timeout=300s deployment/taxly-app -n taxly

# Run database migrations (Verification)
echo -e "\n${GREEN}Verifying database migrations...${NC}"
# We rely on the pod's internal supervisord to run migrations, but we can trigger it or check it here if needed.
# For now, we'll just log that it's being handled by the app.
echo -e "${YELLOW}Migrations are handled automatically by the application pod on startup.${NC}"
# Optional: Trigger one manually just in case, or verify status
kubectl exec -n taxly deployment/taxly-app -- php artisan migrate --force || echo -e "${YELLOW}Migration already run or locked${NC}"

# Get ingress information
echo -e "\n${GREEN}================================================${NC}"
echo -e "${GREEN}   Deployment Complete!${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""

echo -e "${YELLOW}Ingress Information:${NC}"
kubectl get ingress -n taxly

echo ""
echo -e "${YELLOW}LoadBalancer IPs:${NC}"
echo -e "${GREEN}Web Application (for DNS - taxly.ng):${NC}"
kubectl get svc -n ingress-nginx ingress-nginx-controller -o jsonpath='{.status.loadBalancer.ingress[0].ip}'
echo ""
echo -e "${GREEN}MySQL Database (for external clients):${NC}"
kubectl get svc -n taxly mysql-external -o jsonpath='{.status.loadBalancer.ingress[0].ip}' 2>/dev/null || echo "Pending..."
echo ""

echo ""
echo -e "${GREEN}Next Steps:${NC}"
echo -e "1. Point your DNS records for ${YELLOW}taxly.ng${NC} and ${YELLOW}www.taxly.ng${NC} to the Web LoadBalancer IP"
echo -e "2. Wait for DNS propagation (can take up to 24 hours)"
echo -e "3. cert-manager will automatically issue SSL certificates from Let's Encrypt"
echo -e "4. Check certificate status: ${YELLOW}kubectl get certificate -n taxly${NC}"
echo -e "5. Use the MySQL LoadBalancer IP to connect from external database clients"
echo ""
echo -e "${YELLOW}MySQL Connection Info:${NC}"
echo -e "  Host:     $(kubectl get svc -n taxly mysql-external -o jsonpath='{.status.loadBalancer.ingress[0].ip}' 2>/dev/null || echo 'Pending...')"
echo -e "  Port:     3306"
echo -e "  Database: taxly_db"
echo -e "  Username: taxly_user"
echo -e "  Password: (from secrets.yaml)"
echo ""

echo -e "${GREEN}Useful Commands:${NC}"
echo -e "  View pods:       ${YELLOW}kubectl get pods -n taxly${NC}"
echo -e "  View logs:       ${YELLOW}kubectl logs -n taxly deployment/taxly-app -f${NC}"
echo -e "  View certificate:${YELLOW}kubectl describe certificate -n taxly taxly-tls-cert${NC}"
echo -e "  Scale app:       ${YELLOW}kubectl scale deployment/taxly-app --replicas=3 -n taxly${NC}"
echo ""
