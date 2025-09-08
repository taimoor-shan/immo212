/**
 * Optimized Bulk Translation JavaScript
 * Automatically selects best approach and handles progress tracking
 */
class BulkTranslationOptimizer {
    constructor() {
        this.progressModal = null;
        this.progressTimer = null;
        this.currentBatchId = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        this.init();
    }

    init() {
        // Replace existing bulk translation buttons with optimized versions
        this.replaceBulkButtons();
        
        // Create progress modal
        this.createProgressModal();
        
        // Bind events
        this.bindEvents();
    }

    replaceBulkButtons() {
        // Find existing bulk translate buttons and enhance them
        const bulkButtons = document.querySelectorAll('.btn-translate-all, [data-action="bulk-translate"]');
        
        bulkButtons.forEach(button => {
            // Add loading states and smart behavior
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.startSmartBulkTranslation(button);
            });
            
            // Update button text to indicate it's optimized
            if (button.textContent && !button.textContent.includes('Smart')) {
                button.innerHTML = '<i class="fas fa-brain"></i> Smart ' + button.textContent;
                button.title = 'Automatically selects the best translation approach to avoid timeouts';
            }
        });
    }

    async startSmartBulkTranslation(button) {
        const locale = this.getLocaleFromContext();
        const group = this.getGroupFromContext();
        
        if (!locale) {
            this.showError('Please select a target language');
            return;
        }

        // Disable button and show loading
        button.disabled = true;
        button.classList.add('button-loading');
        
        try {
            // First, get estimation to determine best approach
            const estimation = await this.getEstimation(locale, group);
            
            if (estimation.items_needing_translation === 0) {
                this.showSuccess('All translations are already up to date!');
                return;
            }
            
            // Automatically select best approach based on estimation
            const approach = this.selectBestApproach(estimation);
            
            // Show confirmation dialog with estimation details
            const confirmed = await this.showConfirmationDialog(estimation, approach);
            
            if (!confirmed) {
                return;
            }
            
            // Execute the selected approach
            await this.executeTranslation(approach, locale, group, estimation);
            
        } catch (error) {
            this.showError('Failed to start translation: ' + error.message);
            console.error('Translation error:', error);
        } finally {
            button.disabled = false;
            button.classList.remove('button-loading');
        }
    }

    async getEstimation(locale, group = null) {
        const params = new URLSearchParams({ locale });
        if (group) params.append('group', group);
        
        const response = await fetch(`/admin/vig-auto-translations/plugin/estimation?${params}`, {
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        if (!response.ok) {
            throw new Error(result.message || 'Failed to get estimation');
        }
        
        return result.data;
    }

    selectBestApproach(estimation) {
        const { items_needing_translation, estimated_time_minutes } = estimation;
        
        // Auto-select approach based on size and estimated time
        if (items_needing_translation <= 20) {
            return {
                type: 'immediate',
                name: 'Immediate Processing',
                description: 'Process all translations immediately (fastest for small sets)',
                risk: 'low'
            };
        } else if (items_needing_translation <= 100 && estimated_time_minutes <= 3) {
            return {
                type: 'small_batch',
                name: 'Small Batch Processing', 
                description: 'Process in a single optimized batch with progress tracking',
                risk: 'medium'
            };
        } else {
            return {
                type: 'chunked',
                name: 'Background Processing',
                description: 'Process in background chunks to prevent timeouts (recommended for large sets)',
                risk: 'low',
                chunk_size: estimation.recommended_chunk_size
            };
        }
    }

    async showConfirmationDialog(estimation, approach) {
        const { 
            total_items, 
            items_needing_translation, 
            items_already_translated,
            estimated_time_minutes,
            warnings 
        } = estimation;
        
        const warningHtml = warnings.length > 0 
            ? `<div class="alert alert-warning"><strong>Warnings:</strong><ul>${warnings.map(w => `<li>${w}</li>`).join('')}</ul></div>`
            : '';
        
        const riskBadge = {
            low: 'success',
            medium: 'warning', 
            high: 'danger'
        }[approach.risk] || 'secondary';
        
        const html = `
            <div class="modal fade" id="translationConfirmModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Bulk Translation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h6>Total Items</h6>
                                            <h4 class="text-primary">${total_items}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h6>Need Translation</h6>
                                            <h4 class="text-warning">${items_needing_translation}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6><i class="fas fa-brain"></i> Recommended Approach</h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-${riskBadge} me-2">${approach.name}</span>
                                        <small class="text-muted">${approach.description}</small>
                                    </div>
                                    <div class="mt-2">
                                        <small><i class="fas fa-clock"></i> Estimated time: ~${estimated_time_minutes} minutes</small>
                                    </div>
                                </div>
                            </div>
                            
                            ${warningHtml}
                            
                            <p><strong>${items_already_translated}</strong> items are already translated and will be skipped.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmTranslation">
                                <i class="fas fa-language"></i> Start Translation
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal
        const existingModal = document.getElementById('translationConfirmModal');
        if (existingModal) existingModal.remove();
        
        // Add new modal
        document.body.insertAdjacentHTML('beforeend', html);
        
        // Show modal and wait for user decision
        const modal = new bootstrap.Modal(document.getElementById('translationConfirmModal'));
        modal.show();
        
        return new Promise((resolve) => {
            document.getElementById('confirmTranslation').addEventListener('click', () => {
                modal.hide();
                resolve(true);
            });
            
            document.querySelector('[data-bs-dismiss="modal"]').addEventListener('click', () => {
                resolve(false);
            });
        });
    }

    async executeTranslation(approach, locale, group, estimation) {
        switch (approach.type) {
            case 'immediate':
                await this.executeImmediateTranslation(locale, group, estimation);
                break;
                
            case 'small_batch':
                await this.executeSmallBatch(locale, group, estimation);
                break;
                
            case 'chunked':
                await this.executeChunkedTranslation(locale, group, approach.chunk_size, estimation);
                break;
        }
    }

    async executeImmediateTranslation(locale, group, estimation) {
        // Use the existing immediate translation method
        const formData = new FormData();
        formData.append('_token', this.csrfToken);
        formData.append('locale', locale);
        if (group) formData.append('group', group);
        
        const response = await fetch('/admin/vig-auto-translations/plugin/all', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (response.ok) {
            this.showSuccess(result.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            throw new Error(result.message);
        }
    }

    async executeSmallBatch(locale, group, estimation) {
        this.showProgressModal('Processing small batch...');
        
        const formData = new FormData();
        formData.append('_token', this.csrfToken);
        formData.append('locale', locale);
        formData.append('max_items', Math.min(estimation.items_needing_translation, 50));
        if (group) formData.append('group', group);
        
        const response = await fetch('/admin/vig-auto-translations/plugin/process-small-batch', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        this.hideProgressModal();
        
        if (response.ok) {
            this.showSuccess(`Small batch completed! Translated: ${result.data.translated}, Skipped: ${result.data.skipped}, Errors: ${result.data.errors}`);
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(result.message);
        }
    }

    async executeChunkedTranslation(locale, group, chunkSize, estimation) {
        // Start chunked processing
        const formData = new FormData();
        formData.append('_token', this.csrfToken);
        formData.append('locale', locale);
        formData.append('chunk_size', chunkSize);
        if (group) formData.append('group', group);
        
        const response = await fetch('/admin/vig-auto-translations/plugin/start-chunked-translation', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (!response.ok) {
            throw new Error(result.message);
        }
        
        // Start progress tracking
        this.currentBatchId = result.data.batch_id;
        this.showProgressModal('Starting background processing...');
        this.startProgressTracking();
    }

    startProgressTracking() {
        if (!this.currentBatchId) return;
        
        this.progressTimer = setInterval(async () => {
            try {
                const response = await fetch(`/admin/vig-auto-translations/plugin/translation-progress?batch_id=${this.currentBatchId}`, {
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                if (response.ok) {
                    this.updateProgress(result.data);
                    
                    if (result.data.is_complete) {
                        this.stopProgressTracking();
                        this.handleCompletedTranslation(result.data);
                    }
                } else {
                    this.stopProgressTracking();
                    this.showError('Failed to get progress: ' + result.message);
                }
            } catch (error) {
                console.error('Progress tracking error:', error);
            }
        }, 2000); // Check every 2 seconds
    }

    updateProgress(data) {
        const { progress, completion_percentage } = data;
        const progressBar = document.getElementById('translationProgressBar');
        const progressText = document.getElementById('translationProgressText');
        const progressDetails = document.getElementById('translationProgressDetails');
        
        if (progressBar) {
            progressBar.style.width = `${completion_percentage}%`;
            progressBar.setAttribute('aria-valuenow', completion_percentage);
        }
        
        if (progressText) {
            progressText.textContent = `${completion_percentage}% Complete`;
        }
        
        if (progressDetails) {
            progressDetails.innerHTML = `
                <small>
                    Processed: ${progress.processed}/${progress.total} | 
                    Translated: ${progress.translated} | 
                    Skipped: ${progress.skipped} | 
                    Errors: ${progress.errors}
                </small>
            `;
        }
    }

    handleCompletedTranslation(data) {
        this.hideProgressModal();
        
        const { progress } = data;
        const message = `Translation completed! Translated: ${progress.translated}, Skipped: ${progress.skipped}, Errors: ${progress.errors}`;
        
        if (progress.errors > 0) {
            this.showWarning(message);
        } else {
            this.showSuccess(message);
        }
        
        setTimeout(() => location.reload(), 2000);
    }

    stopProgressTracking() {
        if (this.progressTimer) {
            clearInterval(this.progressTimer);
            this.progressTimer = null;
        }
        this.currentBatchId = null;
    }

    createProgressModal() {
        const html = `
            <div class="modal fade" id="translationProgressModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Translation Progress</h5>
                        </div>
                        <div class="modal-body">
                            <div class="progress mb-3" style="height: 25px;">
                                <div id="translationProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span id="translationProgressText">0% Complete</span>
                                </div>
                            </div>
                            <div id="translationProgressDetails" class="text-center">
                                <small>Initializing...</small>
                            </div>
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    This process runs in the background. You can close this window and check back later.
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="bulkTranslationOptimizer.hideProgressModal()">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', html);
        this.progressModal = new bootstrap.Modal(document.getElementById('translationProgressModal'));
    }

    showProgressModal(message = 'Processing...') {
        const progressDetails = document.getElementById('translationProgressDetails');
        if (progressDetails) {
            progressDetails.innerHTML = `<small>${message}</small>`;
        }
        
        if (this.progressModal) {
            this.progressModal.show();
        }
    }

    hideProgressModal() {
        if (this.progressModal) {
            this.progressModal.hide();
        }
        this.stopProgressTracking();
    }

    getLocaleFromContext() {
        // Try to get locale from various sources
        return document.querySelector('[name="ref_lang"]')?.value ||
               document.querySelector('[data-locale]')?.getAttribute('data-locale') ||
               new URLSearchParams(window.location.search).get('ref_lang');
    }

    getGroupFromContext() {
        // Try to get group from various sources  
        return document.querySelector('[name="group"]')?.value ||
               document.querySelector('[data-group]')?.getAttribute('data-group') ||
               new URLSearchParams(window.location.search).get('group');
    }

    showSuccess(message) {
        if (typeof Botble !== 'undefined' && Botble.showSuccess) {
            Botble.showSuccess(message);
        } else {
            alert('Success: ' + message);
        }
    }

    showError(message) {
        if (typeof Botble !== 'undefined' && Botble.showError) {
            Botble.showError(message);
        } else {
            alert('Error: ' + message);
        }
    }

    showWarning(message) {
        if (typeof Botble !== 'undefined' && Botble.showWarning) {
            Botble.showWarning(message);
        } else {
            alert('Warning: ' + message);
        }
    }

    bindEvents() {
        // Handle page unload during progress tracking
        window.addEventListener('beforeunload', () => {
            this.stopProgressTracking();
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize on translation pages
    if (document.querySelector('.btn-translate-all') || 
        document.querySelector('[data-action="bulk-translate"]') ||
        window.location.pathname.includes('vig-auto-translations')) {
        
        window.bulkTranslationOptimizer = new BulkTranslationOptimizer();
    }
});
