@extends('layouts.admin')

@section('title', isset($vocabulary) ? 'Edit Vocabulary' : 'Create Vocabulary')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>
            <i class="bi bi-{{ isset($vocabulary) ? 'pencil' : 'plus-circle' }}"></i> 
            {{ isset($vocabulary) ? 'Edit Vocabulary' : 'Create New Vocabulary' }}
        </h2>
        @if(isset($vocabulary))
        <p class="text-muted chinese-text">{{ $vocabulary->word }}</p>
        @endif
    </div>
    <a href="{{ isset($vocabulary) ? route('admin.vocabularies.show', $vocabulary->id) : route('admin.vocabularies.index') }}" 
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<form action="{{ isset($vocabulary) ? route('admin.vocabularies.update', $vocabulary->id) : route('admin.vocabularies.store') }}" 
      method="POST">
    @csrf
    @if(isset($vocabulary))
        @method('PUT')
    @endif
    
    <div class="row">
        <div class="col-md-8">
            <!-- Basic Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Basic Information
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="topic_id" class="form-label">Topic <span class="text-danger">*</span></label>
                            <select class="form-select @error('topic_id') is-invalid @enderror" 
                                    id="topic_id" 
                                    name="topic_id" 
                                    required>
                                <option value="">Select a topic</option>
                                @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" 
                                        {{ old('topic_id', $preselectedTopicId ?? $vocabulary->topic_id ?? '') == $topic->id ? 'selected' : '' }}>
                                    {{ $topic->name }} ({{ $topic->name_zh }})
                                </option>
                                @endforeach
                            </select>
                            @error('topic_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="level" class="form-label">HSK Level <span class="text-danger">*</span></label>
                            <select class="form-select @error('level') is-invalid @enderror" 
                                    id="level" 
                                    name="level" 
                                    required>
                                @foreach(['HSK1', 'HSK2', 'HSK3', 'HSK4', 'HSK5', 'HSK6'] as $level)
                                <option value="{{ $level }}" 
                                        {{ old('level', $vocabulary->level ?? 'HSK1') == $level ? 'selected' : '' }}>
                                    {{ $level }}
                                </option>
                                @endforeach
                            </select>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="word" class="form-label">Word (Chinese) <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('word') is-invalid @enderror" 
                                   id="word" 
                                   name="word" 
                                   value="{{ old('word', $vocabulary->word ?? '') }}" 
                                   required>
                            @error('word')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="pinyin" class="form-label">Pinyin</label>
                            <input type="text" 
                                   class="form-control @error('pinyin') is-invalid @enderror" 
                                   id="pinyin" 
                                   name="pinyin" 
                                   value="{{ old('pinyin', $vocabulary->pinyin ?? '') }}">
                            @error('pinyin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="phonetic" class="form-label">Phonetic</label>
                            <input type="text" 
                                   class="form-control @error('phonetic') is-invalid @enderror" 
                                   id="phonetic" 
                                   name="phonetic" 
                                   value="{{ old('phonetic', $vocabulary->phonetic ?? '') }}">
                            @error('phonetic')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="simplified" class="form-label">Simplified</label>
                            <input type="text" 
                                   class="form-control @error('simplified') is-invalid @enderror" 
                                   id="simplified" 
                                   name="simplified" 
                                   value="{{ old('simplified', $vocabulary->simplified ?? '') }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="traditional" class="form-label">Traditional</label>
                            <input type="text" 
                                   class="form-control @error('traditional') is-invalid @enderror" 
                                   id="traditional" 
                                   name="traditional" 
                                   value="{{ old('traditional', $vocabulary->traditional ?? '') }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="part_of_speech" class="form-label">Part of Speech</label>
                            <input type="text" 
                                   class="form-control @error('part_of_speech') is-invalid @enderror" 
                                   id="part_of_speech" 
                                   name="part_of_speech" 
                                   value="{{ old('part_of_speech', $vocabulary->part_of_speech ?? '') }}"
                                   placeholder="e.g. verb, noun, adjective">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meaning" class="form-label">Meaning (English) <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('meaning') is-invalid @enderror" 
                               id="meaning" 
                               name="meaning" 
                               value="{{ old('meaning', $vocabulary->meaning ?? '') }}" 
                               required>
                        @error('meaning')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="meaning_vi" class="form-label">Meaning (Vietnamese)</label>
                            <input type="text" 
                                   class="form-control @error('meaning_vi') is-invalid @enderror" 
                                   id="meaning_vi" 
                                   name="meaning_vi" 
                                   value="{{ old('meaning_vi', $vocabulary->meaning_vi ?? '') }}">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="meaning_zh" class="form-label">Meaning (Chinese)</label>
                            <input type="text" 
                                   class="form-control @error('meaning_zh') is-invalid @enderror" 
                                   id="meaning_zh" 
                                   name="meaning_zh" 
                                   value="{{ old('meaning_zh', $vocabulary->meaning_zh ?? '') }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="definition" class="form-label">Definition</label>
                        <textarea class="form-control @error('definition') is-invalid @enderror" 
                                  id="definition" 
                                  name="definition" 
                                  rows="2">{{ old('definition', $vocabulary->definition ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Example -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-chat-quote"></i> Example Sentence
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="example_sentence" class="form-label">Example Sentence (Chinese)</label>
                        <input type="text" 
                               class="form-control @error('example_sentence') is-invalid @enderror" 
                               id="example_sentence" 
                               name="example_sentence" 
                               value="{{ old('example_sentence', $vocabulary->example_sentence ?? '') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="example_translation" class="form-label">Example Translation (English)</label>
                        <input type="text" 
                               class="form-control @error('example_translation') is-invalid @enderror" 
                               id="example_translation" 
                               name="example_translation" 
                               value="{{ old('example_translation', $vocabulary->example_translation ?? '') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="example_highlight" class="form-label">Highlight Word</label>
                        <input type="text" 
                               class="form-control @error('example_highlight') is-invalid @enderror" 
                               id="example_highlight" 
                               name="example_highlight" 
                               value="{{ old('example_highlight', $vocabulary->example_highlight ?? '') }}"
                               placeholder="Which word to highlight in example">
                    </div>
                </div>
            </div>
            
            <!-- Character Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-grid"></i> Character Information
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="radical_info" class="form-label">Radical Info</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="radical_info" 
                                   name="radical_info" 
                                   value="{{ old('radical_info', $vocabulary->radical_info ?? '') }}"
                                   placeholder="e.g. 亻(person)">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="stroke_count" class="form-label">Stroke Count</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="stroke_count" 
                                   name="stroke_count" 
                                   value="{{ old('stroke_count', $vocabulary->stroke_count ?? '') }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="tone_pattern" class="form-label">Tone Pattern</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="tone_pattern" 
                                   name="tone_pattern" 
                                   value="{{ old('tone_pattern', $vocabulary->tone_pattern ?? '') }}"
                                   placeholder="e.g. 3-3">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-puzzle"></i> Practice Sentences (Fill in the blank with ___)
                        </label>
                        <small class="text-muted d-block mb-2">Add practice sentences where ___ represents the word to fill in. One sentence per line.</small>
                        <textarea class="form-control" 
                                  id="sentences_input" 
                                  rows="4"
                                  placeholder="Example:&#10;我每天___中文。&#10;他在学校___汉语。&#10;我想___一点新的单词。">{{ isset($vocabulary) && $vocabulary->sentences ? implode("\n", $vocabulary->sentences) : old('sentences_text') }}</textarea>
                        <small class="text-muted">Each line will be a separate practice sentence</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pronunciation_audio" class="form-label">Pronunciation Audio URL</label>
                            <input type="url" 
                                   class="form-control" 
                                   id="pronunciation_audio" 
                                   name="pronunciation_audio" 
                                   value="{{ old('pronunciation_audio', $vocabulary->pronunciation_audio ?? '') }}"
                                   placeholder="https://example.com/audio.mp3">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" 
                                   class="form-control" 
                                   id="image_url" 
                                   name="image_url" 
                                   value="{{ old('image_url', $vocabulary->image_url ?? '') }}"
                                   placeholder="https://example.com/image.jpg">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-3 bg-light">
                <div class="card-body">
                    <h6><i class="bi bi-info-circle"></i> Quick Tips</h6>
                    <ul class="small mb-0">
                        <li>Fill required fields marked with <span class="text-danger">*</span></li>
                        <li>Pinyin should include tone marks</li>
                        <li>Use simplified characters for most fields</li>
                        <li>You can add translations after creating</li>
                    </ul>
                </div>
            </div>
            
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> 
                            {{ isset($vocabulary) ? 'Update' : 'Create' }} Vocabulary
                        </button>
                        <a href="{{ isset($vocabulary) ? route('admin.vocabularies.show', $vocabulary->id) : route('admin.vocabularies.index') }}" 
                           class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
// Convert sentences textarea to array before form submit
document.querySelector('form').addEventListener('submit', function(e) {
    const sentencesInput = document.getElementById('sentences_input');
    const sentencesText = sentencesInput.value.trim();
    
    // Remove old hidden inputs if any
    document.querySelectorAll('input[name^="sentences["]').forEach(el => el.remove());
    
    if (sentencesText) {
        // Split by newlines and filter empty lines
        const sentences = sentencesText.split('\n')
            .map(line => line.trim())
            .filter(line => line.length > 0);
        
        // Create hidden inputs for each sentence
        sentences.forEach((sentence, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `sentences[${index}]`;
            input.value = sentence;
            this.appendChild(input);
        });
    }
});
</script>
@endpush
@endsection
