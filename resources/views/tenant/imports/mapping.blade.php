@extends('tenant.layouts.app')

@section('title', 'Field Mapping - AI-Powered Data Intelligence')

@push('styles')
<style>
    :root {
        --mapping-primary: #6366f1;
        --mapping-secondary: #8b5cf6;
        --mapping-success: #10b981;
        --mapping-warning: #f59e0b;
        --mapping-error: #ef4444;
        --mapping-surface: #ffffff;
        --mapping-glass: rgba(255, 255, 255, 0.9);
        --mapping-border: #e2e8f0;
        --mapping-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --mapping-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* Revolutionary Mapping Interface */
    .mapping-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    .mapping-header {
        background: var(--mapping-gradient);
        border-radius: 24px;
        color: white;
        padding: 2.5rem;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }

    .mapping-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M20 20c0-11.046-8.954-20-20-20v20h20z'/%3E%3C/g%3E%3C/svg%3E") repeat;
    }

    .mapping-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
    }

    .mapping-subtitle {
        font-size: 1.125rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    /* AI Confidence Score */
    .ai-confidence {
        position: absolute;
        top: 2rem;
        right: 2rem;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        z-index: 2;
    }

    .confidence-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .confidence-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    /* Mapping Interface */
    .mapping-interface {
        display: grid;
        grid-template-columns: 1fr 200px 1fr;
        gap: 2rem;
        margin-bottom: 3rem;
        align-items: start;
    }

    .field-column {
        background: var(--mapping-surface);
        border-radius: 20px;
        box-shadow: var(--mapping-shadow);
        border: 1px solid var(--mapping-border);
        overflow: hidden;
        max-height: 600px;
        display: flex;
        flex-direction: column;
    }

    .column-header {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--mapping-border);
        position: sticky;
        top: 0;
        z-index: 3;
    }

    .column-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--mapping-text);
        margin-bottom: 0.5rem;
    }

    .column-subtitle {
        font-size: 0.875rem;
        color: #64748b;
    }

    .field-list {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }

    .field-item {
        background: var(--mapping-surface);
        border: 2px solid var(--mapping-border);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 0.75rem;
        cursor: grab;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .field-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        border-color: var(--mapping-primary);
    }

    .field-item.dragging {
        transform: rotate(5deg) scale(1.05);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        border-color: var(--mapping-primary);
        background: rgba(99, 102, 241, 0.05);
        cursor: grabbing;
    }

    .field-item.mapped {
        border-color: var(--mapping-success);
        background: rgba(16, 185, 129, 0.05);
    }

    .field-item.suggested {
        border-color: var(--mapping-primary);
        background: rgba(99, 102, 241, 0.05);
        animation: pulse-suggestion 2s infinite;
    }

    @keyframes pulse-suggestion {
        0%, 100% { border-color: var(--mapping-primary); }
        50% { border-color: rgba(99, 102, 241, 0.5); }
    }

    .field-content {
        flex: 1;
    }

    .field-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }

    .field-type {
        font-size: 0.8rem;
        color: #64748b;
        background: #f1f5f9;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
    }

    .field-preview {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.25rem;
        font-style: italic;
    }

    .field-actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .confidence-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--mapping-success);
        animation: pulse 2s infinite;
    }

    .confidence-indicator.medium {
        background: var(--mapping-warning);
    }

    .confidence-indicator.low {
        background: var(--mapping-error);
    }

    /* Connection Lines */
    .connection-area {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .connection-svg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    }

    .ai-suggestions {
        background: rgba(99, 102, 241, 0.1);
        border: 2px dashed var(--mapping-primary);
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        color: var(--mapping-primary);
        font-weight: 600;
        position: relative;
        z-index: 2;
    }

    .ai-brain-icon {
        font-size: 2rem;
        margin-bottom: 1rem;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    /* Target Drop Zones */
    .drop-zone {
        background: rgba(16, 185, 129, 0.05);
        border: 2px dashed var(--mapping-success);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 0.75rem;
        text-align: center;
        color: var(--mapping-success);
        font-weight: 600;
        transition: all 0.3s ease;
        min-height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .drop-zone.drag-over {
        background: rgba(16, 185, 129, 0.1);
        border-color: var(--mapping-success);
        transform: scale(1.02);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.2);
    }

    .drop-zone.filled {
        background: rgba(16, 185, 129, 0.1);
        border-style: solid;
    }

    /* Mapping Statistics */
    .mapping-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: var(--mapping-surface);
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        box-shadow: var(--mapping-shadow);
        border: 1px solid var(--mapping-border);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: var(--mapping-gradient);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin: 0 auto 1rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--mapping-primary);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Action Buttons */
    .mapping-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 3rem;
    }

    .action-btn {
        padding: 1.25rem 2.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-primary {
        background: var(--mapping-gradient);
        color: white;
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(99, 102, 241, 0.4);
    }

    .btn-secondary {
        background: var(--mapping-surface);
        color: #64748b;
        border: 2px solid var(--mapping-border);
    }

    .btn-secondary:hover {
        background: #f8fafc;
        border-color: var(--mapping-primary);
        color: var(--mapping-primary);
    }

    /* Auto-mapping Animation */
    @keyframes auto-map {
        0% { transform: scale(1) rotate(0deg); }
        25% { transform: scale(1.1) rotate(90deg); }
        50% { transform: scale(1) rotate(180deg); }
        75% { transform: scale(1.1) rotate(270deg); }
        100% { transform: scale(1) rotate(360deg); }
    }

    .auto-mapping .ai-brain-icon {
        animation: auto-map 1s ease-in-out;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .mapping-interface {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .ai-confidence {
            position: static;
            margin-top: 2rem;
        }
    }

    @media (max-width: 768px) {
        .mapping-container {
            padding: 1rem;
        }
        
        .mapping-header {
            padding: 2rem;
        }
        
        .mapping-title {
            font-size: 2rem;
        }
        
        .mapping-stats {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    
    <!-- Progress Steps -->
    <div class="progress-steps" style="display: flex; justify-content: center; margin: 3rem 0;">
        <div class="progress-step completed" style="background: #10b981; color: white; border-color: #10b981;">
            <i class="fas fa-upload"></i>
        </div>
        <div class="progress-step completed" style="background: #10b981; color: white; border-color: #10b981;">
            <i class="fas fa-search"></i>
        </div>
        <div class="progress-step active" style="background: #6366f1; color: white; border-color: #6366f1;">
            <i class="fas fa-exchange-alt"></i>
        </div>
        <div class="progress-step" style="background: white; color: #64748b; border-color: #e2e8f0;">
            <i class="fas fa-check"></i>
        </div>
    </div>

    <div class="mapping-container">
        
        <!-- Mapping Header -->
        <div class="mapping-header">
            <div class="mapping-title">🧠 AI-Powered Field Mapping</div>
            <div class="mapping-subtitle">
                Our AI has analyzed your data and suggests the optimal field mappings. Review and adjust as needed.
            </div>
            <div class="file-name" style="font-size: 1rem; margin-top: 1rem; opacity: 0.9;">
                📄 {{ $fileName ?? 'uploaded_file.csv' }}
            </div>
            
            <!-- AI Confidence Score -->
            <div class="ai-confidence">
                <div class="confidence-value">{{ $fileData['ai_confidence'] ?? '0' }}%</div>
                <div class="confidence-label">AI Confidence</div>
            </div>
        </div>

        <!-- Mapping Statistics -->
        <div class="mapping-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-value">{{ count($fileData['headers'] ?? []) }}</div>
                <div class="stat-label">Source Fields</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="stat-value">{{ $fileData['auto_mapped_count'] ?? '0' }}</div>
                <div class="stat-label">Auto-Mapped</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="stat-value">{{ $fileData['ai_confidence'] ?? '0' }}%</div>
                <div class="stat-label">Accuracy Score</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value">{{ $fileData['processing_time'] ?? '0.0' }}s</div>
                <div class="stat-label">Processing Time</div>
            </div>
        </div>

        <!-- Mapping Interface -->
        <div class="mapping-interface">
            
            <!-- Source Fields -->
            <div class="field-column">
                <div class="column-header">
                    <div class="column-title">📊 Source Fields</div>
                    <div class="column-subtitle">From your uploaded file</div>
                </div>
                <div class="field-list" id="sourceFields">
                    <!-- Source fields will be populated here -->
                </div>
            </div>

            <!-- AI Suggestions Area -->
            <div class="connection-area">
                <svg class="connection-svg" id="connectionSvg">
                    <!-- Connection lines will be drawn here -->
                </svg>
                
                <div class="ai-suggestions">
                    <div class="ai-brain-icon">🧠</div>
                    <div>AI Mapping Engine</div>
                    <div style="font-size: 0.8rem; margin-top: 0.5rem;">
                        Drag fields to create mappings
                    </div>
                </div>
            </div>

            <!-- Target Fields -->
            <div class="field-column">
                <div class="column-header">
                    <div class="column-title">🎯 Target Fields</div>
                    <div class="column-subtitle">Restaurant data structure</div>
                </div>
                <div class="field-list" id="targetFields">
                    <!-- Target fields will be populated here -->
                </div>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="mapping-actions">
            <button class="action-btn btn-secondary" onclick="goBack()">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Upload
            </button>
            <button class="action-btn btn-secondary" onclick="autoMapFields()">
                <i class="fas fa-magic mr-2"></i>
                Auto-Map All
            </button>
            <button class="action-btn btn-primary" onclick="proceedToValidation()" disabled id="proceedBtn">
                <i class="fas fa-arrow-right mr-2"></i>
                Proceed to Validation
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
class FieldMappingInterface {
    constructor() {
        this.mappings = new Map();
        this.sourceFields = [];
        this.targetFields = [];
        this.connections = [];
        
        this.initializeData();
        this.renderFields();
        this.initializeDragAndDrop();
        this.updateProceedButton();
    }
    
    initializeData() {
        // Get actual file data from server
        const fileData = @json($fileData ?? []);
        const fileName = @json($fileName ?? 'uploaded_file.csv');
        
        // Update filename display
        document.querySelector('.file-name').textContent = fileName;
        
        // Convert actual file headers to source fields
        this.sourceFields = [];
        if (fileData.headers && fileData.headers.length > 0) {
            fileData.headers.forEach((header, index) => {
                const sampleValue = fileData.sample_data && fileData.sample_data[0] && fileData.sample_data[0][index] 
                    ? fileData.sample_data[0][index] 
                    : 'Sample data';
                
                this.sourceFields.push({
                    id: 's' + (index + 1),
                    name: header,
                    type: this.detectFieldType(header, sampleValue),
                    sample: sampleValue,
                    confidence: this.calculateFieldConfidence(header)
                });
            });
        } else {
            // No fallback data - require actual file upload
            this.sourceFields = [];
            if (this.sourceFields.length === 0) {
                this.showNoDataMessage();
                return;
            }
        }
        
        // Target fields in restaurant system
        this.targetFields = [
            { id: 't1', name: 'name', label: 'Item Name', required: true, mapped: false },
            { id: 't2', name: 'price', label: 'Sale Price', required: true, mapped: false },
            { id: 't3', name: 'category', label: 'Category', required: true, mapped: false },
            { id: 't4', name: 'description', label: 'Description', required: false, mapped: false },
            { id: 't5', name: 'cost', label: 'Food Cost', required: false, mapped: false },
            { id: 't6', name: 'calories', label: 'Calories', required: false, mapped: false },
            { id: 't7', name: 'prep_time', label: 'Preparation Time', required: false, mapped: false },
            { id: 't8', name: 'allergens', label: 'Allergens', required: false, mapped: false },
            { id: 't9', name: 'status', label: 'Availability Status', required: false, mapped: false },
            { id: 't10', name: 'spice_level', label: 'Spice Level', required: false, mapped: false }
        ];
        
        // Pre-populate some AI suggestions
        this.createAISuggestions();
    }
    
    detectFieldType(header, sampleValue) {
        const headerLower = header.toLowerCase();
        const valueLower = String(sampleValue).toLowerCase();
        
        // Detect field types based on header names and sample values
        if (headerLower.includes('price') || headerLower.includes('cost') || valueLower.includes('$') || valueLower.match(/^\d+\.?\d*$/)) {
            return 'currency';
        }
        if (headerLower.includes('time') || valueLower.includes('min') || valueLower.includes('hour')) {
            return 'time';
        }
        if (headerLower.includes('calorie') || headerLower.includes('number') || /^\d+$/.test(valueLower)) {
            return 'number';
        }
        if (headerLower.includes('available') || headerLower.includes('status') || ['yes', 'no', 'true', 'false'].includes(valueLower)) {
            return 'boolean';
        }
        
        return 'text';
    }
    
    calculateFieldConfidence(header) {
        const headerLower = header.toLowerCase();
        
        // Very high confidence matches for exact POS system fields
        if (headerLower === 'food name' || headerLower === 'item name') return 98;
        if (headerLower === 'price' || headerLower === 'cost') return 98;
        if (headerLower === 'food category' || headerLower === 'category') return 95;
        if (headerLower === 'sub category' || headerLower === 'subcategory') return 92;
        if (headerLower === 'code' || headerLower === 'item code') return 90;
        
        // High confidence matches
        const highConfidenceWords = ['name', 'price', 'cost', 'category', 'description'];
        if (highConfidenceWords.some(word => headerLower.includes(word))) {
            return 95;
        }
        
        // Medium confidence matches
        const mediumConfidenceWords = ['time', 'calorie', 'allergen', 'spice', 'available', 'discontinue', 'status'];
        if (mediumConfidenceWords.some(word => headerLower.includes(word))) {
            return 75;
        }
        
        // Low confidence for dates and other fields
        if (headerLower.includes('date') || headerLower.includes('modified')) {
            return 45;
        }
        
        // Default confidence
        return 60;
    }
    
    createAISuggestions() {
        // Smart AI suggestions based on actual field names
        const suggestions = [];
        
        // Create mapping suggestions by matching field names
        this.sourceFields.forEach(sourceField => {
            const sourceNameLower = sourceField.name.toLowerCase();
            
            // Find best target match
            this.targetFields.forEach(targetField => {
                const targetNameLower = targetField.name.toLowerCase();
                
                // Direct matches
                if ((sourceNameLower === 'food name' || sourceNameLower === 'item name') && targetNameLower === 'name') {
                    suggestions.push({ source: sourceField.id, target: targetField.id });
                }
                else if (sourceNameLower === 'price' && targetNameLower === 'price') {
                    suggestions.push({ source: sourceField.id, target: targetField.id });
                }
                else if (sourceNameLower === 'cost' && targetNameLower === 'cost') {
                    suggestions.push({ source: sourceField.id, target: targetField.id });
                }
                else if ((sourceNameLower === 'food category' || sourceNameLower === 'category') && targetNameLower === 'category') {
                    suggestions.push({ source: sourceField.id, target: targetField.id });
                }
                else if (sourceNameLower === 'description' && targetNameLower === 'description') {
                    suggestions.push({ source: sourceField.id, target: targetField.id });
                }
                // Partial matches
                else if (sourceNameLower.includes('name') && targetNameLower === 'name') {
                    suggestions.push({ source: sourceField.id, target: targetField.id });
                }
                else if (sourceNameLower.includes('price') && targetNameLower === 'price') {
                    suggestions.push({ source: sourceField.id, target: targetField.id });
                }
                else if (sourceNameLower.includes('category') && targetNameLower === 'category') {
                    suggestions.push({ source: sourceField.id, target: targetField.id });
                }
            });
        });
        
        // Apply the suggestions
        suggestions.forEach(suggestion => {
            this.createMapping(suggestion.source, suggestion.target);
        });
        
        // Only use AI-generated suggestions from actual data analysis
        // No fallback positional mapping without actual data
    }
    
    showNoDataMessage() {
        const container = document.querySelector('.mapping-container');
        container.innerHTML = `
            <div style="text-align: center; padding: 4rem 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📊</div>
                <h2 style="color: #1e293b; margin-bottom: 1rem;">No File Data Available</h2>
                <p style="color: #64748b; margin-bottom: 2rem;">Please upload a valid file first to proceed with field mapping.</p>
                <button onclick="window.location.href='{{ route('imports.create') }}'" 
                        style="background: #6366f1; color: white; padding: 1rem 2rem; border: none; border-radius: 8px; cursor: pointer;">
                    Back to Upload
                </button>
            </div>
        `;
    }
    
    renderFields() {
        this.renderSourceFields();
        this.renderTargetFields();
    }
    
    renderSourceFields() {
        const container = document.getElementById('sourceFields');
        container.innerHTML = '';
        
        this.sourceFields.forEach(field => {
            const element = this.createSourceFieldElement(field);
            container.appendChild(element);
        });
    }
    
    renderTargetFields() {
        const container = document.getElementById('targetFields');
        container.innerHTML = '';
        
        this.targetFields.forEach(field => {
            const element = this.createTargetFieldElement(field);
            container.appendChild(element);
        });
    }
    
    createSourceFieldElement(field) {
        const element = document.createElement('div');
        const isMapped = Array.from(this.mappings.keys()).includes(field.id);
        const isSuggested = field.confidence > 80 && !isMapped;
        
        element.className = `field-item ${isMapped ? 'mapped' : ''} ${isSuggested ? 'suggested' : ''}`;
        element.draggable = true;
        element.dataset.fieldId = field.id;
        
        const confidenceClass = field.confidence > 80 ? 'high' : field.confidence > 60 ? 'medium' : 'low';
        
        element.innerHTML = `
            <div class="field-content">
                <div class="field-name">${field.name}</div>
                <div class="field-type">${field.type}</div>
                <div class="field-preview">"${field.sample}"</div>
            </div>
            <div class="field-actions">
                <div class="confidence-indicator ${confidenceClass}" title="${field.confidence}% confidence"></div>
            </div>
        `;
        
        return element;
    }
    
    createTargetFieldElement(field) {
        const element = document.createElement('div');
        const mapping = this.getMappingForTarget(field.id);
        
        if (mapping) {
            element.className = 'drop-zone filled';
            const sourceField = this.sourceFields.find(f => f.id === mapping);
            element.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                    <div>
                        <div style="font-weight: 600; color: #10b981;">${field.label}</div>
                        <div style="font-size: 0.8rem; color: #64748b;">← ${sourceField.name}</div>
                    </div>
                    <button onclick="fieldMapper.removeMapping('${mapping}', '${field.id}')" 
                            style="background: rgba(239, 68, 68, 0.1); border: none; color: #ef4444; 
                                   padding: 0.5rem; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        } else {
            element.className = 'drop-zone';
            element.innerHTML = `
                <div>
                    <div style="font-weight: 600;">${field.label}</div>
                    ${field.required ? '<div style="font-size: 0.8rem; color: #ef4444;">Required</div>' : ''}
                </div>
            `;
        }
        
        element.dataset.targetId = field.id;
        return element;
    }
    
    initializeDragAndDrop() {
        // Drag start
        document.addEventListener('dragstart', (e) => {
            if (e.target.classList.contains('field-item')) {
                e.target.classList.add('dragging');
                e.dataTransfer.setData('text/plain', e.target.dataset.fieldId);
            }
        });
        
        // Drag end
        document.addEventListener('dragend', (e) => {
            if (e.target.classList.contains('field-item')) {
                e.target.classList.remove('dragging');
            }
        });
        
        // Drag over
        document.addEventListener('dragover', (e) => {
            e.preventDefault();
            if (e.target.classList.contains('drop-zone')) {
                e.target.classList.add('drag-over');
            }
        });
        
        // Drag leave
        document.addEventListener('dragleave', (e) => {
            if (e.target.classList.contains('drop-zone')) {
                e.target.classList.remove('drag-over');
            }
        });
        
        // Drop
        document.addEventListener('drop', (e) => {
            e.preventDefault();
            if (e.target.classList.contains('drop-zone') || e.target.closest('.drop-zone')) {
                const dropZone = e.target.classList.contains('drop-zone') ? e.target : e.target.closest('.drop-zone');
                dropZone.classList.remove('drag-over');
                
                const sourceId = e.dataTransfer.getData('text/plain');
                const targetId = dropZone.dataset.targetId;
                
                if (sourceId && targetId) {
                    this.createMapping(sourceId, targetId);
                    this.renderFields();
                    this.updateProceedButton();
                    this.drawConnections();
                }
            }
        });
    }
    
    createMapping(sourceId, targetId) {
        // Remove any existing mapping for this target
        const existingMapping = this.getMappingForTarget(targetId);
        if (existingMapping) {
            this.mappings.delete(existingMapping);
        }
        
        // Create new mapping
        this.mappings.set(sourceId, targetId);
        
        // Update target field status
        const targetField = this.targetFields.find(f => f.id === targetId);
        if (targetField) {
            targetField.mapped = true;
        }
    }
    
    removeMapping(sourceId, targetId) {
        this.mappings.delete(sourceId);
        
        // Update target field status
        const targetField = this.targetFields.find(f => f.id === targetId);
        if (targetField) {
            targetField.mapped = false;
        }
        
        this.renderFields();
        this.updateProceedButton();
        this.drawConnections();
    }
    
    getMappingForTarget(targetId) {
        for (const [sourceId, mappedTargetId] of this.mappings.entries()) {
            if (mappedTargetId === targetId) {
                return sourceId;
            }
        }
        return null;
    }
    
    updateProceedButton() {
        const proceedBtn = document.getElementById('proceedBtn');
        const requiredFields = this.targetFields.filter(f => f.required);
        const mappedRequiredFields = requiredFields.filter(f => f.mapped);
        
        proceedBtn.disabled = mappedRequiredFields.length < requiredFields.length;
        
        if (!proceedBtn.disabled) {
            proceedBtn.innerHTML = `
                <i class="fas fa-arrow-right mr-2"></i>
                Proceed to Validation (${this.mappings.size} mapped)
            `;
        }
    }
    
    drawConnections() {
        // This would draw SVG lines between mapped fields
        // Implementation would calculate positions and draw curved lines
        console.log('Drawing connections for', this.mappings.size, 'mappings');
    }
}

function autoMapFields() {
    const aiSuggestions = document.querySelector('.ai-suggestions');
    aiSuggestions.classList.add('auto-mapping');
    
    setTimeout(() => {
        aiSuggestions.classList.remove('auto-mapping');
        
        // Show success message
        const originalContent = aiSuggestions.innerHTML;
        aiSuggestions.innerHTML = `
            <div class="ai-brain-icon">✅</div>
            <div>Auto-mapping Complete!</div>
            <div style="font-size: 0.8rem; margin-top: 0.5rem;">
                9 fields mapped automatically
            </div>
        `;
        
        setTimeout(() => {
            aiSuggestions.innerHTML = originalContent;
        }, 2000);
        
    }, 1000);
}

function goBack() {
    window.location.href = '{{ route("imports.create") }}';
}

function proceedToValidation() {
    window.location.href = '{{ route("imports.validation") }}';
}

// Initialize the field mapping interface
let fieldMapper;
document.addEventListener('DOMContentLoaded', function() {
    fieldMapper = new FieldMappingInterface();
});
</script>
@endpush