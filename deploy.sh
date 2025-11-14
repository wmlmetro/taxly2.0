#!/bin/bash

# Taxly Docker Deployment Script
# This script helps deploy the Taxly application to Digital Ocean

set -e

echo "🚀 Starting Taxly deployment process..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Function to build and run locally
build_local() {
    print_status "Building and running Taxly locally..."
    
    # Copy Docker environment file
    cp .env.docker .env
    
    # Build and start containers
    docker-compose down
    docker-compose build --no-cache
    docker-compose up -d
    
    print_status "Local deployment complete!"
    print_status "Application should be available at: http://localhost:8080"
    print_status "Database available at: localhost:3306"
    print_status "Redis available at: localhost:6379"
}

# Function to deploy to Digital Ocean
deploy_do() {
    print_status "Deploying to Digital Ocean App Platform..."
    
    # Check if doctl is installed
    if ! command -v doctl &> /dev/null; then
        print_error "doctl is not installed. Please install doctl first."
        print_error "Visit: https://docs.digitalocean.com/reference/doctl/how-to/install/"
        exit 1
    fi
    
    # Update app.yaml with your GitHub repo
    print_warning "Please update .do/app.yaml with your GitHub repository information"
    print_warning "Current repo: your-username/taxly"
    
    # Create the app on Digital Ocean
    doctl apps create --spec .do/app.yaml
    
    print_status "Digital Ocean deployment initiated!"
    print_status "Check your Digital Ocean dashboard for deployment status"
}

# Function to build Docker image for manual deployment
build_image() {
    print_status "Building Docker image for manual deployment..."
    
    # Build the image
    docker build -t taxly:latest .
    
    print_status "Docker image built successfully!"
    print_status "Image name: taxly:latest"
    print_status "You can now push this image to your container registry"
}

# Function to show usage
show_usage() {
    echo "Usage: $0 [OPTION]"
    echo ""
    echo "Options:"
    echo "  local     Build and run locally using Docker Compose"
    echo "  do        Deploy to Digital Ocean App Platform"
    echo "  build     Build Docker image for manual deployment"
    echo "  help      Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 local  # Run locally"
    echo "  $0 do     # Deploy to Digital Ocean"
    echo "  $0 build  # Build Docker image"
}

# Main script logic
case "$1" in
    local)
        build_local
        ;;
    do)
        deploy_do
        ;;
    build)
        build_image
        ;;
    help|--help|-h)
        show_usage
        ;;
    *)
        print_error "Invalid option: $1"
        show_usage
        exit 1
        ;;
esac

echo "✅ Deployment process completed!"
