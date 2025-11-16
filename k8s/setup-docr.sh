#!/bin/bash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}   Setup DigitalOcean Container Registry${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""

# Check if doctl is installed
if ! command -v doctl &> /dev/null; then
    echo -e "${RED}Error: doctl is not installed${NC}"
    echo -e "${YELLOW}Install it with:${NC}"
    echo "  macOS: brew install doctl"
    echo "  Linux: snap install doctl"
    exit 1
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

# Create namespace if it doesn't exist
if ! kubectl get namespace taxly &> /dev/null; then
    echo -e "${YELLOW}Creating namespace 'taxly'...${NC}"
    kubectl create namespace taxly
fi

# Check if DOCR secret already exists
if kubectl get secret docr-secret -n taxly &> /dev/null; then
    echo -e "${YELLOW}DOCR secret already exists. Do you want to recreate it? (y/n)${NC}"
    read -p "> " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        kubectl delete secret docr-secret -n taxly
    else
        echo -e "${GREEN}Using existing DOCR secret${NC}"
        exit 0
    fi
fi

# Create DOCR secret
echo -e "${GREEN}Creating DigitalOcean Container Registry secret...${NC}"
echo -e "${YELLOW}This will use your current doctl authentication${NC}"

# Login to DOCR and create secret
doctl registry login

# Get the registry endpoint
REGISTRY="registry.digitalocean.com"

# Create docker-registry secret from doctl credentials
kubectl create secret docker-registry docr-secret \
  --docker-server=$REGISTRY \
  --docker-username=$(doctl registry docker-config | jq -r '.auths["registry.digitalocean.com"].username') \
  --docker-password=$(doctl registry docker-config | jq -r '.auths["registry.digitalocean.com"].password') \
  --docker-email=$(doctl account get --format Email --no-header) \
  -n taxly

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ DOCR secret created successfully${NC}"
else
    echo -e "${RED}✗ Failed to create DOCR secret${NC}"
    echo -e "${YELLOW}Note: Make sure you have a DigitalOcean Container Registry created${NC}"
    echo -e "${YELLOW}Create one at: https://cloud.digitalocean.com/registry${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}   Setup Complete!${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo -e "1. Make sure your DOCR repository is named: ${GREEN}taxly${NC}"
echo -e "2. Build and push your image:"
echo -e "   ${YELLOW}doctl registry login${NC}"
echo -e "   ${YELLOW}docker build -t registry.digitalocean.com/taxly/app:latest .${NC}"
echo -e "   ${YELLOW}docker push registry.digitalocean.com/taxly/app:latest${NC}"
echo -e "3. Deploy your application:"
echo -e "   ${YELLOW}cd k8s && ./deploy.sh${NC}"
echo ""
