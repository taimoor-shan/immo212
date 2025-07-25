#!/bin/bash

# IMMO212 Real Estate Platform - Deployment Script
# CloudPanel + Deployer PHP Deployment Automation
#
# Usage:
#   ./deploy.sh                    # Standard deployment
#   ./deploy.sh migrate           # Deploy with migrations
#   ./deploy.sh quick             # Quick deploy (assets only)
#   ./deploy.sh rollback          # Rollback to previous release
#   ./deploy.sh staging           # Deploy to staging environment

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DEPLOYER_BIN="./vendor/bin/dep"
DEFAULT_ENVIRONMENT="production"

# Functions
print_header() {
    echo -e "${BLUE}"
    echo "=================================================="
    echo "  IMMO212 Real Estate Platform Deployment"
    echo "  CloudPanel + Deployer PHP"
    echo "=================================================="
    echo -e "${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

check_requirements() {
    print_info "Checking deployment requirements..."
    
    # Check if Deployer is installed
    if [ ! -f "$DEPLOYER_BIN" ]; then
        print_error "Deployer PHP not found. Please run: composer install"
        exit 1
    fi
    
    # Check if deploy.php exists
    if [ ! -f "deploy.php" ]; then
        print_error "deploy.php configuration file not found"
        exit 1
    fi
    
    # Check if we're in a git repository
    if [ ! -d ".git" ]; then
        print_error "Not in a git repository"
        exit 1
    fi
    
    # Check for uncommitted changes
    if [ -n "$(git status --porcelain)" ]; then
        print_warning "You have uncommitted changes. Consider committing them first."
        read -p "Continue anyway? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi
    
    print_success "Requirements check passed"
}

show_help() {
    echo "IMMO212 Deployment Script"
    echo ""
    echo "Usage: $0 [COMMAND] [ENVIRONMENT]"
    echo ""
    echo "Commands:"
    echo "  deploy, d          Standard deployment (default)"
    echo "  migrate, m         Deploy with database migrations"
    echo "  quick, q           Quick deploy (assets and code only)"
    echo "  rollback, r        Rollback to previous release"
    echo "  status, s          Show deployment status"
    echo "  help, h            Show this help message"
    echo ""
    echo "Environments:"
    echo "  production         Production server (default)"
    echo "  staging            Staging server"
    echo ""
    echo "Examples:"
    echo "  $0                 # Deploy to production"
    echo "  $0 migrate         # Deploy with migrations to production"
    echo "  $0 quick staging   # Quick deploy to staging"
    echo "  $0 rollback        # Rollback production"
}

deploy_standard() {
    local env=${1:-$DEFAULT_ENVIRONMENT}
    print_info "Starting standard deployment to $env..."
    $DEPLOYER_BIN deploy $env -v
    print_success "Standard deployment completed!"
}

deploy_with_migrations() {
    local env=${1:-$DEFAULT_ENVIRONMENT}
    print_warning "Deploying with database migrations to $env..."
    print_warning "This will run database migrations. Make sure you have a backup!"
    
    read -p "Continue with migrations? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_info "Deployment cancelled"
        exit 0
    fi
    
    $DEPLOYER_BIN deploy:migrate $env -v
    print_success "Deployment with migrations completed!"
}

deploy_quick() {
    local env=${1:-$DEFAULT_ENVIRONMENT}
    print_info "Starting quick deployment to $env..."
    $DEPLOYER_BIN deploy:quick $env -v
    print_success "Quick deployment completed!"
}

rollback_deployment() {
    local env=${1:-$DEFAULT_ENVIRONMENT}
    print_warning "Rolling back deployment on $env..."

    read -p "Are you sure you want to rollback? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_info "Rollback cancelled"
        exit 0
    fi

    $DEPLOYER_BIN app:rollback $env -v
    print_success "Rollback completed!"
}

show_status() {
    local env=${1:-$DEFAULT_ENVIRONMENT}
    print_info "Deployment status for $env:"
    $DEPLOYER_BIN releases $env
}

# Main script logic
print_header

# Parse command line arguments
COMMAND=${1:-deploy}
ENVIRONMENT=${2:-$DEFAULT_ENVIRONMENT}

case $COMMAND in
    deploy|d)
        check_requirements
        deploy_standard $ENVIRONMENT
        ;;
    migrate|m)
        check_requirements
        deploy_with_migrations $ENVIRONMENT
        ;;
    quick|q)
        check_requirements
        deploy_quick $ENVIRONMENT
        ;;
    rollback|r)
        rollback_deployment $ENVIRONMENT
        ;;
    status|s)
        show_status $ENVIRONMENT
        ;;
    help|h)
        show_help
        ;;
    *)
        print_error "Unknown command: $COMMAND"
        show_help
        exit 1
        ;;
esac
