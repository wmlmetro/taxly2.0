# Taxly Docker Deployment Guide

This guide provides comprehensive instructions for deploying the Taxly Laravel application using Docker, both locally and to Digital Ocean.

## 🐳 Docker Setup Overview

The Taxly application has been containerized with the following components:

-   **PHP 8.2-FPM** with required extensions
-   **Nginx** web server
-   **MySQL 8.0** database
-   **Redis** for caching and queues
-   **Supervisor** for process management

## 📁 Docker Configuration Files

```
.
├── Dockerfile                    # Main application container
├── docker-compose.yml          # Local development setup
├── .env.docker                 # Docker environment variables
├── deploy.sh                   # Deployment automation script
├── .do/
│   └── app.yaml               # Digital Ocean App Platform spec
├── docker/
│   ├── nginx/
│   │   └── default.conf       # Nginx configuration
│   ├── supervisor/
│   │   └── supervisord.conf   # Process management
│   └── entrypoint.sh          # Container startup script
└── DOCKER_README.md           # This file
```

## 🚀 Quick Start (Local Development)

### Prerequisites

-   Docker Engine 20.10+
-   Docker Compose 1.29+
-   4GB+ available RAM

### 1. Clone and Setup

```bash
# Clone the repository
git clone https://github.com/deasytech/taxly.git
cd taxly

# Make deployment script executable
chmod +x deploy.sh
```

### 2. Local Deployment

```bash
# Build and run locally
./deploy.sh local
```

### 3. Access the Application

-   **Web Application**: http://localhost:8080
-   **Database**: localhost:3306
-   **Redis**: localhost:6379

### 4. Stop the Application

```bash
docker-compose down
```

## 🔧 Manual Docker Commands

### Build the Image

```bash
docker build -t taxly:latest .
```

### Run with Docker Compose

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f

# Stop all services
docker-compose down

# Rebuild and restart
docker-compose down && docker-compose build --no-cache && docker-compose up -d
```

### Access Containers

```bash
# Access application container
docker-compose exec app bash

# Access database
docker-compose exec db mysql -u taxly_user -p taxly_db

# Access Redis
docker-compose exec redis redis-cli
```

## 🌐 Digital Ocean Deployment

### Option 1: Digital Ocean App Platform (Recommended)

#### Prerequisites

-   Digital Ocean account
-   doctl CLI tool installed
-   GitHub repository with your code

#### Setup Steps

1. **Update Configuration**

    ```bash
    # Edit the Digital Ocean app specification
    nano .do/app.yaml
    ```

    Update the GitHub repository information:

    ```yaml
    github:
        repo: your-username/taxly
        branch: main
    ```

2. **Deploy to Digital Ocean**

    ```bash
    ./deploy.sh do
    ```

3. **Monitor Deployment**
    - Check your Digital Ocean dashboard
    - View build logs in the App Platform interface
    - Update DNS settings to point to your app URL

### Option 2: Manual Docker Deployment

#### Build and Push Image

```bash
# Build the image
docker build -t taxly:latest .

# Tag for your registry
docker tag taxly:latest your-registry/taxly:latest

# Push to registry
docker push your-registry/taxly:latest
```

#### Deploy to Digital Ocean Droplet

1. Create a Droplet with Docker
2. SSH into the Droplet
3. Pull and run your image
4. Set up reverse proxy with Nginx

## 🔒 Security Considerations

### Environment Variables

-   Never commit sensitive data to version control
-   Use Docker secrets or environment variables
-   Update `.env.docker` with production values

### SSL/TLS

-   Enable HTTPS in production
-   Use Let's Encrypt for free SSL certificates
-   Update Nginx configuration for SSL

### Database Security

-   Use strong passwords
-   Restrict database access to application only
-   Regular backups

## 📊 Monitoring and Logging

### View Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs -f redis
```

### Health Checks

The application includes:

-   Database connectivity checks
-   Redis connectivity checks
-   Laravel queue monitoring
-   Horizon dashboard (if enabled)

## 🔧 Troubleshooting

### Common Issues

#### Port Conflicts

```bash
# Check what's using port 8080
sudo lsof -i :8080

# Use different ports in docker-compose.yml
```

#### Permission Issues

```bash
# Fix storage permissions
sudo chown -R $USER:$USER storage bootstrap/cache
```

#### Database Connection Issues

```bash
# Check database container
docker-compose exec db mysql -u root -p

# Reset database
docker-compose down
docker volume rm taxly_db_data
docker-compose up -d
```

#### Build Failures

```bash
# Clear Docker cache
docker system prune -a

# Rebuild without cache
docker-compose build --no-cache
```

## 📈 Scaling

### Horizontal Scaling

```bash
# Scale web containers
docker-compose up -d --scale app=3
```

### Vertical Scaling

-   Update `instance_size_slug` in `.do/app.yaml`
-   Increase Droplet size for manual deployments

## 🔄 CI/CD Integration

### GitHub Actions

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Digital Ocean

on:
    push:
        branches: [main]

jobs:
    deploy:
        runs-on: ubuntu-latest
        steps:
            - uses: actions/checkout@v2
            - name: Install doctl
              uses: digitalocean/action-doctl@v2
              with:
                  token: ${{ secrets.DIGITALOCEAN_ACCESS_TOKEN }}
            - name: Deploy to Digital Ocean
              run: doctl apps create --spec .do/app.yaml
```

## 📞 Support

For issues and questions:

-   Check the troubleshooting section
-   Review Docker logs
-   Ensure all prerequisites are met
-   Verify environment variables are correctly set

## 📝 License

This Docker configuration is part of the Taxly project and follows the same license terms.
