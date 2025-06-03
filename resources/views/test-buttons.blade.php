@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Button Functionality Test Page</h1>
            
            <!-- Test Alerts -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Alert Tests</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-success mr-2" onclick="UCUA.Alert.show('success', 'This is a success message!')">
                        Test Success Alert
                    </button>
                    <button type="button" class="btn btn-danger mr-2" onclick="UCUA.Alert.show('error', 'This is an error message!')">
                        Test Error Alert
                    </button>
                    <button type="button" class="btn btn-warning mr-2" onclick="UCUA.Alert.show('warning', 'This is a warning message!')">
                        Test Warning Alert
                    </button>
                    <button type="button" class="btn btn-info" onclick="UCUA.Alert.show('info', 'This is an info message!')">
                        Test Info Alert
                    </button>
                </div>
            </div>

            <!-- Test Modals -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Modal Tests</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary mr-2" onclick="UCUA.Modal.show('testModal')">
                        Show Test Modal
                    </button>
                    <button type="button" class="btn btn-warning mr-2" onclick="UCUA.Modal.confirm('Are you sure you want to test this?', function() { alert('Confirmed!'); })">
                        Test Confirmation Modal
                    </button>
                    <button type="button" class="btn btn-danger" onclick="UCUA.Modal.confirm('This is a dangerous action!', function() { alert('Dangerous action confirmed!'); }, {title: 'Warning', confirmClass: 'btn-danger', confirmText: 'Yes, Do It'})">
                        Test Danger Confirmation
                    </button>
                </div>
            </div>

            <!-- Test Forms -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Form Tests</h5>
                </div>
                <div class="card-body">
                    <form data-ucua-form data-ucua-options='{"loadingText": "Testing..."}' action="#" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="testEmail">Email (Required)</label>
                            <input type="email" class="form-control" id="testEmail" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="testName">Name (Required)</label>
                            <input type="text" class="form-control" id="testName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="testMessage">Message</label>
                            <textarea class="form-control" id="testMessage" name="message" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Test Form</button>
                        <button type="button" class="btn btn-secondary ml-2" onclick="UCUA.Form.validate(this.form)">
                            Test Validation Only
                        </button>
                    </form>
                </div>
            </div>

            <!-- Test AJAX Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>AJAX Form Test</h5>
                </div>
                <div class="card-body">
                    <form id="ajaxTestForm" data-ajax="true">
                        @csrf
                        <div class="form-group">
                            <label for="ajaxEmail">Email</label>
                            <input type="email" class="form-control" id="ajaxEmail" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-success">Submit AJAX Form</button>
                    </form>
                </div>
            </div>

            <!-- Test Button Loading States -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Button Loading State Tests</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary mr-2" id="loadingTestBtn" onclick="testButtonLoading(this)">
                        Test Loading State
                    </button>
                    <button type="button" class="btn btn-secondary mr-2" onclick="testButtonWithCallback(this)">
                        Test Button with Callback
                    </button>
                    <button type="button" class="btn btn-info" data-ucua-confirm="Are you sure you want to test this button?" onclick="alert('Button clicked after confirmation!')">
                        Test Confirmation Button
                    </button>
                </div>
            </div>

            <!-- Test Navigation Buttons -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Navigation Tests</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary mr-2">
                        Go to Dashboard
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary mr-2" data-ucua-confirm="Are you sure you want to go to login?">
                        Go to Login (with confirmation)
                    </a>
                    <button type="button" class="btn btn-outline-success" onclick="window.history.back()">
                        Go Back
                    </button>
                </div>
            </div>

            <!-- Console Log Test -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Console Tests</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-dark mr-2" onclick="console.log('UCUA object:', window.UCUA)">
                        Log UCUA Object
                    </button>
                    <button type="button" class="btn btn-dark mr-2" onclick="console.log('jQuery version:', $.fn.jquery)">
                        Log jQuery Version
                    </button>
                    <button type="button" class="btn btn-dark" onclick="console.log('Bootstrap version:', $.fn.modal ? 'Available' : 'Not Available')">
                        Check Bootstrap
                    </button>
                </div>
            </div>

            <!-- Instructions -->
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> Testing Instructions</h5>
                <p><strong>To test your button functionality:</strong></p>
                <ol>
                    <li>Open your browser's Developer Tools (F12)</li>
                    <li>Go to the Console tab</li>
                    <li>Look for diagnostic messages starting with 🔍</li>
                    <li>Test each button above and check for errors</li>
                    <li>Use the diagnostic panel in the top-right corner</li>
                </ol>
                <p class="mb-0"><strong>All buttons should work without JavaScript errors in the console.</strong></p>
            </div>
        </div>
    </div>
</div>

<!-- Test Modal -->
<div class="modal fade" id="testModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Modal</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>This is a test modal to verify modal functionality is working correctly.</p>
                <p>You should be able to:</p>
                <ul>
                    <li>See this modal appear with proper styling</li>
                    <li>Close it using the X button</li>
                    <li>Close it by clicking outside the modal</li>
                    <li>Close it using the Close button below</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="UCUA.Modal.hide('testModal')">Close via UCUA</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function testButtonLoading(button) {
    UCUA.Button.setLoading(button, 'Testing...');
    
    setTimeout(function() {
        UCUA.Button.removeLoading(button);
        UCUA.Alert.show('success', 'Button loading test completed!');
    }, 3000);
}

function testButtonWithCallback(button) {
    UCUA.Button.handleButtonClick(button, function() {
        return new Promise(function(resolve) {
            setTimeout(function() {
                UCUA.Alert.show('info', 'Callback completed successfully!');
                resolve();
            }, 2000);
        });
    });
}

// Test AJAX form submission
$(document).ready(function() {
    $('#ajaxTestForm').on('submit', function(e) {
        e.preventDefault();
        
        // Simulate AJAX request
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        
        UCUA.Button.setLoading($btn, 'Submitting...');
        
        setTimeout(function() {
            UCUA.Button.removeLoading($btn);
            UCUA.Alert.show('success', 'AJAX form submitted successfully! (This was just a test)');
            $form[0].reset();
        }, 2000);
    });
});
</script>
@endpush
@endsection
