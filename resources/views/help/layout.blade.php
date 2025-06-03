<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - UCUA Help Center</title>
    @vite(['resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .help-search-results {
            max-height: 300px;
            overflow-y: auto;
        }
        .help-section {
            scroll-margin-top: 100px;
        }
        .help-item {
            transition: all 0.3s ease;
        }
        .help-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .step-number {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            flex-shrink: 0;
        }
        .search-highlight {
            background-color: #fef3c7;
            padding: 2px 4px;
            border-radius: 3px;
        }

        /* Ensure proper layout and visibility */
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Fix header positioning */
        header {
            position: relative;
            z-index: 10;
            width: 100%;
        }

        /* Ensure content is visible */
        .main-content {
            position: relative;
            z-index: 1;
        }

        /* Fix sidebar positioning */
        .help-sidebar {
            position: sticky;
            top: 2rem;
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
        }

        /* Debug styles to ensure visibility */
        .debug-visible {
            border: 2px solid red !important;
            background-color: yellow !important;
            min-height: 50px !important;
        }

        /* Ensure all text is visible */
        * {
            color: inherit !important;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('images/logo.png') }}" alt="UCUA Logo" class="h-10">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">@yield('page-title')</h1>
                            <p class="text-sm text-gray-600">@yield('page-subtitle')</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Search Box -->
                        <div class="relative">
                            <input type="text" 
                                   id="helpSearch" 
                                   placeholder="Search help topics..." 
                                   class="w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                            
                            <!-- Search Results Dropdown -->
                            <div id="searchResults" class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden z-50">
                                <div class="help-search-results p-2">
                                    <!-- Results will be populated here -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Back to Dashboard -->
                        <a href="@yield('dashboard-route')" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 main-content">
            <div class="flex gap-8">
                <!-- Sidebar Navigation -->
                <div class="w-64 flex-shrink-0">
                    <div class="bg-white rounded-lg shadow-sm border p-6 help-sidebar">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Help Topics</h3>
                        <nav class="space-y-2">
                            @yield('help-navigation')
                        </nav>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    @yield('help-content')
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Search Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('helpSearch');
            const searchResults = document.getElementById('searchResults');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            });

            function performSearch(query) {
                const searchUrl = '@yield("search-route")';
                
                fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data.results, data.query);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            }

            function displaySearchResults(results, query) {
                const resultsContainer = searchResults.querySelector('.help-search-results');
                
                if (results.length === 0) {
                    resultsContainer.innerHTML = `
                        <div class="p-4 text-center text-gray-500">
                            <i class="fas fa-search mb-2"></i>
                            <p>No results found for "${query}"</p>
                        </div>
                    `;
                } else {
                    resultsContainer.innerHTML = results.map(result => `
                        <div class="p-3 hover:bg-gray-50 rounded cursor-pointer border-b border-gray-100 last:border-b-0" 
                             onclick="scrollToSection('${result.section_key}')">
                            <div class="font-medium text-gray-900">${result.title}</div>
                            <div class="text-sm text-gray-600 mt-1">${result.content}</div>
                            <div class="text-xs text-blue-600 mt-1">
                                <i class="fas fa-folder mr-1"></i>${result.section}
                            </div>
                        </div>
                    `).join('');
                }
                
                searchResults.classList.remove('hidden');
            }

            function scrollToSection(sectionKey) {
                const section = document.getElementById(sectionKey);
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth' });
                    searchResults.classList.add('hidden');
                    searchInput.value = '';
                }
            }

            // Hide search results when clicking outside
            document.addEventListener('click', function(event) {
                if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                    searchResults.classList.add('hidden');
                }
            });

            // Make scrollToSection globally available
            window.scrollToSection = scrollToSection;
        });
    </script>
</body>
</html>
