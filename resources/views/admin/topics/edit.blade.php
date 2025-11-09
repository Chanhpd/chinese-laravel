@extends('layouts.admin')

@section('title', 'Edit Topic - ' . $topic->name)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="bi bi-pencil"></i> Edit Topic</h2>
        <p class="text-muted">{{ $topic->name }}</p>
    </div>
    <a href="{{ route('admin.topics.show', $topic->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-folder"></i> Basic Information
            </div>
            <div class="card-body">
                <form action="{{ route('admin.topics.update', $topic->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name (English) <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $topic->name) }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="name_zh" class="form-label">Name (Chinese) <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name_zh') is-invalid @enderror" 
                               id="name_zh" 
                               name="name_zh" 
                               value="{{ old('name_zh', $topic->name_zh) }}" 
                               required>
                        @error('name_zh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3">{{ old('description', $topic->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Image URL</label>
                        <input type="url" 
                               class="form-control @error('image_url') is-invalid @enderror" 
                               id="image_url" 
                               name="image_url" 
                               value="{{ old('image_url', $topic->image_url) }}"
                               placeholder="https://example.com/images/topic.jpg">
                        @error('image_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" 
                                   class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" 
                                   name="sort_order" 
                                   value="{{ old('sort_order', $topic->sort_order) }}">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       {{ old('is_active', $topic->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="{{ route('admin.topics.show', $topic->id) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Topic
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Translations Section -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-translate"></i> Translations (Optional)
            </div>
            <div class="card-body">
                <form action="{{ route('admin.topics.translations.update', $topic->id) }}" method="POST">
                    @csrf
                    
                    <div class="accordion" id="translationsAccordion">
                        @foreach(['de' => 'German', 'es' => 'Spanish', 'fr' => 'French', 'it' => 'Italian', 'ja' => 'Japanese', 'ko' => 'Korean', 'ru' => 'Russian', 'vi' => 'Vietnamese', 'zh' => 'Chinese'] as $code => $language)
                        @php
                            $translation = $topic->translations->where('language_code', $code)->first();
                        @endphp
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $code }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $code }}">
                                    <span class="badge bg-secondary me-2">{{ strtoupper($code) }}</span>
                                    {{ $language }}
                                    @if($translation)
                                        <i class="bi bi-check-circle text-success ms-2"></i>
                                    @endif
                                </button>
                            </h2>
                            <div id="collapse{{ $code }}" class="accordion-collapse collapse" data-bs-parent="#translationsAccordion">
                                <div class="accordion-body">
                                    <input type="hidden" name="translations[{{ $loop->index }}][language_code]" value="{{ $code }}">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="translations[{{ $loop->index }}][name]" 
                                               value="{{ $translation->name ?? '' }}">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" 
                                                  name="translations[{{ $loop->index }}][description]" 
                                                  rows="2">{{ $translation->description ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Update Translations
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-light mb-3">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-info-circle"></i> Information</h6>
                <ul class="small mb-0">
                    <li>Created: {{ $topic->created_at->format('Y-m-d H:i') }}</li>
                    <li>Updated: {{ $topic->updated_at->format('Y-m-d H:i') }}</li>
                    <li>Vocabularies: {{ $topic->vocabularies()->count() }}</li>
                </ul>
            </div>
        </div>
        
        <div class="card border-danger">
            <div class="card-body">
                <h6 class="card-title text-danger"><i class="bi bi-exclamation-triangle"></i> Danger Zone</h6>
                <p class="small mb-3">Once you delete a topic, there is no going back. Please be certain.</p>
                <form action="{{ route('admin.topics.destroy', $topic->id) }}" 
                      method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this topic? This action cannot be undone!');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-trash"></i> Delete Topic
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
