@extends('layouts.admin')

@section('title', 'Vocabulary Details')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2 class="chinese-text">{{ $vocabulary->word }}</h2>
        <p class="text-muted">{{ $vocabulary->pinyin }} - {{ $vocabulary->meaning }}</p>
    </div>
    <div>
        <a href="{{ route('admin.vocabularies.edit', $vocabulary->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.vocabularies.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Basic Information
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Word:</dt>
                    <dd class="col-sm-9"><span class="chinese-text" style="font-size: 2rem;">{{ $vocabulary->word }}</span></dd>
                    
                    <dt class="col-sm-3">Simplified:</dt>
                    <dd class="col-sm-9 chinese-text">{{ $vocabulary->simplified }}</dd>
                    
                    <dt class="col-sm-3">Traditional:</dt>
                    <dd class="col-sm-9 chinese-text">{{ $vocabulary->traditional }}</dd>
                    
                    <dt class="col-sm-3">Pinyin:</dt>
                    <dd class="col-sm-9">{{ $vocabulary->pinyin }}</dd>
                    
                    <dt class="col-sm-3">Phonetic:</dt>
                    <dd class="col-sm-9">{{ $vocabulary->phonetic ?? '-' }}</dd>
                    
                    <dt class="col-sm-3">Meaning (EN):</dt>
                    <dd class="col-sm-9">{{ $vocabulary->meaning }}</dd>
                    
                    <dt class="col-sm-3">Meaning (VI):</dt>
                    <dd class="col-sm-9">{{ $vocabulary->meaning_vi ?? '-' }}</dd>
                    
                    <dt class="col-sm-3">Meaning (ZH):</dt>
                    <dd class="col-sm-9 chinese-text">{{ $vocabulary->meaning_zh ?? '-' }}</dd>
                    
                    <dt class="col-sm-3">Part of Speech:</dt>
                    <dd class="col-sm-9"><span class="badge bg-secondary">{{ $vocabulary->part_of_speech ?? '-' }}</span></dd>
                    
                    <dt class="col-sm-3">Topic:</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('admin.topics.show', $vocabulary->topic->id) }}">
                            {{ $vocabulary->topic->name }} ({{ $vocabulary->topic->name_zh }})
                        </a>
                    </dd>
                    
                    <dt class="col-sm-3">Level:</dt>
                    <dd class="col-sm-9"><span class="badge bg-info">{{ $vocabulary->level }}</span></dd>
                </dl>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-chat-quote"></i> Example & Definition
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Example Sentence:</dt>
                    <dd class="col-sm-9">
                        <span class="chinese-text">{{ $vocabulary->example_sentence ?? '-' }}</span>
                    </dd>
                    
                    <dt class="col-sm-3">Translation:</dt>
                    <dd class="col-sm-9">{{ $vocabulary->example_translation ?? '-' }}</dd>
                    
                    <dt class="col-sm-3">Highlight:</dt>
                    <dd class="col-sm-9 chinese-text">{{ $vocabulary->example_highlight ?? '-' }}</dd>
                    
                    <dt class="col-sm-3">Definition:</dt>
                    <dd class="col-sm-9">{{ $vocabulary->definition ?? '-' }}</dd>
                </dl>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <i class="bi bi-grid"></i> Additional Information
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Radical Info:</dt>
                    <dd class="col-sm-9">{{ $vocabulary->radical_info ?? '-' }}</dd>
                    
                    <dt class="col-sm-3">Stroke Count:</dt>
                    <dd class="col-sm-9">{{ $vocabulary->stroke_count ?? '-' }}</dd>
                    
                    <dt class="col-sm-3">Tone Pattern:</dt>
                    <dd class="col-sm-9">{{ $vocabulary->tone_pattern ?? '-' }}</dd>
                    
                    <dt class="col-sm-3">Related Words:</dt>
                    <dd class="col-sm-9">
                        @if($vocabulary->related_words)
                            @foreach($vocabulary->related_words as $word)
                                <span class="badge bg-light text-dark chinese-text me-1">{{ $word }}</span>
                            @endforeach
                        @else
                            -
                        @endif
                    </dd>
                    
                    <dt class="col-sm-3">Similar Chars:</dt>
                    <dd class="col-sm-9">
                        @if($vocabulary->similar_chars)
                            @foreach($vocabulary->similar_chars as $char)
                                <span class="badge bg-light text-dark chinese-text me-1">{{ $char }}</span>
                            @endforeach
                        @else
                            -
                        @endif
                    </dd>
                    
                    <dt class="col-sm-3">Audio URL:</dt>
                    <dd class="col-sm-9">
                        @if($vocabulary->pronunciation_audio)
                            <a href="{{ $vocabulary->pronunciation_audio }}" target="_blank">
                                <i class="bi bi-volume-up"></i> Play Audio
                            </a>
                        @else
                            -
                        @endif
                    </dd>
                    
                    <dt class="col-sm-3">Image URL:</dt>
                    <dd class="col-sm-9">
                        @if($vocabulary->image_url)
                            <a href="{{ $vocabulary->image_url }}" target="_blank">
                                <i class="bi bi-image"></i> View Image
                            </a>
                        @else
                            -
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-translate"></i> Translations
            </div>
            <div class="card-body">
                @forelse($vocabulary->translations as $translation)
                <div class="mb-3 p-2 border rounded">
                    <strong class="badge bg-secondary">{{ strtoupper($translation->language_code) }}</strong>
                    <p class="mb-1 mt-2"><strong>{{ $translation->meaning }}</strong></p>
                    @if($translation->example_translation)
                    <small class="text-muted">{{ $translation->example_translation }}</small>
                    @endif
                </div>
                @empty
                <p class="text-muted mb-0">No translations yet</p>
                @endforelse
            </div>
        </div>
        
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-info-circle"></i> Metadata</h6>
                <ul class="small mb-0">
                    <li>ID: {{ $vocabulary->id }}</li>
                    <li>Created: {{ $vocabulary->created_at->format('Y-m-d H:i') }}</li>
                    <li>Updated: {{ $vocabulary->updated_at->format('Y-m-d H:i') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
