@extends('layouts.master')

@section('title')
    Command Dashboard
@endsection

@section('css')
    <style>
        .command-card {
            transition: all 0.3s ease;
        }

        .command-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .command-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .output-container {
            max-height: 300px;
            overflow-y: auto;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            padding: 1rem;
            font-family: monospace;
        }

        .command-status {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Command Dashboard</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">Command Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Scheduled Commands</h5>
                        <button id="run-all-btn" class="btn btn-primary">
                            <i class="ri-play-circle-line me-1"></i> Run All Commands
                        </button>
                    </div>

                    <div id="all-output" class="output-container mb-4 d-none">
                        <div class="d-flex justify-content-between mb-2">
                            <h6>Command Output</h6>
                            <button class="btn btn-sm btn-light close-output">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                        <div id="all-output-content"></div>
                    </div>

                    <div class="row">
                        @foreach ($commands as $command)
                            <div class="col-md-3 mb-4">
                                <div class="card command-card h-100">
                                    <div class="card-body text-center">
                                        <div class="command-status">
                                            <span class="status-indicator badge bg-light text-dark">Ready</span>
                                        </div>
                                        <div class="command-icon text-primary">
                                            <i class="{{ $command['icon'] }}"></i>
                                        </div>
                                        <h5 class="card-title">{{ $command['name'] }}</h5>
                                        <p class="card-text text-muted">{{ $command['description'] }}</p>
                                        <button class="btn btn-primary run-command-btn"
                                            data-command="{{ $command['command'] }}">
                                            <i class="ri-play-circle-line me-1"></i> Run Command
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Command Output</h5>
                    <div id="command-output" class="output-container">
                        <p class="text-muted">Run a command to see output here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for command execution -->
    <div class="modal fade" id="commandModal" tabindex="-1" aria-labelledby="commandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commandModalLabel">Running Command</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Please wait while the command is executing...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const commandModal = new bootstrap.Modal(document.getElementById('commandModal'));

            // Individual command execution
            document.querySelectorAll('.run-command-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const command = this.getAttribute('data-command');
                    const card = this.closest('.command-card');
                    const statusIndicator = card.querySelector('.status-indicator');

                    // Update status
                    statusIndicator.className = 'status-indicator badge bg-warning';
                    statusIndicator.textContent = 'Running...';

                    // Show modal
                    commandModal.show();

                    // Execute command
                    fetch('{{ route('commands.run') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                command: command
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Hide modal
                            commandModal.hide();

                            // Update output
                            document.getElementById('command-output').innerHTML =
                                `<h6>Command: ${command}</h6>
                             <div class="mt-2">${data.output || data.error || 'No output'}</div>`;

                            // Update status
                            if (data.success) {
                                statusIndicator.className = 'status-indicator badge bg-success';
                                statusIndicator.textContent = 'Success';
                            } else {
                                statusIndicator.className = 'status-indicator badge bg-danger';
                                statusIndicator.textContent = 'Failed';
                            }

                            // Reset status after 5 seconds
                            setTimeout(() => {
                                statusIndicator.className =
                                    'status-indicator badge bg-light text-dark';
                                statusIndicator.textContent = 'Ready';
                            }, 5000);
                        })
                        .catch(error => {
                            // Hide modal
                            commandModal.hide();

                            // Update status
                            statusIndicator.className = 'status-indicator badge bg-danger';
                            statusIndicator.textContent = 'Error';

                            // Update output
                            document.getElementById('command-output').innerHTML =
                                `<h6>Error executing command: ${command}</h6>
                             <div class="mt-2 text-danger">${error.message}</div>`;

                            // Reset status after 5 seconds
                            setTimeout(() => {
                                statusIndicator.className =
                                    'status-indicator badge bg-light text-dark';
                                statusIndicator.textContent = 'Ready';
                            }, 5000);
                        });
                });
            });

            // Run all commands
            document.getElementById('run-all-btn').addEventListener('click', function() {
                // Show modal
                commandModal.show();

                // Reset all statuses
                document.querySelectorAll('.status-indicator').forEach(indicator => {
                    indicator.className = 'status-indicator badge bg-warning';
                    indicator.textContent = 'Queued';
                });

                // Execute all commands
                fetch('{{ route('commands.run-all') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Hide modal
                        commandModal.hide();

                        // Show all output container
                        document.getElementById('all-output').classList.remove('d-none');

                        // Build output HTML
                        let outputHtml = '<h6>All Commands Execution Results:</h6>';

                        Object.entries(data.results).forEach(([command, result]) => {
                            // Update card status
                            const commandBtn = document.querySelector(
                                `[data-command="${command}"]`);
                            if (commandBtn) {
                                const card = commandBtn.closest('.command-card');
                                const statusIndicator = card.querySelector('.status-indicator');

                                if (result.success) {
                                    statusIndicator.className =
                                        'status-indicator badge bg-success';
                                    statusIndicator.textContent = 'Success';
                                } else {
                                    statusIndicator.className =
                                        'status-indicator badge bg-danger';
                                    statusIndicator.textContent = 'Failed';
                                }
                            }

                            // Add to output
                            outputHtml += `
                            <div class="mt-3 p-2 border-top">
                                <h6 class="mb-2">${command}</h6>
                                <div class="ps-3 ${result.success ? 'text-success' : 'text-danger'}">
                                    ${result.success ? 'Success' : 'Failed'}: 
                                    ${result.output || result.error || 'No output'}
                                </div>
                            </div>
                        `;
                        });

                        // Update output
                        document.getElementById('all-output-content').innerHTML = outputHtml;

                        // Reset statuses after 5 seconds
                        setTimeout(() => {
                            document.querySelectorAll('.status-indicator').forEach(
                                indicator => {
                                    indicator.className =
                                        'status-indicator badge bg-light text-dark';
                                    indicator.textContent = 'Ready';
                                });
                        }, 5000);
                    })
                    .catch(error => {
                        // Hide modal
                        commandModal.hide();

                        // Update output
                        document.getElementById('command-output').innerHTML =
                            `<h6>Error executing all commands</h6>
                         <div class="mt-2 text-danger">${error.message}</div>`;

                        // Reset all statuses
                        document.querySelectorAll('.status-indicator').forEach(indicator => {
                            indicator.className = 'status-indicator badge bg-light text-dark';
                            indicator.textContent = 'Ready';
                        });
                    });
            });

            // Close output button
            document.querySelectorAll('.close-output').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.output-container').classList.add('d-none');
                });
            });
        });
    </script>
@endpush
