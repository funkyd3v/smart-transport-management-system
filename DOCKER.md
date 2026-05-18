# Docker Setup Guide - Transport Management System

## Overview

This guide covers the production-ready Docker configuration for the Transport Management System. The setup includes best practices for security, performance, and scalability.

## Directory Structure

```
docker/
├── nginx/
│   └── default.conf          # Nginx configuration with security headers, caching, and compression
├── php/
│   ├── Dockerfile            # Development Dockerfile
│   ├── Dockerfile.prod       # Production Dockerfile (multi-stage build)
│   ├── php-fpm.conf          # PHP-FPM pool configuration
│   └── conf.d/
│       ├── opcache.ini       # OPcache optimization settings
│       └── php.ini           # PHP runtime settings
├── mysql/
│   └── my.cnf                # MySQL production configuration
└── nginx-ssl/                # SSL configuration (optional)
```

## Docker Compose Files

### Development Setup (docker-compose.yml)

Used for local development with:
- Hot reloading
- Debug mode enabled
- All services including Mailpit and Vite
- Full volume mounts for live editing

```bash
docker-compose up
```

### Production Setup (docker-compose.prod.yml)

Used for production deployment with:
- Optimized configurations
- Security hardening
- Resource limits and reservations
- Proper logging
- No exposed development services

```bash
docker-compose -f docker-compose.prod.yml up -d
```

## Key Improvements & Best Practices

### 1. Security Hardening

#### Container Security
- **Non-root user**: All services run as unprivileged user (UID 1000)
- **Read-only volumes**: Application code mounted as read-only in Nginx
- **No privileged containers**: Containers run without elevated privileges
- **Secret management**: Environment variables via `.env` file (not in code)

#### Application Security Headers
```nginx
X-Frame-Options: SAMEORIGIN           # Clickjacking protection
X-Content-Type-Options: nosniff        # MIME type sniffing prevention
X-XSS-Protection: 1; mode=block        # XSS protection
Referrer-Policy: no-referrer-when-downgrade
Permissions-Policy: Restricts browser APIs
Strict-Transport-Security: Forces HTTPS
```

#### File Protection
- Nginx denies access to hidden files (`.git`, `.env`)
- Backup files (`~`) are blocked
- Laravel framework directories cannot be accessed directly
- Only public directory is exposed

### 2. Performance Optimization

#### Nginx Optimization
- **Gzip compression**: Reduces response size by ~70% for text content
- **Static file caching**: 1-year expiration for assets (jpg, css, js, fonts)
- **Buffer optimization**: 32KB buffering for improved throughput
- **Health check endpoint**: `/health` for load balancer monitoring

#### PHP Optimization
- **OPcache**: Opcode caching enabled with 20K file limit
- **Pre-loading**: Ready for Laravel application preloading
- **Memory limits**: Tuned for production workloads (512MB)
- **Process management**: Dynamic PHP-FPM with max 20 children

#### Database Optimization
- **InnoDB buffer pool**: 512MB for caching (production)
- **Connection pooling**: Max 500 connections
- **Query optimization**: Slow query logging (>2 seconds)
- **Binary logging**: Enabled for replication/backups

#### Redis Optimization
- **Memory management**: LRU eviction policy (256MB max)
- **Persistence**: AOF (Append-Only File) for durability
- **Password protection**: Requires authentication

### 3. Health Checks

All services include health checks:

```yaml
healthcheck:
  test: [health-check-command]
  interval: 30s
  timeout: 10s
  retries: 3
  start_period: 40s
```

Services restart automatically if unhealthy.

### 4. Resource Management

Resource limits ensure fair distribution:

```yaml
deploy:
  resources:
    limits:      # Hard cap - container gets killed if exceeded
      cpus: '2'
      memory: 512M
    reservations: # Soft limit - guaranteed minimum
      cpus: '1'
      memory: 256M
```

### 5. Logging

Production setup uses JSON logging driver with:
- 10MB max file size
- 3 rotated files retention
- Structured logging for centralized log aggregation

### 6. Database Configuration

MySQL 8.4 with production settings:
- **UTF-8 support**: Full unicode support (utf8mb4)
- **Performance tuning**: Optimized buffer sizes and connection limits
- **Binary logging**: For replication and point-in-time recovery
- **Slow query logging**: Identifies performance bottlenecks

### 7. Multi-Stage Builds

Dockerfile.prod uses multi-stage builds:
1. **Builder stage**: Installs build dependencies and Composer packages
2. **Production stage**: Only runtime dependencies, smaller final image

Benefits:
- Reduced image size (~40% smaller)
- No build tools in production
- Faster deployment

## Environment Setup

### Development

Create `.env` from example:
```bash
cp .env.example .env
```

Default development credentials:
```
APP_ENV=local
APP_DEBUG=true
DB_USERNAME=tms
DB_PASSWORD=tms
```

### Production

Create production `.env`:
```bash
cp .env.example .env.production
```

