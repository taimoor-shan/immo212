# IMMO212 Real Estate Platform - Deployment System

## 🚀 CloudPanel + Deployer PHP Deployment

This Laravel application uses **CloudPanel** for server management and **Deployer PHP** for automated, zero-downtime deployments.

### ⚡ Quick Commands

```bash
# Standard deployment
./deploy.sh

# Deploy with migrations
./deploy.sh migrate

# Quick deployment (assets only)
./deploy.sh quick

# Rollback to previous release
./deploy.sh rollback

# Test deployment setup
./test-deployment.sh
```

## 📁 Deployment Files

| File | Purpose |
|------|---------|
| `deploy.php` | Main Deployer configuration |
| `deploy.sh` | Deployment helper script |
| `test-deployment.sh` | Deployment testing script |
| `deploy-config.example.php` | Configuration template |
| `.env.production.example` | Production environment template |
| `.env.staging.example` | Staging environment template |
| `DEPLOYMENT.md` | Comprehensive deployment guide |
| `SETUP.md` | Step-by-step setup instructions |

## 🏗️ Architecture

### Server Structure (CloudPanel)
```
/home/cloudpanel/htdocs/yourdomain.com/
├── current -> releases/20250725120000/    # Active release
├── releases/                              # All deployments
│   ├── 20250725120000/                   # Latest
│   ├── 20250724110000/                   # Previous
│   └── 20250723100000/                   # Older
├── shared/                               # Shared between releases
│   ├── .env                             # Environment config
│   ├── storage/                         # Laravel storage
│   └── public/storage -> ../../shared/storage/app/public
└── backups/                             # Database backups
```

### Deployment Flow
1. **Local**: Build assets (`npm run production`)
2. **Server**: Create new release directory
3. **Server**: Clone code from Git repository
4. **Server**: Install dependencies (`composer install`)
5. **Server**: Upload compiled assets
6. **Server**: Run Laravel optimizations
7. **Server**: **Atomic switch** of `current` symlink (zero-downtime)
8. **Server**: Restart PHP-FPM and queues

## 🎯 Features

### ✅ Zero-Downtime Deployments
- Atomic symlink switching
- Shared resources (storage, .env)
- Instant rollback capability

### ✅ Database Safety
- Automatic backups before migrations
- Migration rollback support
- Database restoration capabilities

### ✅ Asset Optimization
- Local asset compilation
- Gzip compression
- Proper file permissions

### ✅ Environment Management
- Production/staging configurations
- Environment validation
- CloudPanel integration

### ✅ Monitoring & Testing
- Deployment testing suite
- Health checks
- Comprehensive logging

## 🚀 Getting Started

### 1. Quick Setup (5 minutes)

```bash
# 1. Update repository in deploy.php
# 2. Configure server details
# 3. Set up SSH keys
# 4. Create .env on server
# 5. Deploy!

./deploy.sh
```

### 2. Detailed Setup

See [SETUP.md](SETUP.md) for complete instructions.

## 📖 Documentation

- **[SETUP.md](SETUP.md)** - Complete setup guide
- **[DEPLOYMENT.md](DEPLOYMENT.md)** - Deployment workflows and troubleshooting
- **[deploy-config.example.php](deploy-config.example.php)** - Configuration options
- **[.env.production.example](.env.production.example)** - Production environment template

## 🛠️ Available Commands

### Deployment Commands
```bash
./deploy.sh                    # Standard deployment
./deploy.sh migrate           # Deploy with migrations
./deploy.sh quick             # Quick deploy (assets only)
./deploy.sh rollback          # Rollback deployment
./deploy.sh staging           # Deploy to staging
```

### Testing Commands
```bash
./test-deployment.sh          # Full deployment test
./test-deployment.sh build    # Test asset building
./test-deployment.sh config   # Test configuration
```

### Deployer Commands
```bash
./vendor/bin/dep deploy production       # Deploy to production
./vendor/bin/dep app:rollback production # Rollback application
./vendor/bin/dep releases production     # Show releases
./vendor/bin/dep logs production         # Show logs
```

## 🔧 Configuration

### Server Configuration (deploy.php)
```php
host('production')
    ->setHostname('your-server-ip.com')
    ->setRemoteUser('cloudpanel')
    ->setDeployPath('/home/cloudpanel/htdocs/yourdomain.com')
    ->set('branch', 'main');
```

### Environment Variables (.env on server)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 🚨 Troubleshooting

### Common Issues

**Deployment fails**:
```bash
./test-deployment.sh  # Run diagnostics
./deploy.sh rollback  # Rollback if needed
```

**Permission errors**:
```bash
ssh cloudpanel@server "chmod -R 775 /path/to/storage"
```

**Asset issues**:
```bash
npm run production
./deploy.sh quick
```

### Getting Help

1. **Run diagnostics**: `./test-deployment.sh`
2. **Check logs**: `./vendor/bin/dep logs production`
3. **Test SSH**: `ssh cloudpanel@your-server`
4. **Review documentation**: [DEPLOYMENT.md](DEPLOYMENT.md)

## 🎉 Success!

Once deployed, your IMMO212 Real Estate Platform will have:

- ⚡ **Zero-downtime deployments**
- 🔄 **Instant rollback capability**
- 🛡️ **Database backup protection**
- 🚀 **Optimized performance**
- 📊 **Comprehensive monitoring**

## 📞 Support

For deployment support:
- Review [DEPLOYMENT.md](DEPLOYMENT.md) for detailed troubleshooting
- Check [SETUP.md](SETUP.md) for configuration help
- Run `./test-deployment.sh` for diagnostics

---

**Built with ❤️ for IMMO212 Real Estate Platform**  
*CloudPanel + Deployer PHP + Laravel = Zero-Downtime Deployments*
