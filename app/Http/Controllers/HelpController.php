<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpController extends Controller
{
    /**
     * Show help page for regular users
     */
    public function userHelp()
    {
        $helpSections = $this->getUserHelpContent();
        return view('help.user', compact('helpSections'));
    }

    /**
     * Show help page for admin users
     */
    public function adminHelp()
    {
        $helpSections = $this->getAdminHelpContent();
        return view('help.admin', compact('helpSections'));
    }

    /**
     * Show help page for UCUA officers
     */
    public function ucuaHelp()
    {
        $helpSections = $this->getUcuaHelpContent();
        return view('help.ucua', compact('helpSections'));
    }

    /**
     * Show help page for department users
     */
    public function departmentHelp()
    {
        $helpSections = $this->getDepartmentHelpContent();
        return view('help.department', compact('helpSections'));
    }

    /**
     * Search help content for regular users
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $results = $this->searchHelpContent($query, 'user');
        
        return response()->json([
            'results' => $results,
            'query' => $query
        ]);
    }

    /**
     * Search help content for admin users
     */
    public function adminSearch(Request $request)
    {
        $query = $request->get('q', '');
        $results = $this->searchHelpContent($query, 'admin');
        
        return response()->json([
            'results' => $results,
            'query' => $query
        ]);
    }

    /**
     * Search help content for UCUA officers
     */
    public function ucuaSearch(Request $request)
    {
        $query = $request->get('q', '');
        $results = $this->searchHelpContent($query, 'ucua');
        
        return response()->json([
            'results' => $results,
            'query' => $query
        ]);
    }

    /**
     * Search help content for department users
     */
    public function departmentSearch(Request $request)
    {
        $query = $request->get('q', '');
        $results = $this->searchHelpContent($query, 'department');
        
        return response()->json([
            'results' => $results,
            'query' => $query
        ]);
    }

    /**
     * Get help content for regular users
     */
    private function getUserHelpContent()
    {
        return [
            'getting_started' => [
                'title' => 'Getting Started',
                'icon' => 'fas fa-play-circle',
                'items' => [
                    [
                        'title' => 'Account Registration',
                        'content' => 'Learn how to create your account and verify your email address.',
                        'steps' => [
                            'Visit the registration page from the login screen',
                            'Fill in your personal details (name, email, phone)',
                            'Create a secure password',
                            'Verify your email with the OTP sent to your inbox',
                            'Complete your profile setup'
                        ]
                    ],
                    [
                        'title' => 'Login Process',
                        'content' => 'Step-by-step guide to accessing your account.',
                        'steps' => [
                            'Go to the user login page',
                            'Enter your registered email and password',
                            'Complete OTP verification (check your email)',
                            'Access your dashboard'
                        ]
                    ]
                ]
            ],
            'dashboard' => [
                'title' => 'Dashboard Navigation',
                'icon' => 'fas fa-tachometer-alt',
                'items' => [
                    [
                        'title' => 'Dashboard Overview',
                        'content' => 'Understanding your dashboard and its features.',
                        'steps' => [
                            'View your report statistics (total, pending, solved)',
                            'Check recent reports in the summary table',
                            'Navigate using the sidebar menu',
                            'Access quick actions from the dashboard cards'
                        ]
                    ],
                    [
                        'title' => 'Sidebar Navigation',
                        'content' => 'Learn about the main navigation menu.',
                        'steps' => [
                            'Report Overview - View dashboard statistics',
                            'Submit Report - Create new safety reports',
                            'My Reports - View all your submitted reports',
                            'Track Report - Check status of specific reports',
                            'Report History - View past reports and their outcomes',
                            'Profile - Manage your account settings'
                        ]
                    ]
                ]
            ],
            'reporting' => [
                'title' => 'Safety Reporting',
                'icon' => 'fas fa-exclamation-triangle',
                'items' => [
                    [
                        'title' => 'Submitting a Report',
                        'content' => 'How to create and submit safety incident reports.',
                        'steps' => [
                            'Click "Submit Report" from the sidebar',
                            'Fill in incident details (location, date, time)',
                            'Select incident type and priority level',
                            'Describe unsafe conditions or acts observed',
                            'Add any additional comments or recommendations',
                            'Review and submit your report'
                        ]
                    ],
                    [
                        'title' => 'Report Status Tracking',
                        'content' => 'Monitor the progress of your submitted reports.',
                        'steps' => [
                            'Use "Track Report" to search by report ID',
                            'Check status updates in "My Reports"',
                            'View department remarks and responses',
                            'Monitor resolution progress and timelines'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get help content for admin users
     */
    private function getAdminHelpContent()
    {
        return [
            'user_management' => [
                'title' => 'User Management',
                'icon' => 'fas fa-users',
                'items' => [
                    [
                        'title' => 'Managing Users',
                        'content' => 'How to create, edit, and manage system users.',
                        'steps' => [
                            'Access "Manage Users" from the admin sidebar',
                            'View all registered users and their roles',
                            'Create new admin or UCUA officer accounts',
                            'Edit user information and permissions',
                            'Deactivate or reactivate user accounts'
                        ]
                    ],
                    [
                        'title' => 'Role Assignment',
                        'content' => 'Assigning roles and permissions to users.',
                        'steps' => [
                            'Select user from the user management page',
                            'Choose appropriate role (Admin, UCUA Officer, Regular User)',
                            'Set specific permissions if needed',
                            'Save changes and notify the user'
                        ]
                    ]
                ]
            ],
            'department_management' => [
                'title' => 'Department Management',
                'icon' => 'fas fa-building',
                'items' => [
                    [
                        'title' => 'Managing Departments',
                        'content' => 'Create and manage port departments.',
                        'steps' => [
                            'Go to "Departments" in the admin panel',
                            'Add new departments with contact details',
                            'Set department head information',
                            'Configure department login credentials',
                            'Assign departments to handle specific report types'
                        ]
                    ]
                ]
            ],
            'warning_letters' => [
                'title' => 'Warning Letter Management',
                'icon' => 'fas fa-envelope',
                'items' => [
                    [
                        'title' => 'Approving Warning Letters',
                        'content' => 'Review and approve UCUA warning letter suggestions.',
                        'steps' => [
                            'Access "Warning Letters" from admin panel',
                            'Review pending warning letter suggestions',
                            'Check violation details and escalation rules',
                            'Approve or reject warning letter requests',
                            'Monitor sent warning letters and responses'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get help content for UCUA officers
     */
    private function getUcuaHelpContent()
    {
        return [
            'report_review' => [
                'title' => 'Report Review Process',
                'icon' => 'fas fa-clipboard-check',
                'items' => [
                    [
                        'title' => 'Reviewing Reports',
                        'content' => 'How to review and process safety reports.',
                        'steps' => [
                            'Access reports from the UCUA dashboard',
                            'Review incident details and evidence',
                            'Assign reports to appropriate departments',
                            'Monitor department responses and actions',
                            'Add investigation notes and remarks'
                        ]
                    ],
                    [
                        'title' => 'Department Assignment',
                        'content' => 'Assigning reports to relevant departments.',
                        'steps' => [
                            'Go to "Assign Departments" page',
                            'Select unassigned reports',
                            'Choose appropriate handling department',
                            'Set priority and deadline if needed',
                            'Notify department of assignment'
                        ]
                    ]
                ]
            ],
            'warning_system' => [
                'title' => 'Warning Letter System',
                'icon' => 'fas fa-exclamation-triangle',
                'items' => [
                    [
                        'title' => 'Suggesting Warning Letters',
                        'content' => 'How to suggest warning letters for violations.',
                        'steps' => [
                            'Review completed investigation reports',
                            'Identify violations requiring formal warnings',
                            'Create warning letter suggestions',
                            'Submit to admin for approval',
                            'Track warning letter status and responses'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get help content for department users
     */
    private function getDepartmentHelpContent()
    {
        return [
            'login_access' => [
                'title' => 'Login & Access',
                'icon' => 'fas fa-sign-in-alt',
                'items' => [
                    [
                        'title' => 'Department Login',
                        'content' => 'How to access your department dashboard.',
                        'steps' => [
                            'Go to the department login page',
                            'Enter your department credentials',
                            'Complete OTP verification',
                            'Access your department dashboard'
                        ]
                    ]
                ]
            ],
            'report_handling' => [
                'title' => 'Report Management',
                'icon' => 'fas fa-tasks',
                'items' => [
                    [
                        'title' => 'Viewing Assigned Reports',
                        'content' => 'How to view and manage reports assigned to your department.',
                        'steps' => [
                            'Check pending reports on your dashboard',
                            'Review report details and incident information',
                            'View UCUA officer remarks and instructions',
                            'Check deadlines and priority levels'
                        ]
                    ],
                    [
                        'title' => 'Adding Department Remarks',
                        'content' => 'How to respond to reports and add remarks.',
                        'steps' => [
                            'Open the assigned report',
                            'Review all incident details thoroughly',
                            'Add your department\'s response and actions taken',
                            'Update report status (in progress, resolved)',
                            'Submit remarks for UCUA review'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Search through help content
     */
    private function searchHelpContent($query, $userType)
    {
        if (empty($query)) {
            return [];
        }

        $helpContent = match($userType) {
            'admin' => $this->getAdminHelpContent(),
            'ucua' => $this->getUcuaHelpContent(),
            'department' => $this->getDepartmentHelpContent(),
            default => $this->getUserHelpContent()
        };

        $results = [];
        $query = strtolower($query);

        foreach ($helpContent as $sectionKey => $section) {
            foreach ($section['items'] as $item) {
                $title = strtolower($item['title']);
                $content = strtolower($item['content']);
                $steps = strtolower(implode(' ', $item['steps'] ?? []));

                if (str_contains($title, $query) || 
                    str_contains($content, $query) || 
                    str_contains($steps, $query)) {
                    
                    $results[] = [
                        'section' => $section['title'],
                        'title' => $item['title'],
                        'content' => $item['content'],
                        'section_key' => $sectionKey
                    ];
                }
            }
        }

        return $results;
    }
}