Update with production values:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_PASSWORD=<strong-password>
REDIS_PASSWORD=<strong-password>
DB_ROOT_PASSWORD=<strong-password>
```

## Usage

### Local Development

```bash
# Start all services
docker-compose up

# Access the application
# App: http://localhost:8000
# Mailpit: http://localhost:8025
# Vite: http://localhost:5173

# Run Laravel commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan seed
```

### Production Deployment

```bash
# Build production image
docker-compose -f docker-compose.prod.yml build

# Start services
docker-compose -f docker-compose.prod.yml up -d

# View logs
docker-compose -f docker-compose.prod.yml logs -f

# Scale queue workers
docker-compose -f docker-compose.prod.yml up -d --scale queue=3
```

### Database Backup

```bash
# Backup MySQL database
docker-compose exec mysql mysqldump -uroot -p$MYSQL_ROOT_PASSWORD $DB_DATABASE > backup.sql

# Restore from backup
cat backup.sql | docker-compose exec -T mysql mysql -uroot -p$MYSQL_ROOT_PASSWORD $DB_DATABASE
```

### Queue Management

```bash
# Process queue jobs
docker-compose -f docker-compose.prod.yml exec queue php artisan queue:work

# Process jobs with retry
docker-compose -f docker-compose.prod.yml exec queue php artisan queue:work --max-tries=3

# Listen to failed jobs
docker-compose -f docker-compose.prod.yml exec queue php artisan queue:retry all
```

## SSL/HTTPS Setup

### Option 1: Let's Encrypt with Certbot

```bash
# Install certbot
sudo apt-get install certbot python3-certbot-nginx

# Generate certificate
sudo certbot certonly --standalone -d yourdomain.com

# Mount certificates in docker-compose.prod.yml
volumes:
  - /etc/letsencrypt:/etc/letsencrypt:ro
```

### Option 2: Self-signed Certificate (Development)

```bash
docker-compose exec web openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/nginx/ssl/private.key \
  -out /etc/nginx/ssl/certificate.crt
```

## Troubleshooting

### Service Won't Start

```bash
# Check logs
docker-compose logs -f [service-name]

# Rebuild image
docker-compose build --no-cache [service-name]
```

### Database Connection Issues

```bash
# Test MySQL connection
docker-compose exec app mysql -h mysql -u tms -p -D tms

# Check MySQL health
docker-compose exec mysql mysqladmin -uroot -p ping
```

### Permissions Issues

```bash
# Fix storage directory permissions
docker-compose exec app chown -R 1000:1000 storage bootstrap/cache
docker-compose exec app chmod -R 755 storage bootstrap/cache
```

### Memory Issues

```bash
# Check container resource usage
docker stats

# Increase memory in compose file and restart
docker-compose down
docker-compose up -d
```

## Performance Monitoring

### Monitor Container Stats

```bash
docker stats
```

### View Slow Queries

```bash
docker-compose exec mysql tail -f /var/log/mysql/slow-query.log
```

### Check PHP-FPM Status

```bash
docker-compose exec app curl localhost:9000/status
```

## Scaling for Production

### Load Balancing

Use Traefik or Nginx for load balancing multiple app instances:

```bash
# Scale to 3 app instances
docker-compose -f docker-compose.prod.yml up -d --scale app=3
```

### Queue Workers

Scale queue workers based on job volume:

```bash
# Scale to 5 queue workers
docker-compose -f docker-compose.prod.yml up -d --scale queue=5
```

## Security Checklist

- [ ] Change default database passwords
- [ ] Set strong Redis password
- [ ] Enable HTTPS/SSL certificates
- [ ] Configure firewall rules
- [ ] Set up regular database backups
- [ ] Monitor container logs
- [ ] Update base images regularly (`alpine`, `nginx`, etc.)
- [ ] Scan images for vulnerabilities: `docker scan [image-name]`
- [ ] Use secret management system (Docker Secrets, Vault)
- [ ] Configure log aggregation (ELK, Datadog, etc.)

## Maintenance

### Update Base Images

```bash
# Pull latest base images
docker pull php:8.3-fpm-alpine
docker pull nginx:1.27-alpine
docker pull mysql:8.4
docker pull redis:7-alpine

# Rebuild application image
docker-compose build --no-cache
```

### Clean Up

```bash
# Remove unused images
docker image prune -a

# Remove unused volumes
docker volume prune

# Remove unused networks
docker network prune

# Full cleanup (use with caution)
docker system prune -a --volumes
```

## Performance Benchmarks

After implementation, expect:
- **Response time**: ~100-200ms (with gzip)
- **Throughput**: 500+ requests/second per app instance
- **Memory usage**: ~200MB per app container
- **CPU usage**: <30% on modern systems

## Additional Resources

- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [Nginx Performance Tuning](https://nginx.org/en/docs/http/ngx_http_gzip_module.html)
- [PHP-FPM Configuration](https://www.php.net/manual/en/install.fpm.configuration.php)
- [MySQL 8.4 Documentation](https://dev.mysql.com/doc/)
- [Redis Security](https://redis.io/docs/management/security/)

## Support

For issues or improvements, please create an issue in the repository.
