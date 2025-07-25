#!/bin/bash

# IMMO212 Real Estate Platform - Deployment Testing Script
# Tests the deployment process and validates all components

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DEPLOYER_BIN="./vendor/bin/dep"
TEST_ENVIRONMENT="staging"
PRODUCTION_ENVIRONMENT="production"

# Functions
print_header() {
    echo -e "${BLUE}"
    echo "=================================================="
    echo "  IMMO212 Deployment Testing Suite"
    echo "  CloudPanel + Deployer PHP Validation"
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

# Test functions
test_prerequisites() {
    print_info "Testing deployment prerequisites..."
    
    local errors=0
    
    # Check Deployer installation
    if [ ! -f "$DEPLOYER_BIN" ]; then
        print_error "Deployer PHP not found"
        errors=$((errors + 1))
    else
        print_success "Deployer PHP found"
    fi
    
    # Check deploy.php configuration
    if [ ! -f "deploy.php" ]; then
        print_error "deploy.php configuration not found"
        errors=$((errors + 1))
    else
        print_success "deploy.php configuration found"
    fi
    
    # Check Git repository
    if [ ! -d ".git" ]; then
        print_error "Not in a Git repository"
        errors=$((errors + 1))
    else
        print_success "Git repository detected"
    fi
    
    # Check package.json for asset building
    if [ ! -f "package.json" ]; then
        print_warning "package.json not found - asset building may fail"
    else
        print_success "package.json found"
    fi
    
    # Check composer.json
    if [ ! -f "composer.json" ]; then
        print_error "composer.json not found"
        errors=$((errors + 1))
    else
        print_success "composer.json found"
    fi
    
    return $errors
}

test_local_build() {
    print_info "Testing local asset build process..."
    
    local errors=0
    
    # Test npm install
    if [ -f "package.json" ]; then
        print_info "Running npm install..."
        if npm ci --production > /dev/null 2>&1; then
            print_success "npm install completed"
        else
            print_error "npm install failed"
            errors=$((errors + 1))
        fi
        
        # Test asset build
        print_info "Running asset build..."
        if npm run production > /dev/null 2>&1; then
            print_success "Asset build completed"
            
            # Check if assets were created
            if [ -d "public/themes" ] || [ -d "public/vendor" ]; then
                print_success "Compiled assets found"
            else
                print_warning "No compiled assets found in public directory"
            fi
        else
            print_error "Asset build failed"
            errors=$((errors + 1))
        fi
    else
        print_info "Skipping asset build - no package.json"
    fi
    
    return $errors
}

test_deployer_config() {
    print_info "Testing Deployer configuration..."
    
    local errors=0
    
    # Test configuration syntax
    if $DEPLOYER_BIN config:dump > /dev/null 2>&1; then
        print_success "Deployer configuration syntax valid"
    else
        print_error "Deployer configuration has syntax errors"
        errors=$((errors + 1))
    fi
    
    # Test host connectivity (if staging is configured)
    print_info "Testing host connectivity..."
    if $DEPLOYER_BIN ssh $TEST_ENVIRONMENT "echo 'Connection test'" > /dev/null 2>&1; then
        print_success "SSH connection to $TEST_ENVIRONMENT successful"
    else
        print_warning "Cannot connect to $TEST_ENVIRONMENT (this is normal if not configured)"
    fi
    
    return $errors
}

test_deployment_dry_run() {
    print_info "Performing deployment dry run..."
    
    local errors=0
    
    # Dry run deployment (if supported)
    print_info "Testing deployment tasks..."
    
    # Test individual tasks
    local tasks=("deploy:prepare" "deploy:vendors" "build:assets" "upload:assets")
    
    for task in "${tasks[@]}"; do
        print_info "Validating task: $task"
        if $DEPLOYER_BIN $task --dry-run $TEST_ENVIRONMENT > /dev/null 2>&1; then
            print_success "Task $task validated"
        else
            print_warning "Task $task validation failed (may not support dry-run)"
        fi
    done
    
    return $errors
}

test_rollback_capability() {
    print_info "Testing rollback capability..."

    local errors=0

    # Check if rollback tasks exist
    if $DEPLOYER_BIN list | grep -q "app:rollback"; then
        print_success "Application rollback task available"
    else
        print_error "Application rollback task not found"
        errors=$((errors + 1))
    fi

    if $DEPLOYER_BIN list | grep -q "rollback"; then
        print_success "Built-in rollback task available"
    else
        print_error "Built-in rollback task not found"
        errors=$((errors + 1))
    fi

    return $errors
}

test_database_backup() {
    print_info "Testing database backup functionality..."
    
    local errors=0
    
    # Check if database backup task exists
    if $DEPLOYER_BIN list | grep -q "database:backup"; then
        print_success "Database backup task available"
    else
        print_error "Database backup task not found"
        errors=$((errors + 1))
    fi
    
    return $errors
}

test_permissions() {
    print_info "Testing file permissions setup..."
    
    local errors=0
    
    # Check if permission task exists
    if $DEPLOYER_BIN list | grep -q "deploy:set_permissions"; then
        print_success "Permission setup task available"
    else
        print_error "Permission setup task not found"
        errors=$((errors + 1))
    fi
    
    return $errors
}

run_comprehensive_test() {
    print_header
    
    local total_errors=0
    
    # Run all tests
    test_prerequisites
    total_errors=$((total_errors + $?))
    
    test_local_build
    total_errors=$((total_errors + $?))
    
    test_deployer_config
    total_errors=$((total_errors + $?))
    
    test_deployment_dry_run
    total_errors=$((total_errors + $?))
    
    test_rollback_capability
    total_errors=$((total_errors + $?))
    
    test_database_backup
    total_errors=$((total_errors + $?))
    
    test_permissions
    total_errors=$((total_errors + $?))
    
    # Summary
    echo ""
    echo "=================================================="
    if [ $total_errors -eq 0 ]; then
        print_success "All deployment tests passed! ✨"
        print_info "Your deployment setup is ready for use."
        echo ""
        print_info "Next steps:"
        echo "1. Configure your server details in deploy.php"
        echo "2. Set up SSH keys for passwordless deployment"
        echo "3. Create .env file on your server"
        echo "4. Run: ./deploy.sh staging (for staging deployment)"
        echo "5. Run: ./deploy.sh (for production deployment)"
    else
        print_error "Found $total_errors issue(s) that need to be resolved."
        print_info "Please fix the issues above before deploying."
    fi
    echo "=================================================="
    
    return $total_errors
}

# Show help
show_help() {
    echo "IMMO212 Deployment Testing Script"
    echo ""
    echo "Usage: $0 [COMMAND]"
    echo ""
    echo "Commands:"
    echo "  test, t            Run comprehensive deployment tests (default)"
    echo "  prerequisites, p   Test only prerequisites"
    echo "  build, b           Test only local build process"
    echo "  config, c          Test only Deployer configuration"
    echo "  help, h            Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0                 # Run all tests"
    echo "  $0 prerequisites   # Test only prerequisites"
    echo "  $0 build           # Test only build process"
}

# Main script logic
case ${1:-test} in
    test|t)
        run_comprehensive_test
        ;;
    prerequisites|p)
        print_header
        test_prerequisites
        ;;
    build|b)
        print_header
        test_local_build
        ;;
    config|c)
        print_header
        test_deployer_config
        ;;
    help|h)
        show_help
        ;;
    *)
        print_error "Unknown command: $1"
        show_help
        exit 1
        ;;
esac
