@props(['comment', 'report', 'maxDepth' => 5, 'currentDepth' => 0])

<div class="threaded-comment {{ $currentDepth > 0 ? 'ml-' . min($currentDepth * 4, 20) : '' }} mb-4">
    <div class="bg-white rounded-lg shadow-sm border {{ $comment->isDepartmentRemark() ? 'border-green-200' : 'border-gray-200' }} p-4">
        <!-- Comment Header -->
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center space-x-2">
                <!-- Author Avatar/Icon -->
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-semibold
                    {{ $comment->user_type === 'ucua_officer' ? 'bg-blue-500' : 
                       ($comment->user_type === 'admin' ? 'bg-purple-500' : 
                       ($comment->user_type === 'department' ? 'bg-green-500' : 'bg-gray-500')) }}">
                    {{ substr($comment->authorName, 0, 1) }}
                </div>
                
                <!-- Author Info -->
                <div>
                    <div class="font-semibold text-gray-900 text-sm">
                        {{ $comment->authorName }}
                        @if($comment->isDepartmentRemark())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-2">
                                <i class="fas fa-lock mr-1"></i>
                                Confidential
                            </span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $comment->created_at->diffForHumans() }}
                        @if($comment->is_edited)
                            <span class="text-gray-400">(edited)</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Thread Level Indicator -->
            @if($currentDepth > 0)
                <div class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">
                    Level {{ $currentDepth }}
                </div>
            @endif
        </div>

        <!-- Comment Content -->
        <div class="prose prose-sm max-w-none mb-3">
            <p class="text-gray-700 whitespace-pre-wrap">{{ $comment->content }}</p>
        </div>

        <!-- Attachments -->
        @if($comment->attachments && $comment->attachments->count() > 0)
            <div class="mb-3">
                <div class="text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-paperclip mr-1"></i>
                    Attachments ({{ $comment->attachments->count() }})
                </div>
                <div class="space-y-2">
                    @foreach($comment->attachments as $attachment)
                        <div class="flex items-center space-x-3 p-2 bg-gray-50 rounded border">
                            <i class="{{ $attachment->iconClass }}"></i>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-gray-900 truncate">
                                    {{ $attachment->original_name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $attachment->formattedSize }}
                                </div>
                            </div>
                            <a href="{{ $attachment->url }}" 
                               target="_blank"
                               class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-download mr-1"></i>
                                Download
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Comment Actions -->
        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
            <div class="flex items-center space-x-4">
                <!-- Reply Button -->
                @if($currentDepth < $maxDepth)
                    <button type="button" 
                            class="text-sm text-green-600 hover:text-green-800 font-medium reply-btn"
                            data-comment-id="{{ $comment->id }}"
                            data-author-name="{{ $comment->authorName }}">
                        <i class="fas fa-reply mr-1"></i>
                        Reply
                    </button>
                @endif
                
                <!-- Reply Count -->
                @if($comment->reply_count > 0)
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-comments mr-1"></i>
                        {{ $comment->reply_count }} {{ Str::plural('reply', $comment->reply_count) }}
                    </span>
                @endif
            </div>
            
            <!-- Timestamp -->
            <div class="text-xs text-gray-400">
                {{ $comment->created_at->format('M j, Y \a\t g:i A') }}
            </div>
        </div>
    </div>

    <!-- Reply Form (Hidden by default) -->
    <div class="reply-form mt-3 hidden" id="reply-form-{{ $comment->id }}">
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <form method="POST" action="{{ route('department.add-remarks') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="report_id" value="{{ $report->id }}">
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Replying to {{ $comment->authorName }}
                    </label>
                    <textarea name="remarks" 
                              rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                              placeholder="Write your reply..."
                              required></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Attachment (optional)
                    </label>
                    <input type="file" 
                           name="attachment"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                    <p class="text-xs text-gray-500 mt-1">
                        Max 10MB. Allowed: JPG, PNG, PDF, DOC, XLS, TXT
                    </p>
                </div>
                
                <div class="flex items-center justify-end space-x-2">
                    <button type="button" 
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 cancel-reply-btn">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-reply mr-1"></i>
                        Post Reply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Nested Replies -->
    @if($comment->replies && $comment->replies->count() > 0 && $currentDepth < $maxDepth)
        <div class="replies mt-4 space-y-3">
            @foreach($comment->replies as $reply)
                <x-department-threaded-comment 
                    :comment="$reply" 
                    :report="$report" 
                    :maxDepth="$maxDepth" 
                    :currentDepth="$currentDepth + 1" />
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle reply button clicks
    document.querySelectorAll('.reply-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            
            // Hide all other reply forms
            document.querySelectorAll('.reply-form').forEach(form => {
                if (form.id !== `reply-form-${commentId}`) {
                    form.classList.add('hidden');
                }
            });
            
            // Toggle current reply form
            replyForm.classList.toggle('hidden');
            
            // Focus on textarea if showing
            if (!replyForm.classList.contains('hidden')) {
                const textarea = replyForm.querySelector('textarea[name="remarks"]');
                textarea.focus();
            }
        });
    });
    
    // Handle cancel reply button clicks
    document.querySelectorAll('.cancel-reply-btn').forEach(button => {
        button.addEventListener('click', function() {
            const replyForm = this.closest('.reply-form');
            replyForm.classList.add('hidden');
            
            // Clear form
            const form = replyForm.querySelector('form');
            form.reset();
        });
    });
});
</script>
@endpush
